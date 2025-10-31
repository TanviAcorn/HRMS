    <div class="quick-links">
        <div class="card card-display border-0 mb-3 p-2 py-3">
            <div class="card-body py-0">
                <div class="row">
                    <div class="col-md-8">
                        <h5 class="card-title mb-0">{{ trans("messages.quick-links") }}</h5>
                    </div>
                </div>
                <!--<div class="row px-3 quick-link-card">
                    @if( ( session()->has('user_employee_id') ) && ( session()->has('user_employee_id') > 0 ) )
                    <a class="q-links" href="{{ config('constants.MY_LEAVES_MASTER_URL') }}" title="{{ trans('messages.my-leave-balance') }}">{{ trans("messages.my-leave-balance") }}</a>
                    <a class="q-links" href="javascript:void(0);" title="{{ trans('messages.apply-for-leave') }}" data-emp-id="{{ Wild_tiger::encode(session()->get('user_employee_id')) }}" href="javascript:void(0);" onclick="openApplyLeaveModel(this);">{{ trans("messages.apply-for-leave") }}</a>
                    <a class="q-links" href="{{ config('constants.SITE_URL') . 'attendance-report'  }}" title="{{ trans('messages.attendance-report') }}">{{ trans("messages.attendance-report") }}</a>
                    <a class="q-links" href="{{ url('https://acornsolution-my.sharepoint.com/:b:/g/personal/tanvi_laddha_acornuniversalconsultancy_com/EcC_C0J0IpxLplSTmF8KJLsBQjLS28cIe4HwsdauxXCOAA?e=Fst7z3') }}" title="Policy Documents">Policy Documents</a>
                    <a class="q-links" href="{{ url('https://adeccopo.peoplestrong.com//altLogin.jsf') }}" title="PeopleStrong Adecco">PeopleStrong Adecco</a>
                    <a class="q-links" href="{{ url('https://acornsolution.sharepoint.com/sites/AcornTube') }}" title="AcornTube">AcornTube</a>
                    <a class="q-links" href="{{ url('https://acornsolution.sharepoint.com/sites/AcornTube') }}" title="HR Ticketing Tool">HR Ticketing Tool</a>
                    @endif
                    <a class="q-links" href="{{ config('constants.NOTIFICATION_URL') }}" title="{{ trans('messages.notifications') }}">{{ trans("messages.notifications") }}</a>
                    
                </div>-->

                <div class="row px-3 quick-link-card">
                    <a class="q-links" href="{{ url('docs/view/HR_POLICY.pdf') }}" title="HR Policy Manual" target="_blank">HR Policy Manual</a>
                    <a class="q-links" href="{{ url('https://acornsolution.sharepoint.com/sites/AcornTube') }}" title="AcornTube" target="_blank">AcornTube</a>
                    <a class="q-links" href="{{ url('https://adeccopo.peoplestrong.com//altLogin.jsf') }}" title="PeopleStrong Adecco" target="_blank">PeopleStrong Adecco</a>
                    <a class="q-links" href="{{ url('https://ithelpdesk.acornuniversalconsultancy.com/') }}" title="HR Ticketing Tool" target="_blank">HR Ticketing Tool</a> 
                    <a class="q-links" href="{{ url('docs/view/HDFC_SALARY_ACCOUNTS_BENEFITS.pdf') }}" title="HDFC Salary Accounts Benefits" target="_blank">HDFC Salary Accounts Benefits</a>
                    <a class="q-links" href="{{ url('https://accounts.zoho.in/signin?servicename=SDPOnDemand&hide_title=true&hideyahoosignin=true&hidefbconnect=true&hide_secure=true&serviceurl=https%3A%2F%2Fsdpondemand.manageengine.in%2Fapp%2Fitdesk%2Fui%2Fssp%2Fpages%2Fhome&signupurl=https://sdpondemand.manageengine.in/AccountCreation.do&portal_name=SDPOnDemand') }}" title="IT Ticketing Tool" target="_blank">IT Ticketing Tool</a>
                    <a class="q-links" href="{{ url('docs/view/Escalation_Matrix.pdf') }}" title="Escalation Matrix" target="_blank">Escalation Matrix</a>
                    <a class="q-links" href="{{ url('https://adminhelpdesk.acornuniversalconsultancy.com/login') }}" title="Admin Ticketing Tool" target="_blank">Admin Ticketing Tool</a>
                    <a class="q-links" href="{{ url('docs/view/New_UAN_Generation_Process.pdf') }}" title="New UAN Generation Process" target="_blank">New UAN Generation Process</a>
                    <a class="q-links" href="{{ url('https://acornsolution.sharepoint.com/:f:/s/AcornUniversal/PR/Em16Hewy-65OlYYbZTsl28cBptpyH311Rc2Yy_Kyd84C_Q?e=0jNg1r') }}" title="New UAN Generation Process" target="_blank">Acorn NewsLetter</a>    
                </div>
            </div>
        </div>
    </div>
    