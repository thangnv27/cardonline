<?php
include 'header.php';

$stats = new Statistics();
$statsModel = new StatisticsAdminModel();
$last_day = 30;
?>
<!--MAIN-->
<div class="container-fluid">
    <div class="row dashboard">
        <div class="col-md-4">
            <?php echo $this->table; ?>
            <?php echo $this->statistics_useronline; ?>
            <?php echo $this->statistics_se_referrers; ?>
            <?php echo $this->statistics_referrers; ?>
            <?php echo $this->statistics_countries; ?>
        </div>
        <div class="col-md-8">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <?php echo Language::$phrases['statistics']['hits_statistical_chart']; ?> (<a href="<?php echo DASHBOARD_URL; ?>/statistics/hits/"><span class="label"><?php echo Language::$phrases['context']['more']; ?></span></a>)
                </div>
                <div class="panel-body">
                    <div id="visits-log"></div>
                    <script type="text/javascript">
                        var visit_chart;
                        var last_day = <?php echo $last_day; ?>;
                        var categories_datetime = [<?php
                            for ($i = $last_day; $i >= 0; $i--) {
                                echo '"' . $stats->Current_Date('Y-m-d', '-' . $i) . '"';
                                echo ", ";
                            }
                            ?>];
                        var data_visitor = [<?php
                            for ($i = $last_day; $i >= 0; $i--) {
                                echo $statsModel->visitor('-' . $i, true, true);
                                echo ", ";
                            }
                            ?>];
                        var data_visit = [<?php
                            for ($i = $last_day; $i >= 0; $i--) {
                                echo $statsModel->visit('-' . $i, true);
                                echo ", ";
                            }
                            ?>];
                    </script>
                </div>
            </div>

            <div class="panel panel-primary">
                <div class="panel-heading">
                    <?php echo Language::$phrases['statistics']['search_engine_referrers_chart']; ?> (<a href="<?php echo DASHBOARD_URL; ?>/statistics/searches/"><span class="label"><?php echo Language::$phrases['context']['more']; ?></span></a>)
                </div>
                <div class="panel-body">
                    <div id="search-engine-log"></div>
                    <script type="text/javascript">
                        var search_chart;
                        var search_series = [
                            <?php
                            $search_engines = $stats->searchengine_list();
                            $total_stats = FALSE;
                            $total_daily = array();

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
                    </script>
                </div>
            </div>
            <?php echo $this->statistics_searchwords; ?>
            <?php echo $this->statistics_visitors; ?>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <?php echo Language::$phrases['statistics']['browsers']; ?> (<a href="<?php echo DASHBOARD_URL; ?>/statistics/browsers/"><span class="label"><?php echo Language::$phrases['context']['more']; ?></span></a>)
                </div>
                <div class="panel-body">
                    <div id="browsers-log"></div>
                    <script type="text/javascript">
                        var browser_chart;
                        var browser_data = [
                            <?php
                            $Browsers = $statsModel->agent_list();
                            foreach ($Browsers as $Browser) {
                                $count = $statsModel->countAgent($Browser);
                                echo "['" . $Browser . " (" . $count . ")', " . $count . "],\r\n";
                            }
                            ?>
                            ];
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
<!--END MAIN-->
<?php include 'footer.php'; ?>