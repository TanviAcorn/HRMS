<div class="punch-time">
    <div class="card card-display border-0 mb-3 p-2 py-3">
        <!-- Card body -->
        <div class="card-body py-0">
            <div class="row">
                <div class="col-9">
                    <h5 class="card-title mb-1">{{ trans("messages.today-punch-time") }}
                    </h5><span id="date"></span>
                </div>
                <div class="col-3 text-right view-all-btn">
                    <a href="javascript:void(0);" title="{{ trans('messages.view-all') }}">{{ trans("messages.view-all") }}
                    </a>
                </div>
            </div>
            <div class="row py-0 mt-3">
                
                <div class="col-6 punch-time-item mt-2">
                    <h4 class="curr-time-title mb-1">{{ trans("messages.current-time") }}</h4>
                    <p class="display-time"> </p>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    const displayTime = document.querySelector(".display-time");
    // Time
    function showTime() {
        let time = new Date();
        displayTime.innerText = time.toLocaleTimeString("en-US", {
            hour12: true
        });

        setTimeout(showTime, 1000);
    }

    showTime();
</script>
<script>
    "use strict";

    function DateAndTime() {
        var dt = new Date();
        //strings
        var days = [
            "sunday",
            "monday",
            "tuesday",
            "wednesday",
            "thursday",
            "friday",
            "saturday"
        ];

        //strings
        var months = [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec"
        ];

        document.getElementById("date").innerHTML =
            days[dt.getDay()] +
            ", " +
            dt.getDate() +
            "<sup>th</sup> " +
            months[dt.getMonth()] +
            ", " +
            dt.getFullYear();
    }

    new DateAndTime();
    setInterval("DateAndTime()", 1000);
</script>