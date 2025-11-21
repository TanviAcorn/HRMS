<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Acorn Universal Consultancy LLP</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #B91C1C; border-radius: 10px 10px 0 0;">
        <tr>
            <td style="padding: 30px; text-align: center;">
                <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: bold; font-family: Arial, sans-serif;">Welcome to Acorn Universal Consultancy LLP!</h1>
            </td>
        </tr>
    </table>
    
    <div style="background: #ffffff; padding: 30px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 10px 10px;">
        <p style="font-size: 16px; margin-bottom: 20px;">Dear <strong>{{ $employeeName }}</strong>,</p>
        
        <p style="font-size: 15px; margin-bottom: 20px;">
            We are delighted to welcome you to <strong>Acorn Universal Consultancy LLP</strong> as a valued member of our growing family.
        </p>
        
        <p style="font-size: 15px; margin-bottom: 20px;">
            Your journey with us begins on <strong>{{ $joiningDate }}</strong> as <strong>{{ $designation }}</strong> in the <strong>{{ $department }}</strong> department. We are confident that your skills and enthusiasm will be a great addition to our team.
        </p>
        
        <div style="background: #f9fafb; border-left: 4px solid #B91C1C; padding: 20px; margin: 25px 0; border-radius: 5px;">
            <h3 style="color: #8B1538; margin-top: 0; margin-bottom: 15px; font-size: 18px;">📋 Your Onboarding Details:</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; color: #4b5563;">Employee ID:</td>
                    <td style="padding: 8px 0; color: #1f2937;">{{ $employeeCode }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; color: #4b5563;">Reporting Manager:</td>
                    <td style="padding: 8px 0; color: #1f2937;">{{ $managerName }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; color: #4b5563;">Work Location:</td>
                    <td style="padding: 8px 0; color: #1f2937;">{{ $location }}</td>
                </tr>
            </table>
        </div>
        

        <div style="background: #eff6ff; border-left: 4px solid #3b82f6; padding: 20px; margin: 25px 0; border-radius: 5px;">
            <p style="margin: 0; font-size: 16px; color: #1e40af; font-style: italic; text-align: center;">
                <strong>Dream | Believe | Achieve</strong>
            </p>
        </div>
        
        <p style="font-size: 15px; margin-bottom: 20px;">
            We encourage you to explore our culture and contribute to our shared success.
        </p>
        
        <p style="font-size: 15px; margin-bottom: 20px;">
            Should you have any queries, feel free to reach out to the HR Team.
        </p>
        
        <p style="font-size: 15px; margin-bottom: 5px;">Once again, welcome aboard!</p>
        
        <p style="font-size: 15px; margin-top: 30px; margin-bottom: 5px;"><strong>Warm Regards,</strong></p>
        <p style="font-size: 15px; margin: 0; color: #8B1538; font-weight: bold;">HR Department</p>
        <p style="font-size: 14px; margin: 0; color: #6b7280;">Acorn Universal Consultancy LLP</p>
    </div>
    
    <div style="text-align: center; padding: 20px; color: #9ca3af; font-size: 12px;">
        <p style="margin: 5px 0;">This is an automated email. Please do not reply to this message.</p>
        <p style="margin: 5px 0;">© {{ date('Y') }} Acorn Universal Consultancy LLP. All rights reserved.</p>
    </div>
</body>
</html>
