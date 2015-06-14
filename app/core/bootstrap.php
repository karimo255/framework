<?php

class Bootstrap {
    private $request;
            function __construct($registry) {
                $this->request = $registry->get('request');
                if (isset($this->request->get['url'])){
                    $url = $this->request->get['url'];
                }else{
                    $url = null;               
                }
		$url = rtrim($url, '/');
		$url = explode('/', $url);
                
                
		if (empty($url[0])){                   

                    $controller = new Index($registry);
                    $controller->index();
                    return false;
                }               

		$file = 'app/controllers/' . $url[0] . '.php';
                $file = strtolower($file);
		if (file_exists($file)) {
                       $controller = new $url[0]($registry);                       
		} else {
			require 'app/controllers/error.php';
			$controller = new Error($registry);
			return false;
		}

                if (isset($url[4])) {                        
			$controller->{$url[1]}($url[2],$url[3],$url[4]);                        
		}elseif (isset($url[3])) {                        
			$controller->{$url[1]}($url[2],$url[3]);
		}elseif (isset($url[2])) {
			$controller->{$url[1]}($url[2]);
		} else {
			if (isset($url[1])) {
				$controller->{$url[1]}();                            
                        }else{
                            $controller->index();
                        }
		}

	}

}
