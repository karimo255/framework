<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class SMTP_mail {

    public function __construct() {
        
    }

    public function sendMail($to_person, $body, $name) {
        require_once "Mail.php";

        $from = "Blog Blasmia  <karimo255@gmail.com>";
        $to = $name . '<' . $to_person . '>';
        $subject = "Ihre Anmeldundg 2014/2015";

        $host = "smtp.gmail.com";
        $username = "karimo255";
        $password = "*********";

        $headers = array(
            'MIME-Version' => '1.0',
            'Content-type' => 'text/html; charset=utf-8',
            'From' => $from,
            'To' => $to,
            'Subject' => $subject
        );
        $smtp = Mail::factory('smtp', array('host' => $host,
                    'auth' => true,
                    'username' => $username,
                    'password' => $password));

        $mail = $smtp->send($to_person, $headers, $body);

        if (PEAR::isError($mail)) {
            return false;
        } else {
            return true;
        }
    }
    public function news_letter_send_mail() {
            $result = $this->query->get_posts_for_news_letter();
            $top_themen_for_news_ltter = '<h3>Die Neuesten Posts für diese Woche :</h3>';
            $this->template->set_template(VIEWS . "templates/body_for_news_letter.php");
            $this->template->set_array($result);
            $top_themen_for_news_ltter .= $this->template->output();
            $emails_to = $this->query->get_all_mails_from_newsletter();
            foreach ($emails_to as $key => $value) {
                $this->sendMail($value->email, $top_themen_for_news_ltter, 'Blasmia Besucher');
            }
        }    

}
