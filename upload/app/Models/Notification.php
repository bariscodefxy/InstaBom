<?php
    namespace App\Models;


    class Notification {
        const PARAM_TYPE_SUCCESS = "success";
        const PARAM_TYPE_DANGER  = "danger";
        const PARAM_TYPE_INFO    = "info";
        const PARAM_TYPE_WARNING = "warning";

        /**
         * @var string $type
         */
        public $type = self::PARAM_TYPE_WARNING;
        /**
         * @var string $title
         */
        public $title = NULL;
        /**
         * @var array $messages
         */
        public $messages = array();
        /**
         * @var array $buttons
         */
        public $buttons = array();
        /**
         * @var bool $closable
         */
        public $closable = TRUE;
    }