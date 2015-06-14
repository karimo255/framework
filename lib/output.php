<?php

/**
 * @version    0.1
 * @package    PHPClasses
 * @copyright  Copyright (C) 2014 Karim Echchennouf. All rights reserved.
 * @license    Free
 *
 */
class Output {

    /**
     * All notices & errors.
     *
     * @access private
     * @var string
     */
    private $output ;
    public function __construct() {
        $this->output = '<section><ul>'; 
    }

    public function addMessage($message, $status) {
        if ($status) {
            $color = 'green';
        } else {
            $color = 'red';
        }
        $this->output .= '<li style="color:' . $color . ';">' . $message . '</li>';
    }

    
    public function __toString() {
        return $this->output . '</ul></section>';
    }

}

?>