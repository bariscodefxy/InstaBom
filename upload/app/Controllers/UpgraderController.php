<?php

    namespace App\Controllers;

    use Utils;
    use Wow;
    use SignatureUtils;

    class UpgraderController extends BaseController {

        // TODO: dat+cnf to iwb converter.
        function DataConverterAction() {
            $data = $this->db->query("SELECT * FROM uye");
            foreach($data as $c) {
                $cPath = Wow::get("project/cookiePath") . "instagram/" . $c["kullaniciAdi"] . ".cnf";
                $dPath = Wow::get("project/cookiePath") . "instagram/" . $c["kullaniciAdi"] . ".dat";
                if(file_exists($cPath) && file_exists($dPath)) {
                    $dHost    = $c["isWebCookie"] == 1 ? "www.instagram.com" : "i.instagram.com";
                    $confData = [];
                    $fp       = fopen($cPath, 'rb');
                    while($line = fgets($fp, 2048)) {
                        $line = trim($line, ' ');
                        if($line[0] == '#') {
                            continue;
                        }
                        $kv               = explode('=', $line, 2);
                        $confData[$kv[0]] = trim($kv[1], "\r\n ");
                    }
                    fclose($fp);
                    if($c["isWebCookie"] == 1) {
                        if(isset($confData["user_agent"])) {
                            unset($confData["user_agent"]);
                        }
                        if(isset($confData["manufacturer"])) {
                            unset($confData["manufacturer"]);
                        }
                        if(isset($confData["device"])) {
                            unset($confData["device"]);
                        }
                        if(isset($confData["model"])) {
                            unset($confData["model"]);
                        }
                    } else {
                        $confData["device_id"] = SignatureUtils::generateDeviceId(md5(Wow::get("project/licenseKey") . $confData["username_id"]));
                    }
                    $cookieData = Utils::cookieToArray(file_get_contents($dPath), $dHost);
                    $cookie_all = [];
                    foreach($cookieData as $k => $v) {
                        $cookie_all[] = $k . "=" . urlencode($v);
                    }
                    $v3Data                = $confData;
                    $v3CookieName          = $c["isWebCookie"] == 1 ? "web_cookie" : "cookie";
                    $v3Data[$v3CookieName] = implode(";", $cookie_all);
                    $newPath = Wow::get("project/cookiePath") . "instagramv3/" . substr($c["instaID"], -1) . "/" . $c["instaID"] . ".iwb";
                    file_put_contents($newPath, json_encode($v3Data));
                }
            }

            return $this->json(["ok"]);
//            exit();
        }

        // TODO: v2 to v3 Wizard
        function DataOperationsAction() {
            $this->db->query("UPDATE uye SET isUsable=0 WHERE isAdmin=1");
            //TODO after this query delete colum isAdmin
            // ALTER TABLE  `uye` DROP  `isAdmin` ;
            // ALTER TABLE  `uye` DROP  `isOtoAktarim` ;
            // ALTER TABLE  `bayi` ADD  `otoBegeniMaxGun` INT( 5 ) UNSIGNED NOT NULL DEFAULT  '30';
            return $this->json("ok");
        }

    }