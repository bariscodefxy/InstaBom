<?php

    namespace App\Controllers\Admin;


    use App\Libraries\Helpers;
    use App\Models\LogonPerson;
    use App\Models\Navigation;
    use Wow;
    use Wow\Core\Controller;
    use Wow\Database\Database;
    use Wow\Net\Response;
    use App\Libraries\InstagramReaction;

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
         * @var InstagramReaction $instagramReaction
         */
        protected $instagramReaction = NULL;

        /**
         * Override onStart
         */
        function onActionExecuting() {
            if(($pass = parent::onActionExecuting()) instanceof Response) {
                return $pass;
            }

            $this->view->setTheme("adminex");
            $this->view->setLayout("layout/admin");


            $this->db = Database::getInstance();


            $this->helper     = new Helpers();
            $this->navigation = new Navigation();
            $this->navigation->add("Instagram Takipçi", "/");

            //Eğer Session da tanımlı bir notification array varsa kullanmak üzere set edelim.
            if(isset($_SESSION["Notifications"]) && is_array($_SESSION["Notifications"])) {
                $this->notifications = $_SESSION["Notifications"];
                unset($_SESSION["Notifications"]);
            }

            $this->logonPerson = (isset($_SESSION["AdminLogonPerson"]) && $_SESSION["AdminLogonPerson"] instanceof LogonPerson) ? (object)$_SESSION["AdminLogonPerson"] : new LogonPerson();

            if($this->logonPerson->isLoggedIn()) {
                $memberData = $this->db->row("SELECT * FROM admin WHERE adminID=:adminID", array("adminID" => $this->logonPerson->member->adminID));
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

            //Eğer logonPerson objemizde bir değişiklik haiz olmuşsa yeni değerleri sonraki sayfalara taşımak için session yardımcımız.
            if((!isset($_SESSION["AdminLogonPerson"]) || isset($_SESSION["AdminLogonPerson"]) && $_SESSION["AdminLogonPerson"] != $this->logonPerson)) {
                $_SESSION["AdminLogonPerson"] = $this->logonPerson;
            }

            //Eğer bir yönlendirme söz konusu ise bazı verileri Session yardımı ile taşımamız gerekebilir.
            if($this->getResultType() == self::RESULT_TYPE_REDIRECT) {
                $_SESSION["Notifications"] = $this->notifications;
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

                        return $this->redirectToUrl(Wow::get("project/adminPrefix") . "/member");
                    }
                    break;
            }
        }


        protected function findAReactionUser() {
            if(isset($_SESSION["AdminReactionUserID"])) {
                $userID       = $_SESSION["AdminReactionUserID"];
                $userReaction = new InstagramReaction($userID);
                if($userReaction->objInstagram->isValid()) {
                    return $userID;
                }
            }
            $users = $this->db->query("SELECT * FROM uye WHERE isActive=1 AND isWebCookie=0 ORDER BY sonOlayTarihi DESC LIMIT 200");
            shuffle($users);
            foreach($users as $user) {
                $userReaction = new InstagramReaction($user["uyeID"]);
                if($userReaction->objInstagram->isValid()) {
                    $_SESSION["AdminReactionUserID"] = $user["uyeID"];

                    return $user["uyeID"];
                    break;
                }
            }
        }


    }