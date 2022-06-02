<?php

    namespace App\Controllers;

    use App\Libraries\InstagramReaction;
    use App\Models\LogonPerson;
    use App\Models\Notification;
    use Wow\Net\Response;

    class AccountController extends BaseController {

        /**
         * @var InstagramReaction $instagramReaction
         */
        private $instagramReaction;
        /**
         * @var array $accountInfo
         */
        protected $accountInfo = NULL;

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

//            //Navigation
//            $this->navigation->add("Hesabım", "/account");

            $this->instagramReaction = new InstagramReaction($this->logonPerson->member->uyeID);
        }

        /**
         * Override onEnd
         */
        function onActionExecuted() {
            if(($pass = parent::onActionExecuted()) instanceof Response) {
                return $pass;
            }
            $this->view->set("accountInfo", $this->accountInfo);
        }


        function IndexAction() {
            $this->view->set('title', "Timeline");
            $this->navigation->add("Timeline", "/account");

            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "more":
                        $timeline = $this->instagramReaction->objInstagram->getTimelineFeed($this->request->data->maxid);

                        $this->view->set("ajaxLoaded", 1);

                        return $this->partialView($timeline, 'shared/list-media');
                        break;
                }
            }


            $timeline = $this->instagramReaction->objInstagram->getTimelineFeed();
            $reels    = $this->instagramReaction->objInstagram->getReelsTrayFeed();
            $this->view->set("reels", $reels);
            if($this->request->query->reels == 1) {
                return $this->json($reels);
            }

            return $this->view($timeline);
        }

        function GeoAction() {
            $this->view->set('title', "Geo Media");

            $this->accountInfo = $this->instagramReaction->objInstagram->getSelfUserInfo();
            $geoMedia          = $this->instagramReaction->objInstagram->getSelfGeoMedia();

            return $this->view($geoMedia);
        }


        function LikeAction() {
            if(empty($this->request->data->id)) {
                return $this->json(array(
                                       "status"  => "error",
                                       "message" => "Id belirtilmedi."
                                   ));
            }
            $this->instagramReaction->objInstagram->like($this->request->data->id);

            return $this->json(array(
                                   "status"  => "success",
                                   "message" => "Beğenildi."
                               ));
        }

        function UnlikeAction() {
            if(empty($this->request->data->id)) {
                return $this->json(array(
                                       "status"  => "error",
                                       "message" => "Id belirtilmedi."
                                   ));
            }
            $this->instagramReaction->objInstagram->unlike($this->request->data->id);

            return $this->json(array(
                                   "status"  => "success",
                                   "message" => "Beğenmekten vazgeçildi."
                               ));
        }

        function FollowAction() {
            if(empty($this->request->data->id)) {
                return $this->json(array(
                                       "status"  => "error",
                                       "message" => "Id belirtilmedi."
                                   ));
            }
            $userFriendship = $this->instagramReaction->objInstagram->userFriendship($this->request->data->id);
            if($userFriendship["status"] != "ok") {
                return $this->json(array(
                                       "status"  => "error",
                                       "message" => "Kullanıcı bulunamadı."
                                   ));
            }
            $data = $this->instagramReaction->objInstagram->follow($this->request->data->id);

            return $this->json(array(
                                   "status"     => "success",
                                   "message"    => "",
                                   "is_private" => $userFriendship["is_private"] ? 1 : 0,
                                   "response"   => $data
                               ));
        }

        function UnfollowAction() {
            if(empty($this->request->data->id)) {
                return $this->json(array(
                                       "status"  => "error",
                                       "message" => "Id belirtilmedi."
                                   ));
            }
            $userFriendship = $this->instagramReaction->objInstagram->userFriendship($this->request->data->id);
            if($userFriendship["status"] != "ok") {
                return $this->json(array(
                                       "status"  => "error",
                                       "message" => "Kullanıcı bulunamadı."
                                   ));
            }
            $this->instagramReaction->objInstagram->unfollow($this->request->data->id);

            return $this->json(array(
                                   "status"     => "success",
                                   "message"    => "",
                                   "is_private" => $userFriendship["is_private"] ? 1 : 0
                               ));
        }


        function BlockAction() {
            if(empty($this->request->data->id)) {
                return $this->json(array(
                                       "status"  => "error",
                                       "message" => "Id belirtilmedi."
                                   ));
            }
            $userFriendship = $this->instagramReaction->objInstagram->userFriendship($this->request->data->id);
            if($userFriendship["status"] != "ok") {
                return $this->json(array(
                                       "status"  => "error",
                                       "message" => "Kullanıcı bulunamadı."
                                   ));
            }
            $this->instagramReaction->objInstagram->block($this->request->data->id);

            return $this->json(array(
                                   "status"     => "success",
                                   "message"    => "",
                                   "is_private" => $userFriendship["is_private"] ? 1 : 0
                               ));
        }

        function UnblockAction() {
            if(empty($this->request->data->id)) {
                return $this->json(array(
                                       "status"  => "error",
                                       "message" => "Id belirtilmedi."
                                   ));
            }
            $userFriendship = $this->instagramReaction->objInstagram->userFriendship($this->request->data->id);
            if($userFriendship["status"] != "ok") {
                return $this->json(array(
                                       "status"  => "error",
                                       "message" => "Kullanıcı bulunamadı."
                                   ));
            }
            $this->instagramReaction->objInstagram->unblock($this->request->data->id);

            return $this->json(array(
                                   "status"     => "success",
                                   "message"    => "",
                                   "is_private" => $userFriendship["is_private"] ? 1 : 0
                               ));
        }

        function EditMediaAction($id) {
            if(empty($id)) {
                return $this->notFound();
            }
            $media = $this->instagramReaction->objInstagram->getMediaInfo($id);
            if($media["status"] != "ok") {
                return $this->notFound();
            }
            if($this->request->method == "POST") {
                $sonuc = $this->instagramReaction->objInstagram->editMedia($id, $this->request->data->aciklama);
                if($sonuc["status"] == "ok") {
                    return $this->json(array(
                                           "status"  => "success",
                                           "message" => "Açıklama düzenlendi."
                                       ));
                } else {
                    return $this->json(array(
                                           "status"  => "error",
                                           "message" => "İşlem gerçekleştirilemedi!"
                                       ));
                }
            }

            return $this->partialView($media);
        }

        function DeleteMediaAction() {
            $sonuc = $this->instagramReaction->objInstagram->deleteMedia($this->request->data->id);

            return $this->json($sonuc["status"] == "ok" ? array("status" => "success") : array("status" => "error"));
        }

        function FeedAction() {
            $this->view->set('title', "Feed");

            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "more":
                        $timeline = $this->instagramReaction->objInstagram->getSelfUserFeed($this->request->data->maxid);

                        $this->view->set("ajaxLoaded", 1);
                        $this->accountInfo = $this->instagramReaction->objInstagram->getSelfUserInfo();

                        return $this->partialView($timeline, 'shared/list-media');
                        break;
                }
            }
            $this->accountInfo = $this->instagramReaction->objInstagram->getSelfUserInfo();
            $feed              = $this->instagramReaction->objInstagram->getSelfUserFeed();

            return $this->view($feed);
        }

        function DiscoverAction() {
            $this->view->set('title', "Keşfet");

            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "more":
                        $timeline = $this->instagramReaction->objInstagram->getPopularFeed($this->request->data->maxid);

                        $this->view->set("ajaxLoaded", 1);

                        return $this->partialView($timeline, 'shared/list-media');
                        break;
                }
            }
            $this->navigation->add("Keşfet", "/account/discover");

            $feed = $this->instagramReaction->objInstagram->getPopularFeed();

            return $this->view($feed);
        }

        function HistoryAction($id = "following") {
            $this->view->set("title", "Geçmiş");
            switch($id) {
                case "following":
                    if($this->request->method == "POST") {
                        switch($this->request->query->formType) {
                            case "more":
                                $timeline = $this->instagramReaction->objInstagram->getFollowingRecentActivity($this->request->data->maxid);

                                $this->view->set("ajaxLoaded", 1);

                                return $this->partialView($timeline, 'shared/list-activity');
                                break;
                        }
                    }
                    $activity = $this->instagramReaction->objInstagram->getFollowingRecentActivity();

                    return $this->view($activity);
                    break;
                case "me":
                    if($this->request->method == "POST") {
                        switch($this->request->query->formType) {
                            case "more":
                                $timeline = $this->instagramReaction->objInstagram->getRecentActivity($this->request->data->maxid);

                                $this->view->set("ajaxLoaded", 1);

                                return $this->partialView($timeline, 'shared/list-activity');
                                break;
                        }
                    }
                    $activity = $this->instagramReaction->objInstagram->getRecentActivity();

                    return $this->view($activity);
                    break;
                default:
                    return $this->notFound();
            }
        }

        function SearchAction($q, $tab = NULL) {
            if(empty($q)) {
                return $this->notFound();
            }

            if(empty($tab)) {
                if(substr($q, 0, 1) == "#") {
                    $tab = "tag";
                } else {
                    $tab = "user";
                }
            }
            $q = str_replace("#", "", $q);
            $q = str_replace("@", "", $q);
            $this->view->set("title", "Arama Sonuçları");
            $searchText = str_replace("ı", "I", $q);
            $searchText = str_replace("İ", "i", $searchText);
            $searchText = urlencode($searchText);
            switch($tab) {
                case "user":
                    $data = $this->instagramReaction->objInstagram->searchUsers($searchText);
                    break;
                case "tag":
                    $data = $this->instagramReaction->objInstagram->searchTags($searchText);
                    break;
                case "location":
                    $data = $this->instagramReaction->objInstagram->searchLocation($searchText);
                    break;
                default:
                    return $this->notFound();
            }
            $this->view->set("q", strip_tags($q));
            $this->view->set("tab", $tab);

            return $this->view($data);
        }

        function TagAction($id) {
            if(empty($id)) {
                return $this->notFound();
            }
            $this->view->set("title", "Tag Detayları #" . strip_tags($id));
            $this->view->set("tag", strip_tags($id));

            $searchText = str_replace("ı", "I", $id);
            $searchText = str_replace("İ", "i", $searchText);
            $searchText = urlencode($searchText);

            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "more":
                        $mediaList = $this->instagramReaction->objInstagram->tagFeed($searchText, $this->request->data->maxid);

                        $this->view->set("ajaxLoaded", 1);

                        return $this->partialView($mediaList, 'shared/list-media');
                        break;
                }
            }

            $this->view->set("q", strip_tags($id));
            $this->view->set("tab", "tag");
            $data = $this->instagramReaction->objInstagram->tagFeed($searchText);

            return $this->view($data);
        }

        function LocationAction($id) {
            if(empty($id)) {
                return $this->notFound();
            }
            $this->view->set("title", "Lokasyon Detayları #" . strip_tags($this->request->query->location));
            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "more":
                        $mediaList = $this->instagramReaction->objInstagram->getLocationFeed($id, $this->request->data->maxid);

                        $this->view->set("ajaxLoaded", 1);

                        return $this->partialView($mediaList, 'shared/list-media');
                        break;
                }
            }
            $this->view->set("location", strip_tags($this->request->query->location));
            $this->view->set("q", strip_tags($this->request->query->location));
            $this->view->set("tab", "location");
            $data = $this->instagramReaction->objInstagram->getLocationFeed($id);

            return $this->view($data);
        }

        function LikedAction() {
            $this->view->set('title', "Beğendiğim Gönderiler");

            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "more":
                        $mediaList = $this->instagramReaction->objInstagram->getLikedMedia($this->request->data->maxid);

                        $this->view->set("ajaxLoaded", 1);

                        return $this->partialView($mediaList, 'shared/list-media');
                        break;
                }
            }
            $liked = $this->instagramReaction->objInstagram->getLikedMedia();

            return $this->view($liked);
        }

        function FollowingAction() {
            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "more":
                        $followingList = $this->instagramReaction->objInstagram->getSelfUsersFollowing($this->request->data->maxid);

                        $this->view->set("ajaxLoaded", 1);
                        $this->accountInfo = $this->instagramReaction->objInstagram->getSelfUserInfo();

                        return $this->partialView($followingList, 'shared/list-user');
                        break;
                }
            }
            $this->accountInfo = $this->instagramReaction->objInstagram->getSelfUserInfo();
            $following         = $this->instagramReaction->objInstagram->getSelfUsersFollowing();

            return $this->view($following);
        }

        function FollowerAction() {
            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "more":
                        $followingList = $this->instagramReaction->objInstagram->getSelfUserFollowers($this->request->data->maxid);

                        $this->view->set("ajaxLoaded", 1);
                        $this->accountInfo = $this->instagramReaction->objInstagram->getSelfUserInfo();

                        return $this->partialView($followingList, 'shared/list-user');
                        break;
                }
            }
            $this->accountInfo = $this->instagramReaction->objInstagram->getSelfUserInfo();
            $follower          = $this->instagramReaction->objInstagram->getSelfUserFollowers();

            return $this->view($follower);
        }


        function ChangeProfilePhotoAction() {
            if($this->request->method == "POST") {
                if(!isset($this->request->data["url"])) {
                    $uploadError = NULL;
                    if($this->request->files->file["error"] === UPLOAD_ERR_OK) {
                        $info = getimagesize($this->request->files->file["tmp_name"]);
                        if($info === FALSE) {
                            $uploadError = "Sadece resim yüklenebilir!";
                        } else {
                            if(($info[2] !== IMAGETYPE_JPEG) && ($info[2] !== IMAGETYPE_PNG)) {
                                $uploadError = "Sadece jpg,png resimleri yüklenebilir!";
                            } else {
                                $this->instagramReaction->objInstagram->changeProfilePicture($this->request->files->file["tmp_name"]);
                            }
                        }
                    } else {
                        $uploadError = "Yükleme başarısız oldu!";
                    }

                    if(!is_null($uploadError)) {
                        $objNotification             = new Notification();
                        $objNotification->type       = $objNotification::PARAM_TYPE_DANGER;
                        $objNotification->title      = "Resim Eklenemedi!";
                        $objNotification->messages[] = $uploadError;
                        $this->notifications[]       = $objNotification;
                    }

                    return $this->redirectToUrl($this->request->referrer);

                } else {
                    $uploadError = NULL;
                    $info        = getimagesize($this->request->data->url);
                    if($info === FALSE) {
                        $uploadError = "Sadece resim yüklenebilir!";
                    } else {
                        if(($info[2] !== IMAGETYPE_JPEG) && ($info[2] !== IMAGETYPE_PNG)) {
                            $uploadError = "Sadece jpg,png resimleri yüklenebilir!";
                        } else {
                            $this->instagramReaction->objInstagram->changeProfilePicture($this->request->data->url);
                        }
                    }


                    if(!is_null($uploadError)) {
                        $objNotification             = new Notification();
                        $objNotification->type       = $objNotification::PARAM_TYPE_DANGER;
                        $objNotification->title      = "Resim Eklenemedi!";
                        $objNotification->messages[] = $uploadError;
                        $this->notifications[]       = $objNotification;
                    }

                    return $this->redirectToUrl($this->request->referrer);
                }
            }

            return $this->partialView();
        }

        function RemoveProfilePhotoAction() {
            $data  = $this->instagramReaction->objInstagram->removeProfilePicture();
            $sonuc = array("status" => "error");
            if($data["status"] == "ok") {
                $sonuc["status"]  = "success";
                $sonuc["message"] = $data["user"]["profile_pic_url"];
            }

            return $this->json($sonuc);
        }

        function SettingsAction() {
            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "accountInformation":
                        $data = $this->request->data;
                        $this->instagramReaction->objInstagram->editProfile($data->external_url, $data->phone_number, $data->first_name, $data->biography, $data->email, $data->gender);
                        break;
                    case "accountPrivacy":
                        if($this->request->data->do == "public") {
                            $this->instagramReaction->objInstagram->setPublicAccount();
                        } else {
                            $this->instagramReaction->objInstagram->setPrivateAccount();
                        }
                        break;
                }

                return $this->redirectToUrl("/account/settings");
            }

            $settings = $this->instagramReaction->objInstagram->getCurrentUser();

            $this->view->set("title", "Kullanıcı Ayarları");
            $this->navigation->add("Hesap Ayarları", "/account/settings");

            return $this->view($settings);
        }

        function LogoutAction() {
            $this->logonPerson = new LogonPerson();

            return $this->redirectToUrl("/");
        }

        function LoadCommentsAction($id) {
            if(empty($id)) {
                return $this->notFound();
            }
            $media = $this->instagramReaction->objInstagram->getMediaInfo($id);
            if($media["status"] != "ok") {
                return $this->notFound();
            }
            $comments = $this->instagramReaction->objInstagram->getMediaComments($id);
            $likers   = $this->instagramReaction->objInstagram->getMediaLikers($id);

            return $this->partialView(array(
                                          "comments" => $comments,
                                          "likers"   => $likers,
                                          "media"    => $media
                                      ));
        }

        function AddCommentAction($id) {
            $comment = $this->request->data->yorum;
            if(!empty($comment)) {
                $this->instagramReaction->objInstagram->comment($id, $comment);
            }

            return $this->json(array("status" => "success"));
        }

        function DeleteCommentAction($id) {
            $mediaId = $this->request->data->mediaId;
            $this->instagramReaction->objInstagram->deleteComment($mediaId, $id);

            return $this->json(array("status" => "success"));
        }

    }