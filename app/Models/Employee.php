<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Keep SoftDeletes
use Laravel\Scout\Searchable;

class Employee extends Model
{
    use HasFactory, SoftDeletes, Searchable; // Use SoftDeletes

    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'department_id', // Updated from department
        'position_id',   // Updated from position
        'phone',
        'address',
        'date_of_birth',
        'gender',
        'nationality',
        'marital_status',
        'start_date',
        'end_date',
        'employment_status',
        'user_id',
        'profile_picture',
        'status',
        'user_id'
    ];

    protected $dates = ['deleted_at']; // Track soft delete timestamp

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'user_id', 'user_id');
    }

    // Relationship with Department model
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Relationship with Position model
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    protected static function boot()
    {
        parent::boot();
    
        static::creating(function ($employee) {
            // ✅ Check if employee record already exists (even if soft-deleted)
            $existing = Employee::withTrashed()->where('user_id', $employee->user_id)->first();
    
            if ($existing) {
                throw new \Exception('An employee record already exists for this user.');
            }
        });
    
        static::deleting(function ($employee) {
            // ✅ Soft delete related records
            $employee->employmentHistories()->delete();
            $employee->educationalHistories()->delete();
        });
    }

    public function employmentHistories()
    {
        return $this->hasMany(EmploymentHistory::class, 'user_id', 'user_id');
    }

    public function educationalHistories()
    {
        return $this->hasMany(EducationalHistory::class, 'user_id', 'user_id');
    }
    
public function document()
{
    return $this->hasMany(EmployeeDocument::class); // If you have a documents table
}



   public function toSearchableArray()
{
    $this->load('department', 'position'); // Ensure relationships are loaded

    return [
        'user_id' => $this->user_id,
        'first_name' => $this->first_name,
        'last_name' => $this->last_name,
        'middle_name' => $this->middle_name,
        'email' => $this->email,
        'phone' => $this->phone,
        'address' => $this->address,
        'date_of_birth' => $this->date_of_birth,
        'gender' => $this->gender,
        'nationality' => $this->nationality,
        'marital_status' => $this->marital_status,
        'start_date' => $this->start_date,
        'end_date' => $this->end_date,
        'employment_status' => $this->employment_status,
        'profile_picture' => $this->profile_picture,
        'status' => $this->status,

        // ✅ Include Department Name instead of department_id
        'department' => $this->department ? $this->department->name : null,

        // ✅ Include Position Name instead of position_id
        'position' => $this->position ? $this->position->name : null,
    ];
}
public function emergencyContacts()
{
    return $this->hasMany(EmergencyContact::class);
}
    
}
