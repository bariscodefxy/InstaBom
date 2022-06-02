<?php

    namespace App\Controllers;


    class ConnectController extends BaseController {

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
                $pushToken = $_REQUEST["pushtoken"] ? $_REQUEST["pushtoken"] : "";
            }
            $csrfToken = $_REQUEST["csrftoken"] ? $_REQUEST["csrftoken"] : "";

            if(empty($deviceID) || empty($osType) || empty($pushToken)) {
                return $this->json(array(
                                       "status" => 0,
                                       "error"  => "Sistemsel bir hata oluştu lütfen tekrar deneyiniz.",
                                       "deviceid" => $deviceID,
                                       "osType" => $osType,
                                       "pushtoken" => $pushToken,
                                       "csrftoken" => $csrfToken,
                                       "allRequest" => $_REQUEST
                                   ));
            }

            $sorgu = $this->db->row("SELECT * FROM token AS t LEFT JOIN uye AS u ON t.uyeID=u.uyeID WHERE t.deviceID=:deviceid AND t.ostype=:ostype ORDER BY t.tokenID DESC LIMIT 1", array(
                "deviceid" => $deviceID,
                "ostype"   => strtolower($osType)
            ));


            if(!isset($sorgu["tokenID"]) || empty($sorgu["tokenID"])) {

                if(trim($oAuth) == "" || empty($oAuth)) {
                    $oAuth = sha1(md5(time(), rand(1, 999999)));
                }
            

                $this->db->query("INSERT INTO token (oAuth,csrfToken,deviceID, pushToken,osType) VALUES (:oauth,:csrftoken,:deviceid,:pushtoken,:ostype)", array(
                    "oauth"     => $oAuth,
                    "csrftoken" => $csrfToken,
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
                $uyeData = $this->db->row("SELECT * FROM uye WHERE uyeID=:uyeid", array("uyeid" => $sorgu["uyeID"]));
            }

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "https://extreme-ip-lookup.com/json/" . self::getUserIP());
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $ipJson = curl_exec($ch);
            curl_close($ch);

            $trUser = json_decode($ipJson, TRUE);

            $data = array(
                "status"   => 1,
                "oAuth"    => $oAuth,
                "userID"   => $sorgu["loginStatus"] == 1 ? $sorgu["uyeID"] : NULL,
                "username" => $sorgu["loginStatus"] == 1 ? $sorgu["kullaniciAdi"] : NULL,
                "instaID"  => $sorgu["loginStatus"] == 1 ? $sorgu["instaID"] : NULL,
                "version"  => "2.5.0",
                "alert"    => array(
                    "status"  => "0",
                    "title"   => "",
                    "message" => ""
                ),
                "market"   => $trUser["countryCode"] == "TR" ? "0" : "1",
                "uyeData"  => $uyeData,
            );

            $data["premium"]    = $uyeData["isPremium"];
            $data["premiumEnd"] = date("d-m-Y H:i", strtotime($uyeData["premiumEndDate"]));

            return $this->json($data);

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

    }