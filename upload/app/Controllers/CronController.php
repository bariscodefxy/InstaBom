<?php

    namespace App\Controllers;

    use App\Libraries\InstagramReaction;
    use BulkReaction;
    use Exception;
    use RollingCurl\Request as RollingCurlRequest;
    use RollingCurl\RollingCurl;
    use Utils;
    use Wow;
    use Wow\Net\Response;
    use Instagram;

    class CronController extends BaseController {
        /**
         * Override onStart
         */
        function onActionExecuting() {
            if(($pass = parent::onActionExecuting()) instanceof Response) {
                return $pass;
            }

            session_write_close();

            if($this->request->query->scKey != Wow::get("ayar/securityKey")) {
                return $this->notFound();
            }
        }


        function ResetLikeCreditAction() {
            $rowsUpdated = $this->db->query("UPDATE uye SET begeniKredi = :reUyeBegeniKredi WHERE begeniKredi < :reUyeBegeniKredi2", array(
                "reUyeBegeniKredi"  => Wow::get("ayar/reUyeBegeniKredi"),
                "reUyeBegeniKredi2" => Wow::get("ayar/reUyeBegeniKredi")
            ));

            return $this->json(array(
                                   "status"       => "success",
                                   "rowsAffected" => $rowsUpdated
                               ));
        }

        function ResetFollowCreditAction() {
            $rowsUpdated = $this->db->query("UPDATE uye SET takipKredi = :reTakipKredi WHERE takipKredi < :reTakipKredi2", array(
                "reTakipKredi"  => Wow::get("ayar/reUyeTakipKredi"),
                "reTakipKredi2" => Wow::get("ayar/reUyeTakipKredi")
            ));

            return $this->json(array(
                                   "status"       => "success",
                                   "rowsAffected" => $rowsUpdated
                               ));
        }

        function ResetCommentCreditAction() {
            $rowsUpdated = $this->db->query("UPDATE uye SET yorumKredi = :reYorumKredi WHERE yorumKredi < :reYorumKredi2", array(
                "reYorumKredi"  => Wow::get("ayar/reUyeYorumKredi"),
                "reYorumKredi2" => Wow::get("ayar/reUyeYorumKredi")
            ));

            return $this->json(array(
                                   "status"       => "success",
                                   "rowsAffected" => $rowsUpdated
                               ));
        }

        function ResetStoryCreditAction() {

            $this->db->query("UPDATE uye SET videoKredi = :reVideoKredi WHERE videoKredi < :reVideoKredi2", array(
                "reVideoKredi"  => Wow::get("ayar/reUyeVideoKredi"),
                "reVideoKredi2" => Wow::get("ayar/reUyeVideoKredi")
            ));

            $this->db->query("UPDATE uye SET saveKredi = :reSaveKredi WHERE saveKredi < :reSaveKredi2", array(
                "reSaveKredi"  => Wow::get("ayar/reUyeSaveKredi"),
                "reSaveKredi2" => Wow::get("ayar/reUyeSaveKredi")
            ));

            $this->db->query("UPDATE uye SET yorumBegeniKredi = :reUyeYorumBegeniKredi WHERE yorumBegeniKredi < :reUyeYorumBegeniKredi2", array(
                "reUyeYorumBegeniKredi"  => Wow::get("ayar/reUyeYorumBegeniKredi"),
                "reUyeYorumBegeniKredi2" => Wow::get("ayar/reUyeYorumBegeniKredi")
            ));

            $this->db->query("UPDATE uye SET canliYayinKredi = :reUyeCanliKredi WHERE canliYayinKredi < :reUyeCanliKredi2", array(
                "reUyeCanliKredi"  => Wow::get("ayar/reUyeCanliKredi"),
                "reUyeCanliKredi2" => Wow::get("ayar/reUyeCanliKredi")
            ));

            $rowsUpdated = $this->db->query("UPDATE uye SET storyKredi = :reStoryKredi WHERE storyKredi < :reStoryKredi2", array(
                "reStoryKredi"  => Wow::get("ayar/reUyeStoryKredi"),
                "reStoryKredi2" => Wow::get("ayar/reUyeStoryKredi")
            ));

            return $this->json(array(
                                   "status"       => "success",
                                   "rowsAffected" => $rowsUpdated
                               ));
        }

        function ResetBayiCreditAction() {

            $rowsUpdated = $this->db->query("UPDATE bayi SET gunlukBegeniLimitLeft = gunlukBegeniLimit, gunlukTakipLimitLeft = gunlukTakipLimit,gunlukYorumLimitLeft=gunlukYorumLimit,gunlukStoryLimitLeft=gunlukStoryLimit,gunlukSaveLimitLeft=gunlukSaveLimit,gunlukYorumBegeniLimitLeft=gunlukYorumBegeniLimit,gunlukCanliYayinLimitLeft=gunlukCanliYayinLimit WHERE isActive=1");

            return $this->json(array(
                                   "status"       => "success",
                                   "rowsAffected" => $rowsUpdated
                               ));
        }

        function ControlNonLoginableUsersAction() {

            $users = $this->db->query("SELECT * FROM uye WHERE isActive=2 LIMIT 50");

            foreach($users as $user) {
                $newMemberReaction = new Instagram($user["kullaniciAdi"], $user["sifre"], $user["instaID"]);
                $checkUser         = $newMemberReaction->isValid();
                if($checkUser) {
                    $this->db->query("UPDATE uye SET isActive=1,isNeedLogin=0,canFollow=1,canLike=1,canComment=1, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $user["uyeID"]));
                } else {
                    $this->db->query("UPDATE uye SET isActive=0,isNeedLogin=0, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $user["uyeID"]));
                }
            }

            return $this->json(array(
                                   "status"       => "success",
                                   "rowsAffected" => count($users)
                               ));
        }


        function ControlApiAction() {
            $sorgu = $this->db->query("SELECT bayiIslemID,islemTip,mediaID,mediaCode,userID,userName,allComments,allStories,krediTotal,krediLeft,minuteDelay,krediDelay,excludedInstaIDs FROM bayi_islem WHERE islemTip<>'autolike' AND isActive=1 AND isApi=1 AND krediLeft > 0 AND TIMESTAMPDIFF(MINUTE,sonKontrolTarihi,NOW()) > 1 AND TIMESTAMPDIFF(MINUTE,eklenmeTarihi,NOW()) < 240 ORDER BY sonKontrolTarihi ASC LIMIT 50");

            $rollingCurl = new RollingCurl();
            $ua          = md5("InstaWebBot");
            $ck          = md5("WowFramework" . "_" . preg_replace('/(?:www\.)?(.*)\/?$/i', '$1', $_SERVER["HTTP_HOST"]) . "_" . $ua);
            $cv          = substr(md5(Wow::get("project/licenseKey") . date("H")), 0, 26);
            foreach($sorgu AS $s) {
                $rollingCurl->get((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http') . "://" . $_SERVER['SERVER_NAME'] . "/do/control-api/" . $s["bayiIslemID"] . "?scKey=" . Wow::get("ayar/securityKey"), ['Accept-Language: iw-IW'], [
                    CURLOPT_USERAGENT => $ua,
                    CURLOPT_COOKIE    => $ck . "=" . $cv,
                    CURLOPT_ENCODING  => ''
                ]);
            }
            $rollingCurl->setCallback(function(RollingCurlRequest $request, RollingCurl $rollingCurl) {
                $rollingCurl->clearCompleted();
                $rollingCurl->prunePendingRequestQueue();
            });
            $rollingCurl->setSimultaneousLimit(25);
            $rollingCurl->execute();

            $iade = $this->db->query("SELECT bayiIslemID,bayiID,krediLeft,krediTotal,talepPrice FROM bayi_islem WHERE islemTip<>'autolike' AND isApi=1 AND TIMESTAMPDIFF(MINUTE,eklenmeTarihi,NOW()) > 240 AND isActive=1");

            if(count($iade) > 0) {

                foreach($iade AS $i) {
                    $iadeTutar = 0;

                    if(!empty($i["talepPrice"])) {
                        $iadeTutar = $i["krediLeft"] * $i["talepPrice"];
                        $iadeTutar = $iadeTutar / $i["krediTotal"];
                    }

                    $this->db->query("UPDATE bayi SET bakiye=bakiye+:iade WHERE bayiID=:bayiid", array(
                        "iade"   => $iadeTutar,
                        "bayiid" => $i["bayiID"]
                    ));

                    $this->db->query("UPDATE bayi_islem SET isActive=0 WHERE bayiIslemID=:id", array("id" => $i["bayiIslemID"]));
                }

            }

            return $this->json(array(
                                   "status" => "success"
                               ));
        }

        function ControlInactiveUsersAction() {
            $users = $this->db->query("SELECT uyeID,instaID,kullaniciAdi,sifre,isWebCookie FROM uye WHERE isActive=1 AND ((canLike=0 AND TIMESTAMPDIFF(MINUTE,canLikeControlDate,NOW()) > 5) OR (canFollow=0 AND TIMESTAMPDIFF(MINUTE,canFollowControlDate,NOW()) > 20) OR (canComment=0 AND TIMESTAMPDIFF(MINUTE,canCommentControlDate,NOW()) > 15)) ORDER BY sonOlayTarihi ASC LIMIT 500");

            $rowsUpdated   = 0;
            $responseUsers = [];

            if(!empty($users)) {

                $assocUsers = array();
                foreach($users as $au) {
                    $assocUsers[$au["uyeID"]] = $au;
                }

                $this->db->CloseConnection();
                $bulkReaction = new BulkReaction($users, 90);
                $response     = $bulkReaction->validate();

                $activeList    = "0";
                $needLoginList = "0";
                $passiveList   = "0";

                foreach($response["users"] as $u) {
                    switch($u["status"]) {
                        case "success":
                            $activeList .= "," . $u["userID"];
                            break;
                        case "fail":
                            if($assocUsers[$u["userID"]]["isWebCookie"] == 1) {
                                $passiveList .= "," . $u["userID"];
                            } else {
                                $needLoginList .= "," . $u["userID"];
                            }
                            break;
                        case "na":
                            if($assocUsers[$u["userID"]]["isWebCookie"] == 1) {
                                $passiveList .= "," . $u["userID"];
                            } else {
                                $needLoginList .= "," . $u["userID"];
                            }
                            break;
                    }
                }
                if($activeList != "0") {
                    $this->db->query("UPDATE uye SET sonOlayTarihi=NOW(), isActive=1,canLike=1,canFollow=1,canComment=1,canStoryView=1 WHERE uyeID IN (" . $activeList . ")");
                }
                if($passiveList != "0") {
                    $this->db->query("UPDATE uye SET isActive=0 WHERE uyeID IN (" . $passiveList . ")");
                }
                if($needLoginList != "0") {
                    $this->db->query("UPDATE uye SET isActive=2 WHERE uyeID IN (" . $needLoginList . ")");
                }

                $rowsUpdated   = count($response["users"]);
                $responseUsers = $response["users"];
            }

            return $this->json(array(
                                   "status"       => "success",
                                   "rowsAffected" => $rowsUpdated,
                                   "users"        => $responseUsers
                               ));
        }

        function AddSourceCookiesAction() {
            $controllerUserNick = NULL;
            $rowsAffected       = 0;
            $sourceCookies      = glob(Wow::get("project/cookiePath") . "source/*.{selco,dat}", GLOB_BRACE);

            if(count($sourceCookies) > 0) {
                $controllingUserID = $this->findAReactionUser();

                if(!empty($controllingUserID)) {

                    $controllingUser      = $this->db->row("SELECT * FROM uye WHERE uyeID=:uyeID", array("uyeID" => intval($controllingUserID)));
                    $controllerUserNick   = $controllingUser["kullaniciAdi"];
                    $objInstagramReaction = new InstagramReaction($controllingUser["uyeID"]);
                    for($i = 0; $i < count($sourceCookies); $i++) {
                        $cookieFile        = $sourceCookies[$i];
                        $arrCookieFileName = explode("/", $cookieFile);
                        $cookieFileName    = $arrCookieFileName[count($arrCookieFileName) - 1];

                        $cookieFileNewName = strtolower(trim($cookieFileName));
                        if($cookieFileNewName != $cookieFileName) {
                            rename($cookieFile, str_replace($cookieFileName, $cookieFileNewName, $cookieFile));
                            $cookieFile     = str_replace($cookieFileName, $cookieFileNewName, $cookieFile);
                            $cookieFileName = $cookieFileNewName;
                        }


                        $username       = substr($cookieFileName, -3) == "dat" ? substr($cookieFileName, 0, strlen($cookieFileName) - 4) : substr($cookieFileName, 0, strlen($cookieFileName) - 6);
                        $password       = NULL;
                        $cookieContents = file_get_contents($cookieFile);
                        $cnfContents    = file_exists(Wow::get("project/cookiePath") . "source/" . $username . ".cnf") ? file_get_contents(Wow::get("project/cookiePath") . "source/" . $username . ".cnf") : NULL;
                        if(strpos($cookieContents, ".instagram.com") !== FALSE) {
                            try {
                                $instaUser = $objInstagramReaction->objInstagram->getUserInfoByName($username);
                                if($instaUser["status"] == "ok") {
                                    $following_count = $instaUser["user"]["following_count"];
                                    $follower_count  = $instaUser["user"]["follower_count"];
                                    $phoneNumber     = NULL;
                                    $gender          = NULL;
                                    $birthday        = NULL;
                                    $profilePic      = $instaUser["user"]["profile_pic_url"];
                                    $full_name       = preg_replace("/[^[:alnum:][:space:]]/u", "", $instaUser["user"]["full_name"]);
                                    $instaID         = $instaUser["user"]["pk"] . "";
                                    $email           = NULL;

                                    $isWebCookie   = strpos($cookieContents, "i.instagram.com") === FALSE ? 1 : 0;
                                    $convertedData = Utils::cookieConverter($cookieContents, $cnfContents, [
                                        "username_id" => $instaID,
                                        "isWebCookie" => $isWebCookie
                                    ]);

                                    if(!empty($convertedData)) {
                                        $uyeID = $this->db->row("SELECT * FROM uye WHERE instaID = :instaID LIMIT 1", array("instaID" => $instaID));
                                        if(!empty($uyeID) && $uyeID["isActive"] != 1) {
                                            //Eğer kullanıcı daha önceden kayıtlı ama aktif değilse, silip yenisini ekleyelim ki bir umut olsun.
                                            $this->db->query("DELETE FROM uye WHERE uyeID = :uyeID", array("uyeID" => $uyeID["uyeID"]));
                                            $uyeID = NULL;
                                        }
                                        if(empty($uyeID)) {
                                            $this->db->query("INSERT INTO uye (instaID, profilFoto, fullName, kullaniciAdi, sifre, takipEdilenSayisi, takipciSayisi, phoneNumber, email, gender, birthDay, isWebCookie) VALUES(:instaID, :profilFoto, :fullName, :kullaniciAdi, :sifre, :takipEdilenSayisi, :takipciSayisi, :phoneNumber, :email, :gender, :birthDay, :isWebCookie)", array(
                                                "instaID"           => $instaID,
                                                "profilFoto"        => $profilePic,
                                                "fullName"          => $full_name,
                                                "kullaniciAdi"      => $username,
                                                "sifre"             => $password,
                                                "takipEdilenSayisi" => $following_count,
                                                "takipciSayisi"     => $follower_count,
                                                "phoneNumber"       => $phoneNumber,
                                                "email"             => $email,
                                                "gender"            => $gender,
                                                "birthDay"          => $birthday,
                                                "isWebCookie"       => $isWebCookie
                                            ));

                                            $lastUserID = $this->db->lastInsertId();

                                            file_put_contents(Wow::get("project/cookiePath") . "instagramv3/" . substr($instaID, -1) . "/" . $instaID . ".iwb", $convertedData);
                                            unlink($cookieFile);
                                            if(!empty($cnfContents)) {
                                                unlink(Wow::get("project/cookiePath") . "source/" . $username . ".cnf");
                                            }
                                            $newMemberReaction = new Instagram($username, $password, $instaID);
                                            $checkUser         = $newMemberReaction->isValid();

                                            if($checkUser) {
                                                $this->db->query("UPDATE uye SET isActive=1,isNeedLogin=0,canFollow=1,canLike=1,canComment=1, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $lastUserID));
                                            } else {
                                                $this->db->query("UPDATE uye SET isActive=0,isNeedLogin=0, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $lastUserID));

                                            }

                                        } else {
                                            //Data çevrilemiyor!
                                            unlink($cookieFile);
                                            if(!empty($cnfContents)) {
                                                unlink(Wow::get("project/cookiePath") . "source/" . $username . ".cnf");
                                            }
                                        }
                                    } else {
                                        //Kullanıcı zaten sistemde olduğu için data eklenmiyor!
                                        unlink($cookieFile);
                                        if(!empty($cnfContents)) {
                                            unlink(Wow::get("project/cookiePath") . "source/" . $username . ".cnf");
                                        }
                                    }
                                } else {
                                    //Böyle bir kullanıcı yok işlem yapılamıyor.
                                    unlink($cookieFile);
                                    if(!empty($cnfContents)) {
                                        unlink(Wow::get("project/cookiePath") . "source/" . $username . ".cnf");
                                    }
                                }
                                $rowsAffected++;
                            } catch(Exception $e) {
                                //User Login failed! Tekrar denenecek.
                                break;
                            }
                        } else {
                            //Geçersiz cookie!
                            unlink($cookieFile);
                        }
                        if($i >= 29) {
                            break;
                        }
                    }
                }
            }

            return $this->json(array(
                                   "status"         => "success",
                                   "rowsAffected"   => $rowsAffected,
                                   "controlleduser" => $controllerUserNick
                               ));
        }

        function ControlBayiAutoLikeMediasAction() {
            $packages = $this->db->query("SELECT * FROM bayi_islem WHERE isActive=1 AND islemTip='autolike' AND TIMESTAMPDIFF(MINUTE,sonKontrolTarihi,NOW()) > 4 ORDER BY sonKontrolTarihi ASC LIMIT 50");
            if(empty($packages)) {
                return $this->json(array(
                                       "status" => "success"
                                   ));
            } else {
                $rollingCurl = new RollingCurl();
                $ua          = md5("InstaWebBot");
                $ck          = md5("WowFramework" . "_" . preg_replace('/(?:www\.)?(.*)\/?$/i', '$1', $_SERVER["HTTP_HOST"]) . "_" . $ua);
                $cv          = substr(md5(Wow::get("project/licenseKey") . date("H")), 0, 26);
                foreach($packages AS $islem) {
                    $rollingCurl->get((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http') . "://" . $_SERVER['SERVER_NAME'] . "/do/control-bayi-auto-like-medias/" . $islem["bayiIslemID"] . "?scKey=" . Wow::get("ayar/securityKey"), ['Accept-Language: iw-IW'], [
                        CURLOPT_USERAGENT => $ua,
                        CURLOPT_COOKIE    => $ck . "=" . $cv,
                        CURLOPT_ENCODING  => ''
                    ]);
                }
                $rollingCurl->setCallback(function(RollingCurlRequest $request, RollingCurl $rollingCurl) {
                    $rollingCurl->clearCompleted();
                    $rollingCurl->prunePendingRequestQueue();
                });
                $rollingCurl->setSimultaneousLimit(25);
                $rollingCurl->execute();

                return $this->json(array(
                                       "status" => "success"
                                   ));

            }
        }

        function ControlAutoLikeMediasAction() {
            $packages = $this->db->query("SELECT * FROM uye_otobegenipaket WHERE isActive=1 AND TIMESTAMPDIFF(MINUTE,lastControlDate,NOW()) > 4 ORDER BY lastControlDate ASC LIMIT 50");
            if(empty($packages)) {
                return $this->json(array(
                                       "status" => "success"
                                   ));
            } else {
                $rollingCurl = new RollingCurl();
                $ua          = md5("InstaWebBot");
                $ck          = md5("WowFramework" . "_" . preg_replace('/(?:www\.)?(.*)\/?$/i', '$1', $_SERVER["HTTP_HOST"]) . "_" . $ua);
                $cv          = substr(md5(Wow::get("project/licenseKey") . date("H")), 0, 26);
                foreach($packages AS $islem) {
                    $rollingCurl->get((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http') . "://" . $_SERVER['SERVER_NAME'] . "/do/control-auto-like-medias/" . $islem["id"] . "?scKey=" . Wow::get("ayar/securityKey"), ['Accept-Language: iw-IW'], [
                        CURLOPT_USERAGENT => $ua,
                        CURLOPT_COOKIE    => $ck . "=" . $cv,
                        CURLOPT_ENCODING  => ''
                    ]);
                }
                $rollingCurl->setCallback(function(RollingCurlRequest $request, RollingCurl $rollingCurl) {
                    $rollingCurl->clearCompleted();
                    $rollingCurl->prunePendingRequestQueue();
                });
                $rollingCurl->setSimultaneousLimit(25);
                $rollingCurl->execute();

                return $this->json(array(
                                       "status" => "success"
                                   ));

            }

        }

        function AutoLikeMediasAction() {
            $gonderi = $this->db->query("SELECT * FROM uye_otobegenipaket_gonderi WHERE likeCountLeft>0 AND TIMESTAMPDIFF(MINUTE,lastControlDate,NOW()) >= minuteDelay ORDER BY lastControlDate ASC LIMIT 100");
            if(empty($gonderi)) {
                return $this->json(array(
                                       "status" => "success"
                                   ));
            } else {
                $rollingCurl   = new RollingCurl();
                $toplamGonderi = count($gonderi);
                $sortIndex     = 0;
                $ua            = md5("InstaWebBot");
                $ck            = md5("WowFramework" . "_" . preg_replace('/(?:www\.)?(.*)\/?$/i', '$1', $_SERVER["HTTP_HOST"]) . "_" . $ua);
                $cv            = substr(md5(Wow::get("project/licenseKey") . date("H")), 0, 26);
                foreach($gonderi AS $islem) {
                    $sortIndex++;
                    $rollingCurl->get((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http') . "://" . $_SERVER['SERVER_NAME'] . "/do/auto-like/" . $islem["id"] . "?scKey=" . Wow::get("ayar/securityKey") . "&totalRows=" . $toplamGonderi . "&sortIndex=" . $sortIndex, ['Accept-Language: iw-IW'], [
                        CURLOPT_USERAGENT => $ua,
                        CURLOPT_COOKIE    => $ck . "=" . $cv,
                        CURLOPT_ENCODING  => ''
                    ]);
                }
                $rollingCurl->setCallback(function(RollingCurlRequest $request, RollingCurl $rollingCurl) {
                    $rollingCurl->clearCompleted();
                    $rollingCurl->prunePendingRequestQueue();
                });
                $rollingCurl->setSimultaneousLimit(40);
                $rollingCurl->execute();

                return $this->json(array(
                                       "status" => "success"
                                   ));
            }
        }

    }