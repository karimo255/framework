<?php

if (!class_exists('Template')) {

    class Template {

        private $file;
        private $dbData = array();
        private $keys = array();
        private $values = array();
        private $data = array();
        private $bool;
        private $output;
        private $sprache_prefix = '';
        private $extra_data = array();

        public function __construct() {
            $lang = new Lang('header');
            $sprache = $lang->autoDetectSprache();
            if ($sprache == 'ar') {
                $this->sprache_prefix = $_SESSION['sprache'];
            }
        }

        public function reset() {
            $this->file = NULL;
            //$this->output = NULL;

            unset($this->dbData);
            unset($this->keys);
            unset($this->values);
            unset($this->extra_data);
            unset($this->data);
        }

        public function set_template($file, $sub_pfad = '') {
            if (func_num_args() == 2) {
                $sub_pfad = $sub_pfad . DIRECTORY_SEPARATOR;
            }
            $pos = strpos($file,".tpl");
            if ($pos !== false) {
                $this->file = TEMPLATES . $sub_pfad . $this->sprache_prefix . '_' . $file;
                if (!is_file($this->file)) {
                    $this->file = TEMPLATES . $sub_pfad . $file;
                }
                if (!file_exists($this->file)) {
                    echo 'file ' . $this->file . ' existiert nicht1!';
                    $logger = new Logger();
                    $logger->w('template', 'file ' . $this->file . '  existiert nicht!2');
                } else {
                    $this->output = file_get_contents($this->file);
                }
            } else {               
                $this->output = $file;
            }
        }

        public function set($key, $value = null) {
            $args = func_num_args();
            if ($args > 1) {
                $this->data[$key] = $value;
            } else {
                $this->data = $key;
            }
        }

        public function set_light($key, $value = NULL) {
            if ($value !== NULL) {
                $this->extra_data[$key] = $value;
            } elseif (is_array(isset($key[0]) ? $key[0] : NULL)) {
                $this->data = $key;
            } elseif (is_array($key)) {
                $this->extra_data = array_merge($this->extra_data, $key);
            }
        }

        //
        public function render() {
            if (count($this->data) == 0) {
                return $this->output;
            }

            $output = '';

            foreach ($this->data as $key => $value) {
                if (is_array($value)) {


                    $i = 0;
                    $tmp_keys = array_keys($value);
                    foreach ($tmp_keys as $k => $val) {

                        $this->keys[] = '{' . $val . '}';
                    }
                    $this->values = array_values($value);
                    $output .= str_replace($this->keys, $this->values, $this->output);
                    unset($this->data);
                } else {
                    $output = $this->output;
                    foreach ($this->data as $key => $value) {
                        if (!is_array($key) && !is_array($value))
                            $output = str_replace("{" . $key . "}", $value, $output);
                    }
                    break;
                }
                $this->reset();
            }
            return $output;
        }

        public function render_light() {
            if (!empty($this->extra_data)) {
                $this->data[] = $this->extra_data;
            }


            $output = '';

            foreach ($this->data as $value) {

                $this->keys = array_map(array($this, 'placeHolder'), array_keys($value));
                $this->values = array_values($value);
                $output .= str_replace($this->keys, $this->values, $this->output);
            }

            $this->reset();

            return ($output === '') ? $this->output : $output;
        }

        private function placeHolder($string) {
            return '{' . $string . '}';
        }

    }

}


