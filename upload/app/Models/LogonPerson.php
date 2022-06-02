<?php

    namespace App\Models;


    use Wow\Util\Collection;

    class LogonPerson {
        /**
         * @var Collection $member
         */
        public $member;
        /**
         * @var bool Is LogonPerson LoggedIn
         */
        private $isLoggedIn = FALSE;

        /**
         * LogonPerson constructor.
         */
        function __construct() {
            $this->member = new Collection();
        }

        /**
         * Is LogonPerson LoggedIn
         *
         * @return bool
         */
        function isLoggedIn() {
            return $this->isLoggedIn;
        }

        /**
         * Set LogonPerson LoggedIn Status
         *
         * @param $isLoggedIn
         */
        function setLoggedIn($isLoggedIn) {
            $this->isLoggedIn = boolval($isLoggedIn);
        }

        /**
         * Set member data
         *
         * @param array $data
         */
        function setMemberData($data) {
            $this->member->setData($data);
        }

        /**
         * Is Loggedin User Administrator
         * @return bool
         */
        function isAdmin() {
            return !$this->isLoggedIn ? FALSE : ($this->member->adminID !== NULL ? TRUE : FALSE);
        }
    }