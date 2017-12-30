<?php include(ADMIN_PATH . 'view' . DS . 'header.php'); ?>
<!--MAIN-->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 main">
            <div class="action-bar">
                <div class="btn-bar pull-left">
                    <a href="../" class="btn btn-primary">&laquo; <?php echo Language::$phrases['navigation']['back']; ?></a>
                </div>
            </div>

            <!--Add new form-->

            <div class="row">
                <div class="col-md-12 col-xs-12">
                    <form class="form-horizontal" method="post" action="">
                        <div class="panel panel-primary">
                            <div class="panel-heading"><?php echo Language::$phrases['context']['permission_for'] . $this->user['username'];?></div>
                            <div class="panel-body">
                                <?php
                                $capability = $this->user['capability'];
                                foreach ($capability as $fn => $act) {
                                    echo '<div class="form-group" style="border-bottom:1px">
                                            <label class="col-sm-2 control-label">' . ucfirst($fn) . '</label>
                                        <div class="col-sm-5">';
                                    foreach ($act as $key => $value) {
                                        echo '<div class="form-group">';
                                        echo '<label class="col-sm-2 control-label" style="font-weight:normal">' . ucfirst($key) . ':</label>';
                                        echo '<div class="col-sm-5">';
                                        $checked = ($value == 1) ? 'checked' : '';
                                        echo '<input type="checkbox" name="capability[' . $fn . '][' . $key . ']" class="cbswitch" ' . $checked . ' />';
                                        echo '</div></div>';
                                    }
                                    echo '</div></div>';
                                }
                                ?>
                            </div>
                            <div class="panel-footer">
                                <div class="row">
                                    <div class="col-sm-2"></div>
                                    <div class="col-sm-5">
                                        <input type="submit" class="btn btn-primary" value="<?php echo Language::$phrases['action']['submit']; ?>" />
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!--END MAIN-->
<?php include(ADMIN_PATH . 'view' . DS . 'footer.php'); ?>