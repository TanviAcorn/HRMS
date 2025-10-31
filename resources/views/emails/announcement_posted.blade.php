<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>New Announcement</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; color: #333;">
    <h2 style="margin-bottom:8px; color:#d32f2f;">New Announcement Posted !</h2>
    @if(!empty($announcement->category))
    <p style="margin:0 0 10px 0;"><strong>Category:</strong> {{ $announcement->category }}</p>
    @endif
    <p style="margin-top:20px;">
        <a href="https://hrms.acornuniversalconsultancy.in/dashboard" target="_blank"
           style="display:inline-block; background:##000080; color:#ffffff; text-decoration:none; padding:10px 16px; border-radius:4px; font-weight:bold;">
            View Announcement here!
        </a>
    </p>
</body>
</html>
