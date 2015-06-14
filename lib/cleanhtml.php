<?php
class cleanHtml{
    public function crl_remove_empty_tags($string, $replaceTo = null) {
    // Return if string not given or empty
    if (!is_string($string) || trim($string) == '')
        return $string;

    // Recursive empty HTML tags
    return preg_replace(
            '/<(\w+)\b(?:\s+[\w\-.:]+(?:\s*=\s*(?:"[^"]*"|"[^"]*"|[\w\-.:]+))?)*\s*\/?>\s*<\/\1\s*>/', !is_string($replaceTo) ? '' : $replaceTo, $string
    );
}
}