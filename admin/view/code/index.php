<?php include(ADMIN_PATH . 'view' . DS . 'header.php'); ?>
<!--MAIN-->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 main">
            <form action="" method="get">
                <!--Table-->
                <div class="action-bar">
                    <a href="./"><span class="label label-primary"><?php echo Language::$phrases['context']['all']; ?></span></a>
                    <a href="./?type=noused"><span class="label label-success"><?php echo Language::$phrases['context']['noused']; ?></span></a>
                    <a href="./?type=used"><span class="label label-danger"><?php echo Language::$phrases['context']['used']; ?></span></a>
                    <a href="./?status=draft"><span class="label label-default"><?php echo Language::$phrases['context']['draft']; ?></span></a>
                    <a href="./?status=trashed"><span class="label label-warning"><?php echo Language::$phrases['context']['trash']; ?></span></a>
                </div>
                <div class="action-bar">
                    <div class="btn-bar pull-left">
                        <?php if ($current_user['capability']['code']['create'] == 1) : ?>
                            <a href="addnew" class="btn btn-success"><?php echo Language::$phrases['action']['addnew']; ?></a>
                        <?php endif; ?>
                        <select name="action">
                            <option value=""><?php echo Language::$phrases['action']['bulkActions']; ?></option>
                            <?php if ($current_user['capability']['code']['edit'] == 1) : ?>
                                <option value="publish"><?php echo Language::$phrases['action']['publish']; ?></option>
                                <option value="move2trash"><?php echo Language::$phrases['action']['move2trash']; ?></option>
                            <?php endif; ?>
                        </select>
                        <button type="submit" class="btn btn-primary"><?php echo Language::$phrases['action']['apply']; ?></button>
                    </div>
                    <div class="search pull-right">
                        <div class="input-group">
                            <input type="text" class="form-control" name="s" />
                            <span class="input-group-btn">
                                <button class="btn btn-primary" type="submit"><?php echo Language::$phrases['action']['search']; ?></button>
                            </span>
                        </div>
                    </div>
                </div>

                <?php echo $this->table; ?>
            </form>
        </div>
    </div>
</div>
<!--END MAIN-->
<?php include(ADMIN_PATH . 'view' . DS . 'footer.php'); ?>