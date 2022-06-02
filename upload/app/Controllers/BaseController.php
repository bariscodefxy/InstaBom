<?php

    namespace App\Controllers;

    use App\Libraries\Helpers;
    use App\Models\LogonPerson;
    use App\Models\Navigation;
    use App\Libraries\InstagramReaction;
    use Wow;
    use Wow\Core\Controller;
    use Wow\Database\Database;
    use Wow\Net\Response;

    class BaseController extends Controller {

        /**
         * @var Database $db Database Object
         */
        protected $db;

        /**
         * @var Helpers $helper Helper Object
         */
        protected $helper;

        /**
         * @var LogonPerson $logonPerson
         */
        protected $logonPerson;

        /**
         * @var Navigation $navigation
         */
        protected $navigation;

        /**
         * @var array $notifications
         */
        public $notifications = array();

        /**
         * Override onStart
         */
        function onActionExecuting() {
            if(($pass = parent::onActionExecuting()) instanceof Response) {
                return $pass;
            }

            $this->db = Database::getInstance();

            $this->helper     = new Helpers();
            $this->navigation = new Navigation();
            $this->navigation->add("Instagram Takipçi", "/");

            //Eğer Session da tanımlı bir notification array varsa kullanmak üzere set edelim.
            if(isset($_SESSION["Notifications"]) && is_array($_SESSION["Notifications"])) {
                $this->notifications = $_SESSION["Notifications"];
                unset($_SESSION["Notifications"]);
            }

            $this->logonPerson = (isset($_SESSION["LogonPerson"]) && $_SESSION["LogonPerson"] instanceof LogonPerson) ? (object)$_SESSION["LogonPerson"] : new LogonPerson();
            if($this->logonPerson->isLoggedIn()) {
                $memberData = $this->db->row("SELECT * FROM uye WHERE uyeID=:uyeID", array("uyeID" => $this->logonPerson->member->uyeID));
                if(empty($memberData)) {
                    $this->logonPerson = new LogonPerson();
                } else {
                    $this->logonPerson->setMemberData($memberData);
                }
            }
        }

        /**
         * Override onEnd
         */
        function onActionExecuted() {
            if(($pass = parent::onActionExecuted()) instanceof Response) {
                return $pass;
            }

            if(session_status() === PHP_SESSION_ACTIVE) {
                //Eğer logonPerson objemizde bir değişiklik haiz olmuşsa yeni değerleri sonraki sayfalara taşımak için session yardımcımız.
                if((!isset($_SESSION["LogonPerson"]) || isset($_SESSION["LogonPerson"]) && $_SESSION["LogonPerson"] != $this->logonPerson)) {
                    $_SESSION["LogonPerson"] = $this->logonPerson;
                }

                //Eğer bir yönlendirme söz konusu ise bazı verileri Session yardımı ile taşımamız gerekebilir.
                if($this->getResultType() == self::RESULT_TYPE_REDIRECT) {
                    $_SESSION["Notifications"] = $this->notifications;
                }
            }

            //Viewda kullanmak üzere logonPerson ve notifications objelerimizi ekliyoruz.
            $this->view->set("logonPerson", $this->logonPerson);
            $this->view->set("notifications", $this->notifications);
            $this->view->set("navigation", $this->navigation);

        }

        /**
         * Middleware
         *
         * @param string $type
         */
        function middleware($type) {
            switch($type) {
                case "logged":
                    if(!$this->logonPerson->isLoggedIn()) {
                        $_SESSION["ReturnUrl"] = $this->request->url;

                        return $this->view(NULL, "shared/login-required");
                    }
                    break;
                case "bayi":
                    $isBayi = $this->logonPerson->member->isBayi;
                    if(empty($isBayi) || $isBayi == 0) {

                        return $this->view(NULL, "shared/bayi-required");
                    }
                    break;
                case "admin":
                    if(!$this->logonPerson->isAdmin()) {
                        return $this->redirectToUrl("/");
                    }
                    break;
            }
        }


        protected function findAReactionUser() {
            $users = $this->db->query("SELECT * FROM uye WHERE isActive=1 AND isWebCookie=0 ORDER BY sonOlayTarihi DESC LIMIT 500");
            shuffle($users);
            foreach($users as $user) {
                $userReaction = new InstagramReaction($user["uyeID"]);
                if($userReaction->objInstagram->isValid()) {
                    return $user["uyeID"];
                    break;
                }
            }

            return NULL;
        }

    }