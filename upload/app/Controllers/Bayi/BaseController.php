<?php



    namespace App\Controllers\Bayi;





    use App\Libraries\Helpers;

    use App\Models\LogonPerson;

    use App\Models\Navigation;

    use App\Models\Notification;

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

            $this->view->setLayout("layout/bayi");





            $this->db = Database::getInstance();





            $this->helper     = new Helpers();

            $this->navigation = new Navigation();

            $this->navigation->add("Instagram Takipçi", "/");



            //Eğer Session da tanımlı bir notification array varsa kullanmak üzere set edelim.

            if(isset($_SESSION["Notifications"]) && is_array($_SESSION["Notifications"])) {

                $this->notifications = $_SESSION["Notifications"];

                unset($_SESSION["Notifications"]);

            }



            $this->logonPerson = (isset($_SESSION["BayiLogonPerson"]) && $_SESSION["BayiLogonPerson"] instanceof LogonPerson) ? (object)$_SESSION["BayiLogonPerson"] : new LogonPerson();



            if($this->logonPerson->isLoggedIn()) {

                $memberData = $this->db->row("SELECT * FROM bayi WHERE bayiID=:bayiID", array("bayiID" => $this->logonPerson->member->bayiID));

                if(empty($memberData)) {

                    $this->logonPerson = new LogonPerson();

                } else {

                    $this->logonPerson->setMemberData($memberData);





                    if(session_id() != $this->logonPerson->member->lastSession && $this->request->ip != $this->logonPerson->member->ipAdresi) {

                        /**

                         * @var LogonPerson $adminLogonPerson

                         */

                        $adminLogonPerson = (isset($_SESSION["AdminLogonPerson"]) && $_SESSION["AdminLogonPerson"] instanceof LogonPerson) ? (object)$_SESSION["AdminLogonPerson"] : new LogonPerson();

                        if(!$adminLogonPerson->isLoggedIn()) {

                            $this->logonPerson         = new LogonPerson();

                            $objNotification           = new Notification();

                            $objNotification->title    = "Çıkış Yapıldı!";

                            $objNotification->type     = $objNotification::PARAM_TYPE_DANGER;

                            $objNotification->messages = array("Farklı bir kullanıcı panele giriş yaptığı için panelden çıkarıldınız. Bayilikler aynı anda tek kullanıcı tarafından kullanılabilmektedir.");

                            $this->notifications[]     = $objNotification;

                            $this->redirectToUrl(Wow::get("project/bayiPrefix"));

                        }

                    }

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

            if((!isset($_SESSION["BayiLogonPerson"]) || isset($_SESSION["BayiLogonPerson"]) && $_SESSION["BayiLogonPerson"] != $this->logonPerson)) {

                $_SESSION["BayiLogonPerson"] = $this->logonPerson;

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



                        return $this->redirectToUrl(Wow::get("project/resellerPrefix") . "/member");

                    }

                    break;

            }

        }





        protected function findAReactionUser() {

            if(isset($_SESSION["BayiReactionUserID"])) {

                $userID       = $_SESSION["BayiReactionUserID"];

                $userReaction = new InstagramReaction($userID);

                if($userReaction->objInstagram->isValid()) {

                    return $userID;

                }

            }

            $users = $this->db->query("SELECT * FROM uye WHERE isActive=1 AND canLike=1 AND canFollow=1 AND canComment=1 AND isWebCookie=0 ORDER BY sonOlayTarihi DESC LIMIT 50");

            shuffle($users);

            foreach($users as $user) {

                $userReaction = new InstagramReaction($user["uyeID"]);

                if($userReaction->objInstagram->isValid()) {

                    $_SESSION["BayiReactionUserID"] = $user["uyeID"];



                    return $user["uyeID"];

                    break;

                }

            }



            return NULL;

        }





    }