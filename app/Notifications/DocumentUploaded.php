<?php

namespace App\Notifications;

use App\Models\Document;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DocumentUploaded extends Notification
{
    use Queueable;

    protected $document;
    protected $user;
    protected $employee;

    public function __construct(Document $document)
    {
        $this->document = $document;
        $this->user = User::find($document->user_id);
        $this->employee = Employee::where('user_id', $document->user_id)->first();
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'user_id' => $this->user->user_id,
            'name' => $this->user->name,
            'profile_picture' => $this->employee->profile_picture ?? null,
            'message' => $this->user->name . ' uploaded a New document for review: ' . $this->document->getDocumentTypeName(),
            'document_info' => 'EmployeeID: ' . $this->user->user_id,
            'url' => route('admin.documents.view', $this->document->id),
            'document_type' => $this->document->getDocumentTypeName(),
        ];
    }
}