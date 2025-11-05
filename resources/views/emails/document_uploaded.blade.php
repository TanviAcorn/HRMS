<!DOCTYPE html>
<html>
<head>
    <title>New Document Uploaded to Your Profile</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; padding: 20px 0; border-bottom: 1px solid #eee; }
        .content { padding: 20px 0; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; font-size: 12px; color: #777; }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 20px 0;
            background-color: #3490dc;
            color: white !important;
            text-decoration: none;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="content">
            <h2>New Document Uploaded to Your Profile</h2>
            
            <p>Hello {{ $employee->v_employee_full_name }},</p>
            
            <p>A new document has been uploaded to your profile by the HR team.</p>
            
            <p><strong>Document Type:</strong> {{ $documentType }}</p>
            
            <p><strong>Uploaded On:</strong> {{ $uploadDate->format('F j, Y h:i A') }}</p>
            
            <p>Please log in to your account to view the document.</p>
            
            <p>
                <a href="{{ url('/login') }}" class="button">Login to Portal</a>
            </p>
            
            <p>Thanks,<br>{{ trans('messages.hr-team') }}</p>
        </div>
    </div>
</body>
</html>
