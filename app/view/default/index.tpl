{php}
include(APP_PATH . 'view' . DS . THEME_NAME . DS . 'functions.php');
{/php}
{assign var="lang" value=Language::$lang_code}
{assign var="siteurl" value=Registry::$siteurl scope="global"}
{assign var="option" value=TPL::getSiteOption() scope="global"}
{assign var="codecoin" value=TPL::CodeCoin() scope="global"}
<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{$title} - {$option->name}</title>
    
    <link rel="icon" type="image/x-icon" href="{$option->favicon}" />
    <link href='http://fonts.googleapis.com/css?family=Roboto+Slab:300,700|Open+Sans&subset=latin,vietnamese' rel='stylesheet' type='text/css'>
    <link type="text/css" rel="stylesheet" href="{$siteurl}/public/css/style1.css" media="screen" />
    
    <script type="text/javascript">
        var siteurl = '{$siteurl}';
    </script>
</head>
<body class="html front logged-in no-sidebars page-node  admin-nw admin-vertical admin-df  " >
    <div id="skip-link">
        <a href="#main-content" class="element-invisible element-focusable">Skip to main content</a>
    </div>
    <div id="message-popup" class="col-md-6">
    </div>
    <div style="display:none">
        <a id="call-popup" href="#message-popup" rel="leanModal" class="btn btn-lg marginbot10">Đăng nhập</a>
    </div>
    <!-- Start home -->
    <section id="home">
        <div class="container home-container">
            <div class="row">
                <div class="col-md-6 wow fadeInLeft" data-wow-delay="0.2s">
                    <div class="intro">
                        <h3><span>Nạp Cash tiết kiệm tới 48%</span></h3>
                        <p>
                            Thậm chí bạn còn có thể được hưởng thêm nhiều ưu đãi trong các lần nạp thẻ tiếp theo, hãy đăng ký tài khoản trước khi giao dịch
                        </p>
                        {if $smarty.session.user_logged_in == null}
                            <p>
                                <a class="btn btn-default btn-bavel btn-lg marginbot10" rel="leanModal" href="#register" id="go">Đăng ký tài khoản</a>
                                <a class="btn btn-lg marginbot10 btnlogin" rel="leanModal" href="#login">Đăng nhập</a>
                            </p>
                        {else}
                            <h5><span>Chào bạn: <a class="username" href="#taikhoan">{$smarty.session.user_logged_in.username}</a></span></h5>
                            <p>
                                <a class="btn btn-default btn-bavel btn-lg marginbot10" href="{$siteurl}/user/logout" >Đăng xuất</a>
                            </p>
                        {/if}


                    </div>
                </div>
                <div class="col-md-6 wow fadeInRight" data-wow-delay="0.2s">
                    <div class="form-wrapp">
                        <img src="{$siteurl}/public/images/lady.png" class="form-image" alt="" />
                        <div class="form-horizontal form-napcash">

                            <ul class="nav nav-tabs marginbot40" role="tablist">
                                <li class="active"><a href="#tab1" role="tab" data-toggle="tab">Nạp card</a></li>
                                <li><a href="#tab2" role="tab" data-toggle="tab">Chuyển khoản</a></li>
                            </ul>
                            <!-- Start tab panes -->
                            <div class="tab-content">
                                <!-- Start tab 1 -->
                                <div class="tab-pane active" id="tab1">
                                    <form method="post" id="frm-napthe">
                                        <div>
                                            <span class="msgcard"></span>
                                            <div class="form-item form-type-radios form-item-mang">
                                                <label for="edit-mang">Chọn nhà mạng </label>
                                                <div id="edit-mang" class="form-radios">
                                                    <div class="form-item form-type-radio form-item-mang">
                                                        <input type="radio" id="edit-mang-mobi" name="chonmang" value="MOBI" class="form-radio" />  
                                                        <label class="option" for="edit-mang-mobi">MobiFone </label>
                                                    </div>
                                                    <div class="form-item form-type-radio form-item-mang">
                                                        <input type="radio" id="edit-mang-vina" name="chonmang" value="VINA" class="form-radio" />
                                                        <label class="option" for="edit-mang-vina">VinaPhone </label>

                                                    </div>
                                                    <div class="form-item form-type-radio form-item-mang">
                                                        <input type="radio" id="edit-mang-vietel" name="chonmang" value="VIETEL" checked="checked" class="form-radio" />
                                                        <label class="option" for="edit-mang-vietel">Viettel </label>

                                                    </div>
                                                    <div class="form-item form-type-radio form-item-mang">
                                                        <input type="radio" id="edit-mang-gate" name="chonmang" value="GATE" class="form-radio" />
                                                        <label class="option" for="edit-mang-gate">FPT Gate </label>

                                                    </div>
                                                    <div class="form-item form-type-radio form-item-mang">
                                                        <input type="radio" id="edit-mang-vtc" name="chonmang" value="VTC" class="form-radio" />
                                                        <label class="option" for="edit-mang-vtc">VTC </label>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-item form-type-textfield form-item-pin-card">
                                                <input class="form-control form-block form-text required" placeholder="Mã thẻ cào" type="text" id="edit-pin-card" name="pin_card" value="{$smarty.post.pin_card}" size="60" maxlength="128" />
                                            </div>
                                            <div class="form-item form-type-textfield form-item-card-serial">
                                                <input class="form-control form-block form-text required" placeholder="Số seri" type="text" id="edit-card-serial" name="card_serial" value="{$smarty.post.card_serial}" size="60" maxlength="128" />
                                            </div>
                                            <input class="btn btn-green btn-bavel btn-lg form-submit" type="submit" id="edit-submit" name="op" value="Nạp thẻ" />
                                            <a href="#bang-gia" rel="leanModal" style="padding-left: 10px;">Xem bảng tỉ giá</a>
                                            <input type="hidden" name="form_id" value="nganluong_form" />
                                        </div>
                                    </form>                
                                </div>
                                <!-- End tab 1 -->

                                <!-- Start tab 2 -->
                                <div class="tab-pane" id="tab2">
                                    {$option->footer_info}
                                </div>
                                <!-- End tab 2 -->

                            </div>
                            <!-- End tab panes -->


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End home -->

    <!-- Start header -->
    <header>
        <div class="navbar navbar-inverse" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <a class="navbar-brand" href="{$siteurl}" title="{$option->name}">
                        <img src="{$option->logo}" title="{$option->name}" alt="{$option->name}" />
                    </a>
                </div>
                <div class="collapse navbar-collapse pull-right">
                    <ul class="nav navbar-nav">
                        <li><a href="#home">Nạp Cash</a></li>
                        <li><a href="#introduce">Hệ thống game</a></li>
                        <li><a href="#pricing">Hướng dẫn</a></li>
                        <li><a href="#contact">Liên hệ</a></li>
                            {if $smarty.session.user_logged_in}
                            <li><a href="#taikhoan">Tài khoản</a></li>
                            {/if}
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </div>
    </header>
    <!-- End header -->
    {if $smarty.session.user_logged_in}
        <section id="taikhoan" class="contain paddingbot-clear">
            <div class="container marginbot60 margintop20">
                <div class="row wow fadeInDown" data-wow-delay="0.4s">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-4">
                                <h4>Thông tin tài khoản</h4>
                                <div class="account-info">
                                    <p>Tài khoản: {$smarty.session.user_logged_in.username}</p>
                                    <p>Số dư: <span class="coin">{$coin}</span></p>
                                    <p><a href="#pay_history"  rel="leanModal">Lịch sử mua code</a></p>
                                    <p><a href="#coin_history"  rel="leanModal">Lịch sử nạp thẻ</a></p>
                                </div>
                            </div>
                            <div class="col-md-8" style="height: 300px;background: #ccc;">
                                <div class="row code-store">
                                    <form id="frm_buycode" method="post" >
                                        <div class="col-md-4">
                                            <div class="code-item-bg">
                                                <label>
                                                    <input type="radio" name="code_count" id="code_count" value="1" checked>
                                                    1 code - {$codecoin->code1|number_format:0} VNĐ
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="code-item-bg">
                                                <label>
                                                    <input type="radio" name="code_count" id="code_count" value="3">
                                                    3 code - {$codecoin->code3|number_format:0} VNĐ
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="code-item-bg">
                                                <label>
                                                    <input type="radio" name="code_count" id="code_count" value="7">
                                                    7 code - {$codecoin->code7|number_format:0} VNĐ
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="code-item-bg">
                                                <label>
                                                    <input type="radio" name="code_count" id="code_count" value="16">
                                                    16 code - {$codecoin->code16|number_format:0} VNĐ
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="code-item-bg">
                                                <label>
                                                    <input type="radio" name="code_count" id="code_count" value="24">
                                                    24 code - {$codecoin->code24|number_format:0} VNĐ
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="code-item-bg">
                                                <label>
                                                    <input type="radio" name="code_count" id="code_count" value="40">
                                                    40 code - {$codecoin->code40|number_format:0} VNĐ
                                                </label>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-md-4">
                                            <input type="submit" onclick="return confirm('Bạn có chắc chắn mua không?');" value="Mua code" class="btn btn-green btn-bavel btn-lg form-submit" />
                                        </div>
                                        <div class="col-md-8" style="margin-top: 10px;">
                                            <label><span class="msg"></span></label>
                                            <label><span class="code"></span></label>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    {/if}
    <!-- End introduce -->

    <!-- Start introduce -->
    <section id="introduce" class="contain paddingbot-clear">
        <div class="container marginbot60">
            <div class="row wow fadeInDown" data-wow-delay="0.4s">
                <div class="col-md-4 col-md-offset-4">
                    <div class="heading centered">
                        <h3>Danh sách các game<br>hỗ trợ sử dụng @cash</h3>
                        <span class="heding-style"></span>
                    </div>
                </div>
            </div>
            <div class="row wow fadeInDown" data-wow-delay="0.4s">
                <div class="col-md-12 text-center">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="portfolio-wrapp">
                                <!-- <div class="image-caption">
                                  <a href="portfolio-detail.html" class="image-link"><span class="icon-link"></span></a>
                                </div> -->
                                <img src="{$siteurl}/public/images/games/Audition.jpg" class="img-responsive" alt="" />
                                <p>Audition</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="portfolio-wrapp">
                                <img src="{$siteurl}/public/images/games/Elsword.jpg" class="img-responsive" alt="" />
                                <p>Elsword</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="portfolio-wrapp">
                                <img src="{$siteurl}/public/images/games/AVA.jpg" class="img-responsive" alt="" />
                                <p>AVA</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="portfolio-wrapp">
                                <img src="{$siteurl}/public/images/games/S4League.jpg" class="img-responsive" alt="" />
                                <p>S4League</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="portfolio-wrapp">
                                <img src="{$siteurl}/public/images/games/SuddenAttack.jpg" class="img-responsive" alt="" />
                                <p>Sudden Attack</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="portfolio-wrapp">
                                <img src="{$siteurl}/public/images/games/ChaosOnline.jpg" class="img-responsive" alt="" />
                                <p>ChaosOnline</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="portfolio-wrapp">
                                <img src="{$siteurl}/public/images/games/FootballClubManager.jpg" class="img-responsive" alt="" />
                                <p>Football Club Manager</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="portfolio-wrapp">
                                <img src="{$siteurl}/public/images/games/Ragnarok2.jpg" class="img-responsive" alt="" />
                                <p>Ragnarok II</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="portfolio-wrapp">
                                <img src="{$siteurl}/public/images/games/DCUO.jpg" class="img-responsive" alt="" />
                                <p>DCUO</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="portfolio-wrapp">
                                <img src="{$siteurl}/public/images/games/PhantasyStarOnline2.jpg" class="img-responsive" alt="" />
                                <p>Phantasy Star Online II</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="portfolio-wrapp">
                                <img src="{$siteurl}/public/images/games/ROMobile.jpg" class="img-responsive" alt="" />
                                <p>RO Mobile</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="portfolio-wrapp">
                                <img src="{$siteurl}/public/images/games/Yulgang2.jpg" class="img-responsive" alt="" />
                                <p>Yulgang II</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="dark-bg contain">
            <div class="container">
                <div class="row">
                    <div class="col-md-7 wow fadeInLeft" data-wow-delay="0.4s">
                        <div class="heading">
                            <h3>Tại sao <span>bạn nên nạp cash từ SellCash247</span></h3>
                            <span class="heding-style"></span>
                        </div>
                        <p>Đơn giản là vì chúng tôi có mức giá tốt nhất, hỗ trợ bạn nạp cash hoặc giải quyết các vấn đề về thanh toán, thẻ lỗi trong nháy mắt.
                        </p>
                        <div class="clearfix">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse1">
                                    <span class="accordion-icon icon-diamond"></span> Giá luôn tốt nhất trên thị trường
                                </a>
                            </div>
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse2">
                                    <span class="accordion-icon icon-like"></span> Nhiều ưu đãi cho những lần mua tiếp theo
                                </a>
                            </div>

                            <!-- <div class="accordion-group"> -->
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse3">
                                    <span class="accordion-icon icon-bubbles"></span> Tư vấn, hỗ trợ nhiệt tình
                                </a>
                            </div><!--
                            <div id="collapse3" class="accordion-body collapse">
                              <div class="accordion-inner">
                                <p>
            
                                </p>
                              </div>
                            </div>
                          </div> -->

                        </div>
                    </div>
                    <div class="col-md-5 wow fadeInRight" data-wow-delay="0.4s">
                        <img src="{$siteurl}/public/images/why.jpg" class="img-responsive pull-right" alt="" />
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End introduce -->

    <!-- Start pricing -->
    <section id="pricing" class="contain">
        <div class="container">
            <div class="row wow fadeInDown" data-wow-delay="0.4s">
                <div class="col-md-4 col-md-offset-4">
                    <div class="heading centered">
                        <h3>Hướng dẫn</h3>
                        <span class="heding-style"></span>
                    </div>
                </div>
            </div>
            <div class="row text-center marginbot20 wow fadeInUp" data-wow-delay="0.4s">
                <div class="col-md-8 col-md-offset-2">
                    <p>
                        Sau khi có mã số nạp tiền, bạn chỉ cần thực hiện các bước sau đây hoặc bạn có thể xem video hướng dẫn bên dưới.
                    </p>
                </div>
            </div>
            <div class="table-code">
            </div>
            <div class="row">
                <div class="col-md-3 wow flipInY" data-wow-delay="0.6s">
                    <div class="pricing-box">
                        <div class="pricing-head">
                            <h4>Bước 1</h4>
                            <div class="price">
                                <a href="https://secure3.playpark.com/refill/refillplaypark/login.aspx" target="_blank">Đăng nhập PlayPark</a>
                            </div>
                        </div>
                        <!-- <ul>
                          <li><strong>Free</strong> update</li>
                          <li><strong>Unlimited</strong> color</li>
                          <li><strong>PSD file</strong> included</li>
                          <li><strong>1 month</strong> done</li>
                          <li><strong>3 month</strong> maintenance</li>
                          <li><strong>Free</strong> icons</li>
                          <li><strong>Documentation</strong> included</li>
                        </ul>
                        <div class="pricing-bottom">
                          <a href="#" class="btn btn-default btn-bavel btn-lg btn-block">Select this</a>
                        </div> -->
                    </div>
                </div>

                <div class="col-md-3 wow flipInY" data-wow-delay="0.8s">
                    <div class="pricing-box">
                        <div class="pricing-head">
                            <h4>Bước 2</h4>
                            <div class="price">Chọn DIRECT REFILL > Game</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 wow flipInY" data-wow-delay="1s">
                    <div class="pricing-box featured">
                        <div class="pricing-head">
                            <h4>Bước 3</h4>
                            <div class="price">Chọn thẻ @cash Singapore</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 wow flipInY" data-wow-delay="1.2s">
                    <div class="pricing-box">
                        <div class="pricing-head">
                            <h4>Bước 4</h4>
                            <div class="price">Nhập thông tin thẻ đã mua</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="row text-center margintop20 marginbot20 wow fadeInUp" data-wow-delay="0.4s">
              <div class="col-md-8 col-md-offset-2">
                <p></p>
              </div>
            </div>
            <div class="row">
            </div> -->
        </div>
    </section>
    <!-- End pricing -->

    <!-- Start team -->
    <section id="team" class="color-bg contain paddingbot-clear">
        <div class="container">
            <div class="row wow fadeInDown" data-wow-delay="0.4s">
                <div class="col-md-8 col-md-offset-2">
                    <div class="heading centered">
                        <h3>Video <span>hướng dẫn</span></h3>
                        <span class="heding-style"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 col-md-offset-2 marginbot40">
                    <div class="videoWrapper">
                        <iframe width="853" height="480" src="{$option->youtube_link}" frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End team -->

    <!-- Start contact -->
    <section id="contact" class="contain">
        <div class="container">
            <div class="row text-center marginbot20 wow fadeInDown" data-wow-delay="0.4s">
                <div class="col-md-6 col-md-offset-3">
                    <div class="heading centered">
                        <h3>Liên hệ</h3>
                        <span class="heding-style"></span>
                    </div>
                    <h5>Nếu bạn có thắc mắc hoặc vấn đề về thanh toán, bạn có thể gửi liên hệ ở form dưới, chúng tôi sẽ trả lời và hỗ trợ bạn trong thời gian sớm nhất</h5>
                </div>
            </div>
            <div class="row wow fadeInUp" data-wow-delay="0.4s">
                <div class="col-md-8 col-md-offset-2">
                    <form class="webform-client-form webform-client-form-10" method="post" id="frm_contact">
                        <div>
                            <ul class="listForm">
                                <li>
                                    <div  class="form-item webform-component webform-component-textfield webform-component--name">
                                        <label class="element-invisible" for="edit-submitted-name">Name <span class="form-required">*</span></label>
                                        <input placeholder="Họ và tên" class="form-control form-text required" type="text" name="name" value="" size="60" maxlength="128" />
                                    </div>
                                </li>
                                <li>
                                    <div  class="form-item webform-component webform-component-email webform-component--email">
                                        <label class="element-invisible" for="edit-submitted-email">Email <span class="form-required">*</span></label>
                                        <input class="email form-control form-text form-email required" placeholder="Địa chỉ email" type="email" id="edit-submitted-email" name="email" size="60" />
                                    </div>
                                </li>
                                <li class="fullwidth">
                                    <div  class="form-item webform-component webform-component-textarea webform-component--message">
                                        <label class="element-invisible" for="edit-submitted-message">Message <span class="form-required">*</span></label>
                                        <div class="form-textarea-wrapper">
                                            <textarea placeholder="Nội dung liên hệ" class="form-control form-textarea required" id="edit-submitted-message" name="message" cols="60" rows="5"></textarea></div>
                                    </div>
                                </li>
                                <li class="fullwidth">
                                    <label class="contact-msg"></label>
                                </li>
                                <li class="fullwidth">
                                    <div class="form-actions">
                                        <input class="webform-submit button-primary btn btn-default btn-bavel btn-lg btn-block form-submit" type="submit" value="Gửi liên hệ" /></div>  
                                </li>

                            </ul>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <div class="col-md-6" id="login">
        <form accept-charset="UTF-8" id="user-login" method="post">
            <div class="result" style="color: orangered; margin-top: 10px;"></div>
            <div class="form-item form-type-textfield form-item-name">
                <label for="edit-name--3">Username <span title="This field is required." class="form-required">*</span></label>
                <input type="text" maxlength="60" size="60" value="" name="name" id="edit-name--3" placeholder="Username" class="form-control form-block form-text required">
            </div>
            <div class="form-item form-type-password form-item-pass">
                <label for="edit-pass--3">Password <span title="This field is required." class="form-required">*</span></label>
                <input type="password" maxlength="128" size="60" name="pass" id="edit-pass--3" placeholder="Mật khẩu" class="form-control form-block form-text required">
            </div>
            <input type="hidden" value="user_login" name="form_id">
            <div id="edit-actions--4" class="form-actions form-wrapper">
                <input type="submit" value="Login" class="btn btn-green btn-bavel btn-lg form-submit"/>
            </div>
        </form>  
    </div>

    <div class="col-md-6" id="register">
        <form id="user-register-form" method="post">
            <div>
                <div class="result" style="color: orangered; margin-top: 10px;"></div>
                <div class="form-wrapper" id="edit-account">
                    <div class="form-item form-type-textfield form-item-name">
                        <label for="edit-name--2">Username</label>
                        <input type="text" maxlength="60" size="60" value="" name="name" id="edit-name--2" class="username form-control form-block form-text required">
                        <div class="description">Nhập tên tài khoản của bạn</div>
                    </div>
                    <div class="form-item form-type-textfield form-item-mail">
                        <label for="edit-mail">E-mail address</label>
                        <input type="text" maxlength="254" size="60" value="" name="mail" id="edit-mail" class="form-control form-block form-text required">
                        <div class="description">Nhập email của bạn</div>
                    </div>
                    <div class="form-item form-type-password-confirm form-item-pass">
                        <div class="form-item form-type-password form-item-pass-pass1 password-parent">
                            <label for="edit-pass-pass1">Password*</label>
                            <input type="password" maxlength="128" size="25" name="pass" id="edit-pass-pass1" class="password-field form-text required password-processed form-control form-block">
                        </div>
                        <div class="form-item form-type-password form-item-pass-pass2 confirm-parent">

                            <label for="edit-pass-pass2">Confirm password*</label>
                            <input type="password" maxlength="128" size="25" name="passcf" id="edit-pass-pass2" class="password-confirm form-text required form-control form-block">
                        </div>
                    </div>
                </div>
                <input type="hidden" value="user_register_form" name="form_id">
                <div id="edit-actions--3" class="form-actions form-wrapper">
                <input type="submit" value="Tạo tài khoản mới" name="op" id="edit-submit--3" class="btn btn-green btn-bavel btn-lg form-submit"></div>
            </div>
        </form>
    </div>

    <div id="bang-gia" class="col-md-4">
            {$option->payment_rate}
    </div>
    <div id="pay_history" class="col-md-6" style="display: none;">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Ngày mua</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                    <th>Mã code</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$history item=h}    
            <tr>
                <td>{$h.payment_date}</td>
                <td>{$h.count}</td>
                <td>{$h.amount}</td>
                <td>{$h.code}</td>
            </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    <div id="coin_history" class="col-md-6" style="display: none;">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Ngày nạp</th>
                    <th>Loại thẻ</th>
                    <th>Mã Pin</th>
                    <th>Số Serial</th>
                    <th>Mệnh giá</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$recharge_history item=c}
            <tr>
                <td>{$c.date}</td>
                <td>{$c.network}</td>
                <td>{$c.pin}</td>
                <td>{$c.serial}</td>
                <td>{$c.value}</td>
            </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    <!-- Start footer -->
    <footer>
        <div class="social-network">
            <a href="http://facebook.com/ppo.vn" class="social-link"><span class="icon-social-facebook"></span></a>
            <a href="https://twitter.com/ppovn" class="social-link"><span class="icon-social-twitter"></span></a>
            <a href="#" class="social-link"><span class="icon-social-youtube"></span></a>
        </div>
        <p class="copyright">Copyright &copy; <a href="http://sellcash247.com" rel="nofollow">Sellcash247.Com</a>. All rights Reserved.</p>
    </footer>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="{$siteurl}/public/js/js2.js"></script>
    <script src="{$siteurl}/public/js/jquery.validate.min.js"></script>
    <script src="{$siteurl}/public/js/app.js"></script>
</body>
</html>
