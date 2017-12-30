<?php include(ADMIN_PATH . 'view' . DS . 'header.php'); ?>
<!--MAIN-->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 main">
            <form action="" method="get">
                <!--Table-->
                <div class="action-bar">
                    <div class="btn-bar pull-left">
                        <?php
                        $stats = new Statistics();
                        $statsModel = new StatisticsAdminModel();
                        $search_engines = $stats->searchengine_list();
                        $search_result['All'] = count($statsModel->searchword('all', 'total'));

                        foreach ($search_engines as $key => $se) {
                            $search_result[$key] = count($statsModel->searchword($key, 'total'));
                        }

                        if (array_key_exists('referred', $_GET)) {
                            if ($_GET['referred'] != '') {
                                $referred = $_GET['referred'];
                            } else {
                                $referred = 'All';
                            }
                        } else {
                            $referred = 'All';
                        }

                        $total = $search_result[$referred];

                        foreach ($search_result as $key => $value) {
                            if ($key == 'All') {
                                $tag = 'All';
                                $name = Language::$phrases['context']['all'];
                            } else {
                                $tag = $search_engines[$key]['tag'];
                                $name = $search_engines[$key]['name'];
                            }
                            if ($referred == $tag) {
                                echo "<a href='?referred={$tag}'><span class='label label-success'>{$name} ({$value})</span></a>&nbsp;";
                            } else {
                                echo "<a href='?referred={$tag}'><span class='label label-primary'>{$name} ({$value})</span></a>&nbsp;";
                            }
                        }
                        ?>
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