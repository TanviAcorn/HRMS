<?php

namespace App\Mail;

use App\EmployeeModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewEmployeeAssetsNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;
    public $assets;
    public $joiningDate;

    /**
     * Create a new message instance.
     *
     * @param EmployeeModel $employee
     * @param array $assets
     * @return void
     */
    public function __construct(EmployeeModel $employee, array $assets = [])
    {
        $this->employee = $employee;
        $this->assets = $assets;
        $this->joiningDate = $employee->dt_joining_date;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Employee Added - Asset Assignment Required')
                   ->view('emails.new_employee_assets_notification')
                   ->with([
                       'employee' => $this->employee,
                       'assets' => $this->assets,
                       'joiningDate' => $this->joiningDate
                   ]);
    }
}
