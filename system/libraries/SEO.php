<?php

class SEO {

    function __construct() {
        
    }

    /**
     * 
     * @param string $title Title of post
     * @param string $seo_title SEO title of the post
     * @return string
     */
    public static function getSeoTitle($title, $seo_title = "") {
        if (trim($seo_title) != "") {
            return $seo_title;
        }
        return $title;
    }

}
