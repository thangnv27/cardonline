var Dialog = {
    show: function (elm) {
        jQuery(elm).bPopup({
            easing: 'easeOutBack', //uses jQuery easing plugin
            speed: 450,
            transition: 'slideDown'
        });
    },
    showHelp: function () {
        jQuery('#help').bPopup({
            easing: 'easeOutBack', //uses jQuery easing plugin
            speed: 450,
            transition: 'slideDown'
        });
    },
    notify: function (data) {
        var message = '<div style="z-index:1000;background:#F6F7F8;padding:15px;position:fixed;bottom:10%;left:20px; max-width:200px;border-radius:5px;display:none;border:solid 1px #C50A01;" id="notify">\
                    <div style="position:relative;"><span class="close_notify" style="position: absolute; cursor: pointer; top: -15px; padding: 0px 5px; right: -15px;">x</span>\
                    <div class="msg_notify">' + data + '</div></div></div>';
        if (jQuery("#notify").length == 0) {
            jQuery("body").append(message);
        } else {
            jQuery(".msg_notify").html(data);
        }
        jQuery("#notify").slideDown(400).fadeTo(400, 100);
        jQuery("span.close_notify").click(function () {
            jQuery(this).parent().parent().fadeTo(400, 0).slideUp(400).remove();
        });
    }
};

jQuery.noConflict();
jQuery(document).ready(function ($) {
    $('#user-login').validate({
        rules: {
            name: {
                required: true
            },
            pass: {
                required: true
            }
        },
        highlight: function (element) {
            $(element).closest('.form-control').addClass('error');
        },
        unhighlight: function (element) {
            $(element).closest('.form-control').removeClass('error');
        },
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function (form) {
            $.ajax({
                url: siteurl + "/user/login_process/", type: "POST", dataType: "json", cache: false,
                data: $(form).serialize(),
                success: function (response, textStatus, XMLHttpRequest) {
                    if (response && response.status === 'success') {
                        $("#login .result").html(response.message);
                        window.location = siteurl;
                    } else if (response && response.status === 'error') {
                        $("#login .result").html(response.message);
                    }
                },
                error: function (MLHttpRequest, textStatus, errorThrown) {
                    console.log(errorThrown);
                },
                complete: function () {
                }
            });
        }

    });

//    Dang ky

    $("#user-register-form").validate({
        rules: {
            username: {
                required: true
            },
            pass: {
                required: true
            },
            passcf: {
                required: true
            },
            mail: {
                required: true,
                email: true
            }
        },
        highlight: function (element, errorClass, validClass) {
            $(element).closest('.form-control').addClass('error');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).closest('.form-control').removeClass('error');
        },
        submitHandler: function (form) {
            $.ajax({
                url: siteurl + "/user/register_process/", type: "POST", dataType: "json", cache: false,
                data: $(form).serialize(),
                success: function (response, textStatus, XMLHttpRequest) {
                    if (response && response.status === 'success') {
                        $("#register .result").html(response.message);
                        window.location = siteurl;
                    } else if (response && response.status === 'error') {
                        $("#register .result").html(response.message);
                    }
                },
                error: function (MLHttpRequest, textStatus, errorThrown) {
                    console.log(errorThrown);
                },
                complete: function () {
                }
            });
        }
    });

//buy code

    $('#frm_buycode').validate({
        rules: {
            username: {
                required: true
            },
            pass: {
                required: true
            },
            passcf: {
                required: true
            },
            mail: {
                required: true,
                email: true
            }
        },
        highlight: function (element, errorClass, validClass) {
            $(element).closest('.form-control').addClass('error');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).closest('.form-control').removeClass('error');
        },
        submitHandler: function (form) {
            $.ajax({
                url: siteurl + "/user/buy_code/", type: "POST", dataType: "json", cache: false,
                data: $(form).serialize(),
                success: function (response, textStatus, XMLHttpRequest) {
                    if (response && response.status === 'success') {
                        $('.msg').html(response.message);
                        $('.coin').html(response.coin);
                        $('.code').html(response.code);
                    } else if (response && response.status === 'error') {
                        $('.msg').html(response.message);
                        $('.code').html('');
                    }
                },
                error: function (MLHttpRequest, textStatus, errorThrown) {
                    console.log(errorThrown);
                },
                complete: function () {
                }
            });
        }

    });
//    nap the
    $('#frm-napthe').validate({
        rules: {
            pin_card: {
                required: true
            },
            card_serial: {
                required: true
            }
        },
        highlight: function (element, errorClass, validClass) {
            $('.form-control').addClass('error');
        },
        unhighlight: function (element, errorClass, validClass) {
            $('.form-control').removeClass('error');
        },
        submitHandler: function (form) {
            $.ajax({
                url: siteurl + "/user/recharge/", type: "POST", dataType: "json", cache: false,
                data: $(form).serialize(),
                success: function (response, textStatus, XMLHttpRequest) {
                    if (response && response.status === 'success') {
                        $('.coin').html(response.coin);
                        $('.msgcard').html(response.message);
                    } else if (response && response.status === 'error') {
                        if (response.login === 'not-login') {
                            $(".btnlogin").click();
                        } else {
                            $('.msgcard').html(response.message);
                        }
                    }
                },
                error: function (MLHttpRequest, textStatus, errorThrown) {
                    console.log(errorThrown);
                },
                complete: function () {
                }
            });
        }

    });

//    contact
    $('#frm_contact').validate({
        rules: {
            name: {
                required: true
            },
            email: {
                required: true
            },
            message: {
                required: true
            }
        },
        highlight: function (element, errorClass, validClass) {
            $('.form-control').addClass('error');
        },
        unhighlight: function (element, errorClass, validClass) {
            $('.form-control').removeClass('error');
        },
        submitHandler: function (form) {
            $.ajax({
                url: siteurl + "/user/contact/", type: "POST", dataType: "json", cache: false,
                data: $(form).serialize(),
                success: function (response, textStatus, XMLHttpRequest) {
                    if (response && response.status === 'success') {
                        $('.contact-msg').html(response.message);
                        form.reset();
                    } else if (response && response.status === 'error') {
                        $('.contact-msg').html(response.message);
                    }
                },
                error: function (MLHttpRequest, textStatus, errorThrown) {
                    console.log(errorThrown);
                },
                complete: function () {
                }
            });
        }
    });
});