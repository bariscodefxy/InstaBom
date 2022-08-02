<?php

    namespace App\Controllers;

    use App\Libraries\InstagramReaction;
    use Exception;
    use Wow;
    use Wow\Net\Response;
    use Instagram;

    class MemberController extends BaseController {

        function onActionExecuting() {
            $actionResponse = parent::onActionExecuting();
            if($actionResponse instanceof Response) {
                return $actionResponse;
            }

            if(Wow::get("project/memberLoginPrefix") != "/member" && $this->route->defaults["controller"] == "Home") {
                return $this->notFound();
            }

            if($this->logonPerson->isLoggedIn()) {
                return $this->redirectToUrl("/tools");
            }
        }

        function IndexAction() {
            //Geri dönüş için mevcut bir url varsa bunu not edelim.
            if(!is_null($this->request->query->returnUrl)) {
                $_SESSION["ReturnUrl"] = $this->request->query->returnUrl;
            }

            if($this->request->method == "POST") {

                if($this->request->data->antiForgeryToken !== $_SESSION["AntiForgeryToken"]) {
                    return $this->notFound();
                }

                $username = strtolower(trim($this->request->data->username));
                $password = trim($this->request->data->password);

                if(empty($username) || empty($password)) {
                    return $this->json(array(
                                           "status" => "0",
                                           "error"  => "Üzgünüz, galiba kullanıcı adınızı veya şifrenizi girmeyi unuttunuz."
                                       ));
                }

                if(!preg_match('/^[a-zA-Z0-9._]+$/', $username)) {
                    return $this->json(array(
                                           "status" => "0",
                                           "error"  => "Üzgünüz, düzgün bir kullanıcı adı giriniz."
                                       ));
                }


                if(!empty(Wow::get("ayar/GoogleCaptchaSiteKey")) && !empty(Wow::get("ayar/GoogleCaptchaSecretKey"))) {
                    $url             = 'https://www.google.com/recaptcha/api/siteverify';
                    $data            = array(
                        'secret'   => Wow::get("ayar/GoogleCaptchaSecretKey"),
                        'response' => $_POST["captcha"]
                    );
                    $options         = array(
                        'http' => array(
                            'method'  => 'POST',
                            'content' => http_build_query($data)
                        )
                    );
                    $context         = stream_context_create($options);
                    $verify          = file_get_contents($url, FALSE, $context);
                    $captcha_success = json_decode($verify);
                    if($captcha_success->success == FALSE) {
                        return $this->json(array(
                                               "status" => "0",
                                               "error"  => "Güvenlik doğrulamasını geçmen gerekiyor."
                                           ));
                    }
                }

                $data         = $this->instaLogin($username, $password);
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
//                            $this->db->query("DELETE FROM uye WHERE kullaniciAdi=:kullaniciadi", array("kullaniciadi" => $username));
                            return $this->json(array(
                                                   "status" => "0",
                                                   "error"  => "Üyelik sözleşmesi hatası. Üyeliğinize giriş yapıp sözleşmeyi onaylamanız gerekmektedir."
                                               ));
                        }

                        if($userData["status"] == "fail") {
                            return $this->json(array(
                                                   "status" => "0",
                                                   "error"  => "Üzgünüz, Instagram taraflı bir sorun oluştu."
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

                        $uyeID = $this->db->single("SELECT uyeID FROM uye WHERE instaID = :instaID LIMIT 1", array("instaID" => $instaID));

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
                                "instaID"           => $instaID
                            ));

                        } else {
                            $this->db->query("INSERT INTO uye (instaID, profilFoto, fullName, kullaniciAdi, sifre, takipEdilenSayisi, takipciSayisi,takipKredi,begeniKredi,yorumKredi,storyKredi,VideoKredi,SaveKredi, phoneNumber, email, gender, birthDay, isWebCookie) VALUES(:instaID, :profilFoto, :fullName, :kullaniciAdi, :sifre, :takipEdilenSayisi, :takipciSayisi, :takipKredi, :begeniKredi,:yorumKredi,:storyKredi,:videokredi,:savekredi, :phoneNumber, :email, :gender, :birthDay, 0)", array(
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
                                "phoneNumber"       => $phoneNumber,
                                "email"             => $email,
                                "gender"            => $gender,
                                "birthDay"          => $birthday
                            ));
                        }
                        $memberData   = $this->db->row("SELECT * FROM uye WHERE instaID=:instaID", array("instaID" => $instaID));
                        $successLogin = TRUE;
                        $this->logonPerson->setLoggedIn(TRUE);
                        $this->logonPerson->setMemberData($memberData);
                        session_regenerate_id(TRUE);
                    }
                }else {
					return $this->json(array(
                                           "status" => "0",
                                           "error"  => "Veriniz alınırken bir sorun oluştu."
                                       ));
				}
                if($successLogin) {
                    $returnUrl = "/tools";
                    if(isset($_SESSION["ReturnUrl"])) {
                        $returnUrl = $_SESSION["ReturnUrl"];
                        unset($_SESSION["ReturnUrl"]);
                    }

                    return $this->json(array(
                                           "status"    => "success",
                                           "returnUrl" => $returnUrl
                                       ));
                } else {
                    return $this->json(array(
                                           "status" => "0",
                                           "error"  => "Üzgünüz, girişinizi yapamadık."
                                       ));
                }

            }

            $_SESSION["AntiForgeryToken"] = md5(uniqid(mt_rand(), TRUE));

            return $this->partialView();
        }

        private function instaLogin($username, $password) {

            $userID     = NULL;
            $forceLogin = TRUE;

            $reactionUserID = $this->findAReactionUser();
            if(!empty($reactionUserID)) {
                $objInstagramReaction = new InstagramReaction($reactionUserID);
                $userData             = $objInstagramReaction->objInstagram->getUserInfoByName($username);

                if($userData["status"] != "ok") {
                    return FALSE;
                } else {
                    $userID = $userData["user"]["pk"];
                    $banned = array();

                    if(Wow::get("ayar/bannedUserIDs") != "") {
                        $banned = explode(",", Wow::get("ayar/bannedUserIDs"));
                    }

                    if(in_array($userID, $banned)) {
                        return FALSE;
                    }
                }

                $findUser = $this->db->row("SELECT * FROM uye WHERE instaID=:instaID AND sifre=:password", array(
                    "instaID"  => $userID,
                    "password" => $password
                ));

                if(!empty($findUser) && $findUser["isWebCookie"] == 0) {
                    $forceLogin = FALSE;
                }
            }

            try {
                $i = new Instagram($username, $password, $userID, TRUE);
                $l = $i->login($forceLogin);

                return array(
                    "Instagram" => $i,
                    "Login"     => $l
                );
            } catch(Exception $e) {
                return FALSE;
            }
        }

    }