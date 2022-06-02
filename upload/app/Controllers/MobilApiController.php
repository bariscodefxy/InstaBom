<?php

    namespace App\Controllers;

    use Wow\Net\Response;

    class MobilApiController extends BaseController {


        /**
         * Override onStart
         */
        function onActionExecuting() {
            if(($pass = parent::onActionExecuting()) instanceof Response) {
                return $pass;
            }


        }

        public function IndexAction() {

            $oAuth = isset($_SERVER["HTTP_OAUTH"]) && !empty($_SERVER["HTTP_OAUTH"]) && $_SERVER["HTTP_OAUTH"] != "undefined" ? $_SERVER["HTTP_OAUTH"] : "";

            $deviceID = isset($_SERVER["HTTP_DEVICEID"]) ? $_SERVER["HTTP_DEVICEID"] : "";

            $osType = isset($_SERVER["HTTP_OSTYPE"]) && in_array($_SERVER["HTTP_OSTYPE"], array(
                "android",
                "ios",
                "extension"
            )) ? $_SERVER["HTTP_OSTYPE"] : "";

            if($osType == "extension") {
                $pushToken = 1;
            } else {
                $pushToken = $this->request->data->pushtoken ? $this->request->data->pushtoken : "";
            }
            $csrfToken = $this->request->data->csrftoken ? $this->request->data->csrftoken : "";

            if(empty($deviceID) || empty($osType) || empty($pushToken)) {
                return $this->json(array(
                                       "status"  => 0,
                                       "error"   => "Sistemsel bir hata oluştu lütfen tekrar deneyiniz.",
                                       "push"    => $pushToken,
                                       "device"  => $deviceID,
                                       "os"      => $osType,
                                       "request" => $_REQUEST
                                   ));
            }

            $sorgu = $this->db->row("SELECT t.tokenID,t.oAuth,t.uyeID,t.loginStatus,t.csrfToken,u.instaID,u.kullaniciAdi,u.isPremium,u.premiumEndDate FROM token AS t LEFT JOIN mobil_uye AS u ON t.uyeID=u.uyeID WHERE t.deviceID=:deviceid AND t.ostype=:ostype ORDER BY t.tokenID DESC LIMIT 1", array(
                "deviceid" => $deviceID,
                "ostype"   => strtolower($osType)
            ));


            if(empty($sorgu["tokenID"])) {

                if(trim($oAuth) == "") {
                    $oAuth = sha1(md5(time(), rand(1, 999999)));
                }

                $this->db->query("INSERT INTO token (oAuth, deviceID, pushToken,osType) VALUES (:oauth,:deviceid,:pushtoken,:ostype)", array(
                    "oauth"     => $oAuth,
                    "deviceid"  => $deviceID,
                    "pushtoken" => $pushToken,
                    "ostype"    => strtolower($osType)
                ));

            } else {

                $oAuth = isset($_SERVER["HTTP_OAUTH"]) ? $_SERVER["HTTP_OAUTH"] : "";

                if($sorgu["oAuth"] != $oAuth) {

                    $oAuth = sha1(md5(time(), rand(1, 999999)));

                    $this->db->query("UPDATE token SET csrfToken=:csrftoken,oAuth=:oauth,pushToken=:pushtoken WHERE tokenID=:tokenid", array(
                        "tokenid"   => $sorgu["tokenID"],
                        "csrftoken" => $csrfToken ? $csrfToken : $sorgu["csrfToken"],
                        "oauth"     => $oAuth,
                        "pushtoken" => $pushToken
                    ));
                }
            }

            $uyeData = array();
            if($sorgu["loginStatus"] == 1) {
                $uyeData = $this->db->row("SELECT takipKredi,begeniKredi,yorumKredi,storyKredi,otoBegeniKredi,bonusKredi FROM mobil_uye WHERE uyeID=:uyeid", array("uyeid" => $sorgu["uyeID"]));
            }

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "https://extreme-ip-lookup.com/json/" . self::getUserIP());
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $ipJson = curl_exec($ch);
            curl_close($ch);

            $trUser = json_decode($ipJson, TRUE);

            return $this->json(array(
                                   "status"     => 1,
                                   "oAuth"      => $oAuth,
                                   "userID"     => $sorgu["loginStatus"] == 1 ? $sorgu["uyeID"] : NULL,
                                   "username"   => $sorgu["loginStatus"] == 1 ? $sorgu["kullaniciAdi"] : NULL,
                                   "instaID"    => $sorgu["loginStatus"] == 1 ? $sorgu["instaID"] : NULL,
                                   "premium"    => $sorgu["loginStatus"] == 1 ? $sorgu["isPremium"] : NULL,
                                   "premiumEnd" => $sorgu["loginStatus"] == 1 && $sorgu["isPremium"] == 1 ? date("d-m-Y H:i", strtotime($sorgu["premiumEndDate"])) : NULL,
                                   "version"    => "3.0",
                                   "alert"      => array(
                                       "status"  => "0",
                                       "title"   => "",
                                       "message" => ""
                                   ),
                                   "market"     => $trUser["countryCode"] == "TR" ? "0" : "1",
                                   "uyeData"    => $uyeData
                               ));

        }

        public function getUserIP() {
            $client  = @$_SERVER['HTTP_CLIENT_IP'];
            $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
            $remote  = $_SERVER['REMOTE_ADDR'];

            if(filter_var($client, FILTER_VALIDATE_IP)) {
                $ip = $client;
            } elseif(filter_var($forward, FILTER_VALIDATE_IP)) {
                $ip = $forward;
            } else {
                $ip = $remote;
            }

            return $ip;
        }

        public function LoginAction() {

            $username  = $this->request->data->username ? $this->request->data->username : "";
            $password  = $this->request->data->password ? $this->request->data->password : "";
            $deviceID  = $this->request->data->deviceID ? $this->request->data->deviceID : "";
            $phoneID   = $this->request->data->phoneID ? $this->request->data->phoneID : "";
            $csrfToken = $this->request->data->csrfToken ? $this->request->data->csrfToken : "";
            $useragent = $this->request->data->useragent ? $this->request->data->useragent : "";

            $instagram = new \MobilInstagram();


            $data = $instagram->MobileLogin($username, $password, $deviceID, $phoneID, $csrfToken);

            return $this->json($data);

        }

    }