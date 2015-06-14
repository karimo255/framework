<?php

class Token {

    
    
    public static function generate($form) {
        $_SESSION[$form.'token'] = base64_encode(openssl_random_pseudo_bytes(32));
        return $_SESSION[$form.'token'];
    }

    public static function check($form,$token) {
        if (isset($_SESSION[$form.'token']) && $token === $_SESSION[$form.'token']) {
            unset($_SESSION[$form.'token']);
            return true;
        }
        return false;        
    }



}
