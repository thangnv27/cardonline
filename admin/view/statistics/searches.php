<?php include(ADMIN_PATH . 'view' . DS . 'header.php'); ?>
<!--MAIN-->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 main">
            <form action="" method="get">
                <!--Table-->
                <div class="action-bar">
                    <div class="btn-bar pull-left">
                        <div class="input-group">
                            <input type="text" class="form-control datepicker" name="startdate" 
                                   value="<?php echo $_GET['startdate']; ?>" data-date="<?php echo $_GET['startdate']; ?>" data-date-format="yyyy-mm-dd" 
                                   placeholder="<?php echo Language::$phrases['context']['start_date']; ?>" style="display: inline;margin-right: 1px;width: 120px;" />
                            <input type="text" class="form-control datepicker" name="enddate" 
                                   value="<?php echo $_GET['enddate']; ?>" data-date="<?php echo $_GET['enddate']; ?>" data-date-format="yyyy-mm-dd" 
                                   placeholder="<?php echo Language::$phrases['context']['end_date']; ?>" style="width: 120px;" />
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-primary"><?php echo Language::$phrases['action']['filter']; ?></button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="panel panel-primary">
                    <div class="panel-heading"><?php echo Language::$phrases['statistics']['hits_statistical_chart']; ?></div>
                    <div class="panel-body">
                        <?php
                        $stats = new Statistics();
                        $statsModel = new StatisticsAdminModel();
                        $last_day = 30;
                        if (isset($_GET['startdate'])) {
                            $startDate = $_GET['startdate'];
                            $endDate = $stats->Current_Date('Y-m-d'); 
                            if (isset($_GET['startdate'])) {
                                $endDate = $_GET['enddate'];
                                if(strtotime($startDate) > strtotime($endDate)){
                                    $startDate = $_GET['enddate'];
                                    $endDate = $_GET['startdate'];
                                }
                            }
                            $last_day = dateDifference($endDate, $startDate);
                        }

                        $search_engines = $stats->searchengine_list();
                        $total_stats = FALSE;
                        $total_daily = array();
                        ?>
                        <div id="search-engine-log"></div>
                        <script type="text/javascript">
                            var search_chart;
                            var last_day = <?php echo $last_day; ?>;
                            <?php if(!isset($_GET['enddate']) or trim($_GET['enddate']) == ""): ?>
                            var categories_datetime = [<?php
                                for ($i = $last_day; $i >= 0; $i--) {
                                    echo '"' . $stats->Current_Date('Y-m-d', '-' . $i) . '"';
                                    echo ", ";
                                }
                                ?>];
                                var search_series = [
                                <?php
                                    foreach ($search_engines as $se) {
                                        echo "{\n";
                                        echo "name: '" . $se['name'] . "',\n";
                                        echo "data: [";

                                        for ($i = $last_day; $i >= 0; $i--) {
                                            $result = count($statsModel->searchengine($se['tag'], '-' . $i)) . ", ";
                                            $total_daily[$i] += $result;
                                            echo $result;
                                        }

                                        echo "]\n";
                                        echo "},\n";
                                    }

                                    if ($total_stats == 1) {
                                        echo "{\n";
                                        echo "name: 'Total',\n";
                                        echo "data: [";

                                        for ($i = $last_day; $i >= 0; $i--) {
                                            echo $total_daily[$i] . ", ";
                                        }

                                        echo "]\n";
                                        echo "},\n";
                                    }
                                ?>
                                ];
                            <?php else: ?>
                            var categories_datetime = [<?php
                                for ($i = $last_day; $i >= 0; $i--) {
                                    echo '"' . date("Y-m-d", strtotime($endDate) - 3600*24*$i) . '"';
                                    echo ", ";
                                }
                                ?>];
                            var search_series = [
                                <?php
                                    foreach ($search_engines as $se) {
                                        echo "{\n";
                                        echo "name: '" . $se['name'] . "',\n";
                                        echo "data: [";

                                        for ($i = $last_day; $i >= 0; $i--) {
                                            $result = count($statsModel->searchengine_visitors(date("Y-m-d", strtotime($endDate) - 3600*24*$i), $se['tag'])) . ", ";
                                            $total_daily[$i] += $result;
                                            echo $result;
                                        }

                                        echo "]\n";
                                        echo "},\n";
                                    }

                                    if ($total_stats == 1) {
                                        echo "{\n";
                                        echo "name: 'Total',\n";
                                        echo "data: [";

                                        for ($i = $last_day; $i >= 0; $i--) {
                                            echo $total_daily[$i] . ", ";
                                        }

                                        echo "]\n";
                                        echo "},\n";
                                    }
                                ?>
                                ];
                            <?php endif; ?>
                        </script>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!--END MAIN-->
<?php include(ADMIN_PATH . 'view' . DS . 'footer.php'); ?>