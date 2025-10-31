<?php

use Illuminate\Support\Facades\Route;
use App\EmployeeModel;
use App\Mail\ProbationAssessmentSubmitted;
use Illuminate\Support\Facades\Mail;

Route::get('/test-email', function () {
    // Replace with an actual employee ID that has an email
    $employee = EmployeeModel::find(1); // Change this to a valid employee ID
    
    if (!$employee) {
        return 'Employee not found';
    }
    
    try {
        $email = new ProbationAssessmentSubmitted(
            $employee,
            'Test Manager' // Test manager name
        );
        
        Mail::to($employee->v_outlook_email_id)->send($email);
        
        return 'Test email sent to: ' . $employee->v_outlook_email_id;
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage() . 
               '\n\nTrace:\n' . $e->getTraceAsString();
    }
});
