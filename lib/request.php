<?php
final class Request {
	public $get = array();
	public $post = array();
	public $cookie = array();
	public $files = array();
	public $server = array();
        
        private $sicherheit;

        public function __construct() {
                $this->sicherheit = new Security();            
		$_GET = $this->clean($_GET);
		$_POST = $this->clean($_POST);
		$_COOKIE = $this->clean($_COOKIE);
		$_FILES = $this->clean($_FILES);
		$_SERVER = $this->clean($_SERVER);
                
		$_GET = $this->sicherheit->clean($_GET);
		$_POST = $this->sicherheit->clean($_POST);
		$_COOKIE = $this->sicherheit->clean($_COOKIE);
		$_FILES = $this->sicherheit->clean($_FILES);
		$_SERVER = $this->sicherheit->clean($_SERVER);                
		
		$this->get = $_GET;
		$this->post = $_POST;
		$this->cookie = $_COOKIE;
		$this->files = $_FILES;
		$this->server = $_SERVER;
	}
	
  	public function clean($data) {
    	if (is_array($data)) {
	  		foreach ($data as $key => $value) {
				unset($data[$key]);
				
	    		$data[$this->clean($key)] = $this->clean($value);
	  		}
		} else { 
	  		$data = htmlspecialchars($data, ENT_COMPAT, 'UTF-8');
		}

		return $data;
	}
}
?>