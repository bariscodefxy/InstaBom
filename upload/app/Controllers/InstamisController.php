<?php

    namespace App\Controllers;

    use BulkReaction;
    use Wow;
    use Wow\Database\Database;

    class InstamisController extends BaseController {
        function IndexAction() {

            $d = array();

            $misDB = Database::getInstance("InstamisConnection");

            $uye = $misDB->query("SELECT t.*,u.instaID,u.kullaniciAdi FROM talepler AS t LEFT JOIN uye AS u ON t.uyeID=u.uyeID WHERE t.adetMax > t.gonderilenAdet AND t.durum='0' AND t.uyeID<>1 ORDER BY t.talepID ASC");

            foreach($uye AS $u) {

                $anauyeIds = substr($u["instaGonderimUyeID"], 0, -1);
                $addSorgu  = "";
                if(!empty($anauyeIds)) {
                    $addSorgu = "AND uyeID NOT IN (" . $anauyeIds . ")";
                }

                if($u["talepTip"] == "takip") {

                    $adet = ($u["adetMax"] - $u["gonderilenAdet"]);

                    $instaID  = $u["instaID"];
                    $username = $u["kullaniciAdi"];

                    $users = $this->db->query("SELECT uyeID,instaID,kullaniciAdi,sifre FROM uye WHERE isActive=1 AND canFollow=1 and isUsable=1 " . $addSorgu . " ORDER BY rand() LIMIT :adet", array("adet" => $adet));

                    if(count($users) > 0) {
                        $bulkReaction      = new BulkReaction($users, Wow::get("ayar/bayiEsZamanliIstek"));
                        $response          = $bulkReaction->follow($instaID, $username);
                        $totalSuccessCount = $response["totalSuccessCount"] ? $response["totalSuccessCount"] : 0;

                    }

                } else if($u["talepTip"] == "begeni") {

                    $adet = $u["adetMax"] - $u["gonderilenAdet"];

                    $username = $u["kullaniciAdi"];
                    $users    = $this->db->query("SELECT uyeID,instaID,kullaniciAdi,sifre FROM uye WHERE isActive=1 AND canLike=1 and isUsable=1 " . $addSorgu . " ORDER BY rand() LIMIT :adet", array("adet" => $adet));

                    if(count($users) > 0) {
                        $bulkReaction      = new BulkReaction($users, Wow::get("ayar/bayiEsZamanliIstek"));
                        $response          = $bulkReaction->like($u["begeniMediaID"], $username);
                        $totalSuccessCount = $response["totalSuccessCount"] ? $response["totalSuccessCount"] : 0;
                    }


                } else if($u["talepTip"] == "story") {
                    $adet = ($u["adetMax"] - $u["gonderilenAdet"]);

                    $users = $this->db->query("SELECT uyeID,instaID,kullaniciAdi,sifre FROM uye WHERE isActive=1 AND canFollow=1 and isUsable=1 " . $addSorgu . " ORDER BY rand() LIMIT :adet", array("adet" => $adet));

                    if(count($users) > 0) {
                        $bulkReaction      = new BulkReaction($users, Wow::get("ayar/bayiEsZamanliIstek"));
                        $response          = $bulkReaction->storyview(json_decode($u["storyData"], TRUE));
                        $totalSuccessCount = $response["totalSuccessCount"] ? $response["totalSuccessCount"] : 0;

                        $response = json_decode($response, TRUE);
                    }

                } else if($u["talepTip"] == "yorum") {
                    $adet = 1;

                    $users = $this->db->query("SELECT uyeID,instaID,kullaniciAdi,sifre FROM uye WHERE isActive=1 AND canComment=1 and isUsable=1 " . $addSorgu . " ORDER BY rand() LIMIT :adet", array("adet" => $adet));

                    if(count($users) > 0) {
                        $yorum             = array($u["yorumMediaID"]);
                        $bulkReaction      = new BulkReaction($users, Wow::get("ayar/bayiEsZamanliIstek"));
                        $response          = $bulkReaction->comment($yorum, "", $u["yorumText"]);
                        $totalSuccessCount = $response["totalSuccessCount"] ? $response["totalSuccessCount"] : 0;

                        $response = json_decode($response, TRUE);
                    }

                }

                if(count($response) > 0) {
                    $usersIds = array();

                    foreach($response["users"] AS $r) {
                        $usersIds[] = $r["userID"];
                    }

                    $misDB->query("UPDATE talepler SET gonderilenAdet=gonderilenAdet+:adet,instaGonderimUyeID=CONCAT(instaGonderimUyeID,:uyeid) WHERE talepID=:talepid", array(
                        "adet"    => $totalSuccessCount,
                        "talepid" => $u["talepID"],
                        "uyeid"   => implode(",", $usersIds) . ",",
                    ));

                }


            }

            return $this->json($d);
        }

    }