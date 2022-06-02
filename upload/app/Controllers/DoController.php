<?php

    namespace App\Controllers;

    use App\Libraries\InstagramReaction;
    use BulkReaction;
    use Wow;
    use Wow\Net\Response;

    class DoController extends BaseController {

        /**
         * Override onStart
         */
        function onActionExecuting() {
            if(($pass = parent::onActionExecuting()) instanceof Response) {
                return $pass;
            }

            if($this->request->query->scKey != Wow::get("ayar/securityKey")) {
                return $this->notFound();
            }
        }

        function AutoLikeAction($id) {
            $sortIndex = intval($this->request->query->sortIndex);
            $totalRows = intval($this->request->query->totalRows);
            if($totalRows > 0 && $sortIndex > 0 && $sortIndex <= $totalRows) {
                $totalDelaySeconds     = 5;
                $totalDelayMiliSeconds = $totalDelaySeconds * 1000000;
                $delayPerRow           = $totalDelayMiliSeconds / $totalRows;
                $delayForThis          = $delayPerRow * ($sortIndex - 1);
                if($delayForThis > 0) {
                    usleep($delayForThis);
                }
            }

            $media = $this->db->row("SELECT * FROM uye_otobegenipaket_gonderi WHERE TIMESTAMPDIFF(MINUTE,lastControlDate,NOW()) >= minuteDelay AND id=:id", array("id" => $id));

            if(empty($media)) {
                return $this->notFound();
            }

            if($media["likeCountLeft"] == 0) {
                $this->db->query("UPDATE uye_otobegenipaket_gonderi SET excludedInstaIDs=NULL WHERE id=:id", ["id" => $media["id"]]);

                return $this->json(array("status" => "success"));
            }

            $reactionUserID       = $this->findAReactionUser();
            $objInstagramReaction = new InstagramReaction($reactionUserID);
            if(empty($reactionUserID)) {
                return $this->json(["status" => "fail"]);
            }

            $mediaInfo = $objInstagramReaction->objInstagram->getMediaInfo($media["mediaID"]);
            if($mediaInfo["status"] == "fail") {
                if($media["failCount"] >= 4) {
                    $this->db->query("DELETE FROM uye_otobegenipaket_gonderi WHERE mediaID=:mediaID", array("mediaID" => $media["mediaID"]));
                } else {
                    $this->db->query("UPDATE uye_otobegenipaket_gonderi SET failCount=failCount+1 WHERE id=:id", array("id" => $media["id"]));
                }

                return $this->json(array("status" => "success"));
            }

            $excludedInstaIDs = empty($media["excludedInstaIDs"]) ? "0" : $media["excludedInstaIDs"];

            if($excludedInstaIDs == "0") {
                $likers = $objInstagramReaction->objInstagram->getMediaLikers($media["mediaID"]);
                foreach($likers["users"] as $user) {
                    if(intval($user["pk"]) > 0) {
                        $excludedInstaIDs .= "," . intval($user["pk"]);
                    }
                }
            }

            $maxUser   = $media["krediDelay"];
            $limitUser = $media["likeCountLeft"];
            if($limitUser > $maxUser) {
                $limitUser = $maxUser;
            }

            $genderSql = "";
            $arrGender = array();
            if(intval($media["gender"]) > 0) {
                $genderSql           = " AND gender=:gender";
                $arrGender["gender"] = intval($media["gender"]);
            }

            $users = $this->db->query("SELECT uyeID,instaID,kullaniciAdi,sifre,isWebCookie FROM uye WHERE isActive=1 AND canLike=1 and isUsable=1 AND instaID NOT IN($excludedInstaIDs) " . $genderSql . " ORDER BY sonOlayTarihi ASC LIMIT :adet", array_merge($arrGender, array("adet" => $limitUser)));
            if(empty($users)) {
                return $this->json(array("status" => "success"));
            }
            $allUserIDs = array_map(function($d) {
                return $d["uyeID"];
            }, $users);
            $allUserIDs = implode(",", $allUserIDs);
            $this->db->query("UPDATE uye SET sonOlayTarihi=NOW() WHERE uyeID IN (" . $allUserIDs . ")");
            $this->db->CloseConnection();

            $bulkReaction      = new BulkReaction($users, 100);
            $response          = $bulkReaction->like($media["mediaID"], $media["mediaCode"]);
            $triedUsers        = $response["users"];
            $totalSuccessCount = $response["totalSuccessCount"];

            $allFailedUserIDs = array_filter(array_map(function($d) {
                return $d["status"] == "fail" ? $d["userID"] : NULL;
            }, $triedUsers), function($d) {
                return $d !== NULL;
            });
            if(!empty($allFailedUserIDs)) {
                $allFailedUserIDs = implode(",", $allFailedUserIDs);
                $this->db->query("UPDATE uye SET canLikeControlDate=NOW() WHERE uyeID IN (" . $allFailedUserIDs . ")");
            }
            $allSuccessInstaIDs = array_filter(array_map(function($d) {
                return $d["status"] == "success" ? $d["instaID"] : NULL;
            }, $triedUsers), function($d) {
                return $d !== NULL;
            });
            if(!empty($allSuccessInstaIDs)) {
                $excludedInstaIDs .= "," . implode(",", $allSuccessInstaIDs);
            }

            $this->db->query("UPDATE uye_otobegenipaket_gonderi SET likeCountLeft=likeCountLeft-:successCount,excludedInstaIDs=:excludedInstaIDs,lastControlDate = NOW() WHERE id=:id", array(
                "id"               => $media["id"],
                "successCount"     => $totalSuccessCount,
                "excludedInstaIDs" => $excludedInstaIDs
            ));

            return $this->json(array("status" => "success"));
        }

        function ControlAutoLikeMediasAction($id) {
            $package = $this->db->row("SELECT * FROM uye_otobegenipaket WHERE isActive=1 AND TIMESTAMPDIFF(MINUTE,lastControlDate,NOW()) > 4 AND id=:id", array("id" => $id));

            if(empty($package)) {
                return $this->notFound();
            }


            if(strtotime($package["endDate"]) < strtotime("now")) {
                $this->db->query("UPDATE uye_otobegenipaket SET isActive=2, lastControlDate = NOW() WHERE id=:id", array("id" => $package["id"]));

                return $this->json(array("status" => "success"));
            }

            $reactionUserID       = $this->findAReactionUser();
            $objInstagramReaction = new InstagramReaction($reactionUserID);
            if(empty($reactionUserID)) {
                return $this->json(["status" => "fail"]);
            }

            $userMedias = $objInstagramReaction->objInstagram->getUserFeed($package["instaID"]);
            if($userMedias["status"] == "ok") {
                foreach($userMedias["items"] as $item) {
                    if($item["media_type"] != 3) {
                        if($item["taken_at"] > strtotime($package["lastControlDate"])) {
                            $rand = rand($package["likeCountMin"], $package["likeCountMax"]);

                            $kontrol = $this->db->row("SELECT * FROM uye_otobegenipaket_gonderi WHERE mediaCode=:code", array("code" => $item["code"]));

                            if(empty($kontrol)) {
                                $this->db->query("INSERT INTO uye_otobegenipaket_gonderi (paketID,mediaID,mediaCode,likeCountTotal, likeCountLeft, imageUrl, minuteDelay, krediDelay, gender) VALUES (:paketID,:mediaID,:mediaCode,:likeCountTotal,:likeCountLeft, :imageUrl,:minuteDelay, :krediDelay, :gender)", array(
                                    "paketID"        => $package["id"],
                                    "mediaID"        => $item["id"],
                                    "mediaCode"      => $item["code"],
                                    "likeCountTotal" => $rand,
                                    "likeCountLeft"  => $rand,
                                    "imageUrl"       => $item["media_type"] == 8 ? str_replace("http:", "https:", $item["carousel_media"][0]["image_versions2"]["candidates"][0]["url"]) : str_replace("http:", "https:", $item["image_versions2"]["candidates"][0]["url"]),
                                    "minuteDelay"    => $package["minuteDelay"],
                                    "krediDelay"     => $package["krediDelay"],
                                    "gender"         => $package["gender"]
                                ));
                            }

                        } else {
                            break;
                        }
                    }
                }
                $this->db->query("UPDATE uye_otobegenipaket SET lastControlDate = NOW() WHERE id=:id", array("id" => $package["id"]));
            }

            return $this->json(array("status" => "success"));

        }


        function ControlApiAction($id) {

            $s = $this->db->row("SELECT * FROM bayi_islem WHERE bayiIslemID=:id AND isApi=1", array("id" => $id));

            if(empty($s)) {
                return FALSE;
            }

            $adet = $s["krediLeft"];
            if($s["islemTip"] == "follow") {

                if($adet > Wow::get("ayar/bayiPaketBasiIstek")) {
                    $adet = Wow::get("ayar/bayiPaketBasiIstek");
                }

                $instaIDs = empty($s["excludedInstaIDs"]) ? "0" : $s["excludedInstaIDs"];
                $users    = $this->db->query("SELECT uyeID,instaID,kullaniciAdi,sifre,isWebCookie FROM uye WHERE canFollow=1 AND isActive=1 AND isUsable=1 AND instaID NOT IN($instaIDs) ORDER BY sonOlayTarihi ASC LIMIT :adet", array("adet" => $adet));
                if(empty($users)) {
                    $this->db->query("UPDATE bayi_islem SET isActive=0 WHERE bayiIslemID=:bayiIslemID", array(
                        "bayiIslemID" => $s["bayiIslemID"]
                    ));

                }
                $allUserIDs = array_map(function($d) {
                    return $d["uyeID"];
                }, $users);
                $allUserIDs = implode(",", $allUserIDs);
                $this->db->query("UPDATE uye SET sonOlayTarihi=NOW() WHERE uyeID IN (" . $allUserIDs . ")");
                $this->db->CloseConnection();

                $bulkReaction      = new BulkReaction($users, Wow::get("ayar/bayiEsZamanliIstek"));
                $response          = $bulkReaction->follow($s["userID"], $s["userName"]);
                $triedUsers        = $response["users"] ? $response["users"] : array();
                $totalSuccessCount = $response["totalSuccessCount"];

                $allSuccessInstaIDs = array_filter(array_map(function($d) {
                    return $d["status"] == "success" ? $d["instaID"] : NULL;
                }, $triedUsers), function($d) {
                    return $d !== NULL;
                });
                $excludedInstaIDs   = empty($s["excludedInstaIDs"]) ? "0" : $s["excludedInstaIDs"];
                if(!empty($allSuccessInstaIDs)) {
                    $excludedInstaIDs .= "," . implode(",", $allSuccessInstaIDs);
                }

                $isActive     = ($s["krediLeft"] - intval($totalSuccessCount)) <= 0 ? 0 : 1;
                $successCount = ($s["krediLeft"] - intval($totalSuccessCount)) > 0 ? intval($totalSuccessCount) : $s["krediLeft"];

                $this->db->query("UPDATE bayi_islem SET krediLeft=krediLeft-:successCount, isActive=:isActive,excludedInstaIDs=:excludedInstaIDs WHERE bayiIslemID=:bayiIslemID", array(
                    "bayiIslemID"      => $s["bayiIslemID"],
                    "successCount"     => $successCount,
                    "isActive"         => $isActive,
                    "excludedInstaIDs" => $excludedInstaIDs
                ));

                if($isActive == 0) {
                    $this->db->query("UPDATE bayi_islem SET excludedInstaIDs=NULL WHERE bayiIslemID=:bayiIslemID", ["bayiIslemID" => $s["bayiIslemID"]]);
                }

            } else if($s["islemTip"] == "like") {

                if($adet > Wow::get("ayar/bayiPaketBasiIstek")) {
                    $adet = Wow::get("ayar/bayiPaketBasiIstek");
                }

                $instaIDs = empty($s["excludedInstaIDs"]) ? "0" : $s["excludedInstaIDs"];

                $users = $this->db->query("SELECT uyeID,instaID,kullaniciAdi,sifre,isWebCookie  FROM uye WHERE canLike=1 AND isActive=1 AND isUsable=1 AND instaID NOT IN($instaIDs)   ORDER BY sonOlayTarihi ASC LIMIT :adet", array("adet" => $adet));
                if(empty($users)) {
                    $this->db->query("UPDATE bayi_islem SET isActive=0 WHERE bayiIslemID=:bayiIslemID", array(
                        "bayiIslemID" => $s["bayiIslemID"]
                    ));
                }
                $allUserIDs = array_map(function($d) {
                    return $d["uyeID"];
                }, $users);
                $allUserIDs = implode(",", $allUserIDs);
                $this->db->query("UPDATE uye SET sonOlayTarihi=NOW() WHERE uyeID IN (" . $allUserIDs . ")");
                $this->db->CloseConnection();

                $bulkReaction      = new BulkReaction($users, Wow::get("ayar/bayiEsZamanliIstek"));
                //$response          = $bulkReaction->like($s["mediaID"], $s["mediaCode"]);
				$response          = $bulkReaction->like($s["mediaID"],$s["username"], $s["mediaCode"]);
                $triedUsers        = $response["users"] ? $response["users"] : array();
                $totalSuccessCount = $response["totalSuccessCount"];

                $allSuccessInstaIDs = array_filter(array_map(function($d) {
                    return $d["status"] == "success" ? $d["instaID"] : NULL;
                }, $triedUsers), function($d) {
                    return $d !== NULL;
                });
                $excludedInstaIDs   = empty($s["excludedInstaIDs"]) ? "0" : $s["excludedInstaIDs"];
                if(!empty($allSuccessInstaIDs)) {
                    $excludedInstaIDs .= "," . implode(",", $allSuccessInstaIDs);
                }

                $isActive     = ($s["krediLeft"] - intval($totalSuccessCount)) <= 0 ? 0 : 1;
                $successCount = ($s["krediLeft"] - intval($totalSuccessCount)) > 0 ? intval($totalSuccessCount) : $s["krediLeft"];

                $this->db->query("UPDATE bayi_islem SET krediLeft=krediLeft-:successCount, isActive=:isActive,excludedInstaIDs=:excludedInstaIDs WHERE bayiIslemID=:bayiIslemID", array(
                    "bayiIslemID"      => $s["bayiIslemID"],
                    "successCount"     => $successCount,
                    "isActive"         => $isActive,
                    "excludedInstaIDs" => $excludedInstaIDs
                ));
                if($isActive == 0) {
                    $this->db->query("UPDATE bayi_islem SET excludedInstaIDs=NULL WHERE bayiIslemID=:bayiIslemID", ["bayiIslemID" => $s["bayiIslemID"]]);
                }

            } else if($s["islemTip"] == "story") {

                if($adet > Wow::get("ayar/bayiPaketBasiIstek")) {
                    $adet = Wow::get("ayar/bayiPaketBasiIstek");
                }

                $instaIDs = empty($s["excludedInstaIDs"]) ? "0" : $s["excludedInstaIDs"];
                $users    = $this->db->query("SELECT uyeID,instaID,kullaniciAdi,sifre,isWebCookie FROM uye WHERE canStoryView=1 AND isActive=1 AND isUsable=1 AND instaID NOT IN($instaIDs)  ORDER BY sonOlayTarihi ASC LIMIT :adet", array("adet" => $adet));
                if(empty($users)) {
                    $this->db->query("UPDATE bayi_islem SET isActive=0 WHERE bayiIslemID=:bayiIslemID", array(
                        "bayiIslemID" => $s["bayiIslemID"]
                    ));

                }
                $allUserIDs = array_map(function($d) {
                    return $d["uyeID"];
                }, $users);
                $allUserIDs = implode(",", $allUserIDs);
                $this->db->query("UPDATE uye SET sonOlayTarihi=NOW() WHERE uyeID IN (" . $allUserIDs . ")");
                $this->db->CloseConnection();
                $stories = json_decode($s["allStories"], TRUE);
                if(count($stories) == 0) {
                    return FALSE;
                }

                $allStories = json_decode($s["allStories"], TRUE);
                foreach($allStories["reel"]["items"] AS $item) {
                    $items[] = array(
                        "getTakenAt" => $item["taken_at"],
                        "itemID"     => $item["id"],
                        "userPK"     => $s["userID"]
                    );
                }
                $bulkReaction      = new BulkReaction($users, Wow::get("ayar/bayiEsZamanliIstek"));
                $response          = $bulkReaction->storyview($items);
                $triedUsers        = $response["users"] ? $response["users"] : array();
                $totalSuccessCount = $response["totalSuccessCount"];

                $allSuccessInstaIDs = array_filter(array_map(function($d) {
                    return $d["status"] == "success" ? $d["instaID"] : NULL;
                }, $triedUsers), function($d) {
                    return $d !== NULL;
                });
                $excludedInstaIDs   = empty($s["excludedInstaIDs"]) ? "0" : $s["excludedInstaIDs"];
                if(!empty($allSuccessInstaIDs)) {
                    $excludedInstaIDs .= "," . implode(",", $allSuccessInstaIDs);
                }

                $isActive     = ($s["krediLeft"] - intval($totalSuccessCount)) <= 0 ? 0 : 1;
                $successCount = ($s["krediLeft"] - intval($totalSuccessCount)) > 0 ? intval($totalSuccessCount) : $s["krediLeft"];

                $this->db->query("UPDATE bayi_islem SET krediLeft=krediLeft-:successCount, isActive=:isActive,excludedInstaIDs=:excludedInstaIDs WHERE bayiIslemID=:bayiIslemID", array(
                    "bayiIslemID"      => $s["bayiIslemID"],
                    "successCount"     => $successCount,
                    "isActive"         => $isActive,
                    "excludedInstaIDs" => $excludedInstaIDs
                ));

                if($isActive == 0) {
                    $this->db->query("UPDATE bayi_islem SET excludedInstaIDs=NULL WHERE bayiIslemID=:bayiIslemID", ["bayiIslemID" => $s["bayiIslemID"]]);
                }

            } else if($s["islemTip"] == "save") {

                if($adet > Wow::get("ayar/bayiPaketBasiIstek")) {
                    $adet = Wow::get("ayar/bayiPaketBasiIstek");
                }


                $instaIDs = empty($s["excludedInstaIDs"]) ? "0" : $s["excludedInstaIDs"];

                $users = $this->db->query("SELECT uyeID,instaID,kullaniciAdi,sifre,isWebCookie  FROM uye WHERE isActive=1 AND isUsable=1 AND instaID NOT IN($instaIDs)  ORDER BY sonOlayTarihi ASC LIMIT :adet", array("adet" => $adet));

                if(empty($users)) {
                    $this->db->query("UPDATE bayi_islem SET isActive=0 WHERE bayiIslemID=:bayiIslemID", array(
                        "bayiIslemID" => $s["bayiIslemID"]
                    ));
                }
                $allUserIDs = array_map(function($d) {
                    return $d["uyeID"];
                }, $users);
                $allUserIDs = implode(",", $allUserIDs);
                $this->db->query("UPDATE uye SET sonOlayTarihi=NOW() WHERE uyeID IN (" . $allUserIDs . ")");
                $this->db->CloseConnection();

                $bulkReaction      = new BulkReaction($users, Wow::get("ayar/bayiEsZamanliIstek"));
                $response          = $bulkReaction->save($s["mediaID"], $s["mediaCode"]);
                $triedUsers        = $response["users"] ? $response["users"] : array();
                $totalSuccessCount = $response["totalSuccessCount"];


                $allSuccessInstaIDs = array_filter(array_map(function($d) {
                    return isset($d["status"]) && $d["status"] == "success" ? $d["instaID"] : NULL;
                }, $triedUsers), function($d) {
                    return $d !== NULL;
                });
                $excludedInstaIDs   = empty($s["excludedInstaIDs"]) ? "0" : $s["excludedInstaIDs"];
                if(!empty($allSuccessInstaIDs)) {
                    $excludedInstaIDs .= "," . implode(",", $allSuccessInstaIDs);
                }

                $isActive     = ($s["krediLeft"] - intval($totalSuccessCount)) <= 0 ? 0 : 1;
                $successCount = ($s["krediLeft"] - intval($totalSuccessCount)) > 0 ? intval($totalSuccessCount) : $s["krediLeft"];

                $this->db->query("UPDATE bayi_islem SET krediLeft=krediLeft-:successCount, isActive=:isActive,excludedInstaIDs=:excludedInstaIDs WHERE bayiIslemID=:bayiIslemID", array(
                    "bayiIslemID"      => $id,
                    "successCount"     => $successCount,
                    "isActive"         => $isActive,
                    "excludedInstaIDs" => $excludedInstaIDs
                ));

                if($isActive == 0) {
                    $this->db->query("UPDATE bayi_islem SET excludedInstaIDs=NULL WHERE bayiIslemID=:bayiIslemID", ["bayiIslemID" => $s["bayiIslemID"]]);
                }

            } else if($s["islemTip"] == "videoview") {
                if($adet > Wow::get("ayar/bayiPaketBasiIstek")) {
                    $adet = Wow::get("ayar/bayiPaketBasiIstek");
                }
                $users = $this->db->query("SELECT uyeID,instaID,kullaniciAdi,sifre,isWebCookie  FROM uye WHERE isActive=1 ORDER BY sonOlayTarihi ASC LIMIT :adet", array("adet" => $adet));
                if(empty($users)) {
                    $this->db->query("UPDATE bayi_islem SET isActive=0 WHERE bayiIslemID=:bayiIslemID", array(
                        "bayiIslemID" => $s["bayiIslemID"]
                    ));

                }
                $allUserIDs = array_map(function($d) {
                    return $d["uyeID"];
                }, $users);
                $allUserIDs = implode(",", $allUserIDs);
                $this->db->query("UPDATE uye SET sonOlayTarihi=NOW() WHERE uyeID IN (" . $allUserIDs . ")");
                $this->db->CloseConnection();

                $bulkReaction      = new BulkReaction($users, Wow::get("ayar/bayiEsZamanliIstek"));
                $response          = $bulkReaction->izlenme($s["mediaCode"]);
                $totalSuccessCount = $response["totalSuccessCount"];

                $isActive     = ($s["krediLeft"] - intval($totalSuccessCount)) <= 0 ? 0 : 1;
                $successCount = ($s["krediLeft"] - intval($totalSuccessCount)) > 0 ? intval($totalSuccessCount) : $s["krediLeft"];

                $this->db->query("UPDATE bayi_islem SET krediLeft=krediLeft-:successCount, isActive=:isActive WHERE bayiIslemID=:bayiIslemID", array(
                    "bayiIslemID"  => $s["bayiIslemID"],
                    "successCount" => $successCount,
                    "isActive"     => $isActive
                ));
            } else if($s["islemTip"] == "comment") {

                $arrComment = json_decode($s["allComments"], TRUE);

                $adet = count($arrComment);

                if($adet > $s["krediLeft"]) {
                    $adet = $s["krediLeft"];
                }
                if($adet > Wow::get("ayar/bayiPaketBasiIstek")) {
                    $adet = Wow::get("ayar/bayiPaketBasiIstek");
                }

                $users = $this->db->query("SELECT uyeID,instaID,kullaniciAdi,sifre,isWebCookie FROM uye WHERE canComment=1 AND isActive=1 AND isUsable=1 ORDER BY sonOlayTarihi ASC LIMIT :adet", array("adet" => $adet));

                $allUserIDs = array_map(function($d) {
                    return $d["uyeID"];
                }, $users);
                $allUserIDs = implode(",", $allUserIDs);
                $this->db->query("UPDATE uye SET sonOlayTarihi=NOW() WHERE uyeID IN (" . $allUserIDs . ")");
                $this->db->CloseConnection();

                $bulkReaction      = new BulkReaction($users, Wow::get("ayar/bayiEsZamanliIstek"));
                $response          = $bulkReaction->comment($s["mediaID"], $s["mediaCode"], $arrComment);
                $totalSuccessCount = $response["totalSuccessCount"];

                $isActive     = ($s["krediLeft"] - intval($totalSuccessCount)) <= 0 ? 0 : 1;
                $successCount = ($s["krediLeft"] - intval($totalSuccessCount)) > 0 ? intval($totalSuccessCount) : $s["krediLeft"];

                $this->db->query("UPDATE bayi_islem SET krediLeft=krediLeft-:successCount,isActive=:isActive WHERE bayiIslemID=:bayiIslemID", array(
                    "bayiIslemID"  => $s["bayiIslemID"],
                    "successCount" => $successCount,
                    "isActive"     => $isActive
                ));
            } else if($s["islemTip"] == "commentlike") {

                if($adet > Wow::get("ayar/bayiPaketBasiIstek")) {
                    $adet = Wow::get("ayar/bayiPaketBasiIstek");
                }

                $instaIDs = empty($s["excludedInstaIDs"]) ? "0" : $s["excludedInstaIDs"];

                $users = $this->db->query("SELECT uyeID,instaID,kullaniciAdi,sifre,isWebCookie  FROM uye WHERE isActive=1 AND instaID NOT IN(" . $instaIDs . ") ORDER BY sonOlayTarihi ASC LIMIT :adet", array("adet" => $adet));
                if(empty($users)) {
                    $this->db->query("UPDATE bayi_islem SET isActive=0 WHERE bayiIslemID=:bayiIslemID", array(
                        "bayiIslemID" => $s["bayiIslemID"]
                    ));
                }

                $allUserIDs = array_map(function($d) {
                    return $d["uyeID"];
                }, $users);
                $allUserIDs = implode(",", $allUserIDs);

                if(!empty($allUserIDs)) {
                    $this->db->query("UPDATE uye SET sonOlayTarihi=NOW() WHERE uyeID IN (" . $allUserIDs . ")");
                }

                $this->db->CloseConnection();

                $bulkReaction      = new BulkReaction($users, Wow::get("ayar/bayiEsZamanliIstek"));
                $response          = $bulkReaction->commentlike($s["mediaID"], $s["likedCommentID"]);
                $triedUsers        = $response["users"] ? $response["users"] : array();
                $totalSuccessCount = $response["totalSuccessCount"];

                $allSuccessInstaIDs = array_filter(array_map(function($d) {
                    return $d["status"] == "success" ? $d["instaID"] : NULL;
                }, $triedUsers), function($d) {
                    return $d !== NULL;
                });

                $excludedInstaIDs = empty($s["excludedInstaIDs"]) ? "0" : $s["excludedInstaIDs"];
                if(!empty($allSuccessInstaIDs)) {
                    $excludedInstaIDs .= "," . implode(",", $allSuccessInstaIDs);
                }

                $isActive     = ($s["krediLeft"] - intval($totalSuccessCount)) <= 0 ? 0 : 1;
                $successCount = ($s["krediLeft"] - intval($totalSuccessCount)) > 0 ? $totalSuccessCount : $s["krediLeft"];

                $this->db->query("UPDATE bayi_islem SET krediLeft=krediLeft-:successCount, isActive=:isActive,excludedInstaIDs=:excludedInstaIDs WHERE bayiIslemID=:bayiIslemID", array(
                    "bayiIslemID"      => $s["bayiIslemID"],
                    "successCount"     => $successCount,
                    "isActive"         => $isActive,
                    "excludedInstaIDs" => $excludedInstaIDs
                ));
                if($isActive == 0) {
                    $this->db->query("UPDATE bayi_islem SET excludedInstaIDs=NULL WHERE bayiIslemID=:bayiIslemID", ["bayiIslemID" => $s["bayiIslemID"]]);
                }

            } else if($s["islemTip"] == "canliyayin") {

                $users = $this->db->query("SELECT uyeID,instaID,kullaniciAdi,sifre,isWebCookie  FROM uye WHERE isActive=1 ORDER BY sonOlayTarihi ASC LIMIT :adet", array("adet" => $adet));

                $allUserIDs = array_map(function($d) {
                    return $d["uyeID"];
                }, $users);
                $allUserIDs = implode(",", $allUserIDs);

                if(!empty($allUserIDs)) {
                    $this->db->query("UPDATE uye SET sonOlayTarihi=NOW() WHERE uyeID IN (" . $allUserIDs . ")");
                }

                $this->db->CloseConnection();

                $bulkReaction = new BulkReaction($users, $adet);
                $now          = strtotime("+10 MINUTES");
                while($now > strtotime(date("d-m-Y H:i:s"))) {
                    $bulkReaction->playLive($s["broadcastID"]);
                }

                $this->db->query("UPDATE bayi_islem SET krediLeft=krediLeft-:successCount, isActive=0 WHERE bayiIslemID=:bayiIslemID", array(
                    "bayiIslemID"  => $s["bayiIslemID"],
                    "successCount" => $adet
                ));

            }

            return $this->json(array(
                                   "status" => "success"
                               ));
        }


        function ControlBayiAutoLikeMediasAction($id) {
            $package = $this->db->row("SELECT * FROM bayi_islem WHERE isActive=1 AND islemTip='autolike' AND TIMESTAMPDIFF(MINUTE,sonKontrolTarihi,NOW()) > 4 AND bayiIslemID=:bayiIslemID", array("bayiIslemID" => $id));

            if(empty($package)) {
                return $this->notFound();
            }

            if(strtotime($package["endDate"]) < strtotime("now")) {
                $this->db->query("UPDATE bayi_islem SET isActive=2, sonKontrolTarihi = NOW(), krediLeft = 0 WHERE bayiIslemID=:bayiIslemID", array("bayiIslemID" => $package["bayiIslemID"]));

                return $this->json(array("status" => "success"));
            }

            $reactionUserID       = $this->findAReactionUser();
            $objInstagramReaction = new InstagramReaction($reactionUserID);
            if(empty($reactionUserID)) {
                return $this->json(["status" => "fail"]);
            }
            $userMedias = $objInstagramReaction->objInstagram->getUserFeed($package["userID"]);
            if($userMedias["status"] == "ok") {
                foreach($userMedias["items"] as $item) {
                    if($item["media_type"] != 3) {
                        if($item["taken_at"] > strtotime($package["sonKontrolTarihi"])) {
                            $rand    = rand($package["minKrediTotal"], $package["maxKrediTotal"]);
                            $kontrol = $this->db->row("SELECT * FROM uye_otobegenipaket_gonderi WHERE mediaCode=:code", array("code" => $item["code"]));

                            if(empty($kontrol)) {
                                $this->db->query("INSERT INTO uye_otobegenipaket_gonderi (bayiIslemID,mediaID,mediaCode,likeCountTotal, likeCountLeft, imageUrl, minuteDelay, krediDelay, gender) VALUES (:bayiIslemID,:mediaID,:mediaCode,:likeCountTotal, :likeCountLeft, :imageUrl, :minuteDelay, :krediDelay, :gender)", array(
                                    "bayiIslemID"    => $package["bayiIslemID"],
                                    "mediaID"        => $item["id"],
                                    "mediaCode"      => $item["code"],
                                    "likeCountTotal" => $rand,
                                    "likeCountLeft"  => $rand,
                                    "imageUrl"       => $item["media_type"] == 8 ? str_replace("http:", "https:", $item["carousel_media"][0]["image_versions2"]["candidates"][0]["url"]) : str_replace("http:", "https:", $item["image_versions2"]["candidates"][0]["url"]),
                                    "minuteDelay"    => $package["minuteDelay"],
                                    "krediDelay"     => $package["krediDelay"],
                                    "gender"         => $package["gender"]
                                ));
                            }

                        } else {
                            break;
                        }
                    }
                }
                $this->db->query("UPDATE bayi_islem SET sonKontrolTarihi = NOW() WHERE bayiIslemID=:bayiIslemID", array("bayiIslemID" => $package["bayiIslemID"]));
            }

            return $this->json(array("status" => "success"));
        }


    }