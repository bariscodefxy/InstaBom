<?php
    namespace App\Models;


    class Navigation {
        /**
         * @var array $navigation
         */
        private $navigation = array();

        /**
         * @param string $text
         * @param string $link
         */
        function add($text, $link) {
            $this->navigation[] = array(
                "text" => $text,
                "link" => $link
            );
        }

        /**
         * Gets Current Navigation
         * @return array
         */
        function get() {
            return $this->navigation;
        }

        /**
         * Empty Navigation
         */
        function clear() {
            $this->navigation = array();
        }
    }