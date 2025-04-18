<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Models\Employee;

class EmployeeActivityNotification extends Notification
{
    use Queueable;

    protected $message;
    protected $user_id;
    protected $isUpdate; // Flag to distinguish between create/update

    public function __construct($message, $user_id, $isUpdate = false)
    {
        $this->message = $message;
        $this->user_id = $user_id;
        $this->isUpdate = $isUpdate;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $user = User::find($this->user_id);
        $employee = Employee::where('user_id', $this->user_id)->first();

        return [
            'user_id' => $this->user_id,
            'name' => optional($user)->name ?? 'Unknown User',
            'profile_picture' => optional($employee)->profile_picture ?? optional($user)->profilePic ?? null,
            'message' => $this->message,
            'action_type' => $this->isUpdate ? 'updated' : 'created',
            'document_info' => 'EmployeeID: ' . $this->user_id,
            'url' => route('admin.notifications'),
        ];
    }
}