<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Language management class
 *
 * @author Karim
 */
class Lang {

    /**
     * saves ini section that is used
     *
     */
    var $section = "";

    /**
     * saves current language
     *
     */
    var $lang = "";

    /**
     * where do i find the language inis
     *
     */
    var $path = "lang/";

    /**
     * parsed ini array
     *
     */
    var $parsed = array();

    /**
     * setup the class
     *
     */
    function __construct($section = "", $path = "") {

        $this->section = $section;
        $this->lang = $this->autoDetectSprache();
        if (!empty($path)) {
            $this->path = $path;
        }

        $this->parse();
    }

    /**
     * parse the language file
     *
     */
    function parse() {
        $filename = $this->path . $this->lang . ".ini";
        $cachedata = $this->path . $this->lang . ".cachedata";
        $cachearray = $this->path . $this->lang . ".cachearray";
        if (!file_exists($filename)) {
            return;
        }

        // caching system
        $ini_size = filesize($filename);

        if (file_exists($cachedata) && file_exists($cachearray)) {
            $cachesize = implode('', file($cachedata));

            if ($ini_size != $cachesize) { // reparse
                $this->reparse($filename);
            } else { // load from cache
                $serialized = base64_decode(implode('', file($cachearray)));
                $this->parsed = unserialize($serialized);
            }
        } else { // reparse
            $this->reparse($filename);
        }
    }

    /**
     * parse ini file and write cache
     *
     */
    function reparse($fname) {
        $this->parsed = parse_ini_file($fname, true);
        $ini_size = filesize($fname);

        $fp = @fopen($this->path . $this->lang . ".cachedata", "w+");
        @fwrite($fp, $ini_size);
        @fclose($fp);

        $fp = @fopen($this->path . $this->lang . ".cachearray", "w+");
        @fwrite($fp, base64_encode(serialize($this->parsed)));
        @fclose($fp);
    }

    function autoDetectSprache() {
        $vorhandeneSprachen = array('de', 'ar');
        if (isset($_GET['sprache'])) {
            $_SESSION['sprache'] = $_GET['sprache'];
            return $_GET['sprache'];
        } elseif (isset($_SESSION['sprache'])) {
            return $_SESSION['sprache'];
        } else {
            if (in_array(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2), $vorhandeneSprachen)) {
                return substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            } else {
                return 'de';
            }
        }
    }

    /**
     * grab translation
     *
     */
    function get($varname) {
        if (!isset($this->parsed[$this->section][$varname])) {
            die("Lang Error2: $this->section[$varname] not found!");
        }
        return $this->parsed[$this->section][$varname];
    }

    function getSection($section) {
        if (isset($this->parsed[$section])) {
        return $this->parsed[$section];
        }
    }
    function getLang() {
        return $this->lang;
    }    

    /**
     * grab translation out of specified section
     *
     */
    function grab($section, $varname) {
        if (!isset($this->parsed[$section][$varname])) {
            die("Lang Error: $section[$varname] not found!");
        }

        return $this->parsed[$section][$varname];
    }

}

?>