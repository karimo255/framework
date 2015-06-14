<?php

if (!isset($_SESSION)) {
    session_start();
}
?>
<?php

if (!class_exists('Login_mod')) {

    class Login_mod {

        //input
        private $email;
        private $pass;
        private $alt_pass;
        private $newPass;
        private $adresse;
        private $mobile;
        //db
        private $dbEmail;
        private $dbPass;
        private $berechtigung;
        //weitere
        private $db;
        private $encrypt = true;
        public $isLoggedIn = false;
        private $isSetCockie;
        private $checkStat;
        private $remember_checkbox;
        private $session_time;
        private $cockies_time;
        private $login_view;

        public function __construct($postData = '') {
            if (!empty($postData) && count($postData) !== 0) {
                foreach ($postData as $key => $value) {
                    $this->$key = $value;
                }
            }
            $this->login_view = new Login_view();
            $this->db = DB::getInstance();

			
        $daten = parse_ini_file(CONFIG . 'config.ini', true);

        $active_session = $daten['Settings']['active_session'];
        $cockies_zeit = $daten['Settings']['cockies_zeit'];
        $datenbank = $daten['DabaBase']['DB'];			
			
            $this->db->select_db($datenbank);
            $this->session_time = active_session * 60;
            $this->cockies_time = cockies_zeit * (24 * 60 * 60);
            $this->ini_login();
            $this->berechtigung = $this->getBerechtigung();
        }

        public function getBerechtigung() {
            $sql = "SELECT `user_grouppe`.`berechtigung` , `users`.`name` , `users`.`email`
FROM user_grouppe
LEFT JOIN `Moschee`.`users` ON `user_grouppe`.`id` = `users`.`Gruppe`
                WHERE `users`.`email` = '$this->email' ";
            if ($this->isLoggedIn()) {
                $userEmail = $_SESSION['email'];
                $where = " OR `users`.`email` = '$userEmail'";
                $sql = $sql . $where;
            }
            return $this->db->query($sql)->row['berechtigung'];
        }

        public function ini_login() {
            if (isset($_COOKIE['cookname']) && !empty($_COOKIE['cookname'])) {
                $this->isSetCockie = true;
                $this->logMeIn(true);
                $this->user = $_COOKIE['cookname'];
            } else {
                $this->isSetCockie = false;
            }
            if (isset($_SESSION['timeout']) && $_SESSION['timeout'] + $this->session_time < time()) {
                $this->logMeOut();
                $_SESSION['timeout'] = NULL;
                return false;
            }
            if (isset($_SESSION['login']) && $_SESSION['login'] == true) {
                $this->isLoggedIn = true;
            }
            $_SESSION['timeout'] = time();
        }

        public function isSetCockie() {
            $this->ini_login();
            return $this->isSetCockie;
        }

        public function isLoggedIn() {
            $this->ini_login();
            return $this->isLoggedIn;
        }

        public function check_pass_regex() {
            $check = preg_match('/(?=.*\d)/', $this->pass);
            $richtig = 0;
            $msg = 0;
            if (!$check) {
                $msg = 20;
                $richtig += 1;
            }
            $check = preg_match('/(?=.*[a-z])/', $this->pass);
            if (!$check) {
                $msg = 21;
                $richtig += 1;
            }
            $check = preg_match('/(?=.*[A-Z])/', $this->pass);
            if (!$check) {
                $msg = 22;
                $richtig += 1;
            }
            if (strlen($this->pass) < 6) {
                $msg = 23;
                $richtig += 1;
            }
            return array($msg, $richtig);
        }

        public function SetCockie_state($cockies_state) {
            $this->isSetCockie = $cockies_state;
        }

        public function LoggedThemIn($session_state) {
            $this->isLoggedIn = $session_state;
        }

        private function isKontoAktiv($email) {
            $was = array('verified', 'verification_code');
            $wer = array('email' => $this->email);
            $table = 'users';
            $result = $this->db->select($table, $was, $wer);
            $result = $result->row;
            if ($result['verified'] == 0) {
                $this->login_view->set_verification_code($result['verification_code']);
                $this->login_view->loginErrors(24);
                exit();
            }
        }

        public function logMeIn($withCockie = false) {
            $check = $this->getDataAndCheckCount();
            if ($check) {
                $richtig = $this->checkUserAndPass($withCockie);
                if ($richtig) {
                    $this->isKontoAktiv($this->email);
                    $this->setSession($withCockie);
                    $errorNummer = 1;
                    $this->setCockie();
                    $return = true;
                } else {
                    $errorNummer = 2;
                    $return = false;
                }
            } else {
                $errorNummer = 2;
                $return = false;
            }
            $this->login_view->loginErrors($errorNummer);
            return $return;
        }

        private function checkUserAndPass($withCockie) {
            if (!$withCockie) {
                $encrypted = md5($this->pass);
                return ($this->email == $this->dbEmail && $encrypted == $this->dbPass);
            } else {
                $encrypted = md5($_COOKIE['cookpass']);
                return ($_COOKIE['cookemail'] == $this->dbEmail && $encrypted == $this->dbPass);
            }
        }

        private function getDataAndCheckCount() {
            $was = array('email', 'password');
            $wer = array('email' => $this->email);
            $table = 'users';
            $result = $this->db->select($table, $was, $wer);
            $count = $result->num_rows;
            if ($count > 0) {
                $this->dbEmail = $result->row['email'];
                $this->dbPass = $result->row['password'];
                $this->checkBerechtigung(2);
                return true;
            } else {
                return false;
            }
        }

        public function check_code() {
            if (md5($this->code) == $this->vcode) {
                $this->enable_acount();
            }
        }

        private function enable_acount() {
            $was = array('verified');
            $wer = array('verification_code' => $this->vcode);
            $table = 'users';
            $result = $this->db->select($table, $was, $wer);
            $result = $result->row;
            if ($result->verified == 1) {
                echo '<p style="color:blue;">Konto ist schon aktiv</p>';
                echo '<p><a href="/user/LogMeIn">Anmelden</a></p>';
                return;
            }
            $sql = "UPDATE `eco`.`users`  SET `verified` = '1'  WHERE `verification_code` = '$this->vcode' ";
            $boolean = $this->db->query($sql);
            if ($boolean) {
                echo '<p style="color:blue;">Konto ist jetzt aktiv</p>';
                echo '<p><a href="/user/LogMeIn">Anmelden</a></p>';
                $text .= '<script>
                       $(document).ready(function(){
                            $(".reset_pass").remove();
                        });    
                            </script>';
                echo $text;
            }
        }

        public function registerMe() {

            $check = $this->getDataAndCheckCount();
            if (!$check) {
                $check = $this->checkUserAndRePass();
                if ($check) {
                    if ($this->encrypt == true) {
                        $encrypted = md5($this->pass);
                    }
                    if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                        $this->login_view->loginErrors(12);
                        exit();
                    }
                    $code = $this->createPassword();
                    $verified_code = md5($code);
                    $sql = "
            INSERT INTO `eco`.`users` (`name` ,`vorname`,`password`,`email`,`verification_code`,`telefon`,`mobile`,`geschlecht`,`Gruppe`) 
            VALUES ('$this->name', '$this->vorname', '$encrypted','$this->email','$verified_code','$this->tel','$this->mobile','$this->sex','1');
            ";
                    $result = $this->db->query($sql);
                    $last_inserd_id = $this->db->getLastId();
                    $sql = "INSERT INTO `eco`.`adress` (`strasse` ,`ort`,`plz`,`userID`) VALUES ('$this->adresse','$this->ort','$this->plz','$last_inserd_id') ";
                    $this->db->query($sql);

                    if ($result) {
                        $errorNummer = 3;
                        $body = '<p>' . $code . '</p>';
                        $mail = new SMTP_mail();
                        $mail->sendMail($this->email, $body, $this->user);
                    } else {
                        
                    }
                } else {
                    $errorNummer = 7;
                }
            } else {
                $errorNummer = 6;
            }
            $this->login_view->loginErrors($errorNummer);
        }

        private function checkUserAndRePass() {
            $res = $this->check_pass_regex();
            if ($res[1] > 0) {
                $this->login_view->loginErrors($res[0]);
                exit();
            }
            if ($this->pass == $this->rePass) {
                return true;
            } else {
                return false;
            }
        }

        private function createPassword() {
            $chars = "abcdefghijkmnopqrstuvwxyz023456789";
            srand((double) microtime() * 1000000);
            $i = 0;
            $pass = '';
            while ($i <= 7) {
                $num = rand() % 33;
                $tmp = substr($chars, $num, 1);
                $pass = $pass . $tmp;
                $i++;
            }
            return $pass;
        }

        private function checkRemember() {
            return ($this->remember_checkbox == 'true');
        }

        private function setSession($withCockie) {
            if (!$withCockie) {
                $_SESSION['login'] = true;
                $_SESSION['email'] = $this->email;
                $_SESSION['pass'] = $this->pass;
            } else {
                $_SESSION['login'] = true;
                $_SESSION['email'] = $_COOKIE['cookemail'];
                $_SESSION['name'] = $_COOKIE['cookname'];
                $_SESSION['pass'] = $_COOKIE['cookpass'];
            }
        }

        private function setCockie() {
            if (!$this->checkRemember()) {
                return;
            }
            setcookie("cookemail", $this->user, time() + 2592000, "/");
            setcookie("cookname", $this->user, time() + 2592000, "/");
            setcookie("cookpass", $this->pass, time() + 2592000, "/");
        }

        public function logMeOut() {
            $_SESSION = array();
            setcookie("cookname", NULL, time() - 2592000, "/");
            setcookie("cookpass", NULL, time() - 2592000, "/");
            setcookie("cookemail", NULL, time() - 2592000, "/");
            unset($_COOKIE['cookname']);
            unset($_COOKIE['cookpass']);
            unset($_COOKIE['cookemail']);

            $this->isSetCockie = false;
            $this->isLoggedIn = false;
            $this->user = '';
        }

        public function passResert() {
            if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                $this->login_view->loginErrors(12);
                exit();
            }
            $sql = "SELECT * FROM `eco`.`users`  WHERE `email`='$this->email' LIMIT 1";
            $result = $this->db->query($sql);
            if ($result->num_rows > 0) {
                $this->dbUser = $result->row['name'];
            } else {
                $this->login_view->loginErrors(10);
                exit();
            }
            $this->newPass = $this->createPassword();
            $copieForMail = $this->newPass;
            if ($this->encrypt == true) {
                $this->newPass = md5($this->newPass);
            }
            $mail = new SMTP_mail();
            $isEmailSend = $mail->sendMail($this->email, $copieForMail, $this->dbUser);
            if ($isEmailSend) {
                $this->login_view->loginErrors(9, $this->email);
            } else {
                $this->login_view->loginErrors();
            }
            $this->updatePass();
        }

        private function updatePass() {
            $sql = "UPDATE `Posts`.`BlogLogin` SET `dbPass`='$newPass' WHERE `mail`='$email' ";
            $result = $this->db->query($sql);
            if ($result) {
                
            }
        }

        private function update_pass_with_user_() {
            $this->pass = md5($this->pass);
            if ($this->query->update_pass_with_user_($this->pass, $this->user)) {
                return true;
            }
        }

        public function redirect() {
            $self = $_SERVER['PHP_SELF'];
            header('Location:' . $self);
        }

        public function insert_email_in_news_letter($email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->login_view->loginErrors(13);
                exit();
            }
            return $this->query->insert_into_newsletter($email);
        }

        public function update_pass() {
            $this->user = $_SESSION['name'];
            $this->getDataAndCheckCount();

            $check = $this->isLoggedIn;
            if (!$check || !$this->checkStat) {
                $this->login_view->loginErrors(14);
                return;
            }

            if (md5($this->alt_pass) != $this->dbPass) {
                $this->login_view->loginErrors(15);
                return;
            }
            if ($this->pass != $this->rePass) {
                $this->login_view->loginErrors(16);
                return;
            }
            if ($this->update_pass_with_user_()) {
                $this->logMeOut();
                $this->login_view->loginErrors(17);
            } else {
                $this->login_view->loginErrors(18);
            }
        }

        public function checkBerechtigung($benoetigt) {
            $rechte = array();
            for ($i = 7; $i >= 0; $i--) {
                $wert = pow(2, $i);
                if ($this->berechtigung >= $wert) {
                    $rechte[] = $wert;
                    $this->berechtigung -= $wert;
                }
            }
            if (!in_array($benoetigt, $rechte)) {
                echo 'Sie haben nicht die Berechtigung diese Aktion auszuführen';
                exit();
            }
        }

        public function __destruct() {
            $_SESSION['userData'] = array();
            $_SESSION['Postdata'] = array();
        }

    }

}
    