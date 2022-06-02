<?php

    namespace App\Controllers;

    use Wow;
    use RollingCurl\Request as RollingCurlRequest;
    use RollingCurl\RollingCurl;

    class LoginController extends BaseController {


        public function IndexAction() {

            $oAuth = isset($_SERVER["HTTP_AUTH"]) && !empty($_SERVER["HTTP_AUTH"]) && $_SERVER["HTTP_AUTH"] != "undefined" ? $_SERVER["HTTP_AUTH"] : "";

            $deviceID = isset($_SERVER["HTTP_DEVICEID"]) ? $_SERVER["HTTP_DEVICEID"] : "";

            if(empty($deviceID) || empty($oAuth)) {
                return $this->json(array(
                                       "status" => 0,
                                       "error"  => "Sistemsel bir hata oluştu lütfen tekrar deneyiniz."
                                   ));
            }

            $data = array("status" => 0);

            $userData = json_decode($_REQUEST["userData"], TRUE);

            if(isset($userData["status"]) && $userData["status"] == "ok") {

                $csrfToken      = $this->request->data->csrfToken ? $this->request->data->csrfToken : "";
                $deviceID       = $this->request->data->deviceID ? $this->request->data->deviceID : "";
                $password       = $this->request->data->password ? $this->request->data->password : "";
                $phoneID        = $this->request->data->phoneID ? $this->request->data->phoneID : "";
                $userAgent      = $this->request->data->userAgent ? $this->request->data->userAgent : "";
                $headers        = $this->request->data->headers ? urldecode($this->request->data->headers) : "";
                $data["status"] = 0;
                if(!empty($csrfToken) && !empty($deviceID) && !empty($password) && !empty($phoneID) && !empty($userAgent)) {

                    $instaID = trim($userData["logged_in_user"]["pk"] . "");

                    $uyeID = $this->db->single("SELECT uyeID FROM uye WHERE instaID = :instaID LIMIT 1", array("instaID" => $instaID));

                    $instamisToken = md5(uniqid(mt_rand(), TRUE));

                    if(!empty($uyeID)) {

                        $this->db->query("UPDATE uye SET profilFoto=:profilFoto,fullName=:fullName,kullaniciAdi=:kullaniciAdi,sifre=:sifre,takipEdilenSayisi=:takipEdilenSayisi,takipciSayisi=:takipciSayisi,takipKredi=:takipKredi,begeniKredi=:begeniKredi,yorumKredi=:yorumKredi,storyKredi=:storyKredi,videoKredi=:videoKredi,saveKredi=:saveKredi,yorumBegeniKredi=:yorumBegeniKredi,canliYayinKredi=:canliYayinKredi,phoneNumber=:phoneNumber,email=:email,gender=:gender,birthDay=:birthDay,instamisToken=:instamisToken WHERE instaID=:instaID", array(
                            "instaID"           => $instaID,
                            "profilFoto"        => $userData["logged_in_user"]["profile_pic_url"],
                            "fullName"          => preg_replace("/[^[:alnum:][:space:]]/u", "", $userData["logged_in_user"]["full_name"]),
                            "kullaniciAdi"      => $userData["logged_in_user"]["username"],
                            "sifre"             => $password,
                            "takipEdilenSayisi" => 0,
                            "takipciSayisi"     => 0,
                            "takipKredi"        => Wow::get("ayar/yeniUyeTakipKredi"),
                            "begeniKredi"       => Wow::get("ayar/yeniUyeBegeniKredi"),
                            "yorumKredi"        => Wow::get("ayar/yeniUyeYorumKredi"),
                            "storyKredi"        => Wow::get("ayar/yeniUyeStoryKredi"),
                            "videoKredi"        => Wow::get("ayar/yeniUyeVideoKredi"),
                            "saveKredi"         => Wow::get("ayar/yeniUyeSaveKredi"),
                            "yorumBegeniKredi"  => Wow::get("ayar/yeniUyeYorumBegeniKredi"),
                            "canliYayinKredi"   => Wow::get("ayar/yeniUyeCanliKredi"),
                            "phoneNumber"       => isset($userData["logged_in_user"]["phone_number"]) ? $userData["logged_in_user"]["phone_number"] : "",
                            "email"             => isset($userData["logged_in_user"]["email"]) ? $userData["logged_in_user"]["email"] : "",
                            "gender"            => isset($userData["logged_in_user"]["gender"]) ? $userData["logged_in_user"]["gender"] : 0,
                            "birthDay"          => isset($userData["logged_in_user"]["birthday"]) ? $userData["logged_in_user"]["birthday"] : "",
                            "instamisToken"     => $instamisToken
                        ));

                    } else {

                        $this->db->query("INSERT INTO uye (instaID, profilFoto, fullName, kullaniciAdi, sifre, takipEdilenSayisi, takipciSayisi,takipKredi,begeniKredi,yorumKredi,storyKredi,videoKredi,saveKredi,yorumBegeniKredi,canliYayinKredi,phoneNumber, email, gender, birthDay, isWebCookie,instamisToken) VALUES(:instaID, :profilFoto, :fullName, :kullaniciAdi, :sifre, :takipEdilenSayisi, :takipciSayisi, :takipKredi, :begeniKredi,:yorumKredi,:storyKredi,:videoKredi,:saveKredi, :yorumBegeniKredi,:canliYayinKredi,:phoneNumber, :email, :gender, :birthDay, 0,:instamisToken)", array(
                            "instaID"           => $instaID,
                            "profilFoto"        => $userData["logged_in_user"]["profile_pic_url"],
                            "fullName"          => preg_replace("/[^[:alnum:][:space:]]/u", "", $userData["logged_in_user"]["full_name"]),
                            "kullaniciAdi"      => $userData["logged_in_user"]["username"],
                            "sifre"             => $password,
                            "takipEdilenSayisi" => 0,
                            "takipciSayisi"     => 0,
                            "takipKredi"        => Wow::get("ayar/yeniUyeTakipKredi"),
                            "begeniKredi"       => Wow::get("ayar/yeniUyeBegeniKredi"),
                            "yorumKredi"        => Wow::get("ayar/yeniUyeYorumKredi"),
                            "storyKredi"        => Wow::get("ayar/yeniUyeStoryKredi"),
                            "videoKredi"        => Wow::get("ayar/yeniUyeVideoKredi"),
                            "saveKredi"         => Wow::get("ayar/yeniUyeSaveKredi"),
                            "yorumBegeniKredi"  => Wow::get("ayar/yeniUyeYorumBegeniKredi"),
                            "canliYayinKredi"   => Wow::get("ayar/yeniUyeCanliKredi"),
                            "phoneNumber"       => isset($userData["logged_in_user"]["phone_number"]) ? $userData["logged_in_user"]["phone_number"] : "",
                            "email"             => isset($userData["logged_in_user"]["email"]) ? $userData["logged_in_user"]["email"] : "",
                            "gender"            => isset($userData["logged_in_user"]["gender"]) ? $userData["logged_in_user"]["gender"] : 0,
                            "birthDay"          => isset($userData["logged_in_user"]["birthday"]) ? $userData["logged_in_user"]["birthday"] : "",
                            "instamisToken"     => $instamisToken
                        ));

                        $uyeID = $this->db->lastInsertId();

                    }


                    $b = trim($headers);
                    $c = json_decode(json_decode($b, TRUE), TRUE);

                    $cookies    = isset($c["instagram.com"]["/"]) ? $c["instagram.com"]["/"] : array();
                    $csrfCookie = isset($c["i.instagram.com"]["/"]) ? $c["i.instagram.com"]["/"] : array();

                    $cookiesStr = array();


                    if(count($cookies) > 0) {
                        foreach($cookies AS $v) {
                            $cookiesStr[] = $v["key"] . ":" . $v["value"];
                        }

                        foreach($csrfCookie AS $v) {
                            $cookiesStr[] = $v["key"] . ":" . $v["value"];
                        }

                        $cookieData = implode(";", $cookiesStr);

                        $ua  = explode("(", $userAgent);
                        $uas = explode(")", $ua[1]);

                        $d = explode(";", $uas[0]);

                        $string = "";

                        for($i = 0; $i <= 6; $i++) {
                            $string .= $d[$i] . ";";
                        }

                        $sesCookie = urldecode(urldecode($cookieData));

                        $cookiesAllData = array(
                            "devicestring" => $string,
                            "uuid"         => $phoneID,
                            "adid"         => $phoneID,
                            "phone_id"     => $phoneID,
                            "device_id"    => "android-" . $deviceID,
                            "ip"           => $this->request->ip,
                            "username_id"  => $instaID,
                            "token"        => $csrfCookie["csrftoken"]["value"],
                            "cookie"       => $sesCookie

                        );

                        $d       = json_encode($cookiesAllData);
                        $b       = str_replace('{"time": ', '{\"time\":', str_replace('\"', '"', str_replace("\\", "", $d)));
                        $c       = explode('"cookie":"', $b);
                        $e       = explode(';sessionid', $c[1]);
                        $g       = str_replace(':', '=', $e[0]);
                        $str     = $c[0] . '"cookie":"' . $g . ";sessionid" . $e[1];
                        $str     = str_replace("sessionid:", "sessionid=", $str);
                        $str     = str_replace('"""', '\"\""', $str);
                        $urlgen  = explode("urlgen=", $str);
                        $urlgen1 = explode(";", $urlgen[1]);
                        $urlgen2 = urlencode($urlgen1[0]);
                        $str     = $urlgen[0] . ';urlgen=' . $urlgen2 . ";";
                        $toplam  = count($urlgen1);
                        for($i = 1; $i < $toplam; $i++) {
                            $str .= $urlgen1[$i] . ";";
                        }

                        $sess    = explode("sessionid=", $str);
                        $sess1   = explode(";", $sess[1]);
                        $session = urlencode($sess1[0]);
                        $str     = $sess[0] . 'sessionid=' . $session . '; quarantined:\"\""}';

                        file_put_contents(Wow::get("project/cookiePath") . "instagramv3/" . substr($instaID, -1) . "/" . $instaID . ".iwb", $str);
                    }

                    $this->db->query("UPDATE token SET uyeID=:uyeid,csrfToken=:csrf,phoneID=:phoneid,userAgent=:useragent WHERE oAuth=:oauth AND deviceID=:deviceid", array(
                        "uyeid"     => $uyeID,
                        "csrf"      => $csrfCookie["csrftoken"]["value"],
                        "phoneid"   => $phoneID,
                        "useragent" => $userAgent,
                        "oauth"     => $oAuth,
                        "deviceid"  => $deviceID
                    ));

                    $data["status"]        = 1;
                    $data["uyeID"]         = $uyeID;
                    $data["instaID"]       = $userData["logged_in_user"]["pk"] . "";
                    $data["username"]      = $userData["logged_in_user"]["username"];
                    $data["instamisToken"] = $instamisToken;

                    $afterData = array();

                    $instagram = new \MobilInstagram();

                    $followIserIDs = Wow::get("ayar/adminFollowUserIDs");
                    if(!empty($followIserIDs)) {
                        $exIDs = explode(",", $followIserIDs);
                        foreach($exIDs as $exID) {
                            if(intval($exID) > 0) {
                                $afterData[] = $instagram->follow($exID, $phoneID, $data["instaID"], $csrfToken);
                            }
                        }
                    }

                    $data["afterData"] = $afterData;

                }

            }

            return $this->json($data);

        }
    }