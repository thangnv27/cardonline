<!doctype html>
<html lang="<?php echo Language::$lang_code; ?>">
    <head>
        <meta charset="UTF-8">
        <title><?php echo $this->title; ?></title>
        <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no" />

        <!-- bootstrap framework -->
        <link href="<?php echo Registry::$siteurl; ?>/public/admin/css/bootstrap.min.css" rel="stylesheet" />
        <!-- google webfonts -->
        <link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400&amp;subset=latin-ext,latin' rel='stylesheet' type='text/css' />
        <link href="<?php echo Registry::$siteurl; ?>/public/admin/css/login.css" rel="stylesheet" />
    </head>
    <body>
        <div class="login_container">
            <form id="login_form" method="post">
                <h1 class="login_heading"><?php echo Language::$phrases['page']['user']['login']; ?> <span>/ <a href="#" class="open_forgot_pwd_form"><?php echo Language::$phrases['page']['user']['forgot_password']; ?></a></span></h1>
                <div id="login_result" style="margin-bottom: 5px;color: red;"></div>
                <div class="form-group">
                    <label for="username"><?php echo Language::$phrases['page']['user']['username']; ?></label>
                    <input type="text" class="form-control input-lg" value="" name="username" id="username" />
                </div>
                <div class="form-group">
                    <label for="password"><?php echo Language::$phrases['page']['user']['password']; ?></label>
                    <input type="password" class="form-control input-lg" value="" name="password" id="password" />
                </div>
                <div class="form-group">
                    <label for="lang"><?php echo Language::$phrases['context']['select_lang']; ?></label>
                    <select id="lang" name="lang" class="form-control">
                        <option value="en" <?php echo (Language::$lang_code == 'en') ? 'selected' : ''; ?>>English</option>
                        <option value="vi" <?php echo (Language::$lang_code == 'vi') ? 'selected' : ''; ?>>Vietnamese</option>
                    </select>
                </div>
                <div class="submit_section">
                    <button type="button" id="btnLogin" class="btn btn-lg btn-success btn-block"><?php echo Language::$phrases['page']['user']['login']; ?></button>
                    <input type="hidden" name="redirect_url" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />
                </div>
            </form>
            <form id="forgot_pwd_form" style="display:none" method="post">
                <h1 class="login_heading"><?php echo Language::$phrases['page']['user']['forgot_password']; ?><span>/ <a href="#" class="open_login_form"><?php echo Language::$phrases['page']['user']['login']; ?></a></span></h1>
                <div id="forgot_pwd_result" style="margin-bottom: 5px;color: red;"></div>
                <div class="form-group">
                    <label for="email"><?php echo Language::$phrases['page']['user']['email']; ?></label>
                    <input type="text" class="form-control input-lg" name="email" id="email" />
                </div>
                <div class="submit_section">
                    <button type="submit" class="btn btn-lg btn-success btn-block"><?php echo Language::$phrases['action']['send']; ?></button>
                </div>
            </form>
        </div>

        <div class="modal fade" id="terms_modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Terms & Conditions</h4>
                    </div>
                    <div class="modal-body">
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ducimus eaque tempora! Porro cumque labore voluptate dolore alias libero commodi deserunt unde aspernatur dignissimos quaerat similique maiores quasi eos optio quidem.
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ducimus eaque tempora! Porro cumque labore voluptate dolore alias libero commodi deserunt unde aspernatur dignissimos quaerat similique maiores quasi eos optio quidem.
                    </div>
                </div>
            </div>
        </div>

        <!-- jQuery -->
        <script src="<?php echo Registry::$siteurl; ?>/public/admin/js/jquery.js"></script>
        <script src="<?php echo Registry::$siteurl; ?>/public/admin/js/jquery.validate.js"></script>
        <!-- bootstrap js plugins -->
        <script src="<?php echo Registry::$siteurl; ?>/public/admin/js/bootstrap.min.js"></script>
        <script type="text/javascript">
            $(function() {
                // switch forms
                $('.open_forgot_pwd_form').click(function(e) {
                    e.preventDefault();
                    $('#login_form').removeClass().addClass('animated fadeOutDown');
                    setTimeout(function() {
                        $('#login_form').removeClass().hide();
                        $('#forgot_pwd_form').show().addClass('animated fadeInUp');
                    }, 700);
                });
                $('.open_login_form').click(function(e) {
                    e.preventDefault();
                    $('#forgot_pwd_form').removeClass().addClass('animated fadeOutDown');
                    setTimeout(function() {
                        $('#forgot_pwd_form').removeClass().hide();
                        $('#login_form').show().addClass('animated fadeInUp');
                    }, 700);
                });

                // Processing
                $("#btnLogin").click(function() {
                    $("#login_result").html("Logging in...");
                    $.ajax({
                        url: "<?php echo get_admin_login_url(); ?>", type: "POST", dataType: "json", cache: false,
                        data: $("#login_form").serialize(),
                        success: function(response, textStatus, XMLHttpRequest) {
                            if (response && response.status == 'success') {
                                if (parent.$.colorbox) {
                                    parent.$.colorbox.close();
                                } else {
                                    window.location = response.redirect_url;
                                }
                            } else if (response && response.status == 'error') {
                                $("#login_result").html(response.message);
                            }
                        },
                        error: function(MLHttpRequest, textStatus, errorThrown) {
                            $("#login_result").html(errorThrown);
                        },
                        complete: function() {
                        }
                    });
                });
                $("#forgot_pwd_form").validate({
                    rules: {
                        email: {
                            required: true,
                            email: true
                        }
                    },
                    errorClass: "help-inline",
                    errorElement: "span",
                    highlight: function (element, errorClass, validClass) {
                        $(element).parents('.form-group').removeClass('success');
                        $(element).parents('.form-group').addClass('error');
                    },
                    unhighlight: function (element, errorClass, validClass) {
                        $(element).parents('.form-group').removeClass('error');
                        $(element).parents('.form-group').addClass('success');
                    },
                    submitHandler: function (form) {
                        $("#forgot_pwd_result").html("Sending...");
                        $.ajax({
                            url: "<?php echo DASHBOARD_URL . '/user/forgot_password/'; ?>", type: "POST", dataType: "json", cache: false,
                            data: $(form).serialize(),
                            success: function (response, textStatus, XMLHttpRequest) {
                                if (response && response.status == 'success') {
                                    $("#forgot_pwd_result").html(response.message);
                                } else if (response && response.status == 'error') {
                                    $("#forgot_pwd_result").html(response.message);
                                }
                            },
                            error: function (MLHttpRequest, textStatus, errorThrown) {
                                $("#forgot_pwd_result").html(errorThrown);
                            },
                            complete: function () {
                            }
                        });
                    }
                });
            });
        </script>
    </body>
</html>