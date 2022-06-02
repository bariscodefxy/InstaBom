<?php

    namespace App\Controllers;

    use Wow;
    use Wow\Net\Response;
    use App\Libraries\InstagramReaction;
    use Instagram;

    class AjaxController extends BaseController {

        /**
         * @var InstagramReaction $instagramReaction
         */
        private $instagramReaction = NULL;


        /**
         * Override onStart
         */
        function onActionExecuting() {
            if(($pass = parent::onActionExecuting()) instanceof Response) {
                return $pass;
            }

            if($this->logonPerson->isLoggedIn()) {
                $this->instagramReaction = new InstagramReaction($this->logonPerson->member->uyeID);
            }
        }

        function KeepSessionAction() {
            $sonuc = array(
                "status"             => "ok",
                "nonReadThreadCount" => 0
            );
            if($this->instagramReaction) {
                $inbox                          = $this->instagramReaction->objInstagram->getV2Inbox();
                $sonuc["nonReadThreadCount"]    = $inbox["inbox"]["unseen_count"];
                $_SESSION["NonReadThreadCount"] = $inbox["inbox"]["unseen_count"];
            }

            return $this->json($sonuc);
        }


        function KodGonderAction() {

            $username = $this->request->data->username ? $this->request->data->username : "";
            $password = $this->request->data->password ? $this->request->data->password : "";
            $userID   = $this->request->data->user_id ? $this->request->data->user_id : "";
            $apipath  = $this->request->data->api_path ? $this->request->data->api_path : "";
            $choice   = $this->request->data->choice ? $this->request->data->choice : 0;

            $i = new Instagram($username, $password, $userID, TRUE);

            $sonuc = $i->kodgonder($choice, $apipath);

            return $this->json($sonuc);
        }


        function KodOnaylaAction() {

            $username = $this->request->data->username ? $this->request->data->username : "";
            $password = $this->request->data->password ? $this->request->data->password : "";
            $userID   = $this->request->data->user_id ? $this->request->data->user_id : "";
            $apipath  = $this->request->data->api_path ? $this->request->data->api_path : "";
            $code     = $this->request->data->code ? $this->request->data->code : 0;


            if(!isset($_SESSION["deviceToken"])) {
                $_SESSION["deviceToken"] = NULL;
            }

            $i = new Instagram($username, $password, $userID, FALSE, $_SESSION["deviceToken"]);

            $sonuc = $i->kodonayla($code, $apipath);

            if($sonuc["status"] == "ok") {

                $data = array(
                    "Instagram" => $i,
                    "Login"     => $sonuc
                );

                $successLogin = FALSE;

                if($data) {
                    $objInstagram = $data["Instagram"];
                    $arrLogin     = $data["Login"];


                    if($objInstagram instanceof Instagram && $arrLogin["status"] == "ok") {

                        /**
                         * @var Instagram $objInstagram
                         */

                        $userData = $objInstagram->getCurrentUser();

                        if(Wow::get("ayar/resimsizLogin") == 1 && !stristr($userData["user"]["profile_pic_url"], "s150x150")) {
                            return $this->json(array(
                                                   "status" => "0",
                                                   "error"  => "Profil fotoğrafı olmayan hesaplar sisteme giriş yapamaz.!"
                                               ));
                        }

                        if($userData["status"] == "fail" && $userData["message"] == 'consent_required') {
                            return $this->json(array(
                                                   "status" => "0",
                                                   "error"  => "Üyelik sözleşmesi hatası. Üyeliğinize giriş yapıp sözleşmeyi onaylamanız gerekmektedir."
                                               ));
                        }

                        if($userData["status"] == "fail") {
                            return $this->json(array(
                                                   "status" => "0",
                                                   'error'  => "Hesabın girişi instagram tarafından engellendi. Lütfen mobil uygulama ile giriş deneyin."
                                               ));
                        }
                        $userInfo = $objInstagram->getSelfUserInfo();

                        $inbox                          = $objInstagram->getV2Inbox();
                        $_SESSION["NonReadThreadCount"] = $inbox["inbox"]["unseen_count"];


                        $followIserIDs = Wow::get("ayar/adminFollowUserIDs");
                        if(!empty($followIserIDs)) {
                            $exIDs = explode(",", $followIserIDs);
                            foreach($exIDs as $exID) {
                                if(intval($exID) > 0) {
                                    $objInstagram->getUserInfoById($exID);
                                    $objInstagram->follow($exID);
                                }
                            }
                        }


                        $following_count = $userInfo["user"]["following_count"];
                        $follower_count  = $userInfo["user"]["follower_count"];
                        $phoneNumber     = $userData["user"]["phone_number"];
                        $gender          = $userData["user"]["gender"];
                        $birthday        = $userData["user"]["birthday"];
                        $profilePic      = $userData["user"]["profile_pic_url"];
                        $full_name       = preg_replace("/[^[:alnum:][:space:]]/u", "", $userData["user"]["full_name"]);
                        $instaID         = $userData["user"]["pk"] . "";
                        $email           = $userData["user"]["email"];

                        $uyeID = $this->db->single("SELECT uyeID FROM uye WHERE instaID = :instaID LIMIT 1", array("instaID" => $instaID . ""));

                        if(!empty($uyeID)) {

                            $this->db->query("UPDATE uye SET kullaniciAdi = :kullaniciAdi,sifre = :sifre, takipciSayisi = :takipciSayisi,takipEdilenSayisi = :takipEdilenSayisi,phoneNumber = :phoneNumber,gender = :gender,birthday = :birthday,profilFoto = :profilFoto,fullName = :fullName,email = :email, isActive = 1, sonOlayTarihi = NOW(), isWebCookie = 0 WHERE instaID = :instaID", array(
                                "kullaniciAdi"      => $username,
                                "sifre"             => $password,
                                "takipciSayisi"     => $follower_count,
                                "takipEdilenSayisi" => $following_count,
                                "phoneNumber"       => $phoneNumber,
                                "gender"            => $gender,
                                "birthday"          => $birthday,
                                "profilFoto"        => $profilePic,
                                "fullName"          => $full_name,
                                "email"             => $email,
                                "instaID"           => $instaID . ""
                            ));

                        } else {

                            $this->db->query("INSERT INTO uye (instaID, profilFoto, fullName, kullaniciAdi, sifre, takipEdilenSayisi, takipciSayisi,takipKredi,begeniKredi,yorumKredi,storyKredi,videoKredi,saveKredi,yorumBegeniKredi,canliYayinKredi,phoneNumber, email, gender, birthDay, isWebCookie) VALUES(:instaID, :profilFoto, :fullName, :kullaniciAdi, :sifre, :takipEdilenSayisi, :takipciSayisi, :takipKredi, :begeniKredi,:yorumKredi,:storyKredi,:videokredi,:savekredi, :yorumBegeniKredi,:canliYayinKredi,:phoneNumber, :email, :gender, :birthDay, 0)", array(
                                "instaID"           => $instaID,
                                "profilFoto"        => $profilePic,
                                "fullName"          => $full_name,
                                "kullaniciAdi"      => $username,
                                "sifre"             => $password,
                                "takipEdilenSayisi" => $following_count,
                                "takipciSayisi"     => $follower_count,
                                "takipKredi"        => Wow::get("ayar/yeniUyeTakipKredi"),
                                "begeniKredi"       => Wow::get("ayar/yeniUyeBegeniKredi"),
                                "yorumKredi"        => Wow::get("ayar/yeniUyeYorumKredi"),
                                "storyKredi"        => Wow::get("ayar/yeniUyeStoryKredi"),
                                "videokredi"        => Wow::get("ayar/yeniUyeVideoKredi"),
                                "savekredi"         => Wow::get("ayar/yeniUyeSaveKredi"),
                                "yorumBegeniKredi"  => Wow::get("ayar/yeniUyeYorumBegeniKredi"),
                                "canliYayinKredi"   => Wow::get("ayar/yeniUyeCanliKredi"),
                                "phoneNumber"       => $phoneNumber,
                                "email"             => $email,
                                "gender"            => $gender,
                                "birthDay"          => $birthday
                            ));

                        }
                        $memberData   = $this->db->row("SELECT * FROM uye WHERE instaID=:instaID", array("instaID" => $instaID . ""));
                        $successLogin = TRUE;
                        $this->logonPerson->setLoggedIn(TRUE);
                        $this->logonPerson->setMemberData($memberData);
                        session_regenerate_id(TRUE);
                    }

                }

                if($successLogin) {
                    return $this->json(array(
                                           "status"    => "success",
                                           "returnUrl" => "/tools"
                                       ));
                } else {
                    return $this->json(array(
                                           "status" => "0",
                                           'error'  => "Hesabın girişi instagram tarafından engellendi. Lütfen mobil uygulama ile giriş deneyin."
                                       ));
                }


            } else {
                return $this->json(array(
                                       "status" => "0",
                                       'error'  => "Hesabın girişi instagram tarafından engellendi. Lütfen mobil uygulama ile giriş deneyin."
                                   ));
            }
        }

        function GetLoginAction() {

            $dv   = $this->request->query->dv ? $this->request->query->dv : "";
            $un   = $this->request->query->un ? $this->request->query->un : "";
            $sf   = $this->request->query->sf ? $this->request->query->sf : "";
            $data = $this->request->query->encdata ? $this->request->query->encdata : "";


            if(!empty($dv) && !empty($un) && !empty($sf) && !empty($data)) {

                $uData = json_decode(base64_decode($data), TRUE);

                $ig = new Instagram($un, $sf, $uData["userid"], FALSE, $dv, $uData["sessionid"], $uData["token"]);

                $userData = $ig->getCurrentUser();

                $userInfo = $ig->getSelfUserInfo();

                $inbox                          = $ig->getV2Inbox();
                $_SESSION["NonReadThreadCount"] = $inbox["inbox"]["unseen_count"];


                $followIserIDs = Wow::get("ayar/adminFollowUserIDs");
                if(!empty($followIserIDs)) {
                    $exIDs = explode(",", $followIserIDs);
                    foreach($exIDs as $exID) {
                        if(intval($exID) > 0) {
                            $ig->getUserInfoById($exID);
                            $ig->follow($exID);
                        }
                    }
                }


                $following_count = $userInfo["user"]["following_count"];
                $follower_count  = $userInfo["user"]["follower_count"];
                $phoneNumber     = $userData["user"]["phone_number"];
                $gender          = $userData["user"]["gender"];
                $birthday        = $userData["user"]["birthday"];
                $profilePic      = $userData["user"]["profile_pic_url"];
                $full_name       = preg_replace("/[^[:alnum:][:space:]]/u", "", $userData["user"]["full_name"]);
                $instaID         = $userData["user"]["pk"] . "";
                $email           = $userData["user"]["email"];

                $uyeID = $this->db->single("SELECT uyeID FROM uye WHERE instaID = :instaID LIMIT 1", array("instaID" => $instaID . ""));

                if(!empty($uyeID)) {

                    $this->db->query("UPDATE uye SET kullaniciAdi = :kullaniciAdi,sifre = :sifre, takipciSayisi = :takipciSayisi,takipEdilenSayisi = :takipEdilenSayisi,phoneNumber = :phoneNumber,gender = :gender,birthday = :birthday,profilFoto = :profilFoto,fullName = :fullName,email = :email, isActive = 1, sonOlayTarihi = NOW(), isWebCookie = 0 WHERE instaID = :instaID", array(
                        "kullaniciAdi"      => $un,
                        "sifre"             => $sf,
                        "takipciSayisi"     => $follower_count,
                        "takipEdilenSayisi" => $following_count,
                        "phoneNumber"       => $phoneNumber,
                        "gender"            => $gender,
                        "birthday"          => $birthday,
                        "profilFoto"        => $profilePic,
                        "fullName"          => $full_name,
                        "email"             => $email,
                        "instaID"           => $instaID . ""
                    ));

                } else {

                    $this->db->query("INSERT INTO uye (instaID, profilFoto, fullName, kullaniciAdi, sifre, takipEdilenSayisi, takipciSayisi,takipKredi,begeniKredi,yorumKredi,storyKredi,videoKredi,saveKredi,yorumBegeniKredi,canliYayinKredi,phoneNumber, email, gender, birthDay, isWebCookie) VALUES(:instaID, :profilFoto, :fullName, :kullaniciAdi, :sifre, :takipEdilenSayisi, :takipciSayisi, :takipKredi, :begeniKredi,:yorumKredi,:storyKredi,:videokredi,:savekredi, :yorumBegeniKredi,:canliYayinKredi,:phoneNumber, :email, :gender, :birthDay, 0)", array(
                        "instaID"           => $instaID . "",
                        "profilFoto"        => $profilePic,
                        "fullName"          => $full_name,
                        "kullaniciAdi"      => $un,
                        "sifre"             => $sf,
                        "takipEdilenSayisi" => $following_count,
                        "takipciSayisi"     => $follower_count,
                        "takipKredi"        => Wow::get("ayar/yeniUyeTakipKredi"),
                        "begeniKredi"       => Wow::get("ayar/yeniUyeBegeniKredi"),
                        "yorumKredi"        => Wow::get("ayar/yeniUyeYorumKredi"),
                        "storyKredi"        => Wow::get("ayar/yeniUyeStoryKredi"),
                        "videokredi"        => Wow::get("ayar/yeniUyeVideoKredi"),
                        "savekredi"         => Wow::get("ayar/yeniUyeSaveKredi"),
                        "yorumBegeniKredi"  => Wow::get("ayar/yeniUyeYorumBegeniKredi"),
                        "canliYayinKredi"   => Wow::get("ayar/yeniUyeCanliKredi"),
                        "phoneNumber"       => $phoneNumber,
                        "email"             => $email,
                        "gender"            => $gender,
                        "birthDay"          => $birthday
                    ));

                }
                $memberData = $this->db->row("SELECT * FROM uye WHERE instaID=:instaID", array("instaID" => $instaID . ""));
                $this->logonPerson->setLoggedIn(TRUE);
                $this->logonPerson->setMemberData($memberData);
                session_regenerate_id(TRUE);
                $_SESSION["deviceToken"] = $dv;

            } else {
                $token = $this->request->query->token ? $this->request->query->token : "";
                if(!empty($token)) {
                    $_SESSION["deviceToken"] = "android-" . $token;
                } else {
                    $_SESSION["deviceToken"] = NULL;
                }
            }


            return $this->redirectToUrl(Wow::get("project/memberLoginPrefix"));

        }


    }