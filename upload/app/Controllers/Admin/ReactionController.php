<?php

    namespace App\Controllers\Admin;

    use Exception;
    use Wow;
    use Wow\Database\Database;
    use Wow\Net\Response;
    use Instagram;
    use InstagramWeb;

    class ReactionController extends BaseController {

        /**
         * @var Instagram $objInstagram
         */
        public $objInstagram;
        /**
         * @var InstagramWeb $objInstagramWeb
         */
        public $objInstagramWeb;

        /**
         * @var Database $db
         */
        protected $db;
        /**
         * @var array $user
         */
        protected $user;

        function onActionExecuting() {
            $actionResponse = parent::onActionExecuting();
            if($actionResponse instanceof Response) {
                return $actionResponse;
            }
            if($this->logonPerson->isLoggedIn()) {
                return $this->redirectToUrl(Wow::get("project/adminPrefix"));
            }
        }

        function UnfollowAction($userID) {

            $user = $this->db->row("SELECT * FROM uye WHERE uyeID=:uyeID", array("uyeID" => intval($userID)));

            if(empty($user)) {
                throw new Exception($userID . " IDli üye bulunamadı!");
            }
            $this->user         = $user;
            $this->objInstagram = new Instagram($this->user["kullaniciAdi"], $this->user["sifre"], $this->user["instaID"]);

            return $this->objInstagram->unfollow($userID);
        }

    }