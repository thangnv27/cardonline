<?php include(ADMIN_PATH . 'view' . DS . 'header.php'); ?>
<!--MAIN-->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 main">
            <form action="" method="get">
                <!--Table-->
                <div class="action-bar">
                    <div class="btn-bar pull-left">
                        <?php if ($current_user['capability']['menu']['manage'] == 1 and !empty($_GET['name'])) : ?>
                        <a href="addnew/?name=<?php echo $_GET['name']; ?>" class="btn btn-success"><?php echo Language::$phrases['action']['addnew']; ?></a>
                        <?php endif; ?>
                        <select name="name">
                            <option value=""><?php echo Language::$phrases['action']['selectMenu']; ?></option>
                            <?php foreach (Utils::getMenuLocation() as $key => $value) {
                                if($key == $_GET['name']){
                                    echo '<option value="' . $key . '" selected>' . $value . '</option>';
                                }else{
                                    echo '<option value="' . $key . '">' . $value . '</option>';
                                }
                            } ?>
                        </select>
                        <button type="submit" class="btn btn-primary"><?php echo Language::$phrases['action']['select']; ?></button>
                    </div>
                    <div class="search pull-right"></div>
                </div>

                <?php echo $this->table; ?>
            </form>
        </div>
    </div>
</div>
<!--END MAIN-->
<?php include(ADMIN_PATH . 'view' . DS . 'footer.php'); ?>