<?php

    namespace App\Controllers\Admin;

    use App\Libraries\InstagramReaction;
    use App\Models\Notification;
    use BulkReaction;
    use Wow;
    use Wow\Net\Response;


    class InstaController extends BaseController {


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
            if($this->request->method == "POST" && !empty($this->request->data["username"])) {
                $username                = trim($this->request->data["username"]);
                $this->instagramReaction = new InstagramReaction($this->findAReactionUser());
                $user                    = $this->instagramReaction->objInstagram->getUserInfoByName($username);
                if($user["status"] != "ok") {
                    $objNotification             = new Notification();
                    $objNotification->type       = $objNotification::PARAM_TYPE_WARNING;
                    $objNotification->title      = "Kullanıcı Yok!";
                    $objNotification->messages[] = $username . " kodlu kullanıcı instagram üzerinde bulunamadı! Hatalı girmediğinizden emin olun.";
                    $this->notifications[]       = $objNotification;
                } else {
                    $id = $user["user"]["pk"];

                    return $this->redirectToUrl(Wow::get("project/adminPrefix") . "/insta/user/" . $id);
                }
            }

            return $this->view();
        }

        function AutoLikePackagesAction($page = 1) {
            if(!intval($page) > 0) {
                $page = 1;
            }

            $limitCount = 50;
            $limitStart = (($page * $limitCount) - $limitCount);

            $sqlWhere = "WHERE 1=1";
            $arrSorgu = array();

            $q = $this->request->query->q;
            if(!empty($q)) {
                $sqlWhere      .= " AND (userName LIKE :q)";
                $arrSorgu["q"] = "%" . $q . "%";
            }


            $isActive = $this->request->query->isActive;
            if(!is_null($isActive) && $isActive !== "") {
                $sqlWhere             .= " AND isActive = :isActive";
                $arrSorgu["isActive"] = $isActive;
            }

            $data = $this->db->query("SELECT * FROM uye_otobegenipaket " . $sqlWhere . " ORDER BY id DESC LIMIT :limitStart,:limitCount", array_merge($arrSorgu, array(
                "limitStart" => $limitStart,
                "limitCount" => $limitCount
            )));


            $totalRows    = $this->db->single("SELECT COUNT(id) FROM uye_otobegenipaket " . $sqlWhere, $arrSorgu);
            $previousPage = $page > 1 ? $page - 1 : NULL;
            $nextPage     = $totalRows > $limitStart + $limitCount ? $page + 1 : NULL;
            $totalPage    = ceil($totalRows / $limitCount);
            $endIndex     = ($limitStart + $limitCount) <= $totalRows ? ($limitStart + $limitCount) : $totalRows;
            $pagination   = array(
                "recordCount"  => $totalRows,
                "pageSize"     => $limitCount,
                "pageCount"    => $totalPage,
                "activePage"   => $page,
                "previousPage" => $previousPage,
                "nextPage"     => $nextPage,
                "startIndex"   => $limitStart + 1,
                "endIndex"     => $endIndex
            );
            $this->view->set("pagination", $pagination);

            return $this->view($data);
        }

        function EditAutoLikePackageAction($id) {
            $paketID = intval($id);
            $paket   = $this->db->row("SELECT * FROM uye_otobegenipaket WHERE id=:paketID", array("paketID" => $paketID));
            if(empty($paket)) {
                return $this->notFound();
            }

            if($this->request->method == "POST") {
                $startDate    = date("Y-m-d H:i:s", strtotime($this->request->data->startDate));
                $endDate      = date("Y-m-d H:i:s", strtotime($this->request->data->endDate));
                $likeCountMin = intval($this->request->data->likeCountMin);
                if($likeCountMin < 1) {
                    $likeCountMin = 1;
                }
                $likeCountMax = intval($this->request->data->likeCountMax);
                if($likeCountMin < 1) {
                    $likeCountMax = 1;
                }
                $minuteDelay = intval($this->request->data->minuteDelay);
                if($minuteDelay < 1) {
                    $minuteDelay = 1;
                }
                $krediDelay = intval($this->request->data->krediDelay);
                if($krediDelay < 1) {
                    $krediDelay = 100;
                }
                if($krediDelay < ceil($likeCountMax / 20)) {
                    $krediDelay = ceil($likeCountMax / 20);
                }
                if($krediDelay > 250) {
                    $krediDelay = 250;
                }
                $gender = NULL;
                if(intval($this->request->data->gender) > 0) {
                    $gender = intval($this->request->data->gender) == 2 ? 2 : 1;
                }
                $isActive = intval($this->request->data->isActive);
                $this->db->query("UPDATE uye_otobegenipaket SET startDate = :startDate, endDate = :endDate, likeCountMin = :likeCountMin, likeCountMax = :likeCountMax,minuteDelay=:minuteDelay,krediDelay=:krediDelay, isActive = :isActive, gender = :gender WHERE id=:id", array(
                    "id"           => $paketID,
                    "startDate"    => $startDate,
                    "endDate"      => $endDate,
                    "likeCountMin" => $likeCountMin,
                    "likeCountMax" => $likeCountMax,
                    "isActive"     => $isActive,
                    "minuteDelay"  => $minuteDelay,
                    "krediDelay"   => $krediDelay,
                    "gender"       => $gender
                ));
                $objNotification             = new Notification();
                $objNotification->title      = "Paket Değişiklikleri Kaydedildi.";
                $objNotification->messages[] = "Pakette yaptığınız değişiklikler kaydedildi.";
                $objNotification->type       = $objNotification::PARAM_TYPE_SUCCESS;
                $this->notifications[]       = $objNotification;

                return $this->redirectToUrl($this->request->referrer);
            }

            $paketdetay = $this->db->query("SELECT * FROM uye_otobegenipaket_gonderi WHERE paketID=:paketID", array("paketID" => $paketID));
            $data       = array(
                "paket"      => $paket,
                "paketdetay" => $paketdetay
            );

            return $this->partialView($data, "admin/insta/edit-auto-like-package");
        }

        function AddAutoLikePackageAction($id) {
            if($this->request->method == "POST") {
                $startDate    = date("Y-m-d H:i:s", strtotime($this->request->data->startDate));
                $endDate      = date("Y-m-d H:i:s", strtotime($this->request->data->endDate));
                $likeCountMin = intval($this->request->data->likeCountMin);
                if($likeCountMin < 1) {
                    $likeCountMin = 1;
                }
                $likeCountMax = intval($this->request->data->likeCountMax);
                if($likeCountMin < 1) {
                    $likeCountMax = 1;
                }
                $minuteDelay = intval($this->request->data->minuteDelay);
                if($minuteDelay < 1) {
                    $minuteDelay = 1;
                }
                $krediDelay = intval($this->request->data->krediDelay);
                if($krediDelay < 1) {
                    $krediDelay = 100;
                }
                if($krediDelay < ceil($likeCountMax / 20)) {
                    $krediDelay = ceil($likeCountMax / 20);
                }
                if($krediDelay > 250) {
                    $krediDelay = 250;
                }
                $gender = NULL;
                if(intval($this->request->data->gender) > 0) {
                    $gender = intval($this->request->data->gender) == 2 ? 2 : 1;
                }
                $this->db->query("INSERT INTO uye_otobegenipaket (instaID,userName,imageUrl,startDate,endDate,lastControlDate,likeCountMin,likeCountMax,minuteDelay,krediDelay,isActive,gender) VALUES (:instaID,:userName,:imageUrl,:startDate,:endDate,NOW(),:likeCountMin, :likeCountMax,:minuteDelay,:krediDelay,1,:gender)", array(
                    "instaID"      => $id,
                    "userName"     => $this->request->data->userName,
                    "imageUrl"     => $this->request->data->imageUrl,
                    "startDate"    => $startDate,
                    "endDate"      => $endDate,
                    "likeCountMin" => $likeCountMin,
                    "likeCountMax" => $likeCountMax,
                    "minuteDelay"  => $minuteDelay,
                    "krediDelay"   => $krediDelay,
                    "gender"       => $gender
                ));
                $objNotification             = new Notification();
                $objNotification->title      = "Paket Tanımlandı.";
                $objNotification->messages[] = $id . " ID li instagram hesabı için paket tanımlandı.";
                $objNotification->type       = $objNotification::PARAM_TYPE_SUCCESS;
                $this->notifications[]       = $objNotification;

                return $this->redirectToUrl($this->request->referrer);
            }
            $this->instagramReaction = new InstagramReaction($this->findAReactionUser());
            $accountInfo             = $this->instagramReaction->objInstagram->getUserInfoById($id);

            return $this->partialView($accountInfo);
        }

        function UserAction($id) {
            $this->instagramReaction = new InstagramReaction($this->findAReactionUser());

            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "more":
                        $data = $this->instagramReaction->objInstagram->getUserFeed($id, $this->request->data->maxid);
                        $this->view->set("ajaxLoaded", 1);

                        return $this->partialView($data, 'admin/insta/list-media');
                        break;
                    case "addMember":
                        $uye = $this->db->row("SELECT * FROM uye WHERE instaID=:instaID", ["instaID" => $id]);
                        if(empty($uye)) {
                            $this->db->query("INSERT uye (instaID,kullaniciAdi,sifre,begeniKredi,takipKredi,yorumKredi,isUsable,isBayi) VALUES(:instaID,:kullaniciAdi,:sifre,:begeniKredi,:takipKredi,:yorumKredi, :isUsable, :isBayi)", array(
                                "instaID"      => $id,
                                "kullaniciAdi" => $this->request->data->kullaniciAdi,
                                "sifre"        => "NA",
                                "begeniKredi"  => intval($this->request->data->begeniKredi),
                                "takipKredi"   => intval($this->request->data->takipKredi),
                                "yorumKredi"   => intval($this->request->data->yorumKredi),
                                "isUsable"     => $this->request->data->isUsable == 1 ? 1 : 0,
                                "isBayi"       => $this->request->data->isBayi == 1 ? 1 : 0
                            ));

                            $objNotification             = new Notification();
                            $objNotification->title      = "Eklendi.";
                            $objNotification->messages[] = "Kullanıcı üye olarak eklendi.";
                            $objNotification->type       = $objNotification::PARAM_TYPE_SUCCESS;
                            $this->notifications[]       = $objNotification;
                        } else {
                            $objNotification             = new Notification();
                            $objNotification->title      = "Ekleme Başarısız!";
                            $objNotification->messages[] = "Kullanıcı üye olarak zaten ekli!";
                            $objNotification->type       = $objNotification::PARAM_TYPE_WARNING;
                            $this->notifications[]       = $objNotification;
                        }

                        return $this->redirectToUrl($this->request->referrer);
                        break;
                }
            }

            $accountInfo = $this->instagramReaction->objInstagram->getUserInfoById($id);

            if($accountInfo["status"] != "ok") {
                return $this->notFound();
            }

            $this->view->set("accountInfo", $accountInfo);

            $member = $this->db->row("SELECT * FROM uye WHERE instaID=:instaID", ["instaID" => $id]);
            if(!empty($member)) {
                $this->view->set("memberUrl", Wow::get("project/adminPrefix") . "/uyeler/uye-detay/" . $member["uyeID"]);

                $username        = $accountInfo["user"]["username"];
                $following_count = $accountInfo["user"]["following_count"];
                $follower_count  = $accountInfo["user"]["follower_count"];
                $profilePic      = $accountInfo["user"]["profile_pic_url"];
                $full_name       = preg_replace("/[^[:alnum:][:space:]]/u", "", $accountInfo["user"]["full_name"]);

                $this->db->query("UPDATE uye SET kullaniciAdi = :kullaniciAdi, takipciSayisi = :takipciSayisi,takipEdilenSayisi = :takipEdilenSayisi,profilFoto = :profilFoto,fullName = :fullName WHERE instaID = :instaID", array(
                    "kullaniciAdi"      => $username,
                    "takipciSayisi"     => $follower_count,
                    "takipEdilenSayisi" => $following_count,
                    "profilFoto"        => $profilePic,
                    "fullName"          => $full_name,
                    "instaID"           => $id
                ));
            }

            $data = NULL;
            if($accountInfo["user"]["is_private"] != 1) {
                $data = $this->instagramReaction->objInstagram->getUserFeed($id);
            }

            return $this->view($data);
        }


        function SendLikeAction($id) {
            $this->instagramReaction = new InstagramReaction($this->findAReactionUser());

            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "send":
                        $adet = intval($this->request->data->adet);
                        if($adet <= 0) {
                            $sonuc = array(
                                "status"  => "error",
                                "code"    => "nolimitdefined",
                                "message" => "Beğeni Eklenemedi. Adet tanımlanmadı!",
                                "users"   => array()
                            );

                            return $this->json($sonuc);
                        }

                        $gender = NULL;
                        if(intval($this->request->data->gender) > 0) {
                            $gender = intval($this->request->data->gender) == 2 ? 2 : 1;
                        }

                        $genderSql = "";
                        $arrGender = array();
                        if(!empty($gender)) {
                            $genderSql           = " AND gender=:gender";
                            $arrGender["gender"] = $gender;
                        }

                        if($adet > Wow::get("ayar/bayiPaketBasiIstek")) {
                            $adet = Wow::get("ayar/bayiPaketBasiIstek");
                        }

                        $likedUsers = array();
                        if(!isset($_SESSION["MediaLikersForMediaID" . $this->request->data->mediaID]) || !is_array($_SESSION["MediaLikersForMediaID" . $this->request->data->mediaID])) {
                            $likers = $this->instagramReaction->objInstagram->getMediaLikers($this->request->data->mediaID);
                            foreach($likers["users"] as $user) {
                                $likedUsers[] = $user["pk"];
                            }
                            $_SESSION["MediaLikersForMediaID" . $this->request->data->mediaID] = $likedUsers;
                        } else {
                            $likedUsers = (array)$_SESSION["MediaLikersForMediaID" . $this->request->data->mediaID];
                        }

                        $triedUserIDs = isset($_SESSION["TriedUsersForLikeMediaID" . $this->request->data->mediaID]) ? $_SESSION["TriedUsersForLikeMediaID" . $this->request->data->mediaID] : NULL;
                        if(empty($triedUserIDs)) {
                            $triedUserIDs = "0";
                        }
                        $userIDs = "0";
                        foreach(explode(",", $triedUserIDs) as $userID) {
                            if(intval($userID) > 0) {
                                $userIDs .= "," . intval($userID);
                            }
                        }


                        $instaIDs      = "0";
                        $likedInstaIDs = $likedUsers;
                        foreach($likedInstaIDs as $instaID) {
                            if(intval($instaID) > 0) {
                                $instaIDs .= "," . intval($instaID);
                            }
                        }

                        $users = $this->db->query("SELECT uyeID,instaID,kullaniciAdi,sifre,isWebCookie FROM uye WHERE isActive=1 AND canLike=1 and isUsable=1 AND uyeID NOT IN($userIDs) AND instaID NOT IN($instaIDs) " . $genderSql . " ORDER BY sonOlayTarihi ASC LIMIT :adet", array_merge($arrGender, array("adet" => $adet)));
                        if(empty($users)) {
                            $sonuc = array(
                                "status"  => "error",
                                "code"    => "nouserleft",
                                "message" => "Beğeni Eklenemedi. Kullanıcı kalmadı!",
                                "users"   => array()
                            );

                            return $this->json($sonuc);
                        }

                        $allUserIDs = array_map(function($d) {
                            return $d["uyeID"];
                        }, $users);
                        $allUserIDs = implode(",", $allUserIDs);
                        $this->db->query("UPDATE uye SET sonOlayTarihi=NOW() WHERE uyeID IN (" . $allUserIDs . ")");
                        $this->db->CloseConnection();

                        $bulkReaction      = new BulkReaction($users, Wow::get("ayar/bayiEsZamanliIstek"));
                        $response          = $bulkReaction->like($this->request->data->mediaID, $this->request->data->extraParams);
                        $triedUsers        = $response["users"];
                        $totalSuccessCount = $response["totalSuccessCount"];
                        $allUserIDs        = array_map(function($d) {
                            return $d["userID"];
                        }, $triedUsers);
                        if(!empty($allUserIDs)) {
                            $allUserIDs                                                           = implode(",", $allUserIDs);
                            $userIDs                                                              .= "," . $allUserIDs;
                            $_SESSION["TriedUsersForLikeMediaID" . $this->request->data->mediaID] = $userIDs;
                        }
                        $allFailedUserIDs = array_filter(array_map(function($d) {
                            if(isset($d["durum"]) && $d["durum"] == 0) {
                                $this->db->query("UPDATE uye SET isActive=2 WHERE uyeID=:uyeid", array("uyeid" => $d["userID"]));
                            }

                            return $d["status"] == "fail" ? $d["userID"] : NULL;
                        }, $triedUsers), function($d) {
                            return $d !== NULL;
                        });
                        if(!empty($allFailedUserIDs)) {
                            $allFailedUserIDs = implode(",", $allFailedUserIDs);
                            $this->db->query("UPDATE uye SET canLike=0,canLikeControlDate=NOW() WHERE uyeID IN (" . $allFailedUserIDs . ")");
                        }

                        $sonuc = array(
                            "status"  => "success",
                            "message" => "Başarılı.",
                            "users"   => $triedUsers
                        );


                        return $this->json($sonuc);
                        break;
                }
            }
            //GET Method
            $media = $this->instagramReaction->objInstagram->getMediaInfo($id);
            if($media["status"] != "ok") {
                return $this->notFound();
            }

            return $this->partialView($media);

        }


        function SendSaveAction($id) {
            $this->instagramReaction = new InstagramReaction($this->findAReactionUser());

            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "send":
                        $adet = intval($this->request->data->adet);
                        if($adet <= 0) {
                            $sonuc = array(
                                "status"  => "error",
                                "code"    => "nolimitdefined",
                                "message" => "Kaydetme Eklenemedi. Adet tanımlanmadı!",
                                "users"   => array()
                            );

                            return $this->json($sonuc);
                        }

                        if($adet > Wow::get("ayar/bayiPaketBasiIstek")) {
                            $adet = Wow::get("ayar/bayiPaketBasiIstek");
                        }

                        $triedUserIDs = isset($_SESSION["TriedUsersForLikeMediaID" . $this->request->data->mediaID]) ? $_SESSION["TriedUsersForLikeMediaID" . $this->request->data->mediaID] : NULL;
                        if(empty($triedUserIDs)) {
                            $triedUserIDs = "0";
                        }
                        $userIDs = "0";
                        foreach(explode(",", $triedUserIDs) as $userID) {
                            if(intval($userID) > 0) {
                                $userIDs .= "," . intval($userID);
                            }
                        }

                        $users = $this->db->query("SELECT uyeID,instaID,kullaniciAdi,sifre,isWebCookie FROM uye WHERE isActive=1 AND canLike=1 and isUsable=1 AND uyeID NOT IN($userIDs) ORDER BY sonOlayTarihi ASC LIMIT :adet", array("adet" => $adet));
                        if(empty($users)) {
                            $sonuc = array(
                                "status"  => "error",
                                "code"    => "nouserleft",
                                "message" => "Kaydetme Eklenemedi. Kullanıcı kalmadı!",
                                "users"   => array()
                            );

                            return $this->json($sonuc);
                        }

                        $allUserIDs = array_map(function($d) {
                            return $d["uyeID"];
                        }, $users);
                        $allUserIDs = implode(",", $allUserIDs);
                        $this->db->query("UPDATE uye SET sonOlayTarihi=NOW() WHERE uyeID IN (" . $allUserIDs . ")");
                        $this->db->CloseConnection();

                        $bulkReaction      = new BulkReaction($users, Wow::get("ayar/bayiEsZamanliIstek"));
                        $response          = $bulkReaction->save($this->request->data->mediaID, $this->request->data->mediaCode);
                        $triedUsers        = $response["users"];
                        $totalSuccessCount = $response["totalSuccessCount"];
                        $allUserIDs        = array_map(function($d) {
                            return $d["userID"];
                        }, $triedUsers);
                        if(!empty($allUserIDs)) {
                            $allUserIDs                                                           = implode(",", $allUserIDs);
                            $userIDs                                                              .= "," . $allUserIDs;
                            $_SESSION["TriedUsersForLikeMediaID" . $this->request->data->mediaID] = $userIDs;
                        }

                        $sonuc = array(
                            "status"  => "success",
                            "message" => "Başarılı.",
                            "users"   => $triedUsers
                        );

                        return $this->json($sonuc);
                        break;
                }
            }
            //GET Method
            $media = $this->instagramReaction->objInstagram->getMediaInfo($id);
            if($media["status"] != "ok") {
                return $this->notFound();
            }

            return $this->partialView($media);

        }


        function SendGoruntulenmeAction($id) {
            $this->instagramReaction = new InstagramReaction($this->findAReactionUser());

            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "send":
                        $adet = intval($this->request->data->adet);
                        if($adet <= 0) {
                            $sonuc = array(
                                "status"  => "error",
                                "code"    => "nolimitdefined",
                                "message" => "Görüntülenme Eklenemedi. Adet tanımlanmadı!",
                                "users"   => array()
                            );

                            return $this->json($sonuc);
                        }

                        if($adet > Wow::get("ayar/bayiPaketBasiIstek")) {
                            $adet = Wow::get("ayar/bayiPaketBasiIstek");
                        }

                        $users = $this->db->query("SELECT uyeID,instaID,kullaniciAdi,sifre,isWebCookie FROM uye WHERE isActive=1 AND canLike=1 and isUsable=1 ORDER BY sonOlayTarihi ASC LIMIT :adet", array("adet" => $adet));
                        if(empty($users)) {
                            $sonuc = array(
                                "status"  => "error",
                                "code"    => "nouserleft",
                                "message" => "Görüntülenme Eklenemedi. Kullanıcı kalmadı!",
                                "users"   => array()
                            );

                            return $this->json($sonuc);
                        }

                        $allUserIDs = array_map(function($d) {
                            return $d["uyeID"];
                        }, $users);
                        $allUserIDs = implode(",", $allUserIDs);
                        $this->db->query("UPDATE uye SET sonOlayTarihi=NOW() WHERE uyeID IN (" . $allUserIDs . ")");
                        $this->db->CloseConnection();

                        $bulkReaction = new BulkReaction($users, Wow::get("ayar/bayiEsZamanliIstek"));
                        $response     = $bulkReaction->izlenme($this->request->data->mediaCode);
                        $triedUsers   = $response["users"];

                        $sonuc = array(
                            "status"  => "success",
                            "message" => "Başarılı.",
                            "users"   => $triedUsers
                        );


                        return $this->json($sonuc);
                        break;
                }
            }
            //GET Method
            $media = $this->instagramReaction->objInstagram->getMediaInfo($id);
            if($media["status"] != "ok") {
                return $this->notFound();
            }

            return $this->partialView($media);

        }

        function CanliYayinAction($id) {
            $this->instagramReaction = new InstagramReaction($this->findAReactionUser());

            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "send":

                        $adet   = intval($this->request->data->adet);
                        $dakika = intval($this->request->data->dakika);
                        if($adet <= 0) {
                            $sonuc = array(
                                "status"  => "error",
                                "code"    => "nolimitdefined",
                                "message" => "Canlı Yayına Kişi Eklenemedi. Adet tanımlanmadı!",
                                "users"   => array()
                            );

                            return $this->json($sonuc);
                        }

                        $gender = NULL;
                        if(intval($this->request->data->gender) > 0) {
                            $gender = intval($this->request->data->gender) == 2 ? 2 : 1;
                        }

                        $genderSql = "";
                        $arrGender = array();
                        if(!empty($gender)) {
                            $genderSql           = " AND gender=:gender";
                            $arrGender["gender"] = $gender;
                        }

                        $broadcast = $this->instagramReaction->objInstagram->getliveInfoByName($id);

                        if(!isset($broadcast["broadcast"]["id"])) {
                            $sonuc = array(
                                "status"  => "error",
                                "code"    => "nolimitdefined",
                                "message" => "Canlı Yayın Bulunamadı. Kişinin aktif canlı yayını bulunamadı!",
                                "users"   => array()
                            );

                            return $this->json($sonuc);
                        }

                        $users = $this->db->query("SELECT uyeID,instaID,kullaniciAdi,sifre,isWebCookie FROM uye WHERE isActive=1 AND isUsable=1 " . $genderSql . " ORDER BY sonOlayTarihi ASC LIMIT :adet", array_merge($arrGender, array("adet" => $adet)));


                        if(empty($users)) {
                            $sonuc = array(
                                "status"  => "error",
                                "code"    => "nouserleft",
                                "message" => "Canlı Yayına Kullanıcı Eklenemedi. Kullanıcı kalmadı!",
                                "users"   => array()
                            );

                            return $this->json($sonuc);
                        }

                        $allUserIDs = array_map(function($d) {
                            return $d["uyeID"];
                        }, $users);
                        $allUserIDs = implode(",", $allUserIDs);
                        $this->db->query("UPDATE uye SET sonOlayTarihi=NOW() WHERE uyeID IN (" . $allUserIDs . ")");
                        $this->db->CloseConnection();

                        $bulkReaction = new BulkReaction($users, $adet);
                        $response     = array();
                        $now          = strtotime("+" . $dakika . " MINUTES");
                        while($now > strtotime(date("d-m-Y H:i:s"))) {
                            $bulkReaction->playLive($broadcast["broadcast"]["id"]);
                        }

                        $triedUsers        = $response["users"];

                        $sonuc = array(
                            "status"  => "success",
                            "message" => "Başarılı.",
                            "users"   => $triedUsers
                        );

                        return $this->json($sonuc);
                        break;
                }
            }
            //GET Method
            $user = $this->instagramReaction->objInstagram->getUserInfoById($id);
            if($user["status"] != "ok") {
                return $this->notFound();
            }

            return $this->partialView($user);

        }

        function SendCommentAction($id) {
            $this->instagramReaction = new InstagramReaction($this->findAReactionUser());

            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "send":

                        $arrCommentg = preg_split("/\\r\\n|\\r|\\n/", $this->request->data->yorum);
                        if(!is_array($arrCommentg) || empty($arrCommentg)) {
                            $sonuc = array(
                                "status"  => "error",
                                "message" => "Yorum Eklenemedi. En az 1 yorum yazmalısınız!",
                                "userID"  => 0
                            );

                            return $this->json($sonuc);
                        }

                        $arrComment = [];
                        foreach($arrCommentg as $comment) {
                            if(!empty($comment)) {
                                $arrComment[] = trim($comment);
                            }
                        }
                        $commentedIndexes = $this->request->query->clearCommentedIndex == 1 ? [] : $_SESSION["CommentedIndexesForMediaID" . $this->request->data->mediaID];
                        if(!is_array($commentedIndexes) || empty($commentedIndexes)) {
                            $commentedIndexes = [];
                        }

                        $arrComment = array_diff_key($arrComment, $commentedIndexes);

                        if(empty($arrComment)) {
                            $sonuc = array(
                                "status"  => "error",
                                "message" => "Yorum Eklenemedi. En az 1 yorum yazmalısınız!",
                                "userID"  => 0
                            );

                            return $this->json($sonuc);
                        }

                        $gender = NULL;
                        if(intval($this->request->data->gender) > 0) {
                            $gender = intval($this->request->data->gender) == 2 ? 2 : 1;
                        }

                        $genderSql = "";
                        $arrGender = array();
                        if(!empty($gender)) {
                            $genderSql           = " AND gender=:gender";
                            $arrGender["gender"] = $gender;
                        }

                        $adet = count($arrComment);

                        if($adet > Wow::get("ayar/bayiPaketBasiIstek")) {
                            $adet = Wow::get("ayar/bayiPaketBasiIstek");
                        }


                        $triedUserIDs = isset($_SESSION["TriedUsersForCommentMediaID" . $this->request->data->mediaID]) ? $_SESSION["TriedUsersForCommentMediaID" . $this->request->data->mediaID] : NULL;
                        if(empty($triedUserIDs)) {
                            $triedUserIDs = "0";
                        }
                        $userIDs = "0";
                        foreach(explode(",", $triedUserIDs) as $userID) {
                            if(intval($userID) > 0) {
                                $userIDs .= "," . intval($userID);
                            }
                        }


                        $users = $this->db->query("SELECT uyeID,instaID,kullaniciAdi,sifre,isWebCookie FROM uye WHERE isActive=1 AND canComment=1 and isUsable=1 AND uyeID NOT IN($userIDs) " . $genderSql . " ORDER BY sonOlayTarihi ASC LIMIT :adet", array_merge($arrGender, array("adet" => $adet)));

                        if(empty($users)) {
                            $sonuc = array(
                                "status"  => "error",
                                "code"    => "nouserleft",
                                "message" => "Yorum Eklenemedi. Kullanıcı kalmadı!",
                                "users"   => array()
                            );

                            return $this->json($sonuc);
                        }

                        $allUserIDs = array_map(function($d) {
                            return $d["uyeID"];
                        }, $users);
                        $allUserIDs = implode(",", $allUserIDs);
                        $this->db->query("UPDATE uye SET sonOlayTarihi=NOW() WHERE uyeID IN (" . $allUserIDs . ")");
                        $this->db->CloseConnection();

                        $bulkReaction      = new BulkReaction($users, Wow::get("ayar/bayiEsZamanliIstek"));
                        $response          = $bulkReaction->comment($this->request->data->mediaID, $this->request->data->mediaCode, $arrComment);
                        $triedUsers        = $response["users"];
                        $totalSuccessCount = $response["totalSuccessCount"];
                        $allUserIDs        = array_map(function($d) {
                            return $d["userID"];
                        }, $triedUsers);
                        if(!empty($allUserIDs)) {
                            $allUserIDs                                                              = implode(",", $allUserIDs);
                            $userIDs                                                                 .= "," . $allUserIDs;
                            $_SESSION["TriedUsersForCommentMediaID" . $this->request->data->mediaID] = $userIDs;
                        }
                        $allFailedUserIDs = array_filter(array_map(function($d) {
                            if(isset($d["durum"]) && $d["durum"] == 0) {
                                $this->db->query("UPDATE uye SET isActive=2 WHERE uyeID=:uyeid", array("uyeid" => $d["userID"]));
                            }

                            return $d["status"] == "fail" ? $d["userID"] : NULL;
                        }, $triedUsers), function($d) {
                            return $d !== NULL;
                        });
                        if(!empty($allFailedUserIDs)) {
                            $allFailedUserIDs = implode(",", $allFailedUserIDs);
                            $this->db->query("UPDATE uye SET canComment=0,canCommentControlDate=NOW() WHERE uyeID IN (" . $allFailedUserIDs . ")");
                        }

                        $sonuc = array(
                            "status"  => "success",
                            "message" => "Başarılı.",
                            "users"   => $triedUsers
                        );

                        foreach($triedUsers as $i => $v) {
                            if($v["status"] == "success") {
                                $commentedIndexes[$v["commentIndex"]] = TRUE;
                            }
                        }
                        $_SESSION["CommentedIndexesForMediaID" . $this->request->data->mediaID] = $commentedIndexes;


                        return $this->json($sonuc);
                        break;
                }
            }
            //GET Method
            $media = $this->instagramReaction->objInstagram->getMediaInfo($id);
            if($media["status"] != "ok") {
                return $this->notFound();
            }

            return $this->partialView($media);

        }


        function SendFollowerAction($id) {
            $this->instagramReaction = new InstagramReaction($this->findAReactionUser());

            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "send":

                        $adet = intval($this->request->data->adet);
                        if($adet <= 0) {
                            $sonuc = array(
                                "status"  => "error",
                                "code"    => "nolimitdefined",
                                "message" => "Takipçi Eklenemedi. Adet tanımlanmadı!",
                                "users"   => array()
                            );

                            return $this->json($sonuc);
                        }

                        $gender = NULL;
                        if(intval($this->request->data->gender) > 0) {
                            $gender = intval($this->request->data->gender) == 2 ? 2 : 1;
                        }

                        $genderSql = "";
                        $arrGender = array();
                        if(!empty($gender)) {
                            $genderSql           = " AND gender=:gender";
                            $arrGender["gender"] = $gender;
                        }

                        if($adet > Wow::get("ayar/bayiPaketBasiIstek")) {
                            $adet = Wow::get("ayar/bayiPaketBasiIstek");
                        }

                        $followedUsers = array();
                        if(!isset($_SESSION["FollowerssForInstaID" . $this->request->data->userID]) || !is_array($_SESSION["FollowerssForInstaID" . $this->request->data->userID])) {
                            $nextMaxID = NULL;
                            $intLoop   = 0;
                            while($follower = $this->instagramReaction->objInstagram->getUserFollowers($this->request->data->userID, $nextMaxID)) {
                                $intLoop++;
                                foreach($follower["users"] as $user) {
                                    $followedUsers[] = $user["pk"];
                                }
                                if(!isset($follower["next_max_id"]) || $intLoop >= 8) {
                                    break;
                                } else {
                                    $nextMaxID = $follower["next_max_id"];
                                }
                            }


                        } else {
                            $followedUsers = (array)$_SESSION["FollowerssForInstaID" . $this->request->data->userID];
                        }


                        $triedUserIDs = isset($_SESSION["TriedUsersForFollowInstaID" . $this->request->data->userID]) ? $_SESSION["TriedUsersForFollowInstaID" . $this->request->data->userID] : NULL;
                        if(empty($triedUserIDs)) {
                            $triedUserIDs = "0";
                        }
                        $userIDs = "0";
                        foreach(explode(",", $triedUserIDs) as $userID) {
                            if(intval($userID) > 0) {
                                $userIDs .= "," . intval($userID);
                            }
                        }

                        $instaIDs         = "0";
                        $followedInstaIDs = $followedUsers;
                        foreach($followedInstaIDs as $instaID) {
                            if(intval($instaID) > 0) {
                                $instaIDs .= "," . intval($instaID);
                            }
                        }

                        $users = $this->db->query("SELECT uyeID,instaID,kullaniciAdi,sifre,isWebCookie FROM uye WHERE isActive=1 AND canFollow=1 and isUsable=1 AND uyeID NOT IN($userIDs) AND instaID NOT IN($instaIDs) " . $genderSql . " ORDER BY sonOlayTarihi ASC LIMIT :adet", array_merge($arrGender, array("adet" => $adet)));


                        if(empty($users)) {
                            $sonuc = array(
                                "status"  => "error",
                                "code"    => "nouserleft",
                                "message" => "Takipçi Eklenemedi. Kullanıcı kalmadı!",
                                "users"   => array()
                            );

                            return $this->json($sonuc);
                        }

                        $allUserIDs = array_map(function($d) {
                            return $d["uyeID"];
                        }, $users);
                        $allUserIDs = implode(",", $allUserIDs);
                        $this->db->query("UPDATE uye SET sonOlayTarihi=NOW() WHERE uyeID IN (" . $allUserIDs . ")");
                        $this->db->CloseConnection();

                        $bulkReaction      = new BulkReaction($users, Wow::get("ayar/bayiEsZamanliIstek"));
                        $response          = $bulkReaction->follow($this->request->data->userID, $this->request->data->userName);
                        $triedUsers        = $response["users"];
                        $totalSuccessCount = $response["totalSuccessCount"];
                        $allUserIDs        = array_map(function($d) {
                            return $d["userID"];
                        }, $triedUsers);
                        if(!empty($allUserIDs)) {
                            $allUserIDs                                                            = implode(",", $allUserIDs);
                            $userIDs                                                               .= "," . $allUserIDs;
                            $_SESSION["TriedUsersForFollowInstaID" . $this->request->data->userID] = $userIDs;
                        }
                        $allFailedUserIDs = array_filter(array_map(function($d) {
                            if(isset($d["durum"]) && $d["durum"] == 0) {
                                $this->db->query("UPDATE uye SET isActive=2 WHERE uyeID=:uyeid", array("uyeid" => $d["userID"]));
                            }

                            return $d["status"] == "fail" ? $d["userID"] : NULL;
                        }, $triedUsers), function($d) {
                            return $d !== NULL;
                        });
                        if(!empty($allFailedUserIDs)) {
                            $allFailedUserIDs = implode(",", $allFailedUserIDs);
                            $this->db->query("UPDATE uye SET canFollow=0,canFollowControlDate=NOW() WHERE uyeID IN (" . $allFailedUserIDs . ")");
                        }


                        $sonuc = array(
                            "status"  => "success",
                            "message" => "Başarılı.",
                            "users"   => $triedUsers
                        );

                        return $this->json($sonuc);
                        break;
                }
            }
            //GET Method
            $user = $this->instagramReaction->objInstagram->getUserInfoById($id);
            if($user["status"] != "ok") {
                return $this->notFound();
            }

            return $this->partialView($user);

        }


        function StoryViewAction($id) {
            $this->instagramReaction = new InstagramReaction($this->findAReactionUser());

            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "send":

                        $adet = intval($this->request->data->adet);
                        if($adet <= 0) {
                            $sonuc = array(
                                "status"  => "error",
                                "code"    => "nolimitdefined",
                                "message" => "Story Görüntülenme Eklenemedi. Adet tanımlanmadı!",
                                "users"   => array()
                            );

                            return $this->json($sonuc);
                        }

                        $gender = NULL;
                        if(intval($this->request->data->gender) > 0) {
                            $gender = intval($this->request->data->gender) == 2 ? 2 : 1;
                        }

                        $genderSql = "";
                        $arrGender = array();
                        if(!empty($gender)) {
                            $genderSql           = " AND gender=:gender";
                            $arrGender["gender"] = $gender;
                        }

                        if($adet > Wow::get("ayar/bayiPaketBasiIstek")) {
                            $adet = Wow::get("ayar/bayiPaketBasiIstek");
                        }

                        $triedUserIDs = isset($_SESSION["TriedUsersForStoryViewInstaID" . $this->request->data->userID]) ? $_SESSION["TriedUsersForStoryViewInstaID" . $this->request->data->userID] : NULL;
                        if(empty($triedUserIDs)) {
                            $triedUserIDs = "0";
                        }
                        $userIDs = "0";
                        foreach(explode(",", $triedUserIDs) as $userID) {
                            if(intval($userID) > 0) {
                                $userIDs .= "," . intval($userID);
                            }
                        }

                        $instaIDs = "0";

                        $users = $this->db->query("SELECT uyeID,instaID,kullaniciAdi,sifre FROM uye WHERE isActive=1 AND canStoryView=1 AND uyeID NOT IN($userIDs) AND instaID NOT IN($instaIDs) " . $genderSql . " ORDER BY sonOlayTarihi ASC LIMIT :adet", array_merge($arrGender, array("adet" => $adet)));

                        if(empty($users)) {
                            $sonuc = array(
                                "status"  => "error",
                                "code"    => "nouserleft",
                                "message" => "Story Görüntülenme Eklenemedi. Kullanıcı kalmadı!",
                                "users"   => array()
                            );

                            return $this->json($sonuc);
                        }

                        $allUserIDs = array_map(function($d) {
                            return $d["uyeID"];
                        }, $users);
                        $allUserIDs = implode(",", $allUserIDs);
                        $this->db->query("UPDATE uye SET sonOlayTarihi=NOW() WHERE uyeID IN (" . $allUserIDs . ")");
                        $this->db->CloseConnection();

                        $bulkReaction = new BulkReaction($users, Wow::get("ayar/bayiEsZamanliIstek"));

                        $items    = array();
                        $getItems = $this->instagramReaction->objInstagram->hikayecek($id);

                        if(count($getItems["reel"]["items"]) < 1) {
                            $sonuc = array(
                                "status"  => "error",
                                "code"    => "nolimitdefined",
                                "message" => "Kullanıcının aktif bir story paylaşımı bulunmamaktadır!",
                                "users"   => array()
                            );

                            return $this->json($sonuc);
                        }

                        foreach($getItems["reel"]["items"] AS $item) {
                            $items[] = array(
                                "getTakenAt" => $item["taken_at"],
                                "itemID"     => $item["id"],
                                "userPK"     => $id
                            );
                        }

                        $response = $bulkReaction->storyview($items);

                        $triedUsers        = $response["users"];
                        $totalSuccessCount = $response["totalSuccessCount"];
                        $allUserIDs        = array_map(function($d) {
                            return $d["userID"];
                        }, $triedUsers);
                        if(!empty($allUserIDs)) {
                            $allUserIDs                                                               = implode(",", $allUserIDs);
                            $userIDs                                                                  .= "," . $allUserIDs;
                            $_SESSION["TriedUsersForStoryViewInstaID" . $this->request->data->userID] = $userIDs;
                        }
                        $allFailedUserIDs = array_filter(array_map(function($d) {
                            if(isset($d["durum"]) && $d["durum"] == 0) {
                                $this->db->query("UPDATE uye SET isActive=2 WHERE uyeID=:uyeid", array("uyeid" => $d["userID"]));
                            }

                            return $d["status"] == "fail" ? $d["userID"] : NULL;
                        }, $triedUsers), function($d) {
                            return $d !== NULL;
                        });
                        if(!empty($allFailedUserIDs)) {
                            $allFailedUserIDs = implode(",", $allFailedUserIDs);
                            $this->db->query("UPDATE uye SET canStoryView=0,canStoryViewControlDate=NOW() WHERE uyeID IN (" . $allFailedUserIDs . ")");
                        }

                        $sonuc = array(
                            "status"  => "success",
                            "message" => "Başarılı.",
                            "users"   => $triedUsers
                        );

                        return $this->json($sonuc);
                        break;
                }
            }
            //GET Method
            $user = $this->instagramReaction->objInstagram->getUserInfoById($id);
            if($user["status"] != "ok") {
                return $this->notFound();
            }

            return $this->partialView($user);

        }
    }