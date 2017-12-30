<?php include(ADMIN_PATH . 'view' . DS . 'header.php'); ?>
<!--MAIN-->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 main">
            <div class="row">
                <form class="form-horizontal" role="form" action="" method="post">
                    <div class="col-md-12 col-xs-12">
                        <div class="panel panel-primary">
                            <div class="panel-heading"><?php echo $this->title; ?></div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-2"></div>
                                    <div class="col-sm-10">
                                        <ul class="nav nav-tabs">
                                            <li class="active"><a href="#tab_general" data-toggle="tab"><?php echo Language::$phrases['context']['general']; ?></a></li>
                                            <li><a href="#tab_contact" data-toggle="tab">Tỉ lệ quy đổi</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="tab-content">
                                    <!--tab thong tin co ban-->
                                    <div class="tab-pane active" id="tab_general">
                                        <?php
                                        echo $this->form->get('name');
                                        echo $this->form->get('slug');
                                        echo $this->form->get('description');
                                        echo $this->form->get('keywords');
                                        echo $this->form->get('logo');
                                        echo $this->form->get('favicon');
                                        echo $this->form->get('sologan');
                                        echo $this->form->get('admin_email');
                                        echo $this->form->get('ga_id');
                                        echo $this->form->get('youtube_link');
                                        echo $this->form->get('footer_info');
                                        echo $this->form->get('payment_rate');
                                        ?>
                                    </div>
                                    
                                    <div class="tab-pane" id="tab_contact">
                                        <?php
                                        echo $this->form->get('code1');
                                        echo $this->form->get('code3');
                                        echo $this->form->get('code7');
                                        echo $this->form->get('code16');
                                        echo $this->form->get('code24');
                                        echo $this->form->get('code40');
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <div class="row">
                                    <div class="col-sm-2"></div>
                                    <div class="col-sm-5">
                                        <input type="submit" class="btn btn-primary" value="<?php echo Language::$phrases['action']['update']; ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
<!--END MAIN-->
<?php include(ADMIN_PATH . 'view' . DS . 'footer.php'); ?>