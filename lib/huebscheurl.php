<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of huebscheURL
 *
 * @author karim
 */
class huebscheURL {
    private function ersetzeUmlaute($url) {
          $from = Array("ä", "ü", "ö", "Ä", "Ü", "Ö"); 
          $to =   Array("ae", "ue", "oe","Ae","Ue","Oe");  
          return str_replace($from, $to, $url);
    }
    public function verzierre($url) {
        $url = $this->ersetzeUmlaute($url);
        // replace non letter or digits by -
        $url = preg_replace('~[^\\pL\d]+~u', '-', $url);

        // trim
        $url = trim($url, '-');

        // transliterate
        $url = iconv('utf-8', 'us-ascii//TRANSLIT', $url);

        // lowercase
        $url = strtolower($url);

        // remove unwanted characters
        $url = preg_replace('~[^-\w]+~', '', $url);

        if (empty($url)) {
            return 'n-a';
        }

        return $url;
    }

}
