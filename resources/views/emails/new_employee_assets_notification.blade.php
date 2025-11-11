<!DOCTYPE html>
<html>
<head>
    <title>New Employee Added - Asset Assignment Required</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9f9f9; }
        .header { background-color: #8B1538; color: white; text-align: center; padding: 20px; border-radius: 5px 5px 0 0; }
        .content { background-color: white; padding: 30px; border-radius: 0 0 5px 5px; }
        .employee-info { background-color: #f5f5f5; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .employee-info p { margin: 8px 0; }
        .assets-section { margin: 20px 0; }
        .assets-list { background-color: #f9f9f9; padding: 15px; border-left: 4px solid #8B1538; margin: 10px 0; }
        .asset-item { padding: 8px 0; border-bottom: 1px solid #e0e0e0; display: flex; align-items: center; }
        .asset-item:last-child { border-bottom: none; }
        .asset-item::before { content: "✓"; color: #28a745; font-weight: bold; margin-right: 10px; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; font-size: 12px; color: #777; }
        .alert-box { background-color: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .button {
            display: inline-block;
            padding: 12px 30px;
            margin: 20px 0;
            background-color: #8B1538;
            color: white !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        strong { color: #8B1538; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin: 0;">🔔 New Employee Asset Assignment Alert</h2>
        </div>
        
        <div class="content">
            <p>Hello IT & Admin Team,</p>
            
            <p>A new employee has been added to the HRMS system and requires asset assignment.</p>
            
            <div class="employee-info">
                <h3 style="margin-top: 0; color: #8B1538;">Employee Details</h3>
                <p><strong>Name:</strong> {{ $employee->v_employee_full_name }}</p>
                <p><strong>Employee Code:</strong> {{ $employee->v_employee_code }}</p>
                <p><strong>Email:</strong> {{ $employee->v_outlook_email_id ?? $employee->v_personal_email_id }}</p>
                <p><strong>Contact:</strong> {{ $employee->v_contact_no }}</p>
                @if($employee->designationInfo)
                <p><strong>Designation:</strong> {{ $employee->designationInfo->v_value }}</p>
                @endif
                @if($employee->teamInfo)
                <p><strong>Team:</strong> {{ $employee->teamInfo->v_value }}</p>
                @endif
                <p><strong>Joining Date:</strong> {{ date('F j, Y', strtotime($joiningDate)) }}</p>
            </div>
            
            @if(!empty($assets) && count($assets) > 0)
            <div class="assets-section">
                <h3 style="color: #8B1538;">Assets to be Assigned ({{ count($assets) }})</h3>
                <div class="assets-list">
                    @foreach($assets as $asset)
                    <div class="asset-item">{{ $asset }}</div>
                    @endforeach
                </div>
            </div>
            
            <div class="alert-box">
                <strong>⚠️ Action Required:</strong> Please ensure these assets are prepared and assigned to the employee before or on their joining date.
            </div>
            @else
            <div class="alert-box">
                <strong>ℹ️ Note:</strong> No specific assets have been assigned to this employee yet.
            </div>
            @endif
            
            <p>Please coordinate with the HR team if you have any questions regarding the asset requirements.</p>
            
            <p>Best regards,<br><strong>HR Team</strong></p>
        </div>
        
        <div class="footer">
            <p>This is an automated notification from the HRMS system.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
