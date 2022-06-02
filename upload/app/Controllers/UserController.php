<?php
    namespace App\Controllers;

    use App\Libraries\InstagramReaction;
    use Wow\Net\Response;

    class UserController extends BaseController {

        /**
         * @var InstagramReaction $iReaction
         */
        private $instagramReaction;
        /**
         * @var array $accountInfo
         */
        protected $accountInfo = NULL;
        /**
         * @var array $userFriendship
         */
        protected $userFriendship = NULL;

        /**
         * Override onStart
         */
        function onActionExecuting() {
            if(($pass = parent::onActionExecuting()) instanceof Response) {
                return $pass;
            }


            //Üye girişi kontrolü.
            if(($pass = $this->middleware("logged")) instanceof Response) {
                return $pass;
            }

            if(empty($this->route->params["usernameid"])) {
                return $this->redirectToUrl("/");
            }


            $this->instagramReaction = new InstagramReaction($this->logonPerson->member->uyeID);

            if(!intval($this->route->params["usernameid"]) > 0) {
                $user = $this->instagramReaction->objInstagram->getUserInfoByName($this->route->params["usernameid"]);
                if($user["status"] != "ok") {
                    return $this->notFound();
                }
                $this->route->params["usernameid"] = $user["user"]["pk"];
            }

            $this->accountInfo = $this->instagramReaction->objInstagram->getUserInfoById($this->route->params["usernameid"]);

            if($this->accountInfo["status"] != "ok") {
                return $this->notFound();
            }

            $this->navigation->add($this->accountInfo["user"]["full_name"], "/user/" . $this->route->params["usernameid"]);

            if($this->route->params["usernameid"] != $this->logonPerson->member->instaID) {
                $this->userFriendship = $this->instagramReaction->objInstagram->userFriendship($this->route->params["usernameid"]);

                if($this->accountInfo["user"]["is_private"] == 1 && $this->userFriendship["following"] != 1) {
                    return $this->view(NULL, "user/private");
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

            $this->view->set("accountInfo", $this->accountInfo);
            $this->view->set("userFriendship", $this->userFriendship);
        }

        function IndexAction() {

            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "more":
                        $data = $this->instagramReaction->objInstagram->getUserFeed($this->route->params["usernameid"], $this->request->data->maxid);
                        $this->view->set("ajaxLoaded", 1);

                        return $this->partialView($data, 'shared/list-media');
                        break;
                }
            }
            $data = $this->instagramReaction->objInstagram->getUserFeed($this->route->params["usernameid"]);;

            return $this->view($data);
        }

        function GeoAction() {
            $geoMedia = $this->instagramReaction->objInstagram->getGeoMedia($this->route->params["usernameid"]);

            return $this->view($geoMedia);
        }

        function TaggedAction() {
            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "more":
                        $data = $this->instagramReaction->objInstagram->getUserTags($this->route->params["usernameid"], $this->request->data->maxid);
                        $this->view->set("ajaxLoaded", 1);

                        return $this->partialView($data, 'shared/list-media');
                        break;
                }
            }
            $data = $this->instagramReaction->objInstagram->getUserTags($this->route->params["usernameid"]);

            return $this->view($data);
        }

        function FollowerAction() {
            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "more":
                        $followingList = $this->instagramReaction->objInstagram->getUserFollowers($this->route->params["usernameid"], $this->request->data->maxid);

                        $this->view->set("ajaxLoaded", 1);

                        return $this->partialView($followingList, 'shared/list-user');
                        break;
                }
            }
            $this->navigation->add("Takipçileri", "/user/" . $this->route->params["usernameid"] . "/follower");

            $follower = $this->instagramReaction->objInstagram->getUserFollowers($this->route->params["usernameid"]);

            return $this->view($follower);
        }

        function FollowingAction() {
            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "more":
                        $followingList = $this->instagramReaction->objInstagram->getUserFollowings($this->route->params["usernameid"], $this->request->data->maxid);

                        $this->view->set("ajaxLoaded", 1);

                        return $this->partialView($followingList, 'shared/list-user');
                        break;
                }
            }
            $this->navigation->add("Takip Ettikleri", "/user/" . $this->route->params["usernameid"] . "/following");

            $following = $this->instagramReaction->objInstagram->getUserFollowings($this->route->params["usernameid"]);

            return $this->view($following);
        }


    }