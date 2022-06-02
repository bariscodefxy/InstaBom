<?php

    namespace App\Controllers;

    use App\Libraries\InstagramReaction;
    use BulkReaction;
    use Wow;
    use Wow\Net\Response;

    class LiveController extends BaseController {

        /**
         * @var InstagramReaction $iReaction
         */
        private $instagramReaction;

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

            //Bayi kontrolü.
            if(($pass = $this->middleware("uye")) instanceof Response) {
                return $pass;
            }
            $this->instagramReaction = new InstagramReaction($this->logonPerson->member->uyeID);

        }

        function IndexAction() {
            $this->view->set("title", "Canlı Yayın Hilesi");

            return $this->view();
        }


        function SendLikeAction($id = NULL) {
        $this->view->set("title", "Canlı Yayın İzleme Gönder");

            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "findUserID":
                        $userData = $this->instagramReaction->objInstagram->getUserInfoByName($this->request->data->username);
                        if($userData["status"] != "ok") {
                            return $this->notFound();
                        } else {
                            $userID = $userData["user"]["pk"];
						    $getliveInfoByName = $this->instagramReaction->objInstagram->getliveInfoByName($userID);
							$yayinID = $getliveInfoByName["broadcast"]["id"];

                            return $this->redirectToUrl("/live/send-like/" . $userID);
                        }
                        break;
                    case "send":

                        if(intval($this->logonPerson->member->begeniKredi) <= 0) {
                            $sonuc = array(
                                "status"  => "error",
                                "code"    => "nocreditleft",
                                "message" => "Takipçi Eklenemedi. Takipçi krediniz kalmadı!",
                                "users"   => array()
                            );

                            return $this->json($sonuc);
                        }

                        $adet = intval($this->request->data->adet);
                        if($adet <= 0) {
                            $sonuc = array(
                                "status"  => "error",
                                "code"    => "nolimitdefined",
                                "message" => "Takipçi Eklenemedi. Adet tanımlanmadı!",
                                "users"   => array()
                            );

                            return $this->json($sonuc);
                        }

                        if($adet > $this->logonPerson->member->begeniKredi) {
                            $adet = $this->logonPerson->member->begeniKredi;
                        }

                        if($adet > Wow::get("ayar/bayiPaketBasiIstek")) {
                            $adet = Wow::get("ayar/bayiPaketBasiIstek");
                        }

                        $followedUsers = array();
                        if(!isset($_SESSION["FollowerssForInstaID" . $this->request->data->userID]) || !is_array($_SESSION["FollowerssForInstaID" . $this->request->data->userID])) {
                            $nextMaxID = NULL;
                            $intLoop   = 0;
                            while($follower = $this->instagramReaction->objInstagram->getUserFollowers($this->request->data->userID, $nextMaxID)) {
                                $intLoop++;
                                foreach($follower["users"] as $user) {
                                    $followedUsers[] = $user["pk"];
                                }
                                if(!isset($follower["next_max_id"]) || $intLoop >= 8) {
                                    break;
                                } else {
                                    $nextMaxID = $follower["next_max_id"];
                                }
                            }


                        } else {
                            $followedUsers = (array)$_SESSION["FollowerssForInstaID" . $this->request->data->userID];
                        }


                        $triedUserIDs = isset($_SESSION["TriedUsersForFollowInstaID" . $this->request->data->userID]) ? $_SESSION["TriedUsersForFollowInstaID" . $this->request->data->userID] : NULL;
                        if(empty($triedUserIDs)) {
                            $triedUserIDs = "0";
                        }
                        $userIDs = "0";
                        foreach(explode(",", $triedUserIDs) as $userID) {
                            if(intval($userID) > 0) {
                                $userIDs .= "," . intval($userID);
                            }
                        }

                        $instaIDs         = "0";
                        $followedInstaIDs = $followedUsers;
                        foreach($followedInstaIDs as $instaID) {
                            if(intval($instaID) > 0) {
                                $instaIDs .= "," . intval($instaID);
                            }
                        }

                        $users = $this->db->query("SELECT uyeID,instaID,kullaniciAdi,sifre,isWebCookie FROM uye WHERE isActive=1 AND canFollow=1 and isUsable=1 AND uyeID NOT IN($userIDs) AND instaID NOT IN($instaIDs) ORDER BY sonOlayTarihi ASC LIMIT :adet", array("adet" => $adet));


                        if(empty($users)) {
                            $sonuc = array(
                                "status"  => "error",
                                "code"    => "nouserleft",
                                "message" => "Takipçi Eklenemedi. Kullanıcı kalmadı!",
                                "users"   => array()
                            );

                            return $this->json($sonuc);
                        }

                        $allUserIDs = array_map(function($d) {
                            return $d["uyeID"];
                        }, $users);
                        $allUserIDs = implode(",", $allUserIDs);
                        $this->db->query("UPDATE uye SET sonOlayTarihi=NOW() WHERE uyeID IN (" . $allUserIDs . ")");
                        $this->db->CloseConnection();
                    	$yayinID = $getliveInfoByName["broadcast"]["id"];
                        $bulkReaction      = new BulkReaction($users, Wow::get("ayar/bayiEsZamanliIstek"));
                        $response          = $bulkReaction->livelike($this->request->data->userName);
                        $triedUsers        = $response["users"];
                        $totalSuccessCount = $response["totalSuccessCount"];
                        $allUserIDs        = array_map(function($d) {
                            return $d["userID"];
                        }, $triedUsers);
                        if(!empty($allUserIDs)) {
                            $allUserIDs                                                            = implode(",", $allUserIDs);
                            $userIDs                                                               .= "," . $allUserIDs;
                            $_SESSION["TriedUsersForFollowInstaID" . $this->request->data->userID] = $userIDs;
                        }
                        $allFailedUserIDs = array_filter(array_map(function($d) {
                            return $d["status"] == "fail" ? $d["userID"] : NULL;
                        }, $triedUsers), function($d) {
                            return $d !== NULL;
                        });
                        if(!empty($allFailedUserIDs)) {
                            $allFailedUserIDs = implode(",", $allFailedUserIDs);
                            $this->db->query("UPDATE uye SET canFollow=0,canFollowControlDate=NOW() WHERE uyeID IN (" . $allFailedUserIDs . ")");
                        }

                        $this->db->query("UPDATE uye SET begeniKredi=begeniKredi-:successCount WHERE uyeID=:uyeID", array(
                            "uyeID"        => $this->logonPerson->member->uyeID,
                            "successCount" => $totalSuccessCount
                        ));
                        $this->logonPerson->member->begeniKredi = intval($this->logonPerson->member->begeniKredi) - $totalSuccessCount;

                        $sonuc = array(
                            "status"     => "success",
                            "message"    => "Başarılı.",
                            "users"      => $triedUsers,
                            "begeniKredi" => $this->logonPerson->member->begeniKredi
                        );

                        return $this->json($sonuc);
                        break;
                }
            } //GET Method
            else {
                if(!is_null($id)) {
			      $user = $this->instagramReaction->objInstagram->getliveInfoByName($id);
                    if($user["broadcast"]["id"]) {

                    }else{
					return header("Location: /live/yayin-bulunamadi");

					}
                    $this->view->set("user", $user);
                }
            }

            $this->navigation->add("Takipçi Gönderimi", "/live/send-like");

            return $this->view();
        }


		  function YayinbulunamadiAction($id = NULL) {
            $this->view->set("title", "Canlı Yayın İzleme Gönder");



            $this->navigation->add("Takipçi Gönderimi", "/live/yayin-bulunanadi");

            return $this->view();
        }
		
		
        function SendViewAction($id = NULL) {
            $this->view->set("title", "Canlı Yayın İzleme Gönder");

            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "findUserID":
                        $userData = $this->instagramReaction->objInstagram->getUserInfoByName($this->request->data->username);
                        if($userData["status"] != "ok") {
                            return $this->notFound();
                        } else {
                            $userID = $userData["user"]["pk"];
						    $getliveInfoByName = $this->instagramReaction->objInstagram->getliveInfoByName($userID);
							$yayinID = $getliveInfoByName["broadcast"]["id"];

                            return $this->redirectToUrl("/live/send-view/" . $userID);
                        }
                        break;
                    case "send":

                        if(intval($this->logonPerson->member->begeniKredi) <= 0) {
                            $sonuc = array(
                                "status"  => "error",
                                "code"    => "nocreditleft",
                                "message" => "Takipçi Eklenemedi. Takipçi krediniz kalmadı!",
                                "users"   => array()
                            );

                            return $this->json($sonuc);
                        }

                        $adet = intval($this->request->data->adet);
                        if($adet <= 0) {
                            $sonuc = array(
                                "status"  => "error",
                                "code"    => "nolimitdefined",
                                "message" => "Takipçi Eklenemedi. Adet tanımlanmadı!",
                                "users"   => array()
                            );

                            return $this->json($sonuc);
                        }

                        if($adet > $this->logonPerson->member->begeniKredi) {
                            $adet = $this->logonPerson->member->begeniKredi;
                        }

                        if($adet > Wow::get("ayar/bayiPaketBasiIstek")) {
                            $adet = Wow::get("ayar/bayiPaketBasiIstek");
                        }

                        $followedUsers = array();
                        if(!isset($_SESSION["FollowerssForInstaID" . $this->request->data->userID]) || !is_array($_SESSION["FollowerssForInstaID" . $this->request->data->userID])) {
                            $nextMaxID = NULL;
                            $intLoop   = 0;
                            while($follower = $this->instagramReaction->objInstagram->getUserFollowers($this->request->data->userID, $nextMaxID)) {
                                $intLoop++;
                                foreach($follower["users"] as $user) {
                                    $followedUsers[] = $user["pk"];
                                }
                                if(!isset($follower["next_max_id"]) || $intLoop >= 8) {
                                    break;
                                } else {
                                    $nextMaxID = $follower["next_max_id"];
                                }
                            }


                        } else {
                            $followedUsers = (array)$_SESSION["FollowerssForInstaID" . $this->request->data->userID];
                        }


                        $triedUserIDs = isset($_SESSION["TriedUsersForFollowInstaID" . $this->request->data->userID]) ? $_SESSION["TriedUsersForFollowInstaID" . $this->request->data->userID] : NULL;
                        if(empty($triedUserIDs)) {
                            $triedUserIDs = "0";
                        }
                        $userIDs = "0";
                        foreach(explode(",", $triedUserIDs) as $userID) {
                            if(intval($userID) > 0) {
                                $userIDs .= "," . intval($userID);
                            }
                        }

                        $instaIDs         = "0";
                        $followedInstaIDs = $followedUsers;
                        foreach($followedInstaIDs as $instaID) {
                            if(intval($instaID) > 0) {
                                $instaIDs .= "," . intval($instaID);
                            }
                        }

                        $users = $this->db->query("SELECT uyeID,instaID,kullaniciAdi,sifre,isWebCookie FROM uye WHERE isActive=1 AND canFollow=1 and isUsable=1 AND uyeID NOT IN($userIDs) AND instaID NOT IN($instaIDs) ORDER BY sonOlayTarihi ASC LIMIT :adet", array("adet" => $adet));


                        if(empty($users)) {
                            $sonuc = array(
                                "status"  => "error",
                                "code"    => "nouserleft",
                                "message" => "Takipçi Eklenemedi. Kullanıcı kalmadı!",
                                "users"   => array()
                            );

                            return $this->json($sonuc);
                        }

                        $allUserIDs = array_map(function($d) {
                            return $d["uyeID"];
                        }, $users);
                        $allUserIDs = implode(",", $allUserIDs);
                        $this->db->query("UPDATE uye SET sonOlayTarihi=NOW() WHERE uyeID IN (" . $allUserIDs . ")");
                        $this->db->CloseConnection();
                    	$yayinID = $getliveInfoByName["broadcast"]["id"];
                        $bulkReaction      = new BulkReaction($users, Wow::get("ayar/bayiEsZamanliIstek"));
                        $response          = $bulkReaction->PlayLive($this->request->data->userID, $this->request->data->userName);
                        $triedUsers        = $response["users"];
                        $totalSuccessCount = $response["totalSuccessCount"];
                        $allUserIDs        = array_map(function($d) {
                            return $d["userID"];
                        }, $triedUsers);
                        if(!empty($allUserIDs)) {
                            $allUserIDs                                                            = implode(",", $allUserIDs);
                            $userIDs                                                               .= "," . $allUserIDs;
                            $_SESSION["TriedUsersForFollowInstaID" . $this->request->data->userID] = $userIDs;
                        }
                        $allFailedUserIDs = array_filter(array_map(function($d) {
                            return $d["status"] == "fail" ? $d["userID"] : NULL;
                        }, $triedUsers), function($d) {
                            return $d !== NULL;
                        });
                        if(!empty($allFailedUserIDs)) {
                            $allFailedUserIDs = implode(",", $allFailedUserIDs);
                            $this->db->query("UPDATE uye SET canFollow=0,canFollowControlDate=NOW() WHERE uyeID IN (" . $allFailedUserIDs . ")");
                        }

                        $this->db->query("UPDATE uye SET begeniKredi=begeniKredi-:successCount WHERE uyeID=:uyeID", array(
                            "uyeID"        => $this->logonPerson->member->uyeID,
                            "successCount" => $totalSuccessCount
                        ));
                        $this->logonPerson->member->begeniKredi = intval($this->logonPerson->member->begeniKredi) - $totalSuccessCount;

                        $sonuc = array(
                            "status"     => "success",
                            "message"    => "Başarılı.",
                            "users"      => $triedUsers,
                            "begeniKredi" => $this->logonPerson->member->begeniKredi
                        );

                        return $this->json($sonuc);
                        break;
                }
            } //GET Method
            else {
                if(!is_null($id)) {
			      $user = $this->instagramReaction->objInstagram->getliveInfoByName($id);
                    if($user["broadcast"]["id"]) {

                    }else{
					return header("Location: /live/yayin-bulunamadi");

					}
                    $this->view->set("user", $user);
                }
            }

            $this->navigation->add("Takipçi Gönderimi", "/live/send-view");

            return $this->view();
        }

     function SendCommentAction($id = NULL) {
            $this->view->set("title", "Canlı Yayın İzleme Gönder");

            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "findUserID":
                        $userData = $this->instagramReaction->objInstagram->getUserInfoByName($this->request->data->username);
                        if($userData["status"] != "ok") {
                            return $this->notFound();
                        } else {
                            $userID = $userData["user"]["pk"];
						    $getliveInfoByName = $this->instagramReaction->objInstagram->getliveInfoByName($userID);
							$yayinID = $getliveInfoByName["broadcast"]["id"];

                            return $this->redirectToUrl("/live/send-comment/" . $userID);
                        }
 break;
                    case "send":

                        if(intval($this->logonPerson->member->yorumKredi) <= 0) {
                            $sonuc = array(
                                "status"  => "error",
                                "code"    => "nocreditleft",
                                "message" => "Yorum Eklenemedi. Yorum krediniz kalmadı!",
                                "users"   => array()
                            );

                            return $this->json($sonuc);
                        }

                        $arrCommentg = $this->request->data->yorum;
                        if(!is_array($arrCommentg) || empty($arrCommentg)) {
                            $sonuc = array(
                                "status"  => "error",
                                "message" => "Yorum Eklenemedi. En az 1 yorum yazmalısınız!",
                                "userID"  => 0
                            );

                            return $this->json($sonuc);
                        }

                        $arrComment = [];
                        foreach($arrCommentg as $comment) {
                            if(!empty($comment)) {
                                $arrComment[] = trim($comment);
                            }
                        }
                        $commentedIndexes = $this->request->query->clearCommentedIndex == 1 ? [] : $_SESSION["CommentedIndexesForMediaID" . $this->request->data->mediaID];
                        if(!is_array($commentedIndexes) || empty($commentedIndexes)) {
                            $commentedIndexes = [];
                        }

                        $arrComment = array_diff_key($arrComment, $commentedIndexes);

                        if(empty($arrComment)) {
                            $sonuc = array(
                                "status"  => "error",
                                "message" => "Yorum Eklenemedi. En az 1 yorum yazmalısınız!",
                                "userID"  => 0
                            );

                            return $this->json($sonuc);
                        }

                        $adet = count($arrComment);

                        if($adet > $this->logonPerson->member->yorumKredi) {
                            $adet = $this->logonPerson->member->yorumKredi;
                        }

                        if($adet > Wow::get("ayar/bayiPaketBasiIstek")) {
                            $adet = Wow::get("ayar/bayiPaketBasiIstek");
                        }

                        $followedUsers = array();
                        if(!isset($_SESSION["FollowerssForInstaID" . $this->request->data->userID]) || !is_array($_SESSION["FollowerssForInstaID" . $this->request->data->userID])) {
                            $nextMaxID = NULL;
                            $intLoop   = 0;
                            while($follower = $this->instagramReaction->objInstagram->getUserFollowers($this->request->data->userID, $nextMaxID)) {
                                $intLoop++;
                                foreach($follower["users"] as $user) {
                                    $followedUsers[] = $user["pk"];
                                }
                                if(!isset($follower["next_max_id"]) || $intLoop >= 8) {
                                    break;
                                } else {
                                    $nextMaxID = $follower["next_max_id"];
                                }
                            }


                        } else {
                            $followedUsers = (array)$_SESSION["FollowerssForInstaID" . $this->request->data->userID];
                        }


                        $triedUserIDs = isset($_SESSION["TriedUsersForFollowInstaID" . $this->request->data->userID]) ? $_SESSION["TriedUsersForFollowInstaID" . $this->request->data->userID] : NULL;
                        if(empty($triedUserIDs)) {
                            $triedUserIDs = "0";
                        }
                        $userIDs = "0";
                        foreach(explode(",", $triedUserIDs) as $userID) {
                            if(intval($userID) > 0) {
                                $userIDs .= "," . intval($userID);
                            }
                        }

                        $instaIDs         = "0";
                        $followedInstaIDs = $followedUsers;
                        foreach($followedInstaIDs as $instaID) {
                            if(intval($instaID) > 0) {
                                $instaIDs .= "," . intval($instaID);
                            }
                        }

                        $users = $this->db->query("SELECT uyeID,instaID,kullaniciAdi,sifre,isWebCookie FROM uye WHERE isActive=1 AND canFollow=1 and isUsable=1 AND uyeID NOT IN($userIDs) AND instaID NOT IN($instaIDs) ORDER BY sonOlayTarihi ASC LIMIT :adet", array("adet" => $adet));


                        if(empty($users)) {
                            $sonuc = array(
                                "status"  => "error",
                                "code"    => "nouserleft",
                                "message" => "Takipçi Eklenemedi. Kullanıcı kalmadı!",
                                "users"   => array()
                            );

                            return $this->json($sonuc);
                        }

                        $allUserIDs = array_map(function($d) {
                            return $d["uyeID"];
                        }, $users);
                        $allUserIDs = implode(",", $allUserIDs);
                        $this->db->query("UPDATE uye SET sonOlayTarihi=NOW() WHERE uyeID IN (" . $allUserIDs . ")");
                        $this->db->CloseConnection();
                    	$yayinID = $getliveInfoByName["broadcast"]["id"];
                        $bulkReaction      = new BulkReaction($users, Wow::get("ayar/bayiEsZamanliIstek"));
                        $response          = $bulkReaction->livecomment($this->request->data->mediaID,$arrComment);
                        $triedUsers        = $response["users"];
                        $totalSuccessCount = $response["totalSuccessCount"];
                        $allUserIDs        = array_map(function($d) {
                            return $d["userID"];
                        }, $triedUsers);
                        if(!empty($allUserIDs)) {
                            $allUserIDs                                                            = implode(",", $allUserIDs);
                            $userIDs                                                               .= "," . $allUserIDs;
                            $_SESSION["TriedUsersForFollowInstaID" . $this->request->data->userID] = $userIDs;
                        }
                        $allFailedUserIDs = array_filter(array_map(function($d) {
                            return $d["status"] == "fail" ? $d["userID"] : NULL;
                        }, $triedUsers), function($d) {
                            return $d !== NULL;
                        });
                        if(!empty($allFailedUserIDs)) {
                            $allFailedUserIDs = implode(",", $allFailedUserIDs);
                            $this->db->query("UPDATE uye SET canFollow=0,canFollowControlDate=NOW() WHERE uyeID IN (" . $allFailedUserIDs . ")");
                        }

                        $this->db->query("UPDATE uye SET begeniKredi=begeniKredi-:successCount WHERE uyeID=:uyeID", array(
                            "uyeID"        => $this->logonPerson->member->uyeID,
                            "successCount" => $totalSuccessCount
                        ));
                        $this->logonPerson->member->begeniKredi = intval($this->logonPerson->member->begeniKredi) - $totalSuccessCount;

                        $sonuc = array(
                            "status"     => "success",
                            "message"    => "Başarılı.",
                            "users"      => $triedUsers,
                            "begeniKredi" => $this->logonPerson->member->begeniKredi
                        );

                        return $this->json($sonuc);
                        break;
                }
            } //GET Method
            else {
                if(!is_null($id)) {
			      $user = $this->instagramReaction->objInstagram->getliveInfoByName($id);
                    if($user["broadcast"]["id"]) {

                    }else{
					return header("Location: /live/yayin-bulunamadi");

					}
                    $this->view->set("user", $user);
                }
            }

            $this->navigation->add("Takipçi Gönderimi", "/live/send-comment");

            return $this->view();
        }


    }