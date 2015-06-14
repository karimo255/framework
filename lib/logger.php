<?php
  
    class Logger{
      
        private $log_file;
        
       
        function __construct($log_file = LOG_FILE) {
            $this->log_file = $log_file;

            if(!file_exists($log_file)){               
                touch($log_file);
            }


            if(!(is_writable($log_file))){   

                echo "LOGGER Fehler: ". $log_file ." kann nicht beschrieben werden  :";
            }
        }
        
       
        public function d($tag, $message){
            $this->writeToLog("DEBUG", $tag, $message);
        }

       
        public function e($tag, $message){
            $this->writeToLog("ERROR", $tag, $message);            
        }

        
        public function w($tag, $message){
            $this->writeToLog("WARNING", $tag, $message);            
        }

        
        public function i($tag, $message){
            $this->writeToLog("INFO", $tag, $message);            
        }


      
        private function writeToLog($status, $tag, $message) {            
            $date = date('[Y-m-d H:i:s]');
            $msg = "$date: [$tag][$status] - $message" . PHP_EOL;
            file_put_contents($this->log_file, $msg, FILE_APPEND);
        }

       
    }
?>
