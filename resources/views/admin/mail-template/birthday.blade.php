 @extends( config('constants.ADMIN_FOLDER') .  config('constants.MAIL_TEMPLATE_FOLDER_PATH') .  'email-common-header-footer')

 @section('content')

 <div style="color: #000; background-color:#fff; width: 600px !important; margin: 0 auto;">
     <div style="padding:0px 30px;">
         <!-- happy birthday -->

         <div><img src="{{ asset ('images/birthday-img.jpg') }}" style="width:100%; object-fit:contain;"></div>
         <div><strong style="font-size: 13px;text-align: justify;color: #202020;">Dear Ms/Mr/Mrs. {{ ( isset($employeeName) ? $employeeName : '' ) }},</strong></div><br>

         <p style="font-weight:light; margin:0; font-size: 14px; color: #383838; font-family: 'Open Sans', sans-serif; ">Hope you are doing well.!</p> <br>
         <p style="font-weight:light; margin:0; font-size: 14px; color: #383838; font-family: 'Open Sans', sans-serif; ">Happy Birthday</p><br>

         <p style="color: #383838;font-size: 14px; margin:0;">We value your special day just as much as we value you. On your Birthday, we send you our warmest and most heartfelt wishes. <br> <br> We are thrilled to be able to share this great day with you, and glad to have you as a valuable asset of the company. <br> <br> We appreciate everything you've done to help us flourish and grow. <br> <br>"Our entire corporate family at {{ config('constants.COMPANY_NAME') }} wishes you a very HAPPY BIRTHDAY! We hope this year brings you immense success in all of your personal and professional endeavours." </p>
         <br>
         <!-- happy birthday end-->
     </div>
 </div>

 @endsection