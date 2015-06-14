<?php

final class Security {

    public function clean($input) {

        if (is_array($input)) {

            foreach ($input as $key => $value) {
                unset($input[$key]);
                $input[$this->clean($key)] = $this->clean($value);
            }
            return $input;
        } else {
            $input = trim($input);            
            $input = strip_tags($input);
            $input = htmlspecialchars($input,  ENT_COMPAT, 'UTF-8');
            $input = stripslashes($input);
            $input = htmlentities($input);
            return self::xss_clean($input);
        }
    }

    public static function xss_clean($value, array $options = array()) {
        if (!is_array($value)) {
            if (!function_exists('htmLawed')) {
                require_once TOOLS .'htmlawed.php';
            }

            return htmLawed($value, array_merge(array('safe' => 1, 'balanced' => 0), $options));
        }

        foreach ($value as $k => $v) {
            $value[$k] = static::xss_clean($v);
        }

        return $value;
    }

}

?>