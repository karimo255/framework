<?php

if (!class_exists('ueberuns')) {

    class ueberuns extends mainView {

        public  $model;
        private $registry;
        private $session;
        private $template;
        private $lang;

        public function __construct($registry) {
            parent::__construct();
            $this->registry = $registry;
            $this->model = new Index_($registry);
            $this->session = $registry->get('session');
            $this->template = $registry->get('template');
            $this->lang = $registry->get('lang');
        }

        public function index() {
            include TEMPLATES . 'hauptSeite/ueberuns.tpl';
        }

        private function cleanCode($code) {
            $code = html_entity_decode($code);
            $array_a = array('&amp;', 'amp;', 'lt;', 'gt;', 'quot;');
            $array_b = array('', '', '<', '>', '');
            $code = str_replace($array_a, $array_b, $code);
            return $code;
        }

        public function __destruct() {
            parent::__destruct();
        }

    }

}