<?php include(ADMIN_PATH . 'view' . DS . 'header.php'); ?>
<script type="text/javascript">
    var browsers_chart = new Array();
    var browsers_data = new Array();
</script>
<?php
function BrowserVersionStats($Browser) {
    $statsModel = new StatisticsAdminModel();
    $count = $statsModel->countAgent($Browser);
    $Browser_tag = strtolower(preg_replace('/[^a-zA-Z]/', '', $Browser));
?>
    <div class="panel panel-primary">
        <div class="panel-heading"><?php echo sprintf('%s Version', $Browser); ?></div>
        <div class="panel-body">
            <div id="browser-<?php echo $Browser_tag; ?>-log"></div>
            <script type="text/javascript">
                browsers_chart.push({
                    tag: '<?php echo $Browser_tag; ?>',
                    chart: null,
                    title: '<?php echo $Browser; ?>'
                });
                count = [
                    <?php
                    $Versions = $statsModel->agent_version_list($Browser);
                    foreach ($Versions as $Version) {
                        $count = $statsModel->count_agent_version($Browser, $Version);
                        echo "['" . $Version . " (" . $count . ")', " . $count . "],\r\n";
                    }
                    ?>
                ];
                browsers_data.push(count);
            </script>
        </div>
    </div>
<?php
}
?>
<!--MAIN-->
<div class="container-fluid">
    <div class="row dashboard">
        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading"><?php echo Language::$phrases['statistics']['browser_statistics']; ?></div>
                <div class="panel-body">
                    <?php
                    $stats = new Statistics();
                    $statsModel = new StatisticsAdminModel();
                    $search_engines = $stats->searchengine_list();
                    $search_result['All'] = count($statsModel->searchengine('all','total'));

                    foreach( $search_engines as $key => $se ) {
                            $search_result[$key] = count($statsModel->searchengine($key,'total'));
                    }
                    ?>
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
        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading"><?php echo Language::$phrases['statistics']['platform']; ?></div>
                <div class="panel-body">
                    <div id="platform-log"></div>
                    <script type="text/javascript">
                        var platform_chart;
                        var platform_data = [
                            <?php
                            $Platforms = $statsModel->platform_list();
                            foreach ($Platforms as $Platform) {
                                $count = $statsModel->countPlatform($Platform);
                                echo "['" . $Platform . " (" . $count . ")', " . $count . "],\r\n";
                            }
                            ?>
                            ];
                    </script>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <?php
            for ($BrowserCount = 0; $BrowserCount < count($Browsers); $BrowserCount++) {
                if ($BrowserCount % 3 == 0) {
                    BrowserVersionStats($Browsers[$BrowserCount]);
                }
            }
            ?>
        </div>
        <div class="col-md-4">
            <?php
            for ($BrowserCount = 0; $BrowserCount < count($Browsers); $BrowserCount++) {
                if ($BrowserCount % 3 == 1) {
                    BrowserVersionStats($Browsers[$BrowserCount]);
                }
            }
            ?>
        </div>
        <div class="col-md-4">
            <?php
            for ($BrowserCount = 0; $BrowserCount < count($Browsers); $BrowserCount++) {
                if ($BrowserCount % 3 == 2) {
                    BrowserVersionStats($Browsers[$BrowserCount]);
                }
            }
            ?>
        </div>
    </div>
</div>
<!--END MAIN-->
<?php include(ADMIN_PATH . 'view' . DS . 'footer.php'); ?>