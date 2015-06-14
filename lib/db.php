<?php
//test
final class DB {

    private $verbindung;
    private $sicherheit;
    static private $instance = null;

    static public function getInstance() {
        if (null === self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __clone() {
        
    }

    private function __construct() {
        $daten = parse_ini_file(CONFIG . 'config.ini', true);


        $hostname = $daten['DabaBase']['host'];
        $username = $daten['DabaBase']['user'];
        $password = $daten['DabaBase']['pass'];
		

        $mysqli = new mysqli($hostname, $username, $password);
        if ($mysqli->connect_errno) {
            printf('Verbindung Gescheitert', $mysqli->connect_error);
            $logger = new Logger();
            $logger->e('Database', 'Verbindung gescheitert' . $mysqli->connect_error);
            exit();
        }
        //
        if (!$mysqli->set_charset("utf8")) {
            printf('Error loading character set utf8: %s\n', $mysqli->error);
        } else {
            // printf("Current character set: %s\n", $mysqli->character_set_name());
        }
        //
        $this->verbindung = $mysqli;
        $this->sicherheit = new Security();
    }

    public function select_db($DB) {
        $result = $this->verbindung->select_db($DB);
        if (!$result) {
            printf("Datenbank : " . $DB . "  konnte nicht ausgewählt werden  ");
        }
        return $result;
    }

    public function query($sql) {
        $result = $this->verbindung->query($sql);
       // echo "<h3>$sql</h3>";
        if ($result) {

            if (is_object($result)) {

                $i = 0;
                $data = array();

                while ($db_result = $result->fetch_assoc()) {
                    $data[$i] = $db_result;
                    $i++;
                }

                mysqli_free_result($result);

                $query = new stdClass();
                $query->row = isset($data[0]) ? $data[0] : array();
                $query->rows = $data;
                $query->num_rows = $i;

                unset($data);

                return $query;
            } else {
                return TRUE;
            }
        } else {
            //   exit('Error: ' . $this->verbindung->error . '<br />Error No: ' . $this->verbindung->errno . '<br />' . $sql);
            return false;
        }
    }

    public function escape($value) {
        if (is_array($value)) {
            foreach ($value as $k => $val) {
                unset($value[$k]);
                $value[$this->escape($k)] = $this->escape($val);
            }
            return $value;
        } else {
            $value = $this->verbindung->real_escape_string($value);
            return $value;
        }
    }

    public function countAffected() {
        return $this->verbindung->affected_rows();
    }

    public function getLastId() {
        return $this->verbindung->insert_id;
    }

    public function __destruct() {
        $this->verbindung->close();
    }

    protected $table = null;
    protected $was_string = null;
    protected $wer_string = null;
    protected $wer_in_string = null;
    protected $logic_op = null;
    protected $orderBy = array();
    protected $groupBy = array();

    private function isAssoc($arr) {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    public function select($table, $was, $wer, $logic_op = 'AND') {
        $this->logic_op = $logic_op;
        $logic_op = strtoupper($logic_op);
        $logic_op = trim($logic_op);

        if ($this->isAssoc($wer)) {
            $this->wer_string($wer);
            if ($logic_op != 'AND' && $logic_op != 'OR' || empty($logic_op)) {
                echo 'logic_op muss ein logicher Operator sein!';
                return false;
            }
        } else {
            $this->wer_in_string($wer);
            if ($logic_op == 'AND' || $logic_op == 'OR' || empty($logic_op)) {
                echo 'logic_op muss eine Kolumne sein!';
                return false;
            }
        }
        $this->was_string($was);


        $query = "SELECT ";
        $query .= $this->was_string;
        $query .= " FROM ";
        $query .= ' ' . $table . ' ';
        $query .= $this->wer_string;
        $query .= $this->wer_in_string;

        return $this->query($query);
    }

    public function insert($table, $data = array(), $wer = array()) {
        $data = $this->escape($data);
        $this->query = $this->build_insert_query($table, $data, $wer);
        $result = $this->query($this->query);
        return $result;
    }

    private function build_insert_query($table, $data, $wer) {
        $this->wer_string($wer);
        $i = 0;
        $keys = '';
        $values = '';
        $a = false;
        $data = array_filter($data);
        foreach ($data as $k => $v) {
            if ($i < count($data) - 1) {
                $a = ',';
            } else {
                $a = '';
            }
            $keys .= "`" . $k . "`" . $a;
            $values .= "'" . $v . "'" . $a;
            $i++;
        }

        $query = 'INSERT INTO `Moschee`.`'
                . $table . '` (' . $keys . ')  '
                . 'VALUES (' . $values . ')'
                . $this->wer_string;
        return $query;
    }

    public function update($table, $where = array(), $data = array()) {
        $data = $this->escape($data);
        $this->query = $this->build_update_query($table, $where, $data);
        $result = $this->query($this->query);
        return $result;
    }

    private function build_update_query($table, $where, $data) {
        $this->wer_string($where);
        $query = '';
        $i = 0;

        foreach ($data as $k => $v) {
            if ($i < count($data) - 1) {
                $a = ',';
            } else {
                $a = '';
            }
            $query .= "`" . $k . "` =  " . "'" . $v . "'" . $a;
            $i = $i + 1;
        }
        $query = ' UPDATE `Moschee`.`' . $table . '` '
                . 'SET ' . $query
                . $this->wer_string;


        return $query;
    }

    private function was_string($was) {
        if (count($was) > 0) {
            $this->was_string = implode(',', $was) . ' ';
        } else {
            $this->was_string = '*';
        }
    }

    private function wer_string($wer) {
        if (count($wer) > 0) {
            $this->wer_string = 'WHERE   ';
        } else {
            $this->wer_string = NULL;
            return;
        }
        $i = 0;
        $a = '';

        foreach ($wer as $k => $v) {
            if ($i < count($wer) - 1) {
                
            } else {
                $this->logic_op = '';
            }
            $i ++;

            $this->wer_string .= "`" . $k . "` =  " . "'" . $v . "'" . $a . ' ' . $this->logic_op . ' ';
        }
    }

    private function wer_in_string($wer_in) {
        $imploeded = implode(',', $wer_in);
        $this->wer_in_string = 'WHERE ' . $this->logic_op . " IN ($imploeded) ";
    }

    public function delete($table, $where = NULL, $and_or = 'AND') {
        $where = $this->escape($where);
        $sql = $this->build_delete_query($table, $where);
        $result = $this->query($sql);
        return $result;
    }

    private function build_delete_query($table, $where) {
        $this->wer_string($where);
        $query = "DELETE  FROM  $table  $this->wer_string";
        return $query;
    }

}

?>
