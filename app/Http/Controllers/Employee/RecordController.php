<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use App\Notifications\EmployeeActivityNotification;
use App\Models\User;

class RecordController extends Controller
{
    public function index()
    {
        $record = Employee::where('user_id', auth()->user()->user_id)->get();
        return view('employee.records.index', compact('record'));
    }

    public function create()
    {
        $existingRecord = Employee::where('user_id', auth()->user()->user_id)->first();

        if ($existingRecord) {
            return redirect()->route('employee.records.index')
                            ->with('error', 'You already have an existing record.');
        }

        $departments = Department::with('positions')->get();

        return view('employee.records.create', [
            'userEmail' => auth()->user()->email,
            'departments' => $departments
        ]);
    }

    public function destroy($id)
    {
        $record = Employee::where('user_id', auth()->user()->user_id)->findOrFail($id);
        $record->delete();

        return redirect()->route('employee.records.index')->with('success', 'Record deleted successfully.');
    }

    public function store(Request $request)
    {
        $existingRecord = Employee::where('user_id', auth()->user()->user_id)->first();

        if ($existingRecord) {
            return redirect()->route('employee.records.index')
                            ->with('error', 'You can only create one record.');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:employees',
            'department' => 'required|exists:departments,id',
            'position' => 'required|exists:positions,id',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:100',
            'marital_status' => 'nullable|string|max:50',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'employment_status' => 'nullable|string|max:50',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'emergency_contact.name' => 'required|string|max:255',
            'emergency_contact.relationship' => 'required|string|max:255',
            'emergency_contact.phone' => 'required|string|max:20',
            'emergency_contact.email' => 'nullable|email|max:255',
            'emergency_contact.address' => 'nullable|string|max:500',
        ]);

        $profilePicturePath = null;
        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        $employee = Employee::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'email' => auth()->user()->email,
            'department_id' => $request->department,
            'position_id' => $request->position,
            'phone' => auth()->user()->phoneNumber,
            'address' => auth()->user()->address,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'nationality' => $request->nationality,
            'marital_status' => $request->marital_status,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'employment_status' => $request->employment_status,
            'user_id' => auth()->user()->user_id,
            'profile_picture' => $profilePicturePath,
        ]);

        // Create emergency contact
        if ($request->has('emergency_contact')) {
            $employee->emergencyContacts()->create($request->input('emergency_contact'));
        }

        // Sync profile picture to users.profilePic
        if ($profilePicturePath) {
            $user = User::find(auth()->user()->user_id);
            if ($user) {
                $user->profilePic = $profilePicturePath;
                $user->save();
            }
        }

        $admins = User::whereIn('role', ['admin', 'hr3'])->get();
        foreach ($admins as $admin) {
            $admin->notify(new EmployeeActivityNotification(
                auth()->user()->name . " has created a new employee record.",
                auth()->user()->user_id,
                false // This is not an update
            ));
        }

        return redirect()->route('employee.records.index')->with('success', 'Employee record created successfully.');
    }

    public function edit($id)
    {
        $record = Employee::where('user_id', auth()->user()->user_id)
            ->with(['department', 'position', 'emergencyContacts'])
            ->findOrFail($id);

        $departments = Department::all();
        $positions = Position::all();

        return view('employee.records.edit', compact('record', 'departments', 'positions'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $id,
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:100',
            'marital_status' => 'nullable|string|max:50',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'employment_status' => 'nullable|string|max:50',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'emergency_contact.name' => 'required|string|max:255',
            'emergency_contact.relationship' => 'required|string|max:255',
            'emergency_contact.phone' => 'required|string|max:20',
            'emergency_contact.email' => 'nullable|email|max:255',
            'emergency_contact.address' => 'nullable|string|max:500',
        ]);

        $record = Employee::where('user_id', auth()->user()->user_id)->findOrFail($id);

        if ($request->hasFile('profile_picture')) {
            if ($record->profile_picture) {
                Storage::disk('public')->delete($record->profile_picture);
            }

            $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'public');
            $validatedData['profile_picture'] = $profilePicturePath;

            // Sync profile picture to users.profilePic
            $user = User::find(auth()->user()->user_id);
            if ($user) {
                $user->profilePic = $profilePicturePath;
                $user->save();
            }
        }

        $record->update($validatedData);

        // Update or create emergency contact
        if ($request->has('emergency_contact')) {
            $record->emergencyContacts()->updateOrCreate(
                ['employee_id' => $record->id],
                $request->input('emergency_contact')
            );
        }

        $admins = User::whereIn('role', ['admin', 'hr3'])->get();
        foreach ($admins as $admin) {
            $admin->notify(new EmployeeActivityNotification(
                auth()->user()->name . " has updated their employee record.",
                auth()->user()->user_id,
                true // This is an update
            ));
        }

        return redirect()->route('employee.records.index')->with('success', 'Record updated successfully.');
    }
}