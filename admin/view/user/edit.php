<?php include(ADMIN_PATH . 'view' . DS . 'header.php'); ?>
<!--MAIN-->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 main">
            <div class="action-bar">
                <div class="btn-bar pull-left">
                    <a href="../" class="btn btn-primary">&laquo; <?php echo Language::$phrases['navigation']['back']; ?></a>
                    <?php if ($current_user['capability']['users']['create'] == 1) : ?>
                    <a href="../addnew" class="btn btn-success"><?php echo Language::$phrases['action']['addnew']; ?></a>
                    <?php endif; ?>
                    <?php if ($current_user['capability']['users']['permission'] == 1) : ?>
                    <a href="<?php echo DASHBOARD_URL . '/user/' . $this->user['id'] . '/permission'; ?>" class="btn btn-info"><?php echo Language::$phrases['action']['permission']; ?></a>
                    <?php endif; ?>
                    <?php if ($current_user['capability']['users']['delete'] == 1) : ?>
                    <a href="<?php echo DASHBOARD_URL . '/user/' . $this->user['id'] . '/delete'; ?>" class="btn btn-danger"
                        <?php echo ' onclick="return confirm(\'' . Language::$phrases['action']['delete.confirm'] . '\');"'; ?>>
                        <?php echo Language::$phrases['action']['delete']; ?>
                    </a>
                    <?php endif; ?>
                    <?php if ($current_user['capability']['users']['edit'] == 1 AND $this->user['activation_key'] != "") : ?>
                    <a href="<?php echo DASHBOARD_URL . '/user/' . $this->user['id'] . '/activation'; ?>" class="btn btn-warning"
                        <?php echo ' onclick="return confirm(\'' . Language::$phrases['action']['activate.confirm'] . '\');"'; ?>>
                        <?php echo Language::$phrases['action']['activate']; ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-xs-12">
                    <?php echo $this->formview; ?>
                </div>
            </div>

        </div>
    </div>
</div>
<!--END MAIN-->
<?php include(ADMIN_PATH . 'view' . DS . 'footer.php'); ?>