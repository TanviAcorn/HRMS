<div class="holiday">
    <div class="card card-display holiday-display border-0 mb-3 p-2 py-3 h-100">
        <div class="card-body pt-0 pb-3 pb-md-0 pb-lg-3">
            <div class="row">
                <div class="col-8">
                    <h5 class="card-title mb-0">{{ trans('messages.holidays') }}</h5>
                </div>
                @if (count($holidayDetails) > 0)
                    <div class="col-4 text-right">
                        <a href="#" data-toggle="modal" data-target="#view-all-holidays"
                            title="{{ trans('messages.view-all') }}">{{ trans('messages.view-all') }}</a>
                    </div>
                @else
                    <div class="col-12 mt-3">{{ trans('messages.no-holiday-record') }}</div>
                @endif
            </div>
        </div>

        <div class="holiday-slider">
            @if (count($holidayDetails) > 0)
                @php $currentVisiableHoliday = false; @endphp
                @foreach ($holidayDetails as $holidayDetail)
                    @php
                        $additionalClass = '';
                        $sliderPastHoliday = '';
                    @endphp
                    @if (strtotime($holidayDetail->dt_holiday_date) >= strtotime(date('Y-m-d')) && $currentVisiableHoliday != true)
                        @php
                            $currentVisiableHoliday = true;
                            $additionalClass = 'center-on-me';
                        @endphp
                    @endif

                    @if (strtotime($holidayDetail->dt_holiday_date) < strtotime(date('Y-m-d')))
                        @php
                            $sliderPastHoliday = 'past-holiday';
                        @endphp
                    @endif

                    <div class="col-12 ">
                        <div class="pl-5 {{ $sliderPastHoliday }}">
                            <h4 class="holiday-slider-name {{ $additionalClass }}">On
                                {{ isset($holidayDetail->v_holiday_name) ? $holidayDetail->v_holiday_name : '' }}
                            </h4>
                            <span
                                type="date">{{ isset($holidayDetail->dt_holiday_date) ? date('D jS M Y', strtotime($holidayDetail->dt_holiday_date)) : '' }}</span>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

<div class="modal fade bd-example-modal-lg holiday-modal document-folder" id="view-all-holidays" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title twt-modal-header-name" id="exampleModalLabel">{{ trans('messages.holidays') }}
                </h5>
                <div class="py-1 px-3">
                    <span class="h5">
                        <a href="javascript:void(0);" class="holiday-year-btn holiday-year-previous-btn"
                            onclick="showPreviousYearHoliday(this);" title="{{ trans('messages.previous') }}"><i
                                class="fas fa-angle-left px-2" aria-hidden="true"></i></a>
                        <span class="current-select-year">{{ date('Y') }}</span>
                        <a href="javascript:void(0);" title="{{ trans('messages.next') }}"
                            onclick="showNextYearHoliday(this);" class="holiday-year-btn holiday-year-next-btn"><i
                                class="fas fa-angle-right  px-2"></i></a></span>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i
                        class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="row py-3 holiday-modal-view">
                    @include(config('constants.ADMIN_FOLDER') . 'dashboard/holiday-modal-view')
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    var indexToGet = $('.holiday-slider .holiday-slider-name').index($('.center-on-me'));
    var lastYear = '{{ config('constants.SYSTEM_START_YEAR') }}';

    $(document).ready(function() {
        if ($.trim($(".current-select-year").html()) == lastYear) {
            $('.holiday-year-previous-btn').attr('disabled', true);
            $('.holiday-year-previous-btn').attr('style', 'pointer-events: none;opacity: 0.5;');
        }
    })

    $('.holiday-slider').slick({
        infinite: true,
        initialSlide: indexToGet,
        speed: 300,
        slidesToShow: 1,
        adaptiveHeight: true,
    });

    function showPreviousYearHoliday(thisElement) {
        // if (typeof $(thisElement).attr('disabled') == 'undefined') {
        // }
        var currentYear = $.trim($(".current-select-year").html());
        // console.log("currentYear = " + currentYear);

        /* if( currentYear == "{{ config('constants.SYSTEM_START_YEAR') }}" ){
        	$(".holiday-year-previous-btn").hide();
        } else {
        	$(".holiday-year-previous-btn").show();
        } */

        currentYear = (parseInt(currentYear) - 1);
        if (currentYear == lastYear) {
            $(thisElement).attr('disabled', true);
            $(thisElement).attr('style', 'pointer-events: none;opacity: 0.5;');
        }

        var runningYear = "{{ date('Y') }}";

        if( currentYear ==  (parseInt(runningYear)) ){
       	 	$('.holiday-year-next-btn').attr('disabled', false);
       	 	$('.holiday-year-next-btn').attr('style', 'pointer-events: all;opacity: 1;');
       }
        
        yearWiseHolidayList(currentYear);
    }

    function showNextYearHoliday(thisElement) {
        var currentYear = $.trim($(".current-select-year").html());

        if (currentYear == "{{ date('Y') }}") {
            //alertifyMessage('error' , "{{ trans('messages.system-error') }}");
            //return false;
        }

        var runningYear = "{{ date('Y') }}";
        
        currentYear = (parseInt(currentYear) + 1);

        if (currentYear != lastYear) {
            $('.holiday-year-previous-btn').attr('disabled', false);
            $('.holiday-year-previous-btn').attr('style', 'pointer-events: all;opacity: 1;');
        }

        if( currentYear ==  (parseInt(runningYear) + 1) ){
        	 $('.holiday-year-next-btn').attr('disabled', true);
        	 $('.holiday-year-next-btn').attr('style', 'pointer-events: none;opacity: 0.5;');
        }

        yearWiseHolidayList(currentYear);
    }

    function yearWiseHolidayList(currentYear) {
        $.ajax({
            type: "POST",
            url: site_url + 'get-year-wise-holiday',
            data: {
                "_token": "{{ csrf_token() }}",
                'selected_year': currentYear
            },
            beforeSend: function() {
                //block ui
                showLoader();
            },
            success: function(response) {
                hideLoader();
                if (response != "" && response != null) {
                    $(".holiday-modal-view").html(response);
                    $(".current-select-year").html(currentYear);
                }
            },
            error: function() {
                hideLoader();
            }
        });

    }
</script>
