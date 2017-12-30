<?php

class WelcomeAdmin extends AdminController {

    private $current_user;

    function __construct() {
        parent::__construct();
        $this->current_user = UserAdmin::checkLogin();
    }

    function index() {
        if ($this->current_user['role'] == 'subscriber') {
            $this->redirect(DASHBOARD_URL . '/user/profile/');
        }
        $title = Language::$phrases['page']['dashboard']['title'];
        $stats = new Statistics();
        $ISOCountryCode = Registry::$settings['ISO_COUNTRY_CODE'];

        ## Summary table
        $table = new Table(Language::$phrases['context']['summary']);
        $table->add_columns(array(
            'col_name' => '',
            'col_value' => '',
        ));

        $row = "";
//        $row .= "<tr><td>" . Language::$phrases['page']['dashboard']['posts'] . "</td>";
//        $row .= "<td><span class=\"label label-primary\">" . $this->model->countPosts() . "</span>";
//        if ($this->current_user['capability']['posts']['view'] == 1) {
//            $row .= " [<a href='" . DASHBOARD_URL . "/post/'>" . Language::$phrases['action']['view'] . "</a>]";
//        }
//        $row .= "</td></tr>";
//        $row .= "<tr><td>" . Language::$phrases['page']['dashboard']['products'] . "</td>";
//        $row .= "<td><span class=\"label label-primary\">" . $this->model->countProducts() . "</span>";
//        if ($this->current_user['capability']['products']['view'] == 1) {
//            $row .= " [<a href='" . DASHBOARD_URL . "/product/'>" . Language::$phrases['action']['view'] . "</a>]";
//        }
//        $row .= "</td></tr>";
        $row .= "<tr><td>" . Language::$phrases['page']['dashboard']['users'] . "</td>";
        $row .= "<td><span class=\"label label-primary\">" . $this->model->countUsers() . "</span>";
        if ($this->current_user['capability']['users']['view'] == 1) {
            $row .= " [<a href='" . DASHBOARD_URL . "/user/'>" . Language::$phrases['action']['view'] . "</a>]";
        }
        $row .= "</td></tr>";
//        $row .= "<tr><td>" . Language::$phrases['page']['dashboard']['pending_orders'] . "</td>";
//        $row .= "<td><span class=\"label label-primary\">" . $this->model->countPendingOrders() . "</span>";
//        if ($this->current_user['capability']['orders']['view'] == 1) {
//            $row .= " [<a href='" . DASHBOARD_URL . "/order/?status=0'>" . Language::$phrases['action']['view'] . "</a>]";
//        }
//        $row .= "</td></tr>";

        $table->add_rows($row);

        ## Statistics
        $statisticsModel = new StatisticsAdminModel();
        $useronline = $statisticsModel->useronline();
        if ($useronline != 0) {
            $useronline = '<a href="' . DASHBOARD_URL . '/statistics/useronline"><span class="label label-warning">' . $useronline . '</span></a>';
        }
        $statistics_useronline = new Table(Language::$phrases['statistics']['useronline'] . ": " . $useronline);
        $statistics_useronline->add_columns(array(
            'col_name' => '',
            'col_visitor' => Language::$phrases['statistics']['visitor'],
            'col_visit' => Language::$phrases['statistics']['visit'],
        ));

        $row = "<tr><td>" . Language::$phrases['statistics']['today'] . "</td>";
        $row .= "<td><span class=\"label label-primary\">" . $statisticsModel->visitor('today', null, true) . "</span></td>";
        $row .= "<td><span class=\"label label-primary\">" . $statisticsModel->visit('today') . "</span></td>";
        $row .= "</tr>";
        $row .= "<tr><td>" . Language::$phrases['statistics']['yesterday'] . "</td>";
        $row .= "<td><span class=\"label label-primary\">" . $statisticsModel->visitor('yesterday', null, true) . "</span></td>";
        $row .= "<td><span class=\"label label-primary\">" . $statisticsModel->visit('yesterday') . "</span></td>";
        $row .= "</tr>";
        $row .= "<tr><td>" . Language::$phrases['statistics']['week'] . "</td>";
        $row .= "<td><span class=\"label label-primary\">" . $statisticsModel->visitor('week', null, true) . "</span></td>";
        $row .= "<td><span class=\"label label-primary\">" . $statisticsModel->visit('week') . "</span></td>";
        $row .= "</tr>";
        $row .= "<tr><td>" . Language::$phrases['statistics']['month'] . "</td>";
        $row .= "<td><span class=\"label label-primary\">" . $statisticsModel->visitor('month', null, true) . "</span></td>";
        $row .= "<td><span class=\"label label-primary\">" . $statisticsModel->visit('month') . "</span></td>";
        $row .= "</tr>";
        $row .= "<tr><td>" . Language::$phrases['statistics']['year'] . "</td>";
        $row .= "<td><span class=\"label label-primary\">" . $statisticsModel->visitor('year', null, true) . "</span></td>";
        $row .= "<td><span class=\"label label-primary\">" . $statisticsModel->visit('year') . "</span></td>";
        $row .= "</tr>";
        $row .= "<tr><td>" . Language::$phrases['statistics']['total'] . "</td>";
        $row .= "<td><span class=\"label label-primary\">" . $statisticsModel->visitor('total', null, true) . "</span></td>";
        $row .= "<td><span class=\"label label-primary\">" . $statisticsModel->visit('total') . "</span></td>";
        $row .= "</tr>";

        $statistics_useronline->add_rows($row);

        ## Statistics recent visitors
        $statistics_visitors = new Table(Language::$phrases['statistics']['recent_visitors'] . " (<a href='" . DASHBOARD_URL . "/statistics/recent_visitors/" . "'><span class='label'>" . Language::$phrases['context']['more'] . "</span></a>)");
        $columns = array(
            'col_date' => Language::$phrases['statistics']['date'],
            'col_ip' => 'IP',
            'col_referred' => Language::$phrases['statistics']['referred'],
            'col_platform' => Language::$phrases['statistics']['platform'],
        );
        $statistics_visitors->add_columns($columns);
        $records = $statisticsModel->recentVisitors(0, 10);
        $row = "";
        foreach ($records as $rec) {
            //Open the line
            $row .= '<tr id="row_' . $rec->ID . '">';
            foreach ($columns as $field => $col_name) {
                $class = "class='$field column-$field' ";
                $style = "";
                $attributes = $class . $style;
                $check_ads = $stats->check_adwords($rec['referred']);

                $by_date = DASHBOARD_URL . '/statistics/recent_visitors/?s=' . $rec['last_counter'];
                $by_ip = DASHBOARD_URL . '/statistics/recent_visitors/?ip=' . $rec['ip'];
                $by_agent = DASHBOARD_URL . '/statistics/recent_visitors/?agent=' . $rec['agent'];
                $by_location = DASHBOARD_URL . '/statistics/recent_visitors/?location=' . $rec['location'];

                //Display the cell
                switch ($field) {
                    case "col_date":
                        $row .= '<td ' . $attributes . '><a href="' . $by_date . '">' . $rec['last_counter'] . '</a></td>';
                        break;
                    case "col_ip":
                        $row .= '<td ' . $attributes . '>';
                        $row .= '<a href="http://www.geoiptool.com/en/?IP=' . $rec['ip'] . '" target="_blank" title="Lookup IP: ' . $rec['ip'] . '">';
                        $row .= "<img src='" . Registry::$siteurl . "/public/statistics/visibility.png' /></a>";
                        $row .= '&nbsp;<a href="' . $by_ip . '">' . $rec['ip'] . "</a>";
                        $row .= '</td>';
                        break;
                    case "col_referred":
                        $row .= '<td ' . $attributes . '>';
                        if (!empty($rec['location'])) {
                            $row .= "<a href='{$by_location}'><img src='" . Registry::$siteurl . "/public/flags/" . $rec['location'] . ".png' title='{$ISOCountryCode[$rec['location']]}'/></a>&nbsp;";
                        }
                        $row .= '<a href="' . $by_agent . '">';
                        if (array_search(strtolower($rec['agent']), array("chrome", "firefox", "msie", "opera", "safari", "iemobile")) !== FALSE) {
                            $row .= "<img src='" . Registry::$siteurl . "/public/statistics/" . $rec['agent'] . ".png' title='{$rec['agent']} {$rec['version']}'/>";
                        } else {
                            $row .= "<img src='" . Registry::$siteurl . "/public/statistics/unknown.png' title='{$rec['agent']}'/>";
                        }
                        $row .= '</a>&nbsp;';
                        if ($check_ads) {
                            $row .= '<a href="' . $rec['referred'] . '" title="' . $rec['referred'] . '" onclick="return confirm(\'' . Language::$phrases['statistics']['ad.click.confirm'] . '\');">';
                        } else {
                            $row .= '<a href="' . $rec['referred'] . '" title="' . $rec['referred'] . '">';
                        }
                        $row .= "<img src='" . Registry::$siteurl . "/public/statistics/link.png' title='{$rec['referred']}'/>";
                        $row .= '</a>';
                        $row .= '</td>';
                        break;
                    case "col_platform":
                        $row .= '<td ' . $attributes . ' title="' . $rec['platform'] . '">';
                        if (file_exists(PUBLIC_PATH . "statistics" . DS . $rec['platform'] . ".png")) {
                            $row .= '<img src="' . Registry::$siteurl . '/public/statistics/' . $rec['platform'] . '.png" />';
                        } elseif ($stats->platform_is_bot($rec['platform'])) {
                            $row .= '<img src="' . Registry::$siteurl . '/public/statistics/Spider.png" />';
                        } else {
                            $row .= '<img src="' . Registry::$siteurl . '/public/statistics/' . strtolower($rec['platform']) . '.png" alt="' . Utils::get_short_content($rec['platform'], 40) . '" />';
                        }
                        $row .= '</td>';
                        break;
                }
            }

            //Close the line
            $row .= '</tr>';
        }
        $statistics_visitors->add_rows($row);

        ## Statistics recent visitors
        $statistics_searchwords = new Table(Language::$phrases['statistics']['latest_search_words'] . " (<a href='" . DASHBOARD_URL . "/statistics/search_words/" . "'><span class='label'>" . Language::$phrases['context']['more'] . "</span></a>)");
        $columns = array(
            'col_date' => Language::$phrases['statistics']['date'],
            'col_ip' => 'IP',
            'col_referred' => Language::$phrases['statistics']['referred'],
            'col_keyword' => Language::$phrases['statistics']['keyword'],
            'col_adwords' => Language::$phrases['statistics']['adwords'],
            'col_platform' => Language::$phrases['statistics']['platform'],
        );
        $statistics_searchwords->add_columns($columns);
        $records = $statisticsModel->searchWords(0, 10, $stats->searchword_query('all'));
        $row = "";
        foreach ($records as $rec) {
            //Open the line
            $row .= '<tr id="row_' . $rec->ID . '">';
            foreach ($columns as $field => $col_name) {
                if (!$stats->Search_Engine_QueryString($rec['referred']))
                    continue;

                $class = "class='$field column-$field' ";
                $style = "";
                $attributes = $class . $style;
                $check_ads = $stats->check_adwords($rec['referred']);

                $by_date = DASHBOARD_URL . '/statistics/recent_visitors/?s=' . $rec['last_counter'];
                $by_ip = DASHBOARD_URL . '/statistics/recent_visitors/?ip=' . $rec['ip'];
                $by_agent = DASHBOARD_URL . '/statistics/recent_visitors/?agent=' . $rec['agent'];
                $by_location = DASHBOARD_URL . '/statistics/recent_visitors/?location=' . $rec['location'];

                //Display the cell
                switch ($field) {
                    case "col_date":
                        $row .= '<td ' . $attributes . '><a href="' . $by_date . '">' . $rec['last_counter'] . '</a></td>';
                        break;
                    case "col_ip":
                        $row .= '<td ' . $attributes . '>';
                        $row .= '<a href="http://www.geoiptool.com/en/?IP=' . $rec['ip'] . '" target="_blank" title="Lookup IP: ' . $rec['ip'] . '">';
                        $row .= "<img src='" . Registry::$siteurl . "/public/statistics/visibility.png' /></a>";
                        $row .= '&nbsp;<a href="' . $by_ip . '">' . $rec['ip'] . "</a>";
                        $row .= '</td>';
                        break;
                    case "col_referred":
                        $row .= '<td ' . $attributes . '>';
                        if (!empty($rec['location'])) {
                            $row .= "<a href='{$by_location}'><img src='" . Registry::$siteurl . "/public/flags/" . $rec['location'] . ".png' title='{$ISOCountryCode[$rec['location']]}'/></a>&nbsp;";
                        }
                        $search_engine = $stats->Search_Engine_Info($rec['referred']);
                        $row .= "<a href='" . DASHBOARD_URL . "/statistics/search_words/?referred={$search_engine['tag']}'>";
                        $row .= "<img src='" . Registry::$siteurl . "/public/statistics/" . $search_engine['image'] . "' title='" . $search_engine['name'] . "'/></a>&nbsp;";
                        $row .= '<a href="' . $by_agent . '">';
                        if (array_search(strtolower($rec['agent']), array("chrome", "firefox", "msie", "opera", "safari", "iemobile")) !== FALSE) {
                            $row .= "<img src='" . Registry::$siteurl . "/public/statistics/" . $rec['agent'] . ".png' title='{$rec['agent']} {$rec['version']}'/>";
                        } else {
                            $row .= "<img src='" . Registry::$siteurl . "/public/statistics/unknown.png' title='{$rec['agent']}'/>";
                        }
                        $row .= '</a>&nbsp;';
                        if ($check_ads) {
                            $row .= '<a href="' . $rec['referred'] . '" title="' . $rec['referred'] . '" onclick="return confirm(\'' . Language::$phrases['statistics']['ad.click.confirm'] . '\');">';
                        } else {
                            $row .= '<a href="' . $rec['referred'] . '" title="' . $rec['referred'] . '">';
                        }
                        $row .= "<img src='" . Registry::$siteurl . "/public/statistics/link.png' title='{$rec['referred']}'/>";
                        $row .= '</a>';
                        $row .= '</td>';
                        break;
                    case "col_keyword":
                        $row .= '<td ' . $attributes . '>' . substr($stats->Search_Engine_QueryString($rec['referred']), 0, 100) . '</td>';
                        break;
                    case "col_adwords":
                        $row .= '<td ' . $attributes . '>';
                        if ($check_ads) {
                            $row .= '<span class="label label-success">' . Language::$phrases['context']['yes'] . '</span>';
                        } else {
                            $row .= '<span class="label label-default">' . Language::$phrases['context']['no'] . '</span>';
                        }
                        $row .= '</td>';
                        break;
                    case "col_platform":
                        $row .= '<td ' . $attributes . ' title="' . $rec['platform'] . '">';
                        if (file_exists(PUBLIC_PATH . "statistics" . DS . $rec['platform'] . ".png")) {
                            $row .= '<img src="' . Registry::$siteurl . '/public/statistics/' . $rec['platform'] . '.png" />';
                        } elseif ($stats->platform_is_bot($rec['platform'])) {
                            $row .= '<img src="' . Registry::$siteurl . '/public/statistics/Spider.png" />';
                        } else {
                            $row .= '<img src="' . Registry::$siteurl . '/public/statistics/' . strtolower($rec['platform']) . '.png" alt="' . Utils::get_short_content($rec['platform'], 40) . '" />';
                        }
                        $row .= '</td>';
                        break;
                }
            }

            //Close the line
            $row .= '</tr>';
        }
        $statistics_searchwords->add_rows($row);

        ## Statistics top referring sites
        $statistics_referrers = new Table(Language::$phrases['statistics']['top_referring_sites'] . " (<a href='" . DASHBOARD_URL . "/statistics/referrers/" . "'><span class='label'>" . Language::$phrases['context']['more'] . "</span></a>)");
        $columns = array(
            'col_referring_sites' => Language::$phrases['statistics']['referring_sites'],
            'col_reference' => Language::$phrases['statistics']['reference'],
        );
        $row = "";
        $statistics_referrers->add_columns($columns);

        $result = $statisticsModel->getAllReferrers("referred <> ''");
        $urls = array();
        foreach ($result as $item) {
            $url = parse_url($item['referred']);
            if (empty($url['host']))
                continue;

            $urls[] = $url['host'];
        }
        $get_urls = array_count_values($urls);
        arsort($get_urls);

        //Get the records registered in the prepare_items method
        $records = array();
        $i = 1;
        foreach ($get_urls as $host => $count) {
            if (in_array($i, range(0, 10))) {
                $records[$host] = $count;
            }
            $i++;
        }

        //Loop for each record
        if (is_array($records) and !empty($records)) {
            foreach ($records as $key => $rec) {

                //Open the line
                $row .= '<tr id="row_' . $rec->ID . '">';
                foreach ($columns as $field => $col_name) {
                    $class = "class='$field column-$field' ";
                    $style = "";
                    $attributes = $class . $style;

                    $by_referrer = DASHBOARD_URL . '/statistics/referrers/?referrer=' . $key;

                    //Display the cell
                    switch ($field) {
                        case "col_referring_sites":
                            $row .= '<td ' . $attributes . '><a href="' . $by_referrer . '">' . $key . '</a></td>';
                            break;
                        case "col_reference":
                            $row .= '<td ' . $attributes . '>' . $rec . '</td>';
                            break;
                    }
                }

                //Close the line
                $row .= '</tr>';
            }
        }

        $statistics_referrers->add_rows($row);

        ## statistics top countries
        $statistics_countries = new Table(Language::$phrases['statistics']['top_countries'] . " (<a href='" . DASHBOARD_URL . "/statistics/countries/" . "'><span class='label'>" . Language::$phrases['context']['more'] . "</span></a>)");
        $columns = array(
            'col_rank' => Language::$phrases['statistics']['rank'],
            'col_flag' => Language::$phrases['statistics']['flag'],
            'col_country' => Language::$phrases['statistics']['country'],
            'col_visit_count' => Language::$phrases['statistics']['visit_count'],
        );
        $row = "";
        $statistics_countries->add_columns($columns);

        //Get the records registered in the prepare_items method
        $records = $statisticsModel->getLocations(0, 10);

        //Loop for each record
        if (is_array($records) and !empty($records)) {
            foreach ($records as $key => $rec) {

                //Open the line
                $row .= '<tr id="row_' . $rec->ID . '">';
                foreach ($columns as $field => $col_name) {
                    $class = "class='$field column-$field' ";
                    $style = "";
                    $attributes = $class . $style;

                    //Display the cell
                    switch ($field) {
                        case "col_rank":
                            $row .= '<td ' . $attributes . '>' . ($key + 1) . '</td>';
                            break;
                        case "col_flag":
                            if (empty($rec['location'])) {
                                $row .= "<td {$attributes}><img src='" . Registry::$siteurl . "/public/flags/000.png' /></td>";
                            } else {
                                $row .= "<td {$attributes}><img src='" . Registry::$siteurl . "/public/flags/{$rec['location']}.png' /></td>";
                            }
                            break;
                        case "col_country":
                            $row .= '<td ' . $attributes . '>' . $ISOCountryCode[$rec['location']] . '</td>';
                            break;
                        case "col_visit_count":
                            $row .= '<td ' . $attributes . '>' . $rec['visit'] . '</td>';
                            break;
                    }
                }

                //Close the line
                $row .= '</tr>';
            }
        }

        $statistics_countries->add_rows($row);

        ## Statistics search engine referrers
        $statistics_se_referrers = new Table(Language::$phrases['statistics']['search_engine_referrers']);
        $columns = array(
            'col_name' => '',
            'col_today' => Language::$phrases['statistics']['today'],
            'col_yesterday' => Language::$phrases['statistics']['yesterday'],
            'col_month' => Language::$phrases['statistics']['month'],
        );
        $row = "";
        $statistics_se_referrers->add_columns($columns);

        //Get the records registered in the prepare_items method
        $records = $stats->searchengine_list();
        $se_today_total = 0;
        $se_yesterday_total = 0;
        $se_month_total = 0;

        //Loop for each record
        if (is_array($records) and !empty($records)) {
            foreach ($records as $rec) {

                //Open the line
                $row .= '<tr id="row_' . $rec->ID . '">';
                foreach ($columns as $field => $col_name) {
                    $class = "class='$field column-$field' ";
                    $style = "";
                    $attributes = $class . $style;

                    //Display the cell
                    switch ($field) {
                        case "col_name":
                            $row .= "<td {$attributes}><img src='" . Registry::$siteurl . "/public/statistics/{$rec['image']}' /> " . $rec['name'] . ":</td>";
                            break;
                        case "col_today":
                            $se_temp = count($statisticsModel->searchengine($rec['tag'], 'today'));
                            $se_today_total += $se_temp;
                            $row .= '<td ' . $attributes . '><span class="label label-primary">' . $se_temp . '</span></td>';
                            break;
                        case "col_yesterday":
                            $se_temp = count($statisticsModel->searchengine($rec['tag'], 'yesterday'));
                            $se_yesterday_total += $se_temp;
                            $row .= '<td ' . $attributes . '><span class="label label-primary">' . $se_temp . '</span></td>';
                            break;
                        case "col_month":
                            $se_temp = count($statisticsModel->searchengine($rec['tag'], 'month'));
                            $se_month_total += $se_temp;
                            $row .= '<td ' . $attributes . '><span class="label label-primary">' . $se_temp . '</span></td>';
                            break;
                    }
                }

                //Close the line
                $row .= '</tr>';
            }
        }

        $row .= "<tr><td>" . Language::$phrases['statistics']['daily_total'] . ":</td>";
        $row .= "<td><span class=\"label label-success\">{$se_today_total}</span></td>";
        $row .= "<td><span class=\"label label-success\">{$se_yesterday_total}</span></td>";
        $row .= "<td><span class=\"label label-success\">{$se_month_total}</span></td>";
        $row .= "</tr>";
        $row .= "<tr><td>" . Language::$phrases['statistics']['total'] . ":</td>";
        $engine_total = count($statisticsModel->searchengine('all'));
        $row .= "<td colspan=\"2\" align=\"center\"><span class=\"label label-success\">{$engine_total}</span></td>";
        $row .= "</tr>";

        $statistics_se_referrers->add_rows($row);

        $this->render("index", array(
            'title' => $title,
            'table' => $table->createView(),
            'statistics_useronline' => $statistics_useronline->createView(),
            'statistics_referrers' => $statistics_referrers->createView(),
            'statistics_countries' => $statistics_countries->createView(),
            'statistics_visitors' => $statistics_visitors->createView(),
            'statistics_searchwords' => $statistics_searchwords->createView(),
            'statistics_se_referrers' => $statistics_se_referrers->createView(),
        ));
    }

}
