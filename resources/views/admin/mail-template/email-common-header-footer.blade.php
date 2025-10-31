<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        td {
            padding: 0;
        }
    </style>
</head>

<body style="background-color:#fafafa; font-family: 'Open Sans', sans-serif;padding:20px;">
    <table cellpadding="10" cellspacing="0" align="center" border="0" style="border-collapse:collapse;border:0;color: #000; width: 600px !important; margin: 0 auto; background-color:#fff;">
        <thead>
            <tr>
                <th style="text-align:center; padding-bottom:5px;">
                    <a href="javascript:void(0);"><img src="{{ asset ('images/logo.png') }}" style="width:170px; object-fit:contain;"></a>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    @yield('content')
                </td>
            </tr>
            <tr>
                <td>
                    <div class="" style="line-height: 7px; color: #000; background-color:#fff; width: 600px !important; margin: 0 auto; padding-top: 20px; padding-top:10px;">
                            @if( isset($sendCommonFooter) && ($sendCommonFooter != true ) )

                            @else
                        <tr>
                            <td>
                                <div class="" style="padding: 0px 30px;max-width:600px;margin:0 auto;">
                                    <p style="font-weight:light; margin:0; font-size: 14px; color: #383838; font-family: 'Open Sans', sans-serif; margin-bottom:0;">Regards,</p>
                                    <p style="font-weight:light; margin:0; font-size: 14px; color: #383838; font-family: 'Open Sans', sans-serif; margin-bottom:10px;">HR team</p>
                                </div>
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <td>
                                <div class="copy-right-footer" style="margin-top: -12px; text-align:center; background-color:#8d191a; padding:20px 0px;">
                                    <h4 style="font-weight:bold; margin:0; padding-top:10px; margin-bottom:0px; font-size: 14px; color: #fff; font-weight: normal; padding-bottom:5px;">{{ config('constants.COMPANY_NAME') }}</h4>
                                    <h6 style="font-weight:bold; margin:0; padding-bottom:10px; font-size: 12px; color: #fff; font-weight: normal;">© <?php echo date('Y') ?> {{ config('constants.COMPANY_NAME') }} | <a href="https://{{ config('constants.COMPANY_WBESITE') }}" target=_blank style="text-decoration:none; color: #fff;">{{ config('constants.COMPANY_WBESITE') }}</a></h6>
                                </div>
                            </td>
                        </tr>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>