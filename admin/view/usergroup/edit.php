<?php include(ADMIN_PATH . 'view' . DS . 'header.php'); ?>
<!--MAIN-->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 main">
            <div class="action-bar">
                <div class="btn-bar pull-left">
                    <a href="../" class="btn btn-primary">&laquo; <?php echo Language::$phrases['navigation']['back']; ?></a>
                    <?php if ($current_user['capability']['userGroups']['permission'] == 1) : ?>
                        <a href="<?php echo DASHBOARD_URL . '/userGroup/' . $this->group['id'] . '/permission'; ?>" class="btn btn-info"><?php echo Language::$phrases['action']['permission']; ?></a>
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