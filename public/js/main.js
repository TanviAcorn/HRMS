// main js
(function($) {
    $.fn.vldt = function(btn, field) {
        return this.each(function() {
            var $form = $(this),
                submit = $form.find(btn),
                fields = $form.find(field),
                validation;

            fields.each(function() {
                var reqmsg = $("<div/>", {
                    class: "invalid-feedback",
                    html: $(this).attr("data-title")
                        ? $(this).attr("data-title") + " is required"
                        : "Required Field"
                });
                $(this)
                    .parent()
                    .find(".invalid-feedback")
                    .remove();
                reqmsg.appendTo($(this).parent());
            });
            submit.on("click", function(event) {
                if ($form[0].checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                } else {
                    showLoader();
                }
                $form.addClass("was-validated");
            });
        });
    };
    $.extend({
        ovrly: function(msg) {
            var methods = {
                init: function() {
                    var i = msg;
                    if (typeof i === "undefined") {
                        i = "Please Wait";
                    }
                    $(".overlay-block").remove();
                    $(
                        '<div class="overlay-block"><h2><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw d-block mx-auto" aria-hidden="true"></i>' +
                            i +
                            "</h2></div>"
                    ).appendTo("body");
                    // return this;
                },
                kill: function() {
                    $(".overlay-block").fadeOut(250, function() {
                        $(this).remove();
                    });
                }
            };
            return methods;
        }
    });
})(jQuery);

function showLoader() {
    $.ovrly().init();
}

function hideLoader() {
    $.ovrly().kill();
}

// if ($(window).innerWidth() > 1200) {
//     $("#wrapper").addClass("toggled");
// }

// swipe functionality beta
$(document).on("touchstart", function (event) {
    var xClick = event.originalEvent.touches[0].pageX;
    $(this).one("touchmove", function (event) {
        var xMove = event.originalEvent.touches[0].pageX;
        if (Math.floor(xClick - xMove) < -15) {
            // console.log('right', xMove, xClick);
            if (xClick < 30) {
                $('#wrapper').addClass('toggled');
            }
        } else if (Math.floor(xClick - xMove) > 5) {
            // console.log('left', xMove, xClick);
            if (xClick < 270 && $('#wrapper').hasClass('toggled')) {
                $('#wrapper').removeClass('toggled');
            }
        }
    });
    $(document).on("touchend", function () {
        $(this).off("touchmove");
    });
});

$(document).ready(function(e) {
    // sidebar
    $(document).on("click", ".navbar-toggler", function() {
        $("#wrapper").toggleClass("toggled");
    });
    // sidebar sub menu
    $('.sidebar [data-toggle="collapse"]').on("click", function() {
        var current = $(this);
        current
            .parent()
            .siblings()
            .find(".collapse.show")
            .collapse("hide");
    });

    // sidebar close on outside click
    $(document).on("click", function(e) {
        if (
            $(window).innerWidth() < 1200 &&
            !$(e.target).closest("#sidebar").length > 0 &&
            !$(e.target).is(".navbar-toggler")
        ) {
            $("#wrapper").removeClass("toggled");
        }
    });

    // page-title & breadcrumb
    $("#pageTitle").text($("#page").attr("data-pagetitle"));
    $("#pageBc").text($("#page").attr("data-pagebc"));

    // alertify defaults
    // if (!$('body').hasClass('login-page')){
    //     alertify.defaults.theme.ok = "btn btn-primary";
    //     alertify.defaults.theme.cancel = "btn btn-danger";
    //     alertify.defaults.theme.input = "form-control";
    // }
    
    // validation
    $("form").vldt('[type="submit"]', "[required]");

    // dataTables call
    if ($(".table-dataTable").length > 0) {
        // last column
        var last = $(".table-dataTable tr th").length - 1,
            secondlast = $(".table-dataTable tr th").length - 2;

        // something on datatables
        $(".table-dataTable").DataTable({
            order: [],
            columnDefs: [
                {
                    searchable: false,
                    orderable: false,
                    targets: [last]
                }
            ]
        });
    }
});

// validations starts below

var $form = $("form");

