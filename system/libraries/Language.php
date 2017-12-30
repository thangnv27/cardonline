<?php

class Language {

    public static $phrases;
    public static $lang_code;
    public static $lang_content;
    private $expiration;

    public function __construct() {
        $this->expiration = time() + 3600 * 24 * 365;
        self::$lang_code = $this->getLang();
        $lang_actived = LanguageAdminModel::getLangActived();
        self::$lang_content = ($lang_actived['code'] != NULL and $lang_actived['code'] != "") ? $lang_actived['code'] : DEFAULT_LANG;
    }

    /**
     * Set default language code
     * @param string $lang_code
     */
    public function setLang($lang_code) {
        setcookie('lang', $lang_code, $this->expiration, '/');
    }

    /**
     * Get default language code
     * @return string
     */
    public function getLang() {
        //determine page language
        $lang_code = isset($_COOKIE['lang']) ? $_COOKIE['lang'] : DEFAULT_LANG;

        //set the language cookie and update cookie expiration date
        if (!isset($_COOKIE['lang'])) {
            $this->setLang($lang_code);
        }

        return $lang_code;
    }

    public function loadPhrases($lang_file) {
        $xml = new DomDocument('1.0');

        //path to language directory
        $lang_path = (LANG_PATH . $lang_file . '.xml');
        $xml->load($lang_path);

        $rootNode = $xml->documentElement;
        $childNodes = $rootNode->childNodes;
        for ($i = 0; $i < $childNodes->length; $i++) {
            if ($childNodes->item($i)->nodeType == XML_ELEMENT_NODE) {
                if ($childNodes->item($i)->hasChildNodes()) {
                    $nodes = $childNodes->item($i)->childNodes;
                    for ($j = 0; $j < $nodes->length; $j++) {
                        if ($nodes->item($j)->nodeType == XML_ELEMENT_NODE) {
                            $attr = array();
                            foreach ($nodes->item($j)->attributes as $attributeName => $attributeNode) {
                                $attributeName = $attributeNode->nodeName;
                                $attr[$attributeName] = (string) $attributeNode->value;
                            }
                            if ($childNodes->item($i)->hasAttributes()) {
                                $attr2 = array();
                                foreach ($childNodes->item($i)->attributes as $attributeName => $attributeNode) {
                                    $attributeName = $attributeNode->nodeName;
                                    $attr2[$attributeName] = (string) $attributeNode->value;
                                }
                                //self::$phrases[$childNodes->item($i)->nodeName][$attr2['name']][$nodes->item($j)->nodeName][$attr['name']] = $nodes->item($j)->nodeValue;
                                self::$phrases[$childNodes->item($i)->nodeName][$attr2['name']][$attr['name']] = $nodes->item($j)->nodeValue;
                            } else {
                                //self::$phrases[$childNodes->item($i)->nodeName][$nodes->item($j)->nodeName][$attr['name']] = $nodes->item($j)->nodeValue;
                                self::$phrases[$childNodes->item($i)->nodeName][$attr['name']] = $nodes->item($j)->nodeValue;
                            }
                        }
                    }
                }
            }
        }
        return self::$phrases;
    }

}
