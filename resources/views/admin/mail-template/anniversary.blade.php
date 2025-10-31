 @extends( config('constants.ADMIN_FOLDER') .  config('constants.MAIL_TEMPLATE_FOLDER_PATH') .  'email-common-header-footer')

 @section('content')

 <div style="color: #000; background-color:#fff; width: 600px !important; margin: 0 auto;">
     <div style="padding:0px 30px;">

         <!-- For Work Anniversary -->
         <div><img src="{{ asset ('images/work-anniversary-email.png') }}" style="width:100%; object-fit:contain;"></div>

         <div><strong style="font-size: 13px;text-align: justify;color: #202020;"> Dear {{ ( isset($employeeName) ? $employeeName : '' ) }},</strong></div><br>
         <p style="font-weight:light; margin:0; font-size: 13px; color: #383838; font-family: 'Open Sans', sans-serif; ">It's been {{ ( isset($noOfYear) ? $noOfYear : '' ) }} year(s) with Acorn Universal Consultancy LLP! We are so happy you have decided to join us and grow with us.</p><br>
         <p style="font-weight:light; margin:0; font-size: 13px; color: #383838; font-family: 'Open Sans', sans-serif; ">You work hard and you make a great addition to our team. Happy work anniversary!</p><br>
         <p style="font-weight:light; margin:0; font-size: 13px; color: #383838; font-family: 'Open Sans', sans-serif; ">You are a valued member of our team and we are sincerely grateful that you have chosen to stay with us. We hope that you are as proud of your achievements as we are, and we look forward to spending many more years with you at {{ config('constants.COMPANY_NAME') }}.</p>
         <br>
         <!-- For Work Anniversary end-->
     </div>
 </div>
 <?php /*  @endsection */ ?>



 @endsection