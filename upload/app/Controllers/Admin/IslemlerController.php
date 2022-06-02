<?php

    namespace App\Controllers\Admin;

    use App\Libraries\InstagramReaction;
    use Exception;
    use Wow;
    use Wow\Net\Response;
    use Instagram;
    use InstagramAPI\Exception\InstagramException;

    class IslemlerController extends BaseController {

        function onActionExecuting() {
            if(($actionResponse = parent::onActionExecuting()) instanceof Response) {
                return $actionResponse;
            }
            //Üye girişi kontrolü.
            if(($pass = $this->middleware("logged")) instanceof Response) {
                return $pass;
            }
        }

        function IndexAction() {
            $this->navigation->add("İşlemler", Wow::get("project/adminPrefix") . "/islemler");
            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "removePassiveUsers":
                        $passiveUsers = $this->db->query("SELECT instaID,kullaniciAdi FROM uye WHERE isActive=0");
                        foreach($passiveUsers as $pUser) {
                            $filePath = Wow::get("project/cookiePath") . "instagramv3/" . substr($pUser["instaID"], -1) . "/" . $pUser["instaID"] . ".iwb";
                            if(file_exists($filePath)) {
                                unlink($filePath);
                            }
                        }
                        $this->db->query("DELETE FROM uye WHERE isActive=0");

                        return $this->json("ok");
                        break;

                    case "removeNAUsers":
                        $passiveUsers = $this->db->query("SELECT instaID,kullaniciAdi FROM uye WHERE isActive=99");
                        foreach($passiveUsers as $pUser) {
                            $filePath = Wow::get("project/cookiePath") . "instagramv3/" . substr($pUser["instaID"], -1) . "/" . $pUser["instaID"] . ".iwb";
                            if(file_exists($filePath)) {
                                unlink($filePath);
                            }
                        }
                        $this->db->query("DELETE FROM uye WHERE isActive=99");

                        return $this->json("ok");
                        break;
                        
                        case "removeNaturalPassiveUsers":
                        $passiveUsers = $this->db->query("SELECT instaID,kullaniciAdi FROM uye WHERE isActive=31");
                        foreach($passiveUsers as $pUser) {
                            $filePath = Wow::get("project/cookiePath") . "instagramv3/" . substr($pUser["instaID"], -1) . "/" . $pUser["instaID"] . ".iwb";
                            if(file_exists($filePath)) {
                                unlink($filePath);
                            }
                        }
                        $this->db->query("DELETE FROM uye WHERE isActive=31");

                        return $this->json("ok");
                        break;
                }
            }

            $data                      = array();
            $data["countNaturalPassiveUsers"] = $this->db->single("SELECT COUNT(*) FROM uye WHERE isActive=31");
            $data["countPassiveUsers"] = $this->db->single("SELECT COUNT(*) FROM uye WHERE isActive=0");
            $data["countNaUsers"]      = $this->db->single("SELECT COUNT(*) FROM uye WHERE isActive=99");
            $data["listOnlyNaUsers"]       = $this->db->query("SELECT CONCAT(kullaniciAdi,':',sifre) AS userpass FROM uye WHERE isActive=99 ORDER BY uyeID DESC");
            $data["listOnlyPasifUsers"]       = $this->db->query("SELECT CONCAT(kullaniciAdi,':',sifre) AS userpass FROM uye WHERE isActive=0 ORDER BY uyeID DESC");
            $data["listNaturalPasifList"]       = $this->db->query("SELECT CONCAT(kullaniciAdi,':',sifre) AS userpass FROM uye WHERE isActive=31 ORDER BY uyeID DESC");

            return $this->view($data);
        }


        function replace4byte($string, $replacement = '') {
            return preg_replace('%(?:
          \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
        | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
        | \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
    )%xs', $replacement, $string);
        }

        function AddUserPassAction() {
            session_write_close();
            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "addUserPass":
                        $userpass    = $this->request->data->userpass;
                        $expUserpass = explode(":", $userpass);
                        $sonuc       = array(
                            "status"   => "error",
                            "message"  => "user:password hatalı gönderildi",
                            "instaID"  => "0",
                            "userNick" => ""
                        );
                        if(count($expUserpass) > 1) {
                            $username   = strtolower(trim($expUserpass[0]));
                            $password   = trim($expUserpass[1]);
                            $deviceID   = isset($expUserpass[2]) ? trim($expUserpass[2]) : FALSE;
                            $sessiondID = isset($expUserpass[3]) ? trim($expUserpass[3]) : FALSE;
                            $userID     = NULL;


                            try {
                                $reactionUserID       = $this->findAReactionUser();
                                $objInstagramReaction = new InstagramReaction($reactionUserID);
                            } catch(Exception $e) {
                                try {
                                    $reactionUserID       = $this->findAReactionUser();
                                    $objInstagramReaction = new InstagramReaction($reactionUserID);
                                } catch(Exception $e) {
                                    $reactionUserID = $this->findAReactionUser();
                                    try {
                                        $objInstagramReaction = new InstagramReaction($reactionUserID);
                                    } catch(Exception $e) {
                                        $sonuc = array(
                                            "status"   => "error",
                                            "message"  => "Login başarısız.",
                                            "instaID"  => "0",
                                            "userNick" => $username
                                        );

                                        return $this->json($sonuc);
                                    }

                                }

                            }

                            try {
                                $userData = $objInstagramReaction->objInstagram->getUserInfoByName($username);
                            } catch(Exception $e) {
                                try {
                                    $userData = $objInstagramReaction->objInstagram->getUserInfoByName($username);
                                } catch(Exception $e) {
                                    $userData = $objInstagramReaction->objInstagram->getUserInfoByName($username);
                                }

                            }


                            if($userData["status"] != "ok") {
                                $sonuc = array(
                                    "status"   => "error",
                                    "message"  => "Kullanıcı bulunamadı!",
                                    "instaID"  => "0",
                                    "userNick" => $username
                                );
                            } else {
                                $userID = $userData["user"]["pk"] . "";

                                try {
                                    $i = new Instagram($username, $password, $userID, FALSE, $deviceID, $sessiondID);
                                    $l = $i->login(TRUE);
                                } catch(Exception $e) {
                                    try {
                                        $i = new Instagram($username, $password, $userID, FALSE, $deviceID, $sessiondID);
                                        $l = $i->login(TRUE);
                                    } catch(Exception $e) {
                                        $sonuc = array(
                                            "status"   => "error",
                                            "message"  => "Login başarısız.",
                                            "instaID"  => "0",
                                            "userNick" => $username
                                        );

                                        return $this->json($sonuc);
                                    }
                                }

                                if($l["status"] == "ok" && !isset($l["step_name"])) {

                                    $userData = $i->getCurrentUser();
                                    $userInfo = $i->getSelfUserInfo();

                                    $following_count = $userInfo["user"]["following_count"];
                                    $follower_count  = $userInfo["user"]["follower_count"];
                                    $phoneNumber     = $userData["user"]["phone_number"];
                                    $gender          = $userData["user"]["gender"];
                                    $birthday        = $userData["user"]["birthday"];
                                    $profilePic      = $userData["user"]["profile_pic_url"];
                                    $full_name       = self::replace4byte(preg_replace("/[^[:alnum:][:space:]]/u", "", $userData["user"]["full_name"]));
                                    $instaID         = $userData["user"]["pk"] . "";
                                    $email           = $userData["user"]["email"];

                                    $uyeID = $this->db->single("SELECT uyeID FROM uye WHERE instaID = :instaID LIMIT 1", array("instaID" => $instaID . ""));

                                    if(empty($uyeID)) {

                                        $this->db->query("INSERT INTO uye (instaID, profilFoto, fullName, kullaniciAdi, sifre, takipEdilenSayisi, takipciSayisi, phoneNumber, email, gender, birthDay) VALUES(:instaID, :profilFoto, :fullName, :kullaniciAdi, :sifre, :takipEdilenSayisi, :takipciSayisi, :phoneNumber, :email, :gender, :birthDay)", array(
                                            "instaID"           => $instaID . "",
                                            "profilFoto"        => $profilePic,
                                            "fullName"          => $full_name,
                                            "kullaniciAdi"      => $username,
                                            "sifre"             => $password,
                                            "takipEdilenSayisi" => $following_count,
                                            "takipciSayisi"     => $follower_count,
                                            "phoneNumber"       => $phoneNumber,
                                            "email"             => $email,
                                            "gender"            => isset($gender) ? $gender : "0",
                                            "birthDay"          => isset($birthday) ? $birthday : " "
                                        ));
                                        $sonuc = array(
                                            "status"   => "success",
                                            "message"  => "Kullanıcı Eklendi",
                                            "instaID"  => $instaID,
                                            "userNick" => $username
                                        );

                                    } else {

                                        $this->db->query("UPDATE uye SET takipciSayisi = :takipciSayisi,takipEdilenSayisi = :takipEdilenSayisi,profilFoto = :profilFoto,fullName = :fullName, isActive=1, isWebCookie=0 WHERE instaID = :instaID", array(
                                            "takipciSayisi"     => $follower_count,
                                            "takipEdilenSayisi" => $following_count,
                                            "profilFoto"        => $profilePic,
                                            "fullName"          => $full_name,
                                            "instaID"           => $instaID . ""
                                        ));

                                        $sonuc = array(
                                            "status"   => "success",
                                            "message"  => "Login Yenilendi. Kullanıcı Aktifleştirildi.",
                                            "instaID"  => $instaID,
                                            "userNick" => $username,
                                            "userData" => $userData
                                        );
                                    }


                                } else {

                                    if(isset($l["step_name"])) {
                                        $sonuc = array(
                                            "status"   => "error",
                                            "message"  => "Onaya Düşmüş Hesap!",
                                            "instaID"  => "0",
                                            "userNick" => $username
                                        );
                                    } else {
                                        $sonuc = array(
                                            "status"   => "error",
                                            "message"  => "Kullanıcı bulunamadı!",
                                            "instaID"  => "0",
                                            "userNick" => $username,
                                            "hata"     => $l
                                        );
                                    }

                                }

                            }
                        }

                        return $this->json($sonuc);
                        break;
                }
            }

            return $this->view();
        }

        function AddCookiesAction() {
            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "uploadCookies":
                        $uploads_dir = Wow::get("project/cookiePath") . "source/";
                        foreach($this->request->files->files["error"] as $key => $error) {
                            if($error == UPLOAD_ERR_OK) {
                                $tmp_name     = $this->request->files->files["tmp_name"][$key];
                                $name         = $this->request->files->files["name"][$key];
                                $splittedName = explode(".", $name);
                                if(count($splittedName) > 1) {
                                    $extension = $splittedName[count($splittedName) - 1];
                                    if($extension == "selco" || $extension == "dat" || $extension == "cnf") {
                                        move_uploaded_file($tmp_name, $uploads_dir . strtolower($name));
                                    }
                                }
                            }
                        }

                        return $this->json("ok");
                        break;
                }
            }
            $data                       = array();
            $sourceCookies              = glob(Wow::get("project/cookiePath") . "source/*.{selco,dat}", GLOB_BRACE);
            $data["countSourceCookies"] = count($sourceCookies);

            return $this->view($data);
        }


        function CinsiyetTespitAction() {
            if($this->request->method == "POST") {
                $lastUserID    = intval($this->request->data->lastUserID);
                $lastUserIDNew = NULL;
                $uUsers        = $this->db->query("SELECT uyeID,SUBSTRING_INDEX(SUBSTRING_INDEX(fullName, ' ', 1), ' ', -1) AS ad FROM uye WHERE SUBSTRING_INDEX(SUBSTRING_INDEX(fullName, ' ', 1), ' ', -1)<>'' AND (gender IS NULL OR gender=3) AND uyeID>:uyeID LIMIT 80", array("uyeID" => $lastUserID));
                foreach($uUsers as $u) {
                    $lastUserIDNew = $u["uyeID"];
                    $fIsim         = $this->db->row("SELECT * FROM isimler WHERE isimler=:isimler AND cinsiyet<>'U'", array("isimler" => $u["ad"]));
                    if(!empty($fIsim)) {
                        $fGender = $fIsim["cinsiyet"] == "K" ? 2 : 1;
                        $this->db->query("UPDATE uye SET gender = :gender WHERE uyeID=:uyeID", array(
                            "uyeID"  => $u["uyeID"],
                            "gender" => $fGender
                        ));
                    }
                }
                $lastUserID  = $lastUserIDNew;
                $isCompleted = 0;
                if(empty($lastUserIDNew)) {
                    $lastUserID  = 0;
                    $isCompleted = 1;
                }

                $data = [
                    "lastUserID"  => $lastUserID,
                    "isCompleted" => $isCompleted
                ];

                return $this->json($data);
            }

            $data = $this->db->query("SELECT gender,COUNT(uyeID) 'toplamSayi' FROM uye GROUP BY gender ORDER BY toplamSayi DESC");

            return $this->view($data);
        }


    }