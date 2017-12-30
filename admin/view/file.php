<?php include(ADMIN_PATH . 'view' . DS . 'header.php'); ?>
<!--MAIN-->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 main">
            <div class="row">
                <iframe src="<?php echo Registry::$siteurl . '/public/admin/elfinder/elfinder.php'; ?>" style="border: none; width: 100%;height: 500px;"></iframe>
            </div>
        </div>
    </div>
</div>
<!--END MAIN-->
<?php include(ADMIN_PATH . 'view' . DS . 'footer.php'); ?>