<div class="col-sm-3 col-md-2 sidebar">
    <ul class="nav nav-sidebar">
        <!--<li class="active"><a href="#">Overview</a></li>-->
        <?php if ($current_user['capability']['posts']['create'] == 1) : ?>
            <li><a href="<?php echo DASHBOARD_URL . '/post/addnew' ?>"><?php echo Language::$phrases['action']['addnew.post']; ?></a></li>
        <?php endif; ?>
        <?php if ($current_user['capability']['products']['create'] == 1) : ?>
            <li><a href="<?php echo DASHBOARD_URL . '/product/addnew'; ?>"><?php echo Language::$phrases['action']['addnew.product']; ?></a></li>
        <?php endif; ?>
        <?php if ($current_user['capability']['orders']['view'] == 1) : ?>
            <li><a href="<?php echo DASHBOARD_URL . '/order/?status=0'; ?>"><?php echo Language::$phrases['page']['order']['labels']; ?></a></li>
        <?php endif; ?>
    </ul>
</div>