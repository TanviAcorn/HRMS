<!DOCTYPE html>
<html>
<head>
    <title>Probation Assessment Submitted</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color:rgb(133, 36, 19); padding: 20px; text-align: center;font-weight: bold;color: rgb(255, 255, 255);}
        .content { padding: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Probation Assessment Submitted</h2>
        </div>
        
        <div class="content">
            <p>Dear {{ $employee->v_employee_full_name }},</p>
            
            <p>Your probation assessment form has been reviewed and submitted by your manager, {{ $managerName }} on {{ $submissionDate }}.</p>
            
            <div style="background-color:rgb(133, 17, 17); padding: 15px; border-radius: 5px; margin: 15px 0 color: rgb(255, 255, 255);">
                <h3 style="margin-top: 0; color: #2c3e50;">Assessment Decision</h3>
                
                <p><strong>Decision:</strong> 
                    @if($assessment->vch_decision === 'confirm')
                        <span style="color: #27ae60;">Confirmed</span> - Your probation has been successfully completed.
                    @elseif($assessment->vch_decision === 'extend')
                        <span style="color: #f39c12;">Extended</span> - Your probation has been extended by 
                        {{ $assessment->i_extend_months }} months until {{ $assessment->dt_extend_upto_date }}.
                    @else
                        {{ ucfirst($assessment->vch_decision) }}
                    @endif
                </p>
                
                @if($assessment->vch_decision === 'extend' && $assessment->vch_training_details)
                    <p><strong>Areas for Improvement:</strong></p>
                    <p>{{ $assessment->vch_training_details }}</p>
                @endif
            </div>
            
            <p><strong>Next Steps:</strong></p>
            <ul>
                @if($assessment->vch_decision === 'confirm')
                    <li>You are now a confirmed employee of the organization</li>
                    <li>You will be eligible for all employee benefits as per company policy</li>
                @endif
                
                <li>For any queries, please contact your manager or HR department</li>
            </ul>
            
            <p>Thank you for your dedication and hard work during your probation period.</p>
            
            <p>Best regards,<br>
            {{ config('constants.ROLE_HR_TEAM') }}</p>
        </div>
        
    </div>
</body>
</html>
