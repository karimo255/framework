<?php

class Download {

    var $filePath;


    function __construct($filePath) {
                       
        if ($filePath != null) {
            $this->setFilePath($filePath);
        }else {
            die('filepath not defined');
        }
    }
    
    // Setter & Getter
    private function setFilePath($path) {
        $this->filePath = $path;
    }    
    private function getFileName() {
        return basename($this->getFilePath());
    }

    
    private function getFilePath() {
        return $this->filePath;
    } 
    
    //progress    
    public function exportData() {
            $this->executeHeaders();
            set_time_limit(0);
            readfile($this->getFilePath());
    }

    public function executeHeaders() {

        header("Pragma: no-cache");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false);
        header("Content-Type: file");
        header("Content-type: application/octet-stream");
        header('Content-Disposition: attachment; filename="' . $this->getFileName() . '";');
        header("Content-Transfer-Encoding: binary");
    }

}
