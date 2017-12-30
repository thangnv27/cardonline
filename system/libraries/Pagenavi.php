<?php

class Pagenavi {

    /**
     *
     * @var string
     */
    private $url;

    /**
     * Page number
     * @var int
     */
    private $page;

    /**
     * Limit record for result
     * @var int
     */
    private $limit;

    public function __construct($url, $page, $limit) {
        if (strpos($url, "?page=") === FALSE) {
            if (strpos($url, "&page=") === FALSE) {
                if (strpbrk($url, "?") === FALSE) {
                    $this->url = $url . "?page=";
                } else {
                    $this->url = $url . "&page=";
                }
            } else {
                $url = explode("&page=$page", $url);
                if ($url[1] != "") {
                    $this->url = $url[0] . $url[1] . "&page=";
                } else {
                    $this->url = $url[0] . "&page=";
                }
            }
        } else {
            $url = explode("?page=$page", $url);
            if ($url[1] != "") {
                $this->url = $url[0] . "?" . substr($url[1], 1) . "&page=";
            } else {
                $this->url = $url[0] . "?page=";
            }
        }
        $this->page = (intval($page) == 0) ? 1 : intval($page);
        $this->limit = intval($limit);
    }

    public function start($limit) {
        $start = ($this->page - 1) * $limit;
        return $start;
    }

    public function pageList($count, $echo = FALSE) {
        if ($count == 0) {
            return "";
        }
        $total_page = ceil($count / $this->limit);
        $pages_to_show = 5;
        $larger_page_to_show = 3;
        $larger_page_multiple = 10;
        $pages_to_show_minus_1 = $pages_to_show - 1;
        $half_page_start = floor($pages_to_show_minus_1 / 2);
        $half_page_end = ceil($pages_to_show_minus_1 / 2);
        $start_page = $this->page - $half_page_start;
        if ($start_page <= 0)
            $start_page = 1;

        $end_page = $paged + $half_page_end;

        if (( $end_page - $start_page ) != $pages_to_show_minus_1)
            $end_page = $start_page + $pages_to_show_minus_1;

        if ($end_page > $total_page) {
            $start_page = $total_page - $pages_to_show_minus_1;
            $end_page = $total_page;
        }

        if ($start_page < 1)
            $start_page = 1;

        $out = '<ul class="pagination list-inline">';
        if ($this->page != 1 && $this->page) {
            $out .= "<li class=\"first\"><a href=\"{$this->url}1\">&laquo;</a></li>";
        } else if ($this->page == 1) {
            $out .= "<li class=\"first disabled\"><a href=\"javascript://\">&laquo;</a></li>";
        }
        if (($this->page - 1) > 0) {
            $out .= "<li class=\"previous\"><a href=\"{$this->url}" . ($this->page - 1) . "\">&lt;</a></li>";
        }
//        for ($i = 1; $i <= $total_page; $i++) {
//            if ($i == $this->page) {
//                $out .= "<span class='current'>" . $i . "</span>";
//            } else {
//                $out .= "<a href=\"{$this->url}" . $i . "\" title='Page $i'><span class='page'> $i </span></a>";
//            }
//            $out .= " ";
//        }
        #############
        if ($start_page >= 2 && $pages_to_show < $total_page)
            $out .= "<li class=\"disabled extend\"><a href=\"javascript://\">...</a></li>";

        // Smaller pages
        $larger_pages_array = array();
        if ($larger_page_multiple) {
            for ($i = $larger_page_multiple; $i <= $total_page; $i+= $larger_page_multiple) {
                $larger_pages_array[] = $i;
            }
        }

        $larger_page_start = 0;
        foreach ($larger_pages_array as $larger_page) {
            if ($larger_page < ($start_page - $half_page_start) && $larger_page_start < $larger_page_to_show) {
                $out .= "<li class=\"smaller page\"><a href=\"{$this->url}" . $larger_page . "\">{$larger_page}</a></li>";
                $larger_page_start++;
            }
        }

        if ($larger_page_start)
            $out .= "<li class=\"disabled extend\"><a href=\"javascript://\">...</a></li>";

        // Page numbers
        foreach (range($start_page, $end_page) as $i) {
            if ($i == $this->page) {
                $out .= "<li class=\"active\"><a href=\"javascript://\">{$i}</a></li>";
            } else {
                $out .= "<li class=\"smaller page\"><a href=\"{$this->url}" . $i . "\">{$i}</a></li>";
            }
        }

        // Large pages
        $larger_page_end = 0;
        $larger_page_out = '';
        foreach ($larger_pages_array as $larger_page) {
            if ($larger_page > ($end_page + $half_page_end) && $larger_page_end < $larger_page_to_show) {
                $larger_page_out .= "<li class=\"larger page\"><a href=\"{$this->url}" . $larger_page . "\">{$larger_page}</a></li>";
                $larger_page_end++;
            }
        }

        if ($larger_page_out) {
            $out .= "<li class=\"disabled extend\"><a href=\"javascript://\">...</a></li>";
        }
        $out .= $larger_page_out;

        if ($end_page < $total_page) {
            $out .= "<li class=\"disabled extend\"><a href=\"javascript://\">...</a></li>";
        }
        #############

        if (($this->page + 1) < $total_page) {
            $out .= "<li class=\"next\"><a href=\"{$this->url}" . ($this->page + 1) . "\">&gt;</a></li>";
        }
        if ($this->page != $total_page && $this->page != 0) {
            $out .= "<li class=\"last\"><a href=\"{$this->url}{$total_page}\">&raquo;</a></li>";
        } else {
            $out .= "<li class=\"last disabled\"><a href=\"javascript://\">&raquo;</a></li>";
        }
        $out .= "</ul>";

        if ($echo)
            echo $out;

        return $out;
    }

}