//input validations - types
var input_text = $form.find("input[type=text]");
var input_password = $form.find("input[type=password]");
var input_email = $form.find("input[type=email], .isEmail");
var input_textarea = $form.find("textarea");
var input_select = $form.find("select, input[type=select]");
var input_checkbox = $form.find("input[type=checkbox]");
// var input_radio = $form.find('input[type=radio]');					//more feasible if already one checked

//input validations - custom
var input_fileUpload = $form.find("input[type=file], .isFile");
var input_number = $form.find("input[type=number], .isNum"); //may have float
var input_integer = $form.find(".isInt"); //no floats
var input_time = $form.find(".isTime");
var input_username = $form.find(".isUsername");
var input_phone = $form.find(".isPhone"); //mobile number

//regex validations - without sapce
var input_alpha = $form.find(".isAlpha");
var input_alphaNum = $form.find(".isAlphaNum");
var input_alphaSp = $form.find(".isAlphaSp");
var input_alphaNumSp = $form.find(".isAlphaNumSp");
//regex validations - with sapce
var input_alphaWs = $form.find(".isAlphaWs");
var input_alphaNumWs = $form.find(".isAlphaNumWs");
var input_alphaSpWs = $form.find(".isAlphaSpWs");
var input_alphaNumSpWs = $form.find(".isAlphaNumSpWs");

//input - password-matching
var input_pass_main = $form.find(".isMPass");
var input_pass_confirm = $form.find(".isCPass");

//input dates-comparison
var input_fdate = $form.find(".fdate-val");
var input_ldate = $form.find(".ldate-val");

//validator btn
var validateSubmit = $form.find(".btn-validate");

//regex
var rg_username = /^[a-zA-Z0-9]{3,15}$/; //ok
var rg_alpha = /^([A-Za-z]*)$/; //ok
var rg_alphaWs = /^([A-Za-z\s]*)$/; //ok
var rg_alphaNum = /^([A-Za-z0-9]*)$/; //ok
var rg_alphaNumWs = /^([A-Za-z0-9\s]*)$/; //ok
//var rg_alphaSp 	= /^[a-zA-Z0-9]{3,15}$/;
//var rg_alphaSpWs 	= /^[a-zA-Z0-9]{3,15}$/;
var rg_alphaNumSp = /^([A-Za-z0-9_]*)$/; // ~
var rg_alphaNumSpWs = /^[A-Za-z0-9\s\/\(\)_\-.,:]*$/; // ~
var rg_num = /^([0-9]*(\.[0-9]{1,2})?)$/; //ok
var rg_int = /^\d+$/; //ok
var rg_email = /^([a-zA-Z0-9]{1}[\w\-\.]*\@([\da-zA-Z\-]{1,}\.){1,}[\da-zA-Z\-]{2,4})$/; // ~
var rg_url = /^[A-Za-z0-9\._:\-\/]*$/; // ~
var rg_time = /^([0-1][0-9]|2[0-3]):([0-5][0-9])$/; //24 hr - ok
var rg_timeBig = /^([0-9]|[0-9]?[0-9])([:][0-5]?[0-9])?$/; //hh:mm (upto 99:59) - ok
var rg_phone = /^[6789]\d{9}$/; // ~

//regex validation function
function regValidate(input, regex, msg) {
    var $input = input;
    var $regex = regex;
    $input.each(function() {
        $(this).on("change", function() {
            var $val = $(this)
                .val()
                .trim();
            if ($val != "") {
                if (!$val.match($regex)) {
                    $(this)
                        .removeClass("is-valid")
                        .addClass("is-invalid");
                    $(this)
                        .val("")
                        .focus();
                    if (msg) {
                        alertifyMessage("error", msg);
                    }
                } else {
                    $(this)
                        .removeClass("is-invalid")
                        .addClass("is-valid");
                    $(this).val($val);
                }
            }
        });
    });
}
regValidate(
    input_username,
    rg_username,
    "Username must be alpha-numeric and in between 3 to 15 characters. Space is not allowed."
);
regValidate(input_integer, rg_int);
regValidate(input_number, rg_num, "Please enter number only.");
regValidate(input_time, rg_timeBig);
regValidate(input_phone, rg_phone, "Please enter valid Mobile Number");
regValidate(input_email, rg_email, "Please enter valid Email address.");
regValidate(input_alphaWs, rg_alphaWs, "Only alphabets & space are allowed.");
