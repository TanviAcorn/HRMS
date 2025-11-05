<?php

namespace App\Mail;

use App\EmployeeModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DocumentUploaded extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;
    public $documentType;
    public $remarks;
    public $uploadDate;

    /**
     * Create a new message instance.
     *
     * @param EmployeeModel $employee
     * @param string $documentType
     * @param string|null $remarks
     * @return void
     */
    public function __construct(EmployeeModel $employee, string $documentType, ?string $remarks = null)
    {
        $this->employee = $employee;
        $this->documentType = $documentType;
        $this->remarks = $remarks;
        $this->uploadDate = now();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Document Added to Your Profile')
                   ->view('emails.document_uploaded')
                   ->with([
                       'employee' => $this->employee,
                       'documentType' => $this->documentType,
                       'remarks' => $this->remarks,
                       'uploadDate' => $this->uploadDate
                   ]);
    }
}
