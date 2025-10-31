<?php

namespace App\Mail;

use App\EmployeeModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProbationAssessmentSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;
    public $managerName;
    public $submissionDate;
    public $assessment;

    /**
     * Create a new message instance.
     *
     * @param EmployeeModel $employee
     * @param string $managerName
     * @param \App\Models\ProbationAssessment $assessment
     * @return void
     */
    public function __construct(EmployeeModel $employee, string $managerName, $assessment)
    {
        $this->employee = $employee;
        $this->managerName = $managerName;
        $this->assessment = $assessment;
        $this->submissionDate = now()->format('F j, Y');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Probation Assessment Has Been Submitted')
                   ->view('emails.probation-assessment-submitted')
                   ->with([
                       'employee' => $this->employee,
                       'managerName' => $this->managerName,
                       'submissionDate' => $this->submissionDate,
                       'assessment' => $this->assessment
                   ]);
    }
}
