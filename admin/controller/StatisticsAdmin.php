<?php

class StatisticsAdmin extends AdminController {

    private $current_user;

    function __construct() {
        parent::__construct();
        $this->current_user = UserAdmin::checkLogin();
    }

    function useronline() {
        $title = Language::$phrases['statistics']['useronline'];
        $table = new Table($title);
        $columns = array(
            'col_ip' => 'IP',
            'col_referred' => Language::$phrases['statistics']['referred'],
            'col_url' => Language::$phrases['statistics']['url'],
            'col_browser' => Language::$phrases['statistics']['browser'],
            'col_platform' => Language::$phrases['statistics']['platform'],
        );
        $row = "";
        $table->add_columns($columns);

        $request = $this->getRequest();
        $search_query = $request->get('s');
        $agent = $request->get('agent');
        $where = array();
        if (!empty($search_query)) {
            $where = "ip LIKE '%$search_query%' OR date LIKE '%$search_query%' OR "
                    . "referred LIKE '%$search_query%' OR url LIKE '%$search_query%' OR "
                    . "agent LIKE '%$search_query%' OR platform LIKE '%$search_query%'";
        }
        if (!empty($agent)) {
            $where = "agent = '$agent'";
        }

        // Pagination
        $currentURL = trailingslashit($request->getCurrentRquestUrl());
        if (count($request->all()) > 0) {
            $currentURL = $request->getCurrentRquestUrl();
        }
        $limit = 50;
        $pager = new Pagenavi($currentURL, $request->get('page'), $limit);
        $start = $pager->start($limit);
        $countRecords = $this->model->useronline($where);
        $table->add_pagenavi($pager->pageList($countRecords));

        //Get the records registered in the prepare_items method
        $records = $this->model->getUseronline($start, $limit, $where);

        //Loop for each record
        if (is_array($records) and !empty($records)) {
            $stats = new Statistics();

            foreach ($records as $rec) {

                //Open the line
                $row .= '<tr id="row_' . $rec->ID . '">';
                foreach ($columns as $field => $col_name) {
                    $class = "class='$field column-$field' ";
                    $style = "";
                    $attributes = $class . $style;
                    $check_ads = $stats->check_adwords($rec['referred']);

                    $by_ip = DASHBOARD_URL . '/statistics/recent_visitors/?ip=' . $rec['ip'];
                    $by_agent = DASHBOARD_URL . '/statistics/useronline/?agent=' . $rec['agent'];

                    //Display the cell
                    switch ($field) {
                        case "col_ip":
                            $row .= '<td ' . $attributes . '>';
                            $row .= '<a href="http://www.geoiptool.com/en/?IP=' . $rec['ip'] . '" target="_blank" title="Lookup IP: ' . $rec['ip'] . '">';
                            $row .= "<img src='" . Registry::$siteurl . "/public/statistics/visibility.png' /></a>";
                            $row .= '&nbsp;<a href="' . $by_ip . '">' . $rec['ip'] . "</a>";
                            $row .= '</td>';
                            break;
                        case "col_url":
                            $row .= '<td ' . $attributes . '><a href="' . $rec['url'] . '" title="' . $rec['url'] . '">' . substr($rec['url'], 0, 50) . '[...]</a></td>';
                            break;
                        case "col_referred":
                            $row .= '<td ' . $attributes . '>';
                            if ($check_ads) {
                                $row .= '<a href="' . $rec['referred'] . '" title="' . $rec['referred'] . '" onclick="return confirm(\'' . Language::$phrases['statistics']['ad.click.confirm'] . '\');">';
                            } else {
                                $row .= '<a href="' . $rec['referred'] . '" title="' . $rec['referred'] . '">';
                            }
                            $row .= substr($rec['referred'], 0, 50) . '[...]</a></td>';
                            break;
                        case "col_browser":
                            $row .= '<td ' . $attributes . '><a href="' . $by_agent . '">';
                            if (array_search(strtolower($rec['agent']), array("chrome", "firefox", "msie", "opera", "safari", "iemobile")) !== FALSE) {
                                $row .= "<img src='" . Registry::$siteurl . "/public/statistics/" . $rec['agent'] . ".png' title='{$rec['agent']} {$rec['version']}'/>";
                            } else {
                                $row .= "<img src='" . Registry::$siteurl . "/public/statistics/unknown.png' title='{$rec['agent']}'/>";
                            }
                            $row .= '</a></td>';
                            break;
                        case "col_platform":
                            $row .= '<td ' . $attributes . ' title="' . $rec['platform'] . '">';
                            if (file_exists(PUBLIC_PATH . "statistics" . DS . $rec['platform'] . ".png")) {
                                $row .= '<img src="' . Registry::$siteurl . '/public/statistics/' . $rec['platform'] . '.png" />';
                            } elseif (strpos($rec['platform'], "compatible;") !== FALSE) {
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
        }

        $table->add_rows($row);

        $this->render("statistics/useronline", array(
            'title' => $title,
            'table' => $table->createView(),
            'all' => $this->model->useronline(),
        ));
    }

    function recent_visitors() {
        $ISOCountryCode = Registry::$settings['ISO_COUNTRY_CODE'];

        $title = Language::$phrases['statistics']['recent_visitors'];
        $table = new Table($title);
        $columns = array(
            'col_date' => Language::$phrases['statistics']['date'],
            'col_ip' => 'IP',
            'col_referred' => Language::$phrases['statistics']['referred'],
            'col_platform' => Language::$phrases['statistics']['platform'],
        );
        $row = "";
        $table->add_columns($columns);

        $request = $this->getRequest();
        $search_query = $request->get('s');
        $agent = $request->get('agent');
        $location = $request->get('location');
        $ip = $request->get('ip');
        $where = array();
        if (!empty($search_query)) {
            $where = "ip LIKE '%$search_query%' OR last_counter LIKE '%$search_query%' OR "
                    . "referred LIKE '%$search_query%' OR location LIKE '%$search_query%' OR "
                    . "agent LIKE '%$search_query%' OR platform LIKE '%$search_query%'";
        }
        if (!empty($agent)) {
            $where = "agent = '$agent'";
        }
        if (!empty($location)) {
            $where = "location = '$location'";
        }
        if (!empty($ip)) {
            $where = "ip = '$ip'";
        }

        // Pagination
        $currentURL = trailingslashit($request->getCurrentRquestUrl());
        if (count($request->all()) > 0) {
            $currentURL = $request->getCurrentRquestUrl();
        }
        $limit = 100;
        $pager = new Pagenavi($currentURL, $request->get('page'), $limit);
        $start = $pager->start($limit);
        $countRecords = $this->model->countRecentVisitors($where);
        $table->add_pagenavi($pager->pageList($countRecords));

        //Get the records registered in the prepare_items method
        $records = $this->model->recentVisitors($start, $limit, $where);

        $table->caption = $title . ": " . $countRecords;

        //Loop for each record
        if (is_array($records) and !empty($records)) {
            $stats = new Statistics();

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
                            if (empty($ip)) {
                                $row .= '&nbsp;<a href="' . $by_ip . '">' . $rec['ip'] . "</a>";
                            } else {
                                $row .= '&nbsp;' . $rec['ip'];
                            }
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
        }

        $table->add_rows($row);

        $this->render("statistics/recent_visitors", array(
            'title' => $title,
            'table' => $table->createView(),
            'all' => $this->model->visitor('total', null, true),
        ));
    }

    function search_words() {
        $ISOCountryCode = Registry::$settings['ISO_COUNTRY_CODE'];

        $stats = new Statistics();
        $title = Language::$phrases['statistics']['search_words'];
        $table = new Table($title);
        $columns = array(
            'col_date' => Language::$phrases['statistics']['date'],
            'col_ip' => 'IP',
            'col_referred' => Language::$phrases['statistics']['referred'],
            'col_keyword' => Language::$phrases['statistics']['keyword'],
            'col_adwords' => Language::$phrases['statistics']['adwords'],
            'col_platform' => Language::$phrases['statistics']['platform'],
        );
        $row = "";
        $table->add_columns($columns);

        $request = $this->getRequest();
        $referred = $request->get('referred');
        $search_query = $request->get('s');
        $where = "";
        if (!empty($search_query)) {
            $where = "(ip LIKE '%$search_query%' OR last_counter LIKE '%$search_query%' OR "
                    . "location LIKE '%$search_query%' OR agent LIKE '%$search_query%' OR "
                    . "platform LIKE '%$search_query%')";
        }
        if (!empty($referred)) {
            if (empty($where)) {
                $where = $stats->searchword_query($referred);
            } else {
                $where .= " AND " . $stats->searchword_query($referred);
            }
        } else {
            if (empty($where)) {
                $where = $stats->searchword_query('all');
            } else {
                $where .= " AND " . $stats->searchword_query('all');
            }
        }

        // Pagination
        $currentURL = trailingslashit($request->getCurrentRquestUrl());
        if (count($request->all()) > 0) {
            $currentURL = $request->getCurrentRquestUrl();
        }
        $limit = 50;
        $pager = new Pagenavi($currentURL, $request->get('page'), $limit);
        $start = $pager->start($limit);
        $countRecords = $this->model->countSearchWords($where);
        $table->add_pagenavi($pager->pageList($countRecords));

        //Get the records registered in the prepare_items method
        $records = $this->model->searchWords($start, $limit, $where);

        //Loop for each record
        if (is_array($records) and !empty($records)) {
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
                            if ($stats->check_adwords($rec['referred'])) {
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
        }

        $table->add_rows($row);

        $this->render("statistics/search_words", array(
            'title' => $title,
            'table' => $table->createView(),
        ));
    }

    function referrers() {
        $title = Language::$phrases['statistics']['referrers'];
        $request = $this->getRequest();
        $referrer = $request->get('referrer');
        $search_query = $request->get('s');
        $currentURL = trailingslashit($request->getCurrentRquestUrl());
        if (count($request->all()) > 0) {
            $currentURL = $request->getCurrentRquestUrl();
        }
        $where = array();
        if (!empty($search_query)) {
            $where = "referred LIKE '%$search_query%' AND referred <> ''";
        } else {
            $where = "referred <> ''";
        }

        if (!empty($referrer)) {
            $table = new Table();
            $columns = array(
                'col_date' => Language::$phrases['statistics']['date'],
                'col_ip' => 'IP',
                'col_referred' => Language::$phrases['statistics']['referred'],
                'col_platform' => Language::$phrases['statistics']['platform'],
            );
            $row = "";
            $table->add_columns($columns);

            $where = "referred LIKE '%$referrer%' AND referred <> ''";

            // Pagination
            $limit = 100;
            $pager = new Pagenavi($currentURL, $request->get('page'), $limit);
            $start = $pager->start($limit);
            $countRecords = $this->model->countRecentVisitors($where);
            $table->add_pagenavi($pager->pageList($countRecords));

            //Get the records registered in the prepare_items method
            $records = $this->model->recentVisitors($start, $limit, $where);

            $table->caption = $title . ": " . $referrer . " (" . $countRecords . ")";

            //Loop for each record
            if (is_array($records) and !empty($records)) {
                $stats = new Statistics();

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
            }

            $table->add_rows($row);
        } else {
            $table = new Table($title);
            $columns = array(
                'col_referring_sites' => Language::$phrases['statistics']['referring_sites'],
                'col_referring_url' => Language::$phrases['statistics']['referring_url'],
                'col_reference' => Language::$phrases['statistics']['reference'],
            );
            $row = "";
            $table->add_columns($columns);

            // Pagination
            $result = $this->model->getAllReferrers($where);
            $urls = array();
            foreach ($result as $item) {
                $url = parse_url($item['referred']);
                if (empty($url['host']))
                    continue;

                $urls[] = $url['host'];
            }
            $get_urls = array_count_values($urls);
            arsort($get_urls);

            $limit = 100;
            $pager = new Pagenavi($currentURL, $request->get('page'), $limit);
            $start = $pager->start($limit);
            $countRecords = count($get_urls);
            $table->add_pagenavi($pager->pageList($countRecords));

            //Get the records registered in the prepare_items method
            $records = array();
            $i = 1;
            foreach ($get_urls as $host => $count) {
                if (in_array($i, range($start, $start + $limit))) {
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
                            case "col_referring_url":
                                $row .= '<td ' . $attributes . '><a href="http://' . $key . '/" target="_blank">http://' . $key . '/</a></td>';
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

            $table->add_rows($row);
        }


        $this->render("statistics/referrers", array(
            'title' => $title,
            'table' => $table->createView(),
            'all' => $this->model->countReferrers("referred <> ''"),
        ));
    }

    function countries() {
        $ISOCountryCode = Registry::$settings['ISO_COUNTRY_CODE'];

        $title = Language::$phrases['statistics']['top_countries'];
        $table = new Table($title);
        $columns = array(
            'col_rank' => Language::$phrases['statistics']['rank'],
            'col_flag' => Language::$phrases['statistics']['flag'],
            'col_country' => Language::$phrases['statistics']['country'],
            'col_visit_count' => Language::$phrases['statistics']['visit_count'],
        );
        $row = "";
        $table->add_columns($columns);

        $request = $this->getRequest();
        $search_query = $request->get('s');
        $where = array();
        if (!empty($search_query)) {
            $where = "location LIKE '%$search_query%'";
        }

        // Pagination
        $currentURL = trailingslashit($request->getCurrentRquestUrl());
        if (count($request->all()) > 0) {
            $currentURL = $request->getCurrentRquestUrl();
        }
        $limit = 100;
        $pager = new Pagenavi($currentURL, $request->get('page'), $limit);
        $start = $pager->start($limit);
        $countRecords = $this->model->countLocations($where);
        $table->add_pagenavi($pager->pageList($countRecords));

        //Get the records registered in the prepare_items method
        $records = $this->model->getLocations($start, $limit, $where);

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

        $table->add_rows($row);

        $this->render("statistics/countries", array(
            'title' => $title,
            'table' => $table->createView(),
        ));
    }

    function hits() {
        $title = Language::$phrases['statistics']['hits_statistics'];

        $this->render("statistics/hits", array(
            'title' => $title,
        ));
    }

    function searches() {
        $title = Language::$phrases['statistics']['searches_statistics'];

        $this->render("statistics/searches", array(
            'title' => $title,
        ));
    }

    function browsers() {
        $title = Language::$phrases['statistics']['browsers'];

        $this->render("statistics/browsers", array(
            'title' => $title,
        ));
    }

}
