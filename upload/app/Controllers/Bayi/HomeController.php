<?php



    namespace App\Controllers\Bayi;



    use App\Libraries\InstagramReaction;

    use App\Models\Notification;

    use BulkReaction;

    use DateTime;

    use SmmApi;

    use Wow;

    use Wow\Net\Response;



    class HomeController extends BaseController {



        /**

         * @var array $data

         */

        protected $data;

        protected $comments     = array();

        protected $commentCount = 0;





        function onActionExecuting() {

            if(($actionResponse = parent::onActionExecuting()) instanceof Response) {

                return $actionResponse;

            }

            //Üye girişi kontrolü.

            if(($pass = $this->middleware("logged")) instanceof Response) {

                return $pass;

            }

        }



        /**

         * @param string $type

         *

         * @return bool

         */

        private function hasLimit($type) {

            switch($type) {

                case "like":

                    return $this->logonPerson->member->gunlukBegeniLimitLeft > 0;

                    break;

                case "follow":

                    return $this->logonPerson->member->gunlukTakipLimitLeft > 0;

                    break;

                case "comment":

                    return $this->logonPerson->member->gunlukYorumLimitLeft > 0;

                    break;

                case "commentlike":

                    return $this->logonPerson->member->gunlukYorumBegeniLimitLeft > 0;

                    break;

                case "canli":

                    return $this->logonPerson->member->gunlukCanliYayinLimitLeft > 0;

                    break;

                case "videoview":

                    return $this->logonPerson->member->gunlukVideoLimitLeft > 0;

                    break;

                case "save":

                    return $this->logonPerson->member->gunlukSaveLimitLeft > 0;

                    break;

                case "story":

                    return $this->logonPerson->member->gunlukStoryLimitLeft > 0;

                    break;

                case "autolike":

                    return $this->logonPerson->member->toplamOtoBegeniLimitLeft > 0;

                    break;

                default:

                    return FALSE;

            }

        }





        function IndexAction() {

            $this->view->set("title", "Bayi");



            if(isset($this->request->query->changeapi)) {

                $this->db->query("UPDATE bayi SET apiKey=:apikey WHERE bayiID=:bayiID", array(

                    "bayiID" => $this->logonPerson->member->bayiID,

                    "apikey" => md5(microtime() . rand(0, 999999))

                ));



                return $this->redirectToUrl($this->request->referrer);

            }



            $data = $this->db->row("SELECT * FROM bayi WHERE bayiID=:bayiid", array("bayiid" => $this->logonPerson->member->bayiID));



            return $this->view($data);

        }





        function TaleplerAction($id = 1) {



            $this->view->set('title', "Talepler");



            $data          = array();

            $sayfa         = $id == 1 ? 0 : ($id * 100) - 100;

            $data["sayfa"] = $id;



            if($this->request->ajax) {





                $talepler = $this->db->query("SELECT talepID FROM smm_talepler WHERE bayiID=:bayiid ORDER BY talepID DESC LIMIT {$sayfa},100", array("bayiid" => $this->logonPerson->member->bayiID));



                if(count($talepler) > 0) {

                    $talep = array();



                    foreach($talepler AS $t) {

                        $talep[] = $t["talepID"];

                    }



                    $total = $this->db->single("SELECT COUNT(talepID) FROM smm_talepler WHERE bayiID=:bayiid", array("bayiid" => $this->logonPerson->member->bayiID));



                    $data = array(

                        SmmApi::postDataSmm("talepler", array("talepler" => implode(',', $talep))),

                        "page" => array(

                            "total" => $total,

                            "now"   => $id

                        )

                    );



                    return $this->json($data);

                } else {

                    return $this->json(array(

                                           "status" => 2,

                                           "error"  => "Yapmış olduğunuz bir talep bulunamamıştır."

                                       ));

                }





            }



            return $this->view($data);



        }



        function ApiDocsAction() {



            $this->view->set("title", "Api Dökümanı");



            $data = array();



            return $this->view($data);



        }





        function SendLikeAction($id = NULL) {

            $this->view->set("title", "Beğeni Gönderim Aracı");



            if(!$this->hasLimit("like") && $this->request->query->bayiIslemID == NULL) {

                if($this->request->method == "POST") {

                    $sonuc = array(

                        "status"  => "error",

                        "code"    => "error",

                        "message" => "İşlem limitiniz yok!",

                        "users"   => array()

                    );



                    return $this->json($sonuc);

                } else {

                    return $this->view("like", "bayi/home/no-limit");

                }

            }



            if($this->request->method == "POST") {

                $this->instagramReaction = new InstagramReaction($this->findAReactionUser());

                switch($this->request->query->formType) {

                    case "findMediaID":

                        $mediaData = $this->instagramReaction->getMediaData($this->request->data->mediaUrl);

                        if(!$mediaData) {

                            return $this->notFound();

                        } else {

                            $mediaID = $mediaData["media_id"];



                            return $this->redirectToUrl(Wow::get("project/resellerPrefix") . "/home/send-like/" . $mediaID);

                        }

                        break;

                    case "send":

                        $arrErrors   = array();

                        $bayiIslemID = intval($this->request->query->bayiIslemID);

                        $bayiIslem   = NULL;

                        if($bayiIslemID > 0) {

                            $bayiIslem = $this->db->row("SELECT * FROM bayi_islem WHERE bayiIslemID=:bayiIslemID AND bayiID=:bayiID AND isActive=1 AND islemTip='like'", array(

                                "bayiIslemID" => $bayiIslemID,

                                "bayiID"      => $this->logonPerson->member->bayiID

                            ));

                            if(empty($bayiIslem)) {

                                $arrErrors[] = "İlgili işlem kaydı bulunamadı!";

                            }

                        } else {

                            $adet = intval($this->request->data->adet);

                            if(!$adet > 0) {

                                $arrErrors[] = "Adet'i hatalı girdiniz.";

                            }

                            if($adet > $this->logonPerson->member->begeniMaxKredi) {

                                $arrErrors[] = "Girdiğiniz adet, girebileceğiniz max adetten büyük!";

                            }

                            if(!$this->logonPerson->member->gunlukBegeniLimitLeft > 0) {

                                $arrErrors[] = "İşlem hakkınız kalmadı!";

                            }

                        }

                        if(!empty($arrErrors)) {

                            $sonuc = array(

                                "status"  => "error",

                                "code"    => "error",

                                "message" => implode(",", $arrErrors),

                                "users"   => array()

                            );



                            return $this->json($sonuc);

                        }



                        if(!($bayiIslemID > 0)) {



                            $findMedia = $this->instagramReaction->objInstagram->getMediaInfo($this->request->data->mediaID);

                            if($findMedia["status"] == "fail") {

                                $sonuc = array(

                                    "status"  => "error",

                                    "code"    => "error",

                                    "message" => "Media bulunamadı! Silinmiş olabilir.",

                                    "users"   => array()

                                );



                                return $this->json($sonuc);

                            }





                            $likedUsers = array();

                            $likers     = $this->instagramReaction->objInstagram->getMediaLikers($this->request->data->mediaID);

                            foreach($likers["users"] as $user) {

                                $likedUsers[] = $user["pk"];

                            }





                            $instaIDs      = "0";

                            $likedInstaIDs = $likedUsers;

                            foreach($likedInstaIDs as $instaID) {

                                if(intval($instaID) > 0) {

                                    $instaIDs .= "," . intval($instaID);

                                }

                            }



                            $gender = NULL;

                            if($this->logonPerson->member->begeniGender === 1 && intval($this->request->data->gender) > 0) {

                                $gender = intval($this->request->data->gender) == 2 ? 2 : 1;

                            }

                            $this->db->query("INSERT INTO bayi_islem (bayiID,islemTip,mediaID,mediaCode,userID,userName,imageUrl,krediTotal,krediLeft, excludedInstaIDs,gender) VALUES(:bayiID,:islemTip,:mediaID,:mediaCode,:userID,:userName,:imageUrl,:krediTotal,:krediLeft, :excludedInstaIDs,:gender)", array(

                                "bayiID"           => $this->logonPerson->member->bayiID,

                                "islemTip"         => "like",

                                "mediaID"          => $this->request->data->mediaID,

                                "mediaCode"        => $this->request->data->mediaCode,

                                "userID"           => $this->request->data->userID,

                                "userName"         => $this->request->data->userName,

                                "imageUrl"         => $this->request->data->imageUrl,

                                "krediTotal"       => $adet,

                                "krediLeft"        => $adet,

                                "excludedInstaIDs" => $instaIDs,

                                "gender"           => $gender

                            ));

                            $bayiIslemID = $this->db->lastInsertId();



                            $this->db->query("UPDATE bayi SET gunlukBegeniLimitLeft = gunlukBegeniLimitLeft - 1 WHERE bayiID=:bayiID", array("bayiID" => $this->logonPerson->member->bayiID));

                            $this->logonPerson->member->gunlukBegeniLimitLeft--;





                            $sonuc = array(

                                "status"      => "success",

                                "message"     => "Başarılı.",

                                "users"       => array(),

                                "bayiIslemID" => $bayiIslemID

                            );



                            return $this->json($sonuc);

                        }



                        $adet = $bayiIslem["krediLeft"];

                        if($adet > Wow::get("ayar/bayiPaketBasiIstek")) {

                            $adet = Wow::get("ayar/bayiPaketBasiIstek");

                        }





                        $instaIDs  = empty($bayiIslem["excludedInstaIDs"]) ? "0" : $bayiIslem["excludedInstaIDs"];

                        $genderSql = "";

                        $arrGender = array();

                        if(intval($bayiIslem["gender"]) > 0) {

                            $genderSql           = " AND gender=:gender";

                            $arrGender["gender"] = intval($bayiIslem["gender"]);

                        }



                        $users = $this->db->query("SELECT uyeID,instaID,kullaniciAdi,sifre,isWebCookie  FROM uye WHERE canLike=1 AND isActive=1 AND isUsable=1 AND instaID NOT IN($instaIDs) " . $genderSql . "  ORDER BY sonOlayTarihi ASC LIMIT :adet", array_merge($arrGender, array("adet" => $adet)));

                        if(empty($users)) {

                            $this->db->query("UPDATE bayi_islem SET isActive=0 WHERE bayiIslemID=:bayiIslemID", array(

                                "bayiIslemID" => $bayiIslem["bayiIslemID"]

                            ));



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

                        $response          = $bulkReaction->like($bayiIslem["mediaID"], $bayiIslem["userName"], $bayiIslem["userID"]);

                        $triedUsers        = $response["users"];

                        $totalSuccessCount = $response["totalSuccessCount"];



                        $allFailedUserIDs = array_filter(array_map(function($d) {

                            return $d["status"] == "fail" ? $d["userID"] : NULL;

                        }, $triedUsers), function($d) {

                            return $d !== NULL;

                        });

                        if(!empty($allFailedUserIDs)) {

                            $allFailedUserIDs = implode(",", $allFailedUserIDs);

                            $this->db->query("UPDATE uye SET canLike=0,canLikeControlDate=NOW() WHERE uyeID IN (" . $allFailedUserIDs . ")");

                        }



                        $allSuccessInstaIDs = array_filter(array_map(function($d) {

                            return $d["status"] == "success" ? $d["instaID"] : NULL;

                        }, $triedUsers), function($d) {

                            return $d !== NULL;

                        });

                        $excludedInstaIDs   = empty($bayiIslem["excludedInstaIDs"]) ? "0" : $bayiIslem["excludedInstaIDs"];

                        if(!empty($allSuccessInstaIDs)) {

                            $excludedInstaIDs .= "," . implode(",", $allSuccessInstaIDs);

                        }



                        $isActive = ($bayiIslem["krediLeft"] - intval($totalSuccessCount)) <= 0 ? 0 : 1;

                        $this->db->query("UPDATE bayi_islem SET krediLeft=krediLeft-:successCount, isActive=:isActive,excludedInstaIDs=:excludedInstaIDs WHERE bayiIslemID=:bayiIslemID", array(

                            "bayiIslemID"      => $bayiIslem["bayiIslemID"],

                            "successCount"     => $totalSuccessCount,

                            "isActive"         => $isActive,

                            "excludedInstaIDs" => $excludedInstaIDs

                        ));

                        if($isActive == 0) {

                            $this->db->query("UPDATE bayi_islem SET excludedInstaIDs=NULL WHERE bayiIslemID=:bayiIslemID", ["bayiIslemID" => $bayiIslem["bayiIslemID"]]);

                        }



                        $sonuc = array(

                            "status"      => "success",

                            "message"     => "Başarılı.",

                            "users"       => $triedUsers,

                            "bayiIslemID" => $bayiIslemID

                        );



                        return $this->json($sonuc);

                        break;

                }

            } //GET Method

            else {

                if(!is_null($id)) {

                    $this->instagramReaction = new InstagramReaction($this->findAReactionUser());

                    $media                   = $this->instagramReaction->objInstagram->getMediaInfo($id);



                    if($media["status"] != "ok") {

                        return $this->notFound();

                    }

                    $this->view->set("media", $media);

                }

            }



            $this->navigation->add("Beğeni Gönderimi", Wow::get("project/resellerPrefix") . "/home/send-like");



            return $this->view();

        }





        function SendVideoViewAction($id = NULL) {



            $this->view->set("title", "Video Görüntülenme Gönderim Aracı");



            if(!$this->hasLimit("videoview") && $this->request->query->bayiIslemID == NULL) {

                if($this->request->method == "POST") {

                    $sonuc = array(

                        "status"  => "error",

                        "code"    => "error",

                        "message" => "İşlem limitiniz yok!",

                        "users"   => array()

                    );



                    return $this->json($sonuc);

                } else {

                    return $this->view("videoview", "bayi/home/no-limit");

                }

            }



            if($this->request->method == "POST") {

                $this->instagramReaction = new InstagramReaction($this->findAReactionUser());

                switch($this->request->query->formType) {

                    case "findMediaID":

                        $mediaData = $this->instagramReaction->getMediaData($this->request->data->mediaUrl);

                        if(!$mediaData) {

                            return $this->notFound();

                        } else {

                            $mediaID = $mediaData["media_id"];



                            return $this->redirectToUrl(Wow::get("project/resellerPrefix") . "/home/send-video-view/" . $mediaID);

                        }

                        break;

                    case "send":

                        $arrErrors   = array();

                        $bayiIslemID = intval($this->request->query->bayiIslemID);

                        $bayiIslem   = NULL;

                        if($bayiIslemID > 0) {

                            $bayiIslem = $this->db->row("SELECT * FROM bayi_islem WHERE bayiIslemID=:bayiIslemID AND bayiID=:bayiID AND isActive=1 AND islemTip='videoview'", array(

                                "bayiIslemID" => $bayiIslemID,

                                "bayiID"      => $this->logonPerson->member->bayiID

                            ));

                            if(empty($bayiIslem)) {

                                $arrErrors[] = "İlgili işlem kaydı bulunamadı!";

                            }

                        } else {

                            $adet = intval($this->request->data->adet);

                            if(!$adet > 0) {

                                $arrErrors[] = "Adet'i hatalı girdiniz.";

                            }

                            if($adet > $this->logonPerson->member->videoMaxKredi) {

                                $arrErrors[] = "Girdiğiniz adet, girebileceğiniz max adetten büyük!";

                            }

                            if(!$this->logonPerson->member->gunlukVideoLimitLeft > 0) {

                                $arrErrors[] = "İşlem hakkınız kalmadı!";

                            }

                        }

                        if(!empty($arrErrors)) {

                            $sonuc = array(

                                "status"  => "error",

                                "code"    => "error",

                                "message" => implode(",", $arrErrors),

                                "users"   => array(),

                                "hata"    => 5

                            );



                            return $this->json($sonuc);

                        }



                        if(!($bayiIslemID > 0)) {



                            $findMedia = $this->instagramReaction->objInstagram->getMediaInfo($this->request->data->mediaID);

                            if($findMedia["status"] == "fail") {

                                $sonuc = array(

                                    "status"  => "error",

                                    "code"    => "error",

                                    "message" => "Media bulunamadı! Silinmiş olabilir.",

                                    "users"   => array(),

                                    "hata"    => 4

                                );



                                return $this->json($sonuc);

                            }



                            $this->db->query("INSERT INTO bayi_islem (bayiID,islemTip,mediaID,mediaCode,userID,userName,imageUrl,krediTotal,krediLeft) VALUES(:bayiID,:islemTip,:mediaID,:mediaCode,:userID,:userName,:imageUrl,:krediTotal,:krediLeft)", array(

                                "bayiID"     => $this->logonPerson->member->bayiID,

                                "islemTip"   => "videoview",

                                "mediaID"    => $this->request->data->mediaID,

                                "mediaCode"  => $this->request->data->mediaCode,

                                "userID"     => $this->request->data->userID,

                                "userName"   => $this->request->data->userName,

                                "imageUrl"   => $this->request->data->imageUrl,

                                "krediTotal" => $adet,

                                "krediLeft"  => $adet

                            ));

                            $bayiIslemID = $this->db->lastInsertId();



                            $this->db->query("UPDATE bayi SET gunlukVideoLimitLeft = gunlukVideoLimitLeft - 1 WHERE bayiID=:bayiID", array("bayiID" => $this->logonPerson->member->bayiID));

                            $this->logonPerson->member->gunlukVideoLimitLeft--;





                            $sonuc = array(

                                "status"      => "success",

                                "message"     => "Başarılı.",

                                "users"       => array(),

                                "bayiIslemID" => $bayiIslemID,

                                "hata"        => 3

                            );



                            return $this->json($sonuc);

                        }



                        $adet = $bayiIslem["krediLeft"];

                        if($adet > Wow::get("ayar/bayiPaketBasiIstek")) {

                            $adet = Wow::get("ayar/bayiPaketBasiIstek");

                        }



                        if(empty($adet)) {

                            $sonuc = array(

                                "status"      => "fail",

                                "message"     => "Kredi kalmadı.",

                                "users"       => array(),

                                "bayiIslemID" => $bayiIslemID

                            );



                            return $this->json($sonuc);

                        }



                        $users = $this->db->query("SELECT uyeID,instaID,kullaniciAdi,sifre,isWebCookie  FROM uye WHERE isActive=1 ORDER BY sonOlayTarihi ASC LIMIT :adet", array("adet" => $adet));





                        if(empty($users)) {

                            $this->db->query("UPDATE bayi_islem SET isActive=0 WHERE bayiIslemID=:bayiIslemID", array(

                                "bayiIslemID" => $bayiIslem["bayiIslemID"]

                            ));



                            $sonuc = array(

                                "status"  => "error",

                                "code"    => "nouserleft",

                                "message" => "Video görüntülenme Eklenemedi. Kullanıcı kalmadı!",

                                "users"   => array(),

                                "hata"    => 1

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

                        $response     = $bulkReaction->izlenme($bayiIslem["mediaCode"]);



                        $triedUsers        = $response["users"];

                        $totalSuccessCount = $response["totalSuccessCount"];



                        $isActive = ($bayiIslem["krediLeft"] - intval($totalSuccessCount)) <= 0 ? 0 : 1;

                        $this->db->query("UPDATE bayi_islem SET krediLeft=krediLeft-:successCount, isActive=:isActive WHERE bayiIslemID=:bayiIslemID", array(

                            "bayiIslemID"  => $bayiIslem["bayiIslemID"],

                            "successCount" => $totalSuccessCount > 0 ? $totalSuccessCount : 0,

                            "isActive"     => $isActive

                        ));



                        $sonuc = array(

                            "status"      => "success",

                            "message"     => "Başarılı.",

                            "users"       => $triedUsers,

                            "bayiIslemID" => $bayiIslemID,

                            "hata"        => 2

                        );



                        return $this->json($sonuc);

                        break;

                }

            } //GET Method

            else {

                if(!is_null($id)) {

                    $this->instagramReaction = new InstagramReaction($this->findAReactionUser());

                    $media                   = $this->instagramReaction->objInstagram->getMediaInfo($id);

                    if($media["status"] != "ok") {

                        return $this->notFound();

                    }

                    $this->view->set("media", $media);

                }

            }



            $this->navigation->add("Video Görüntülenme Gönderimi", Wow::get("project/resellerPrefix") . "/home/send-video-view");



            return $this->view();

        }





        function SendCanliYayinAction($id = NULL) {

            $this->view->set("title", "Canlı Yayına İzleyici Gönderim Aracı");



            if(!$this->hasLimit("canli") && $this->request->query->bayiIslemID == NULL) {

                if($this->request->method == "POST") {

                    $sonuc = array(

                        "status"  => "error",

                        "code"    => "error",

                        "message" => "İşlem limitiniz yok!",

                        "users"   => array()

                    );



                    return $this->json($sonuc);

                } else {

                    return $this->view("canli", "bayi/home/no-limit");

                }

            }



            if($this->request->method == "POST") {

                $this->instagramReaction = new InstagramReaction($this->findAReactionUser());

                switch($this->request->query->formType) {

                    case "findUserID":

                        $userData = $this->instagramReaction->objInstagram->getUserInfoByName($this->request->data->username);

                        if($userData["status"] != "ok") {

                            $objNotification           = new Notification();

                            $objNotification->type     = $objNotification::PARAM_TYPE_DANGER;

                            $objNotification->title    = "Hata!";

                            $objNotification->messages = ["Aradığınız üyelik bulunumadı!"];

                            $this->notifications[]     = $objNotification;



                            return $this->redirectToUrl($this->request->referrer);

                        } else {

                            $userID = $userData["user"]["pk"];



                            return $this->redirectToUrl(Wow::get("project/resellerPrefix") . "/home/send-canli-yayin/" . $userID);

                        }

                        break;

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





                        $this->db->query("UPDATE bayi SET gunlukCanliYayinLimitLeft = gunlukCanliYayinLimitLeft - 1 WHERE bayiID=:bayiID", array("bayiID" => $this->logonPerson->member->bayiID));

                        $this->logonPerson->member->gunlukCanliYayinLimitLeft--;



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

                            $bulkReaction->playLive($broadcast["broadcast"]["id"]);

                            $bulkReaction->playLive($broadcast["broadcast"]["id"]);

                        }



                        $triedUsers        = $response["users"];

                        $totalSuccessCount = $response["totalSuccessCount"];





                        $sonuc = array(

                            "status"  => "success",

                            "message" => "Başarılı."

                        );



                        return $this->json($sonuc);

                        break;

                }

            } //GET Method

            else {

                if(!is_null($id)) {

                    $this->instagramReaction = new InstagramReaction($this->findAReactionUser());

                    $user                    = $this->instagramReaction->objInstagram->getUserInfoById($id);

                    if($user["status"] != "ok") {

                        return $this->notFound();

                    }

                    $this->view->set("user", $user);

                }

            }



            $this->navigation->add("Takipçi Gönderimi", Wow::get("project/resellerPrefix") . "/home/send-follower");



            return $this->view();



        }



        function SendSaveAction($id = NULL) {

            $this->view->set("title", "Kaydetme Gönderim Aracı");



            if(!$this->hasLimit("save") && $this->request->query->bayiIslemID == NULL) {

                if($this->request->method == "POST") {

                    $sonuc = array(

                        "status"  => "error",

                        "code"    => "error",

                        "message" => "İşlem limitiniz yok!",

                        "users"   => array()

                    );



                    return $this->json($sonuc);

                } else {

                    return $this->view("save", "bayi/home/no-limit");

                }

            }



            if($this->request->method == "POST") {

                $this->instagramReaction = new InstagramReaction($this->findAReactionUser());

                switch($this->request->query->formType) {

                    case "findMediaID":

                        $mediaData = $this->instagramReaction->getMediaData($this->request->data->mediaUrl);

                        if(!$mediaData) {

                            return $this->notFound();

                        } else {

                            $mediaID = $mediaData["media_id"];



                            return $this->redirectToUrl(Wow::get("project/resellerPrefix") . "/home/send-save/" . $mediaID);

                        }

                        break;

                    case "send":

                        $arrErrors   = array();

                        $bayiIslemID = intval($this->request->query->bayiIslemID);

                        $bayiIslem   = NULL;

                        if($bayiIslemID > 0) {

                            $bayiIslem = $this->db->row("SELECT * FROM bayi_islem WHERE bayiIslemID=:bayiIslemID AND bayiID=:bayiID AND isActive=1 AND islemTip='save'", array(

                                "bayiIslemID" => $bayiIslemID,

                                "bayiID"      => $this->logonPerson->member->bayiID

                            ));

                            if(empty($bayiIslem)) {

                                $arrErrors[] = "İlgili işlem kaydı bulunamadı!";

                            }

                        } else {

                            $adet = intval($this->request->data->adet);

                            if(!$adet > 0) {

                                $arrErrors[] = "Adet'i hatalı girdiniz.";

                            }

                            if($adet > $this->logonPerson->member->saveMaxKredi) {

                                $arrErrors[] = "Girdiğiniz adet, girebileceğiniz max adetten büyük!";

                            }

                            if(!$this->logonPerson->member->gunlukSaveLimitLeft > 0) {

                                $arrErrors[] = "İşlem hakkınız kalmadı!";

                            }

                        }

                        if(!empty($arrErrors)) {

                            $sonuc = array(

                                "status"  => "error",

                                "code"    => "error",

                                "message" => implode(",", $arrErrors),

                                "users"   => array()

                            );



                            return $this->json($sonuc);

                        }



                        if(!($bayiIslemID > 0)) {



                            $findMedia = $this->instagramReaction->objInstagram->getMediaInfo($this->request->data->mediaID);

                            if($findMedia["status"] == "fail") {

                                $sonuc = array(

                                    "status"  => "error",

                                    "code"    => "error",

                                    "message" => "Media bulunamadı! Silinmiş olabilir.",

                                    "users"   => array()

                                );



                                return $this->json($sonuc);

                            }





                            $likedUsers = array();

                            $likers     = $this->instagramReaction->objInstagram->getMediaLikers($this->request->data->mediaID);

                            foreach($likers["users"] as $user) {

                                $likedUsers[] = $user["pk"];

                            }





                            $instaIDs      = "0";

                            $likedInstaIDs = $likedUsers;

                            foreach($likedInstaIDs as $instaID) {

                                if(intval($instaID) > 0) {

                                    $instaIDs .= "," . intval($instaID);

                                }

                            }



                            $this->db->query("INSERT INTO bayi_islem (bayiID,islemTip,mediaID,mediaCode,userID,userName,imageUrl,krediTotal,krediLeft, excludedInstaIDs) VALUES(:bayiID,:islemTip,:mediaID,:mediaCode,:userID,:userName,:imageUrl,:krediTotal,:krediLeft, :excludedInstaIDs)", array(

                                "bayiID"           => $this->logonPerson->member->bayiID,

                                "islemTip"         => "save",

                                "mediaID"          => $this->request->data->mediaID,

                                "mediaCode"        => $this->request->data->mediaCode,

                                "userID"           => $this->request->data->userID,

                                "userName"         => $this->request->data->userName,

                                "imageUrl"         => $this->request->data->imageUrl,

                                "krediTotal"       => $adet,

                                "krediLeft"        => $adet,

                                "excludedInstaIDs" => $instaIDs

                            ));

                            $bayiIslemID = $this->db->lastInsertId();



                            $this->db->query("UPDATE bayi SET gunlukSaveLimitLeft = gunlukSaveLimitLeft - 1 WHERE bayiID=:bayiID", array("bayiID" => $this->logonPerson->member->bayiID));

                            $this->logonPerson->member->gunlukSaveLimitLeft--;





                            $sonuc = array(

                                "status"      => "success",

                                "message"     => "Başarılı.",

                                "users"       => array(),

                                "bayiIslemID" => $bayiIslemID

                            );



                            return $this->json($sonuc);

                        }



                        $adet = $bayiIslem["krediLeft"];

                        if($adet > Wow::get("ayar/bayiPaketBasiIstek")) {

                            $adet = Wow::get("ayar/bayiPaketBasiIstek");

                        }





                        $instaIDs  = empty($bayiIslem["excludedInstaIDs"]) ? "0" : $bayiIslem["excludedInstaIDs"];

                        $genderSql = "";

                        $arrGender = array();

                        if(intval($bayiIslem["gender"]) > 0) {

                            $genderSql           = " AND gender=:gender";

                            $arrGender["gender"] = intval($bayiIslem["gender"]);

                        }



                        $users = $this->db->query("SELECT uyeID,instaID,kullaniciAdi,sifre,isWebCookie  FROM uye WHERE isActive=1 AND isUsable=1 AND instaID NOT IN($instaIDs) " . $genderSql . "  ORDER BY sonOlayTarihi ASC LIMIT :adet", array_merge($arrGender, array("adet" => $adet)));

                        if(empty($users)) {

                            $this->db->query("UPDATE bayi_islem SET isActive=0 WHERE bayiIslemID=:bayiIslemID", array(

                                "bayiIslemID" => $bayiIslem["bayiIslemID"]

                            ));



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

                        $response          = $bulkReaction->save($bayiIslem["mediaID"], $bayiIslem["mediaCode"]);

                        $triedUsers        = $response["users"];

                        $totalSuccessCount = $response["totalSuccessCount"];



                        $allSuccessInstaIDs = array_filter(array_map(function($d) {

                            return $d["status"] == "success" ? $d["instaID"] : NULL;

                        }, $triedUsers), function($d) {

                            return $d !== NULL;

                        });

                        $excludedInstaIDs   = empty($bayiIslem["excludedInstaIDs"]) ? "0" : $bayiIslem["excludedInstaIDs"];

                        if(!empty($allSuccessInstaIDs)) {

                            $excludedInstaIDs .= "," . implode(",", $allSuccessInstaIDs);

                        }



                        $isActive = ($bayiIslem["krediLeft"] - intval($totalSuccessCount)) <= 0 ? 0 : 1;

                        $this->db->query("UPDATE bayi_islem SET krediLeft=krediLeft-:successCount, isActive=:isActive,excludedInstaIDs=:excludedInstaIDs WHERE bayiIslemID=:bayiIslemID", array(

                            "bayiIslemID"      => $bayiIslem["bayiIslemID"],

                            "successCount"     => $totalSuccessCount,

                            "isActive"         => $isActive,

                            "excludedInstaIDs" => $excludedInstaIDs

                        ));

                        if($isActive == 0) {

                            $this->db->query("UPDATE bayi_islem SET excludedInstaIDs=NULL WHERE bayiIslemID=:bayiIslemID", ["bayiIslemID" => $bayiIslem["bayiIslemID"]]);

                        }



                        $sonuc = array(

                            "status"      => "success",

                            "message"     => "Başarılı.",

                            "users"       => $triedUsers,

                            "bayiIslemID" => $bayiIslemID

                        );



                        return $this->json($sonuc);

                        break;

                }

            } //GET Method

            else {

                if(!is_null($id)) {

                    $this->instagramReaction = new InstagramReaction($this->findAReactionUser());

                    $media                   = $this->instagramReaction->objInstagram->getMediaInfo($id);



                    if($media["status"] != "ok") {

                        return $this->notFound();

                    }

                    $this->view->set("media", $media);

                }

            }



            $this->navigation->add("Kaydetme Gönderimi", Wow::get("project/resellerPrefix") . "/home/send-save");



            return $this->view();

        }





        function SendFollowerAction($id = NULL) {

            $this->view->set("title", "Takipçi Gönderim Aracı");



            if(!$this->hasLimit("follow") && $this->request->query->bayiIslemID == NULL) {

                if($this->request->method == "POST") {

                    $sonuc = array(

                        "status"  => "error",

                        "code"    => "error",

                        "message" => "İşlem limitiniz yok!",

                        "users"   => array()

                    );



                    return $this->json($sonuc);

                } else {

                    return $this->view("follow", "bayi/home/no-limit");

                }

            }



            if($this->request->method == "POST") {

                $this->instagramReaction = new InstagramReaction($this->findAReactionUser());

                switch($this->request->query->formType) {

                    case "findUserID":

                        $userData = $this->instagramReaction->objInstagram->getUserInfoByName($this->request->data->username);

                        if($userData["status"] != "ok") {

                            return $this->notFound();

                        } else {

                            $userID = $userData["user"]["pk"];



                            return $this->redirectToUrl(Wow::get("project/resellerPrefix") . "/home/send-follower/" . $userID);

                        }

                        break;

                    case "send":

                        $arrErrors   = array();

                        $bayiIslemID = intval($this->request->query->bayiIslemID);

                        $bayiIslem   = NULL;

                        if($bayiIslemID > 0) {

                            $bayiIslem = $this->db->row("SELECT * FROM bayi_islem WHERE bayiIslemID=:bayiIslemID AND bayiID=:bayiID AND isActive=1 AND islemTip='follow'", array(

                                "bayiIslemID" => $bayiIslemID,

                                "bayiID"      => $this->logonPerson->member->bayiID

                            ));

                            if(empty($bayiIslem)) {

                                $arrErrors[] = "İlgili işlem kaydı bulunamadı!";

                            }

                        } else {

                            $adet = intval($this->request->data->adet);

                            if(!$adet > 0) {

                                $arrErrors[] = "Adet'i hatalı girdiniz.";

                            }

                            if($adet > $this->logonPerson->member->takipMaxKredi) {

                                $arrErrors[] = "Girdiğiniz adet, girebileceğiniz max adetten büyük!";

                            }

                            if(!$this->logonPerson->member->gunlukTakipLimitLeft > 0) {

                                $arrErrors[] = "İşlem hakkınız kalmadı!";

                            }

                        }

                        if(!empty($arrErrors)) {

                            $sonuc = array(

                                "status"  => "error",

                                "code"    => "error",

                                "message" => implode(",", $arrErrors),

                                "users"   => array()

                            );



                            return $this->json($sonuc);

                        }



                        if(!($bayiIslemID > 0)) {



                            $followedUsers = array();

                            $nextMaxID     = NULL;

                            $intLoop       = 0;

                            while($follower = $this->instagramReaction->objInstagram->getUserFollowers($this->request->data->userID, $nextMaxID)) {

                                $intLoop++;

                                foreach($follower["users"] as $user) {

                                    $followedUsers[] = $user["pk"];

                                }

                                if(!isset($follower["next_max_id"]) || $intLoop >= 5) {

                                    break;

                                } else {

                                    $nextMaxID = $follower["next_max_id"];

                                }

                            }



                            $instaIDs         = "0";

                            $followedInstaIDs = $followedUsers;

                            foreach($followedInstaIDs as $instaID) {

                                if(intval($instaID) > 0) {

                                    $instaIDs .= "," . intval($instaID);

                                }

                            }



                            $gender = NULL;

                            if($this->logonPerson->member->takipGender === 1 && intval($this->request->data->gender) > 0) {

                                $gender = intval($this->request->data->gender) == 2 ? 2 : 1;

                            }

                            $this->db->query("INSERT INTO bayi_islem (bayiID,islemTip,userID,userName,imageUrl,krediTotal,krediLeft,excludedInstaIDs,gender) VALUES(:bayiID,:islemTip,:userID,:userName,:imageUrl,:krediTotal,:krediLeft,:excludedInstaIDs,:gender)", array(

                                "bayiID"           => $this->logonPerson->member->bayiID,

                                "islemTip"         => "follow",

                                "userID"           => $this->request->data->userID,

                                "userName"         => $this->request->data->userName,

                                "imageUrl"         => $this->request->data->imageUrl,

                                "krediTotal"       => $adet,

                                "krediLeft"        => $adet,

                                "excludedInstaIDs" => $instaIDs,

                                "gender"           => $gender

                            ));



                            $bayiIslemID = $this->db->lastInsertId();



                            $this->db->query("UPDATE bayi SET gunlukTakipLimitLeft = gunlukTakipLimitLeft - 1 WHERE bayiID=:bayiID", array("bayiID" => $this->logonPerson->member->bayiID));

                            $this->logonPerson->member->gunlukTakipLimitLeft--;



                            $sonuc = array(

                                "status"      => "success",

                                "message"     => "Başarılı.",

                                "users"       => array(),

                                "bayiIslemID" => $bayiIslemID

                            );



                            return $this->json($sonuc);

                        }



                        $adet = $bayiIslem["krediLeft"];

                        if($adet > Wow::get("ayar/bayiPaketBasiIstek")) {

                            $adet = Wow::get("ayar/bayiPaketBasiIstek");

                        }





                        $instaIDs  = empty($bayiIslem["excludedInstaIDs"]) ? "0" : $bayiIslem["excludedInstaIDs"];

                        $genderSql = "";

                        $arrGender = array();

                        if(intval($bayiIslem["gender"]) > 0) {

                            $genderSql           = " AND gender=:gender";

                            $arrGender["gender"] = intval($bayiIslem["gender"]);

                        }

                        $users = $this->db->query("SELECT uyeID,instaID,kullaniciAdi,sifre,isWebCookie FROM uye WHERE canFollow=1 AND isActive=1 AND isUsable=1 AND instaID NOT IN($instaIDs) " . $genderSql . " ORDER BY sonOlayTarihi ASC LIMIT :adet", array_merge($arrGender, array("adet" => $adet)));

                        if(empty($users)) {

                            $this->db->query("UPDATE bayi_islem SET isActive=0 WHERE bayiIslemID=:bayiIslemID", array(

                                "bayiIslemID" => $bayiIslem["bayiIslemID"]

                            ));



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

                        $response          = $bulkReaction->follow($bayiIslem["userID"], $bayiIslem["userName"]);

                        $triedUsers        = $response["users"];

                        $totalSuccessCount = $response["totalSuccessCount"];



                        $allFailedUserIDs = array_filter(array_map(function($d) {

                            return $d["status"] == "fail" ? $d["userID"] : NULL;

                        }, $triedUsers), function($d) {

                            return $d !== NULL;

                        });

                        if(!empty($allFailedUserIDs)) {

                            $allFailedUserIDs = implode(",", $allFailedUserIDs);

                            $this->db->query("UPDATE uye SET canFollow=0,canFollowControlDate=NOW() WHERE uyeID IN (" . $allFailedUserIDs . ")");

                        }



                        $allSuccessInstaIDs = array_filter(array_map(function($d) {

                            return $d["status"] == "success" ? $d["instaID"] : NULL;

                        }, $triedUsers), function($d) {

                            return $d !== NULL;

                        });

                        $excludedInstaIDs   = empty($bayiIslem["excludedInstaIDs"]) ? "0" : $bayiIslem["excludedInstaIDs"];

                        if(!empty($allSuccessInstaIDs)) {

                            $excludedInstaIDs .= "," . implode(",", $allSuccessInstaIDs);

                        }



                        $isActive = ($bayiIslem["krediLeft"] - intval($totalSuccessCount)) <= 0 ? 0 : 1;

                        $this->db->query("UPDATE bayi_islem SET krediLeft=krediLeft-:successCount, isActive=:isActive,excludedInstaIDs=:excludedInstaIDs WHERE bayiIslemID=:bayiIslemID", array(

                            "bayiIslemID"      => $bayiIslem["bayiIslemID"],

                            "successCount"     => $totalSuccessCount,

                            "isActive"         => $isActive,

                            "excludedInstaIDs" => $excludedInstaIDs

                        ));



                        if($isActive == 0) {

                            $this->db->query("UPDATE bayi_islem SET excludedInstaIDs=NULL WHERE bayiIslemID=:bayiIslemID", ["bayiIslemID" => $bayiIslem["bayiIslemID"]]);

                        }



                        $sonuc = array(

                            "status"      => "success",

                            "message"     => "Başarılı.",

                            "users"       => $triedUsers,

                            "bayiIslemID" => $bayiIslemID

                        );



                        return $this->json($sonuc);



                        break;

                }

            } //GET Method

            else {

                if(!is_null($id)) {

                    $this->instagramReaction = new InstagramReaction($this->findAReactionUser());

                    $user                    = $this->instagramReaction->objInstagram->getUserInfoById($id);

                    if($user["status"] != "ok") {

                        return $this->notFound();

                    }

                    $this->view->set("user", $user);

                }

            }



            $this->navigation->add("Takipçi Gönderimi", Wow::get("project/resellerPrefix") . "/home/send-follower");



            return $this->view();

        }



        function SendCommentAction($id = NULL) {

            $this->view->set("title", "Yorum Gönderim Aracı");



            if(!$this->hasLimit("comment") && $this->request->query->bayiIslemID == NULL) {

                if($this->request->method == "POST") {

                    $sonuc = array(

                        "status"  => "error",

                        "code"    => "error",

                        "message" => "İşlem limitiniz yok!",

                        "users"   => array()

                    );



                    return $this->json($sonuc);

                } else {

                    return $this->view("comment", "bayi/home/no-limit");

                }

            }



            if($this->request->method == "POST") {

                $this->instagramReaction = new InstagramReaction($this->findAReactionUser());

                switch($this->request->query->formType) {

                    case "findMediaID":

                        $mediaData = $this->instagramReaction->getMediaData($this->request->data->mediaUrl);

                        if(!$mediaData) {

                            return $this->notFound();

                        } else {

                            $mediaID = $mediaData["media_id"];



                            return $this->redirectToUrl(Wow::get("project/resellerPrefix") . "/home/send-comment/" . $mediaID);

                        }

                        break;

                    case "send":

                        $arrErrors        = array();

                        $bayiIslemID      = intval($this->request->query->bayiIslemID);

                        $bayiIslem        = NULL;

                        $commentedIndexes = $this->request->query->clearCommentedIndex == 1 ? [] : $_SESSION["CommentedIndexesForMediaID" . $bayiIslem["mediaID"]];

                        if($bayiIslemID > 0) {

                            $bayiIslem = $this->db->row("SELECT * FROM bayi_islem WHERE bayiIslemID=:bayiIslemID AND bayiID=:bayiID AND isActive=1 AND islemTip='comment'", array(

                                "bayiIslemID" => $bayiIslemID,

                                "bayiID"      => $this->logonPerson->member->bayiID

                            ));

                            if(empty($bayiIslem)) {

                                $arrErrors[] = "İlgili işlem kaydı bulunamadı!";

                            }

                        } else {

                            $yorumlarg = preg_split("/\\r\\n|\\r|\\n/", $this->request->data->yorum);

                            $yorumlar  = [];

                            foreach($yorumlarg as $yorum) {

                                if(!empty($yorum)) {

                                    $yorumlar[] = trim($yorum);

                                }

                            }

                            $adet = count($yorumlar);

                            if(!is_array($yorumlar) || empty($yorumlar)) {

                                $arrErrors[] = "En az 1 yorum tanımlamalısınız.";

                            }

                            if($adet > $this->logonPerson->member->yorumMaxKredi) {

                                $arrErrors[] = "Girdiğiniz yorum adedi, girebileceğiniz max adetten büyük!";

                            }

                            if(!$this->logonPerson->member->gunlukYorumLimitLeft > 0) {

                                $arrErrors[] = "İşlem hakkınız kalmadı!";

                            }

                        }

                        if(!empty($arrErrors)) {

                            $sonuc = array(

                                "status"  => "error",

                                "code"    => "error",

                                "message" => implode(",", $arrErrors),

                                "users"   => array()

                            );



                            return $this->json($sonuc);

                        }



                        if(!($bayiIslemID > 0)) {



                            $findMedia = $this->instagramReaction->objInstagram->getMediaInfo($this->request->data->mediaID);

                            if($findMedia["status"] == "fail") {

                                $sonuc = array(

                                    "status"  => "error",

                                    "code"    => "error",

                                    "message" => "Media bulunamadı! Silinmiş olabilir.",

                                    "users"   => array()

                                );



                                return $this->json($sonuc);

                            }



                            $gender = NULL;

                            if($this->logonPerson->member->yorumGender === 1 && intval($this->request->data->gender) > 0) {

                                $gender = intval($this->request->data->gender) == 2 ? 2 : 1;

                            }



                            $this->db->query("INSERT INTO bayi_islem (bayiID,islemTip,mediaID,mediaCode,userID,userName,imageUrl,allComments,krediTotal,krediLeft,gender) VALUES(:bayiID,:islemTip,:mediaID,:mediaCode,:userID,:userName,:imageUrl,:allComments,:krediTotal,:krediLeft,:gender)", array(

                                "bayiID"      => $this->logonPerson->member->bayiID,

                                "islemTip"    => "comment",

                                "mediaID"     => $this->request->data->mediaID,

                                "mediaCode"   => $this->request->data->mediaCode,

                                "userID"      => $this->request->data->userID,

                                "userName"    => $this->request->data->userName,

                                "imageUrl"    => $this->request->data->imageUrl,

                                "allComments" => json_encode($yorumlar),

                                "krediTotal"  => $adet,

                                "krediLeft"   => $adet,

                                "gender"      => $gender

                            ));



                            $bayiIslemID = $this->db->lastInsertId();



                            $this->db->query("UPDATE bayi SET gunlukYorumLimitLeft = gunlukYorumLimitLeft - 1 WHERE bayiID=:bayiID", array("bayiID" => $this->logonPerson->member->bayiID));

                            $this->logonPerson->member->gunlukYorumLimitLeft--;



                            $sonuc = array(

                                "status"      => "success",

                                "message"     => "Başarılı.",

                                "users"       => array(),

                                "bayiIslemID" => $bayiIslemID

                            );



                            return $this->json($sonuc);



                        }





                        $arrComment = json_decode($bayiIslem["allComments"]);

                        if(!is_array($commentedIndexes) || empty($commentedIndexes)) {

                            $commentedIndexes = [];

                        }



                        $arrComment = array_diff_key($arrComment, $commentedIndexes);



                        $adet = count($arrComment);



                        if($adet > $bayiIslem["krediLeft"]) {

                            $adet = $bayiIslem["krediLeft"];

                        }

                        if($adet > Wow::get("ayar/bayiPaketBasiIstek")) {

                            $adet = Wow::get("ayar/bayiPaketBasiIstek");

                        }

                        $genderSql = "";

                        $arrGender = array();

                        if(intval($bayiIslem["gender"]) > 0) {

                            $genderSql           = " AND gender=:gender";

                            $arrGender["gender"] = intval($bayiIslem["gender"]);

                        }

                        $users = $this->db->query("SELECT uyeID,instaID,kullaniciAdi,sifre,isWebCookie FROM uye WHERE canComment=1 AND isActive=1 AND isUsable=1 " . $genderSql . " ORDER BY sonOlayTarihi ASC LIMIT :adet", array_merge($arrGender, array("adet" => $adet)));

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

                        $response          = $bulkReaction->comment($bayiIslem["mediaID"], $bayiIslem["mediaCode"], $arrComment);

                        $triedUsers        = $response["users"];

                        $totalSuccessCount = $response["totalSuccessCount"];



                        $allFailedUserIDs = array_filter(array_map(function($d) {

                            return $d["status"] == "fail" ? $d["userID"] : NULL;

                        }, $triedUsers), function($d) {

                            return $d !== NULL;

                        });

                        if(!empty($allFailedUserIDs)) {

                            $allFailedUserIDs = implode(",", $allFailedUserIDs);

                            $this->db->query("UPDATE uye SET canComment=0,canCommentControlDate=NOW() WHERE uyeID IN (" . $allFailedUserIDs . ")");

                        }



                        $isActive = ($bayiIslem["krediLeft"] - intval($totalSuccessCount)) <= 0 ? 0 : 1;

                        $this->db->query("UPDATE bayi_islem SET krediLeft=krediLeft-:successCount,isActive=:isActive WHERE bayiIslemID=:bayiIslemID", array(

                            "bayiIslemID"  => $bayiIslem["bayiIslemID"],

                            "successCount" => $totalSuccessCount,

                            "isActive"     => $isActive

                        ));



                        if($isActive == 0) {

                            $this->db->query("UPDATE bayi_islem SET excludedInstaIDs=NULL WHERE bayiIslemID=:bayiIslemID", ["bayiIslemID" => $bayiIslem["bayiIslemID"]]);

                        }



                        $sonuc = array(

                            "status"      => "success",

                            "message"     => "Başarılı.",

                            "users"       => $triedUsers,

                            "bayiIslemID" => $bayiIslemID

                        );



                        foreach($triedUsers as $i => $v) {

                            if($v["status"] == "success") {

                                $commentedIndexes[$v["commentIndex"]] = TRUE;

                            }

                        }

                        $_SESSION["CommentedIndexesForMediaID" . $bayiIslem["mediaID"]] = $commentedIndexes;



                        return $this->json($sonuc);



                        break;



                }

            } //GET Method

            else {

                if(!is_null($id)) {

                    $this->instagramReaction = new InstagramReaction($this->findAReactionUser());

                    $media                   = $this->instagramReaction->objInstagram->getMediaInfo($id);

                    if($media["status"] != "ok") {

                        return $this->notFound();

                    }

                    $this->view->set("media", $media);

                }

            }



            $this->navigation->add("Yorum Gönderimi", Wow::get("project/resellerPrefix") . "/home/send-comment");



            return $this->view();

        }





        public function getAllComments($mediaID, $username, $last = NULL) {

            $commentData = "";

            for($i = 0; $i < 50; $i++) {



                $comments = $this->instagramReaction->objInstagram->getMediaComments($mediaID, $last);



                if(count($comments["comments"]) > 0) {



                    $b = 0;

                    foreach($comments["comments"] AS $comment) {

                        if($b == 0) {

                            $last = isset($comment["pk"]) ? $comment["pk"] : "";

                        }

                        $b++;

                        if($comment["user"]["username"] == $username) {

                            $commentData = $comment;

                            break;

                        }

                    }



                }



                if(!empty($commentData)) {

                    return array(

                        "status"  => 1,

                        "comment" => $commentData

                    );

                    break;

                }



            }



        }



        function SendCommentLikeAction($id = NULL) {

            $this->view->set("title", "Yoruma Beğeni Gönderim Aracı");



            if(!$this->hasLimit("commentlike") && $this->request->query->bayiIslemID == NULL) {

                if($this->request->method == "POST") {

                    $sonuc = array(

                        "status"  => "error",

                        "code"    => "error",

                        "message" => "İşlem limitiniz yok!",

                        "users"   => array()

                    );



                    return $this->json($sonuc);

                } else {

                    return $this->view("comment", "bayi/home/no-limit");

                }

            }



            if($this->request->method == "POST") {

                $this->instagramReaction = new InstagramReaction($this->findAReactionUser());

                switch($this->request->query->formType) {

                    case "findMediaID":

                        $mediaData = $this->instagramReaction->getMediaData($this->request->data->mediaUrl);

                        if(!$mediaData) {



                            return $this->notFound();

                        } else {

                            $mediaID = $mediaData["media_id"];



                            $commentData = self::getAllComments($mediaID, $this->request->data->username);



                            if($commentData["status"] == 1) {



                                return $this->redirectToUrl(Wow::get("project/resellerPrefix") . "/home/send-comment-like/" . $mediaID . "?yorumid=" . $commentData["comment"]["pk"] . "&yorum=" . urlencode("<b>" . $commentData["comment"]["user"]["username"] . "</b> : " . $commentData["comment"]["text"]));



                            } else {

                                $sonuc = array(

                                    "status"  => "error",

                                    "code"    => "error",

                                    "message" => $commentData,

                                    "users"   => array()

                                );



                                return $this->json($sonuc);

                            }

                        }

                        break;

                    case "send":

                        $arrErrors   = array();

                        $bayiIslemID = intval($this->request->query->bayiIslemID);

                        $bayiIslem   = NULL;



                        if($bayiIslemID > 0) {

                            $bayiIslem = $this->db->row("SELECT * FROM bayi_islem WHERE bayiIslemID=:bayiIslemID AND bayiID=:bayiID AND isActive=1", array(

                                "bayiIslemID" => $bayiIslemID,

                                "bayiID"      => $this->logonPerson->member->bayiID

                            ));

                            if(empty($bayiIslem)) {

                                $arrErrors[] = "İlgili işlem kaydı bulunamadı!";

                            }

                        } else {



                            $adet = $this->request->data->adet;



                            if($adet > $this->logonPerson->member->yorumBegeniMaxKredi) {

                                $arrErrors[] = "Girdiğiniz yorum beğeni adedi, girebileceğiniz max adetten büyük!";

                            }

                            if(!$this->logonPerson->member->gunlukYorumBegeniLimitLeft > 0) {

                                $arrErrors[] = "İşlem hakkınız kalmadı!";

                            }

                        }

                        if(!empty($arrErrors)) {

                            $sonuc = array(

                                "status"  => "error",

                                "code"    => "error",

                                "message" => implode(",", $arrErrors),

                                "users"   => array()

                            );



                            return $this->json($sonuc);

                        }



                        if(!($bayiIslemID > 0)) {



                            $findMedia = $this->instagramReaction->objInstagram->getMediaInfo($this->request->data->mediaID);

                            if($findMedia["status"] == "fail") {

                                $sonuc = array(

                                    "status"  => "error",

                                    "code"    => "error",

                                    "message" => "Media bulunamadı! Silinmiş olabilir.",

                                    "users"   => array()

                                );



                                return $this->json($sonuc);

                            }



                            $gender = NULL;

                            if($this->logonPerson->member->yorumBegeniGender === 1 && intval($this->request->data->gender) > 0) {

                                $gender = intval($this->request->data->gender) == 2 ? 2 : 1;

                            }





                            $this->db->query("INSERT INTO bayi_islem (bayiID,islemTip,mediaID,mediaCode,userID,userName,imageUrl,krediTotal,krediLeft,gender,likedComment,likedCommentID) VALUES(:bayiID,:islemTip,:mediaID,:mediaCode,:userID,:userName,:imageUrl,:krediTotal,:krediLeft,:gender,:likedComment,:likedCommentID)", array(

                                "bayiID"         => $this->logonPerson->member->bayiID,

                                "islemTip"       => "commentLike",

                                "mediaID"        => $this->request->data->mediaID,

                                "mediaCode"      => $this->request->data->mediaCode,

                                "userID"         => $this->request->data->userID,

                                "userName"       => $this->request->data->userName,

                                "imageUrl"       => $this->request->data->imageUrl,

                                "krediTotal"     => $adet,

                                "krediLeft"      => $adet,

                                "gender"         => $gender,

                                "likedComment"   => $this->request->data->yorumText,

                                "likedCommentID" => $this->request->data->yorumID

                            ));









                            $bayiIslemID = $this->db->lastInsertId();



                            $this->db->query("UPDATE bayi SET gunlukYorumBegeniLimitLeft = gunlukYorumBegeniLimitLeft - 1 WHERE bayiID=:bayiID", array("bayiID" => $this->logonPerson->member->bayiID));

                            $this->logonPerson->member->gunlukYorumBegeniLimitLeft--;



                            $sonuc = array(

                                "status"      => "success",

                                "message"     => "Başarılı.",

                                "users"       => array(),

                                "bayiIslemID" => $bayiIslemID

                            );



                            return $this->json($sonuc);



                        }



                        $adet = $this->request->data->adet;



                        if($adet > $bayiIslem["krediLeft"]) {

                            $adet = $bayiIslem["krediLeft"];

                        }



                        if($adet > Wow::get("ayar/bayiPaketBasiIstek")) {

                            $adet = Wow::get("ayar/bayiPaketBasiIstek");

                        }



                        $users = $this->db->query("SELECT uyeID,instaID,kullaniciAdi,sifre,isWebCookie FROM uye WHERE isActive=1 AND isWebCookie = 0 ORDER BY sonOlayTarihi ASC LIMIT :adet", array("adet" => $adet));

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

                        $response          = $bulkReaction->commentLike($bayiIslem["mediaID"], $bayiIslem["likedCommentID"]);

                        $triedUsers        = $response["users"];

                        $totalSuccessCount = $response["totalSuccessCount"];



                        $allFailedUserIDs = array_filter(array_map(function($d) {

                            return $d["status"] == "fail" ? $d["userID"] : NULL;

                        }, $triedUsers), function($d) {

                            return $d !== NULL;

                        });

                        if(!empty($allFailedUserIDs)) {

                            $allFailedUserIDs = implode(",", $allFailedUserIDs);

                            $this->db->query("UPDATE uye SET canLike=0,canCommentControlDate=NOW() WHERE uyeID IN (" . $allFailedUserIDs . ")");

                        }



                        $isActive = ($bayiIslem["krediLeft"] - intval($totalSuccessCount)) <= 0 ? 0 : 1;

                        $this->db->query("UPDATE bayi_islem SET krediLeft=krediLeft-:successCount,isActive=:isActive WHERE bayiIslemID=:bayiIslemID", array(

                            "bayiIslemID"  => $bayiIslem["bayiIslemID"],

                            "successCount" => $totalSuccessCount,

                            "isActive"     => $isActive

                        ));



                        if($isActive == 0) {

                            $this->db->query("UPDATE bayi_islem SET excludedInstaIDs=NULL WHERE bayiIslemID=:bayiIslemID", ["bayiIslemID" => $bayiIslem["bayiIslemID"]]);

                        }



                        $sonuc = array(

                            "status"      => "success",

                            "message"     => "Başarılı.",

                            "users"       => $triedUsers,

                            "bayiIslemID" => $bayiIslemID

                        );



                        return $this->json($sonuc);



                        break;



                }

            } //GET Method

            else {

                if(!is_null($id)) {

                    $this->instagramReaction = new InstagramReaction($this->findAReactionUser());

                    $media                   = $this->instagramReaction->objInstagram->getMediaInfo($id);

                    if($media["status"] != "ok") {

                        return $this->notFound();

                    }

                    $this->view->set("media", $media);

                    $this->view->set("comment", array(

                        "commentID" => $this->request->query->yorumid,

                        "comment"   => urldecode($this->request->query->yorum)

                    ));

                }

            }



            $this->navigation->add("Yorum Beğeni Gönderimi", Wow::get("project/resellerPrefix") . "/home/send-comment-like");



            return $this->view();

        }





        function SendStoryAction($id = NULL) {

            $this->view->set("title", "Story Gönderim Aracı");



            if(!$this->hasLimit("story") && $this->request->query->bayiIslemID == NULL) {

                if($this->request->method == "POST") {

                    $sonuc = array(

                        "status"  => "error",

                        "code"    => "error",

                        "message" => "İşlem limitiniz yok!",

                        "users"   => array()

                    );



                    return $this->json($sonuc);

                } else {

                    return $this->view("story", "bayi/home/no-limit");

                }

            }



            if($this->request->method == "POST") {

                $this->instagramReaction = new InstagramReaction($this->findAReactionUser());

                switch($this->request->query->formType) {

                    case "findUserID":

                        $userData = $this->instagramReaction->objInstagram->getUserInfoByName($this->request->data->username);

                        if($userData["status"] != "ok") {

                            $objNotification           = new Notification();

                            $objNotification->type     = $objNotification::PARAM_TYPE_DANGER;

                            $objNotification->title    = "Hata!";

                            $objNotification->messages = ["Aradığınız üyelik bulunumadı!"];

                            $this->notifications[]     = $objNotification;



                            return $this->redirectToUrl($this->request->referrer);

                        } else {

                            $userID = $userData["user"]["pk"];



                            return $this->redirectToUrl(Wow::get("project/resellerPrefix") . "/home/send-story/" . $userID);

                        }

                        break;

                    case "send":

                        $arrErrors   = array();

                        $bayiIslemID = intval($this->request->query->bayiIslemID);

                        $bayiIslem   = NULL;

                        if($bayiIslemID > 0) {

                            $bayiIslem = $this->db->row("SELECT * FROM bayi_islem WHERE bayiIslemID=:bayiIslemID AND bayiID=:bayiID AND isActive=1 AND islemTip='story'", array(

                                "bayiIslemID" => $bayiIslemID,

                                "bayiID"      => $this->logonPerson->member->bayiID

                            ));

                            if(empty($bayiIslem)) {

                                $arrErrors[] = "İlgili işlem kaydı bulunamadı!";

                            }

                        } else {

                            $adet = intval($this->request->data->adet);

                            if(!$adet > 0) {

                                $arrErrors[] = "Adet'i hatalı girdiniz.";

                            }

                            if($adet > $this->logonPerson->member->storyMaxKredi) {

                                $arrErrors[] = "Girdiğiniz adet, girebileceğiniz max adetten büyük!";

                            }

                            if(!$this->logonPerson->member->gunlukStoryLimitLeft > 0) {

                                $arrErrors[] = "İşlem hakkınız kalmadı!";

                            }

                        }

                        if(!empty($arrErrors)) {

                            $sonuc = array(

                                "status"  => "error",

                                "code"    => "error",

                                "message" => implode(",", $arrErrors),

                                "users"   => array()

                            );



                            return $this->json($sonuc);

                        }

                        $items    = array();

                        $getItems = $this->instagramReaction->objInstagram->hikayecek($id);

                        if(!($bayiIslemID > 0)) {

                            $instaIDs = "0";

                            $gender   = NULL;





                            if(count($getItems["reel"]["items"]) < 1) {

                                $sonuc = array(

                                    "status"  => "error",

                                    "code"    => "nolimitdefined",

                                    "message" => "Kullanıcının aktif bir story paylaşımı bulunmamaktadır!",

                                    "users"   => array()

                                );



                                return $this->json($sonuc);

                            }



                            $this->db->query("INSERT INTO bayi_islem (bayiID,islemTip,userID,userName,imageUrl,krediTotal,krediLeft,excludedInstaIDs,gender) VALUES(:bayiID,:islemTip,:userID,:userName,:imageUrl,:krediTotal,:krediLeft,:excludedInstaIDs,:gender)", array(

                                "bayiID"           => $this->logonPerson->member->bayiID,

                                "islemTip"         => "story",

                                "userID"           => $this->request->data->userID,

                                "userName"         => $this->request->data->userName,

                                "imageUrl"         => $this->request->data->imageUrl,

                                "krediTotal"       => $adet,

                                "krediLeft"        => $adet,

                                "excludedInstaIDs" => $instaIDs,

                                "gender"           => $gender

                            ));



                            $bayiIslemID = $this->db->lastInsertId();



                            $this->db->query("UPDATE bayi SET gunlukStoryLimitLeft = gunlukStoryLimitLeft - 1 WHERE bayiID=:bayiID", array("bayiID" => $this->logonPerson->member->bayiID));

                            $this->logonPerson->member->gunlukStoryLimitLeft--;



                            $sonuc = array(

                                "status"      => "success",

                                "message"     => "Başarılı.",

                                "users"       => array(),

                                "bayiIslemID" => $bayiIslemID

                            );



                            return $this->json($sonuc);

                        }



                        $adet = $bayiIslem["krediLeft"];

                        if($adet > Wow::get("ayar/bayiPaketBasiIstek")) {

                            $adet = Wow::get("ayar/bayiPaketBasiIstek");

                        }





                        $instaIDs = empty($bayiIslem["excludedInstaIDs"]) ? "0" : $bayiIslem["excludedInstaIDs"];

                        $users    = $this->db->query("SELECT uyeID,instaID,kullaniciAdi,sifre,isWebCookie FROM uye WHERE canStoryView=1 AND isActive=1 AND isUsable=1 AND instaID NOT IN($instaIDs)  ORDER BY sonOlayTarihi ASC LIMIT :adet", array("adet" => $adet));

                        if(empty($users)) {

                            $this->db->query("UPDATE bayi_islem SET isActive=0 WHERE bayiIslemID=:bayiIslemID", array(

                                "bayiIslemID" => $bayiIslem["bayiIslemID"]

                            ));



                            $sonuc = array(

                                "status"  => "error",

                                "code"    => "nouserleft",

                                "message" => "Story Eklenemedi. Kullanıcı kalmadı!",

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

                        foreach($getItems["reel"]["items"] AS $item) {

                            $items[] = array(

                                "getTakenAt" => $item["taken_at"],

                                "itemID"     => $item["id"],

                                "userPK"     => $id

                            );

                        }

                        $bulkReaction      = new BulkReaction($users, Wow::get("ayar/bayiEsZamanliIstek"));

                        $response          = $bulkReaction->storyview($items);

                        $triedUsers        = $response["users"];

                        $totalSuccessCount = $response["totalSuccessCount"];

                        $allFailedUserIDs  = array();

                        $allFailedUserIDs  = array_filter(array_map(function($d) {

                            return $d["status"] == "fail" ? $d["userID"] : NULL;

                        }, $triedUsers), function($d) {

                            return $d !== NULL;

                        });

                        if(!empty($allFailedUserIDs)) {

                            $allFailedUserIDs = implode(",", $allFailedUserIDs);

                        }



                        $allSuccessInstaIDs = array_filter(array_map(function($d) {

                            return $d["status"] == "success" ? $d["instaID"] : NULL;

                        }, $triedUsers), function($d) {

                            return $d !== NULL;

                        });

                        $excludedInstaIDs   = empty($bayiIslem["excludedInstaIDs"]) ? "0" : $bayiIslem["excludedInstaIDs"];

                        if(!empty($allSuccessInstaIDs)) {

                            $excludedInstaIDs .= "," . implode(",", $allSuccessInstaIDs);

                        }



                        $isActive = ($bayiIslem["krediLeft"] - intval($totalSuccessCount)) <= 0 ? 0 : 1;

                        $this->db->query("UPDATE bayi_islem SET krediLeft=krediLeft-:successCount, isActive=:isActive,excludedInstaIDs=:excludedInstaIDs WHERE bayiIslemID=:bayiIslemID", array(

                            "bayiIslemID"      => $bayiIslem["bayiIslemID"],

                            "successCount"     => $totalSuccessCount,

                            "isActive"         => $isActive,

                            "excludedInstaIDs" => $excludedInstaIDs

                        ));



                        if($isActive == 0) {

                            $this->db->query("UPDATE bayi_islem SET excludedInstaIDs=NULL WHERE bayiIslemID=:bayiIslemID", ["bayiIslemID" => $bayiIslem["bayiIslemID"]]);

                        }



                        $sonuc = array(

                            "status"      => "success",

                            "message"     => "Başarılı.",

                            "users"       => $triedUsers,

                            "bayiIslemID" => $bayiIslemID

                        );



                        return $this->json($sonuc);



                        break;

                }

            } //GET Method

            else {

                if(!is_null($id)) {

                    $this->instagramReaction = new InstagramReaction($this->findAReactionUser());

                    $user                    = $this->instagramReaction->objInstagram->getUserInfoById($id);

                    if($user["status"] != "ok") {

                        return $this->notFound();

                    }

                    $this->view->set("user", $user);

                }

            }



            $this->navigation->add("Story Gönderimi", Wow::get("project/resellerPrefix") . "/home/send-story");



            return $this->view();

        }



        function AddAutoLikePackageAction($id = NULL) {

            $this->view->set("title", "Oto Beğeni Aracı");



            if(!$this->hasLimit("autolike")) {

                return $this->view("autolike", "bayi/home/no-limit");

            }



            if($this->request->method == "POST") {

                $this->instagramReaction = new InstagramReaction($this->findAReactionUser());

                switch($this->request->query->formType) {

                    case "findUserID":

                        $userData = $this->instagramReaction->objInstagram->getUserInfoByName($this->request->data->username);

                        if($userData["status"] != "ok") {

                            return $this->notFound();

                        } else {

                            $userID = $userData["user"]["pk"];



                            return $this->redirectToUrl(Wow::get("project/resellerPrefix") . "/home/add-auto-like-package/" . $userID);

                        }

                        break;

                    case "send":

                        $arrErrors = array();

                        $minadet   = intval($this->request->data->minadet);

                        $maxadet   = intval($this->request->data->maxadet);

                        $startDate = $this->request->data->startDate;

                        $days      = intval($this->request->data->days);

                        if(!$minadet > 0) {

                            $arrErrors[] = "Min. Adet'i hatalı girdiniz.";

                        }

                        if($maxadet > $this->logonPerson->member->otoBegeniMaxKredi) {

                            $arrErrors[] = "Max. Adet'i hatalı girdiniz.";

                        }

                        if($minadet > $maxadet) {

                            $arrErrors[] = "Min. adet Max. adetten küçük olmalıdır.!";

                        }

                        if(!$this->helper->is_date($startDate, "d.m.Y H:i:s")) {

                            $arrErrors[] = "Başlangıç tarihi hatalı girildi!";

                        }

                        if($days == 0 || $days > $this->logonPerson->member->otoBegeniMaxGun) {

                            $arrErrors[] = "Oto beğeni süresi hatalı girildi!";

                        }

                        if(!$this->logonPerson->member->toplamOtoBegeniLimitLeft > 0) {

                            $arrErrors[] = "İşlem hakkınız kalmadı!";

                        }

                        if(!empty($arrErrors)) {

                            $objNotification           = new Notification();

                            $objNotification->type     = $objNotification::PARAM_TYPE_DANGER;

                            $objNotification->title    = "Ekleme Başarısız!";

                            $objNotification->messages = $arrErrors;

                            $this->notifications[]     = $objNotification;



                            return $this->redirectToUrl($this->request->referrer);

                        }

                        if(strtotime($startDate) < strtotime("now")) {

                            $startDate = date("d.m.Y H:i:s", strtotime("now"));

                        }

                        $endDate = new DateTime($startDate);

                        $endDate->modify("+" . $days . " day");

                        $minuteDelay = intval($this->request->data->minuteDelay);

                        if($minuteDelay < 1) {

                            $minuteDelay = 1;

                        }

                        $krediDelay = intval($this->request->data->krediDelay);

                        if($krediDelay < 1) {

                            $krediDelay = 100;

                        }

                        if($krediDelay < ceil($maxadet / 20)) {

                            $krediDelay = ceil($maxadet / 20);

                        }

                        if($krediDelay > 250) {

                            $krediDelay = 250;

                        }

                        $gender = NULL;

                        if($this->logonPerson->member->otoBegeniGender === 1 && intval($this->request->data->gender) > 0) {

                            $gender = intval($this->request->data->gender) == 2 ? 2 : 1;

                        }



                        $this->db->query("INSERT INTO bayi_islem (bayiID,islemTip,userID,userName,imageUrl,minKrediTotal,maxKrediTotal,krediTotal,krediLeft,startDate,endDate,minuteDelay,krediDelay,gender) VALUES(:bayiID,:islemTip,:userID,:userName,:imageUrl,:minKrediTotal,:maxKrediTotal,:krediTotal,:krediLeft,:startDate,:endDate,:minuteDelay,:krediDelay,:gender)", array(

                            "bayiID"        => $this->logonPerson->member->bayiID,

                            "islemTip"      => "autolike",

                            "userID"        => $this->request->data->userID,

                            "userName"      => $this->request->data->userName,

                            "imageUrl"      => $this->request->data->imageUrl,

                            "minKrediTotal" => $minadet,

                            "maxKrediTotal" => $maxadet,

                            "krediTotal"    => $minadet,

                            "krediLeft"     => $minadet,

                            "startDate"     => date("Y-m-d H:i:s", strtotime($startDate)),

                            "endDate"       => $endDate->format("Y-m-d H:i:s"),

                            "minuteDelay"   => $minuteDelay,

                            "krediDelay"    => $krediDelay,

                            "gender"        => $gender

                        ));



                        $this->db->query("UPDATE bayi SET toplamOtoBegeniLimitLeft = toplamOtoBegeniLimitLeft - 1 WHERE bayiID=:bayiID", array("bayiID" => $this->logonPerson->member->bayiID));

                        $this->logonPerson->member->toplamOtoBegeniLimitLeft--;



                        $objNotification             = new Notification();

                        $objNotification->type       = $objNotification::PARAM_TYPE_SUCCESS;

                        $objNotification->title      = "Oto Beğeni İşlemi Tanımlandı.";

                        $objNotification->messages[] = "Beğeniler sistem tarafından arka planda gönderilmektedir. İşlem durumlarını İşlem Geçmişi mesüsünde takip edebilirisiniz.";

                        $this->notifications[]       = $objNotification;



                        return $this->redirectToUrl($this->request->referrer);

                        break;

                }

            } //GET Method

            else {

                if(!is_null($id)) {

                    $this->instagramReaction = new InstagramReaction($this->findAReactionUser());

                    $user                    = $this->instagramReaction->objInstagram->getUserInfoById($id);

                    if($user["status"] != "ok") {

                        return $this->notFound();

                    }

                    $this->view->set("user", $user);

                }

            }



            return $this->view();

        }



        function ListAction($page = 1) {



            if($this->request->method == "POST") {

                switch($this->request->query->formType) {

                    case "packageDetails":

                        $bayiIslemID = intval($this->request->query->bayiIslemID);

                        $paket       = $this->db->row("SELECT * FROM bayi_islem WHERE bayiID=:bayiID AND bayiIslemID=:bayiIslemID", array(

                            "bayiID"      => $this->logonPerson->member->bayiID,

                            "bayiIslemID" => $bayiIslemID

                        ));

                        if(empty($paket)) {

                            return $this->notFound();

                        }

                        $paketdetay = $this->db->query("SELECT * FROM uye_otobegenipaket_gonderi WHERE bayiIslemID=:bayiIslemID", array("bayiIslemID" => $bayiIslemID));

                        $data       = array(

                            "paket"      => $paket,

                            "paketdetay" => $paketdetay

                        );



                        return $this->partialView($data, "bayi/home/auto-like-package-detail");

                        break;

                    case "updateAutoLikePackage";

                        $bayiIslemID = intval($this->request->query->bayiIslemID);

                        $paket       = $this->db->row("SELECT * FROM bayi_islem WHERE bayiID=:bayiID AND bayiIslemID=:bayiIslemID", array(

                            "bayiID"      => $this->logonPerson->member->bayiID,

                            "bayiIslemID" => $bayiIslemID

                        ));

                        if(empty($paket)) {

                            return $this->notFound();

                        }

                        $arrErrors = array();

                        $minadet   = intval($this->request->data->minadet);

                        $maxadet   = intval($this->request->data->maxadet);

                        $isActive  = intval($this->request->data->isActive);

                        if(!$minadet > 0) {

                            $arrErrors[] = "Min. Adet'i hatalı girdiniz.";

                        }

                        if(!$maxadet > $this->logonPerson->member->otoBegeniMaxKredi) {

                            $arrErrors[] = "Max. Adet'i hatalı girdiniz.";

                        }

                        if($minadet > $maxadet) {

                            $arrErrors[] = "Min. adet Max. adetten küçük olmalıdır.!";

                        }

                        if($maxadet > $this->logonPerson->member->otoBegeniMaxKredi) {

                            $arrErrors[] = "Girdiğiniz adet, girebileceğiniz max adetten büyük!";

                        }

                        if($isActive > 2) {

                            $arrErrors[] = "Durum'u hatalı girdiniz.";

                        }

                        if(!empty($arrErrors)) {

                            $objNotification           = new Notification();

                            $objNotification->type     = $objNotification::PARAM_TYPE_DANGER;

                            $objNotification->title    = "Düzenleme Başarısız!";

                            $objNotification->messages = $arrErrors;

                            $this->notifications[]     = $objNotification;



                            return $this->redirectToUrl($this->request->referrer);

                        }

                        $minuteDelay = intval($this->request->data->minuteDelay);

                        if($minuteDelay < 1) {

                            $minuteDelay = 1;

                        }

                        $krediDelay = intval($this->request->data->krediDelay);

                        if($krediDelay < 1) {

                            $krediDelay = 100;

                        }

                        if($krediDelay < ceil($minadet / 20)) {

                            $krediDelay = ceil($minadet / 20);

                        }

                        if($krediDelay > 250) {

                            $krediDelay = 250;

                        }

                        $gender = NULL;

                        if($this->logonPerson->member->otoBegeniGender === 1 && intval($this->request->data->gender) > 0) {

                            $gender = intval($this->request->data->gender) == 2 ? 2 : 1;

                        }

                        $this->db->query("UPDATE bayi_islem SET minKrediTotal=:minKrediTotal,maxKrediTotal=:maxKrediTotal,krediTotal=:krediTotal, krediLeft=:krediLeft,isActive=:isActive,minuteDelay=:minuteDelay,krediDelay=:krediDelay, gender=:gender WHERE bayiID=:bayiID AND bayiIslemID=:bayiIslemID", array(

                            "bayiID"        => $this->logonPerson->member->bayiID,

                            "bayiIslemID"   => $bayiIslemID,

                            "minKrediTotal" => $minadet,

                            "maxKrediTotal" => $maxadet,

                            "krediTotal"    => $minadet,

                            "krediLeft"     => $minadet,

                            "isActive"      => $isActive,

                            "minuteDelay"   => $minuteDelay,

                            "krediDelay"    => $krediDelay,

                            "gender"        => $gender

                        ));



                        $objNotification        = new Notification();

                        $objNotification->type  = $objNotification::PARAM_TYPE_SUCCESS;

                        $objNotification->title = "Oto Beğeni Düzenlendi.";

                        $this->notifications[]  = $objNotification;



                        return $this->redirectToUrl($this->request->referrer);

                        break;

                    default:

                        return $this->notFound();

                }

            }



            $this->view->set("title", "İşlem Listesi");



            if(!intval($page) > 0) {

                $page = 1;

            }



            $sqlWhere           = "WHERE bayiID=:bayiID";

            $arrSorgu           = array();

            $arrSorgu["bayiID"] = $this->logonPerson->member->bayiID;



            $q = $this->request->query->q;

            if(!empty($q)) {

                $sqlWhere             .= " AND (userName LIKE :userName)";

                $arrSorgu["userName"] = "%" . $q . "%";

            }



            $islemTip = $this->request->query->islemTip;

            if(!is_null($islemTip) && $islemTip !== "") {

                $sqlWhere             .= " AND islemTip = :islemTip";

                $arrSorgu["islemTip"] = $islemTip;

            }



            $isActive = $this->request->query->isActive;

            if(!is_null($isActive) && $isActive !== "") {

                $sqlWhere             .= " AND isActive = :isActive";

                $arrSorgu["isActive"] = $isActive;

            }



            $limitCount = 20;

            $limitStart = (($page * $limitCount) - $limitCount);



            $data = $this->db->query("SELECT * FROM bayi_islem " . $sqlWhere . " ORDER BY bayiIslemID DESC LIMIT :limitStart,:limitCount", array_merge($arrSorgu, array(

                "limitStart" => $limitStart,

                "limitCount" => $limitCount

            )));



            $totalRows    = $this->db->single("SELECT COUNT(bayiIslemID) FROM bayi_islem " . $sqlWhere, $arrSorgu);

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

    }