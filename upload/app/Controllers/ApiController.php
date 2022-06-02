<?php



    namespace App\Controllers;



    use ApiService;

    use App\Libraries\InstagramReaction;

    use SmmApi;

    use Wow\Net\Response;



    class ApiController extends BaseController {



        private $key               = NULL;

        private $bayi              = NULL;

        private $instagramReaction = NULL;



        /**

         * Override onStart

         */

        function onActionExecuting() {

            if(($pass = parent::onActionExecuting()) instanceof Response) {

                return $pass;

            }



            $this->key = isset($_REQUEST["key"]) ? $_REQUEST["key"] : "";



            $this->bayi = $this->db->row("SELECT * FROM bayi WHERE apiKey=:apikey", array("apikey" => $this->key));



            if(empty($this->bayi)) {

                return $this->json(array(

                                       "status" => 0,

                                       "error"  => "Api Hatalıdır. Lütfen kontrol ederek tekrar deneyiniz.",

                                       "data"   => $this->request

                                   ));

            }





        }



        public function getAllComments($mediaID, $username, $last = NULL) {

            $commentData             = "";

            try {

                $this->instagramReaction = new InstagramReaction($this->findAReactionUser());

            }catch(\Exception $e) {

                $this->instagramReaction = new InstagramReaction($this->findAReactionUser());

            }





            for($i = 0; $i < 50; $i++) {



                $comments = $this->instagramReaction->objInstagram->getMediaComments($mediaID, $last);



                if(!empty($comments["comments"])) {

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



            return array(

                "status"  => 0,

                "comment" => $commentData

            );



        }



        function V2Action() {



            $data = array();



            $action = isset($_REQUEST["action"]) ? $_REQUEST["action"] : "";

            unset($this->bayi["username"]);

            unset($this->bayi["password"]);

            unset($this->bayi["komisyonOran"]);

            unset($this->bayi["notlar"]);



            if($action == "services") {



                $services = array(

                    array(

                        "service"  => "1",

                        "name"     => "Instagram Takipçi",

                        "type"     => "Default",

                        "category" => "Takipçi",

                        "rate"     => $this->bayi["takipPrice"],

                        "min"      => "1",

                        "max"      => $this->bayi["takipMaxKredi"]

                    ),

                    array(

                        "service"  => "2",

                        "name"     => "Instagram Beğeni",

                        "type"     => "Default",

                        "category" => "Beğeni",

                        "rate"     => $this->bayi["begeniPrice"],

                        "min"      => "1",

                        "max"      => $this->bayi["begeniMaxKredi"]

                    ),

                    array(

                        "service"  => "3",

                        "name"     => "Instagram Yorum",

                        "type"     => "Custom Comments",

                        "category" => "Yorum",

                        "rate"     => $this->bayi["yorumPrice"],

                        "min"      => "1",

                        "max"      => $this->bayi["yorumMaxKredi"]

                    ),

                    array(

                        "service"  => "4",

                        "name"     => "Story Görüntülenme",

                        "type"     => "Default",

                        "category" => "Diğer",

                        "rate"     => $this->bayi["storyPrice"],

                        "min"      => "1",

                        "max"      => $this->bayi["storyMaxKredi"]

                    ),

                    array(

                        "service"  => "5",

                        "name"     => "Video Görüntülenme",

                        "type"     => "Default",

                        "category" => "Diğer",

                        "rate"     => $this->bayi["videoPrice"],

                        "min"      => "1",

                        "max"      => $this->bayi["videoMaxKredi"]

                    ),

                    array(

                        "service"  => "6",

                        "name"     => "Kaydetme Gönderme",

                        "type"     => "Default",

                        "category" => "Diğer",

                        "rate"     => $this->bayi["savePrice"],

                        "min"      => "1",

                        "max"      => $this->bayi["saveMaxKredi"]

                    ),

                    array(

                        "service"  => "7",

                        "name"     => "Yorum Beğenisi Gönderme",

                        "type"     => "Default",

                        "category" => "Diğer",

                        "rate"     => $this->bayi["yorumBegeniPrice"],

                        "min"      => "1",

                        "max"      => $this->bayi["yorumBegeniMaxKredi"]

                    ),

                    array(

                        "service"  => "8",

                        "name"     => "Canlı Yayına İzleyici Gönderme",

                        "type"     => "Default",

                        "category" => "Diğer",

                        "rate"     => $this->bayi["canliYayinPrice"],

                        "min"      => "1",

                        "max"      => $this->bayi["canliYayinMaxKredi"]

                    )

                );



                return $this->json($services);



            } else if($action == "add") {



                if(empty($this->bayi["bakiye"])) {

                    $data = array(

                        "status" => 0,

                        "error"  => "Yetersiz Bakiye!"

                    );



                    return $this->json($data);

                }



                $arrErrors               = "";

                try {

                    $this->instagramReaction = new InstagramReaction($this->findAReactionUser());

                } catch(\Exception $e) {

                    try {

                        $this->instagramReaction = new InstagramReaction($this->findAReactionUser());

                    }catch(\Exception $e) {

                        $this->instagramReaction = new InstagramReaction($this->findAReactionUser());

                    }



                }





                $service  = $_REQUEST["service"] ? $_REQUEST["service"] : "";

                $link     = $_REQUEST["link"] ? $_REQUEST["link"] : "";

                $quantity = $_REQUEST["quantity"] ? intval($_REQUEST["quantity"]) : "";



                if($service == 1) {



                    if(!empty($link) && !empty($quantity)) {



                        if(!$quantity > 0) {

                            $arrErrors = "Adet'i hatalı girdiniz.";

                        }

                        if($quantity > $this->bayi["takipMaxKredi"]) {

                            $arrErrors = "Girdiğiniz adet, girebileceğiniz max adetten büyük!";

                        }



                        $price = 0;

                        if($this->bayi["takipPrice"] > 0) {

                            $price = ($this->bayi["takipPrice"] * $quantity) / 1000;

                        }



                        if($this->bayi["bakiye"] < $price) {

                            $arrErrors = "Bakiyeniz bu işlem için yetersiz!";

                        }



                        if(empty($arrErrors)) {



                            $username = str_replace("/", "", parse_url($link)["path"]);

                            $userData = $this->instagramReaction->objInstagram->getUserInfoByName($username);



                            if($userData["status"] != "ok") {

                                $data = array(

                                    "status" => 0,

                                    "error"  => "Kullanıcı bulunamadı!"

                                );



                                return $this->json($data);

                            }





                            $followedUsers = array();

                            $nextMaxID     = NULL;

                            $intLoop       = 0;

                            while($follower = $this->instagramReaction->objInstagram->getUserFollowers($userData["user"]["pk"], $nextMaxID)) {

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



                            $data = array(

                                "bayiID"           => $this->bayi["bayiID"],

                                "islemTip"         => "follow",

                                "userID"           => $userData["user"]["pk"]."",

                                "userName"         => $userData["user"]["username"],

                                "imageUrl"         => $userData["user"]["hd_profile_pic_url_info"]["url"],

                                "krediTotal"       => $quantity,

                                "krediLeft"        => $quantity,

                                "excludedInstaIDs" => $instaIDs,

                                "start_count"      => $userData["user"]["follower_count"],

                                "tutar"            => $price

                            );



                            $service = new ApiService();

                            $orderID = $service->addData($data);



                        }



                    } else {

                        $arrErrors = "Parametreler eksik!";

                    }



                } else if($service == 2) {



                    if(!empty($link) && !empty($quantity)) {



                        if(!$quantity > 0) {

                            $arrErrors = "Adet'i hatalı girdiniz.";

                        }

                        if($quantity > $this->bayi["begeniMaxKredi"]) {

                            $arrErrors = "Girdiğiniz adet, girebileceğiniz max adetten büyük!";

                        }



                        $price = 0;

                        if($this->bayi["begeniPrice"] > 0) {

                            $price = ($this->bayi["begeniPrice"] * $quantity) / 1000;

                        }





                        if($this->bayi["bakiye"] < $price) {

                            $arrErrors = "Bakiyeniz bu işlem için yetersiz.!";

                        }



                        if(empty($arrErrors)) {



                            $mediaData = $this->instagramReaction->getMediaData($link);

                            $mediaData = $this->instagramReaction->objInstagram->getMediaInfo($mediaData["media_id"]);



                            if($mediaData["status"] != "ok") {

                                $data = array(

                                    "status" => 0,

                                    "error"  => "Media bulunamadı."

                                );



                                return $this->json($data);

                            }



                            $likedUsers = array();

                            $likers     = $this->instagramReaction->objInstagram->getMediaLikers($mediaData["items"][0]["id"]);

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



                            if(isset($mediaData["items"][0]["media_type"]) && $mediaData["items"][0]["media_type"] == 8) {

                                $imageURL = $mediaData["items"][0]["carousel_media"][0]["image_versions2"]["candidates"]["0"]["url"];

                            } else {

                                $imageURL = $mediaData["items"][0]["image_versions2"]["candidates"]["0"]["url"];

                            }



                            $data = array(

                                "bayiID"           => $this->bayi["bayiID"],

                                "islemTip"         => "like",

                                "mediaID"          => $mediaData["items"][0]["id"],

                                "mediaCode"        => $mediaData["items"][0]["code"],

                                "userID"           => $mediaData["items"][0]["user"]["pk"]."",

                                "userName"         => $mediaData["items"][0]["user"]["username"],

                                "imageUrl"         => $imageURL,

                                "krediTotal"       => $quantity,

                                "krediLeft"        => $quantity,

                                "excludedInstaIDs" => $instaIDs,

                                "start_count"      => $mediaData["items"][0]["like_count"],

                                "tutar"            => $price

                            );



                            $service = new ApiService();

                            $orderID = $service->addData($data);



                        }



                    } else {

                        $arrErrors = "Parametreler eksik!";

                    }



                } else if($service == 3) {



                    $getcomments = $_REQUEST["comments"] ? $_REQUEST["comments"] : "";



                    if(!empty($link) && !empty($getcomments)) {



                        $adet = count(explode("\n", $getcomments));



                        if(!$adet > 0) {

                            $arrErrors = "Adet'i hatalı girdiniz.";

                        }

                        if($adet > $this->bayi["yorumMaxKredi"]) {

                            $arrErrors = "Girdiğiniz adet, girebileceğiniz max adetten büyük!";

                        }



                        $price = 0;

                        if($this->bayi["yorumPrice"] > 0) {

                            $price = ($this->bayi["yorumPrice"] * $quantity) / 1000;

                        }





                        if($this->bayi["bakiye"] < $price) {

                            $arrErrors = "Bakiyeniz bu işlem için yetersiz.!";

                        }



                        if(empty($arrErrors)) {



                            $mediaData = $this->instagramReaction->getMediaData($link);

                            $mediaData = $this->instagramReaction->objInstagram->getMediaInfo($mediaData["media_id"]);



                            if(isset($mediaData["items"][0]["comments_disabled"]) && $mediaData["items"][0]["comments_disabled"] == "1") {

                                $data = array(

                                    "status" => 0,

                                    "error"  => "Medya yorumlara kapalıdır.!"

                                );



                                return $this->json($data);

                            }



                            if($mediaData["status"] != "ok") {

                                $data = array(

                                    "status" => 0,

                                    "error"  => "Media bulunamadı."

                                );



                                return $this->json($data);

                            }



                            $commentUsers = array();

                            $comments     = $this->instagramReaction->objInstagram->getMediaComments($mediaData["items"][0]["id"]);



                            if(count($comments["comments"]) > 0) {

                                foreach($comments["comments"] as $comment) {

                                    $commentUsers[] = $comment["pk"];

                                }

                            }



                            $instaIDs          = "0";

                            $commentedInstaIDs = $commentUsers;

                            foreach($commentedInstaIDs as $instaID) {

                                if(intval($instaID) > 0) {

                                    $instaIDs .= "," . intval($instaID);

                                }

                            }





                            if(isset($mediaData["items"][0]["media_type"]) && $mediaData["items"][0]["media_type"] == 8) {

                                $imageURL = $mediaData["items"][0]["carousel_media"][0]["image_versions2"]["candidates"]["0"]["url"];

                            } else {

                                $imageURL = $mediaData["items"][0]["image_versions2"]["candidates"]["0"]["url"];

                            }



                            $data = array(

                                "bayiID"           => $this->bayi["bayiID"],

                                "islemTip"         => "comment",

                                "mediaID"          => $mediaData["items"][0]["id"],

                                "mediaCode"        => $mediaData["items"][0]["code"],

                                "userID"           => $mediaData["items"][0]["user"]["pk"]."",

                                "userName"         => $mediaData["items"][0]["user"]["username"],

                                "imageUrl"         => $imageURL,

                                "krediTotal"       => $adet,

                                "krediLeft"        => $adet,

                                "excludedInstaIDs" => $instaIDs,

                                "comments"         => json_encode(explode("\n", $getcomments)),

                                "start_count"      => $comments["comment_count"],

                                "tutar"            => $price

                            );



                            $service = new ApiService();

                            $orderID = $service->addData($data);



                        }



                    } else {

                        $arrErrors = "Parametreler eksik!";

                    }



                } else if($service == 4) {



                    $arrErrors = "";

                    if(!empty($link) && !empty($quantity)) {



                        if(!$quantity > 0) {

                            $arrErrors = "Adet'i hatalı girdiniz.";

                        }

                        if($quantity > $this->bayi["storyMaxKredi"]) {

                            $arrErrors = "Girdiğiniz adet, girebileceğiniz max adetten büyük!";

                        }



                        $price = 0;

                        if($this->bayi["storyPrice"] > 0) {

                            $price = ($this->bayi["storyPrice"] * $quantity) / 1000;

                        }



                        if($this->bayi["bakiye"] < $price) {

                            $arrErrors = "Bakiyeniz bu işlem için yetersiz.!";

                        }





                        $username = str_replace("/", "", parse_url($link)["path"]);

                        $userData = $this->instagramReaction->objInstagram->getUserInfoByName($username);



                        if($userData["status"] != "ok") {

                            $arrErrors = "Kullanıcı bulunamadı!";

                        }



                        if(empty($arrErrors)) {



                            $getItems = $this->instagramReaction->objInstagram->hikayecek($userData["user"]["pk"]);



                            if(count($getItems["reel"]["items"]) < 1) {

                                $arrErrors = "Kullanıcının aktif bir story paylaşımı bulunmamaktadır!";

                            }



                            if(empty($arrErrors)) {



                                $data = array(

                                    "bayiID"      => $this->bayi["bayiID"],

                                    "islemTip"    => "story",

                                    "userID"      => $userData["user"]["pk"]."",

                                    "userName"    => $userData["user"]["username"],

                                    "imageUrl"    => $userData["user"]["hd_profile_pic_url_info"]["url"],

                                    "krediTotal"  => $quantity,

                                    "krediLeft"   => $quantity,

                                    "allStories"  => json_encode($getItems),

                                    "start_count" => 0,

                                    "tutar"       => $price

                                );



                                $service = new ApiService();

                                $orderID = $service->addData($data);





                            }



                        }



                    } else {

                        $arrErrors = "Parametreler eksik!";

                    }



                } else if($service == 5) {



                    if(!empty($link) && !empty($quantity)) {



                        if(!$quantity > 0) {

                            $arrErrors = "Adet'i hatalı girdiniz.";

                        }

                        if($quantity > $this->bayi["videoMaxKredi"]) {

                            $arrErrors = "Girdiğiniz adet, girebileceğiniz max adetten büyük!";

                        }



                        $price = 0;

                        if($this->bayi["videoPrice"] > 0) {

                            $price = ($this->bayi["videoPrice"] * $quantity) / 1000;

                        }



                        if($this->bayi["bakiye"] < $price) {

                            $arrErrors = "Bakiyeniz bu işlem için yetersiz.!";

                        }



                        if(empty($arrErrors)) {



                            $mediaData = $this->instagramReaction->getMediaData($link);

                            $mediaData = $this->instagramReaction->objInstagram->getMediaInfo($mediaData["media_id"]);



                            if($mediaData["status"] != "ok") {

                                $data = array(

                                    "status" => 0,

                                    "error"  => "Media bulunamadı."

                                );



                                return $this->json($data);

                            }



                            if($mediaData["items"][0]["media_type"] != 2) {

                                $data = array(

                                    "status" => 0,

                                    "error"  => "Sadece video gönderilerine görüntülenme gönderilmektedir."

                                );



                                return $this->json($data);

                            }





                            if(isset($mediaData["items"][0]["media_type"]) && $mediaData["items"][0]["media_type"] == 8) {

                                $imageURL = $mediaData["items"][0]["carousel_media"][0]["image_versions2"]["candidates"]["0"]["url"];

                            } else {

                                $imageURL = $mediaData["items"][0]["image_versions2"]["candidates"]["0"]["url"];

                            }





                            $data = array(

                                "bayiID"      => $this->bayi["bayiID"],

                                "islemTip"    => "videoview",

                                "mediaID"     => $mediaData["items"][0]["id"],

                                "mediaCode"   => $mediaData["items"][0]["code"],

                                "userID"      => $mediaData["items"][0]["user"]["pk"]."",

                                "userName"    => $mediaData["items"][0]["user"]["username"],

                                "imageUrl"    => $imageURL,

                                "krediTotal"  => $quantity,

                                "krediLeft"   => $quantity,

                                "start_count" => $mediaData["items"][0]["view_count"],

                                "tutar"       => $price

                            );



                            $service = new ApiService();

                            $orderID = $service->addData($data);



                        }



                    } else {

                        $arrErrors = "Parametreler eksik!";

                    }



                } else if($service == 6) {



                    if(!empty($link) && !empty($quantity)) {



                        if(!$quantity > 0) {

                            $arrErrors = "Adet'i hatalı girdiniz.";

                        }

                        if($quantity > $this->bayi["saveMaxKredi"]) {

                            $arrErrors = "Girdiğiniz adet, girebileceğiniz max adetten büyük!";

                        }



                        $price = 0;

                        if($this->bayi["savePrice"] > 0) {

                            $price = ($this->bayi["savePrice"] * $quantity) / 1000;

                        }



                        if($this->bayi["bakiye"] < $price) {

                            $arrErrors = "Bakiyeniz bu işlem için yetersiz.!";

                        }



                        if(empty($arrErrors)) {



                            $mediaData = $this->instagramReaction->getMediaData($link);

                            $mediaData = $this->instagramReaction->objInstagram->getMediaInfo($mediaData["media_id"]);



                            if($mediaData["status"] != "ok") {

                                $data = array(

                                    "status" => 0,

                                    "error"  => "Media bulunamadı."

                                );



                                return $this->json($data);

                            }





                            if(isset($mediaData["items"][0]["media_type"]) && $mediaData["items"][0]["media_type"] == 8) {

                                $imageURL = $mediaData["items"][0]["carousel_media"][0]["image_versions2"]["candidates"]["0"]["url"];

                            } else {

                                $imageURL = $mediaData["items"][0]["image_versions2"]["candidates"]["0"]["url"];

                            }



                            $data = array(

                                "bayiID"      => $this->bayi["bayiID"],

                                "islemTip"    => "save",

                                "mediaID"     => $mediaData["items"][0]["id"],

                                "mediaCode"   => $mediaData["items"][0]["code"],

                                "userID"      => $mediaData["items"][0]["user"]["pk"]."",

                                "userName"    => $mediaData["items"][0]["user"]["username"],

                                "imageUrl"    => $imageURL,

                                "krediTotal"  => $quantity,

                                "krediLeft"   => $quantity,

                                "start_count" => 0,

                                "tutar"       => $price

                            );



                            $service = new ApiService();

                            $orderID = $service->addData($data);



                        }



                    } else {

                        $arrErrors = "Parametreler eksik!";

                    }

                } else if($service == 7) {



                    if(!empty($link) && !empty($quantity)) {



                        if(!$quantity > 0) {

                            $arrErrors = "Adet'i hatalı girdiniz.";

                        }

                        if($quantity > $this->bayi["yorumBegeniMaxKredi"]) {

                            $arrErrors = "Girdiğiniz adet, girebileceğiniz max adetten büyük!";

                        }



                        $price = 0;

                        if($this->bayi["yorumBegeniPrice"] > 0) {

                            $price = ($this->bayi["yorumBegeniPrice"] * $quantity) / 1000;

                        }



                        if($this->bayi["bakiye"] < $price) {

                            $arrErrors = "Bakiyeniz bu işlem için yetersiz!";

                        }



                        $parse = explode("|", $link);



                        if(count($parse) < 2 && !empty($parse[1])) {

                            $arrErrors = "Gönderilen link hatalı link|username şeklinde gönderilmelidir.";

                        }



                        if(empty($arrErrors)) {



                            $mediaData = $this->instagramReaction->getMediaData($parse[0]);



                            $mediaInfo = $this->instagramReaction->objInstagram->getMediaInfo($mediaData["media_id"]);



                            if($mediaInfo["status"] != "ok") {

                                $data = array(

                                    "status" => 0,

                                    "error"  => "Media bulunamadı.",

                                    "media"  => $parse[0]

                                );



                                return $this->json($data);

                            }



                            $commentData = self::getAllComments($mediaData["media_id"], $parse[1]);

                            $commentID   = NULL;



                            if($commentData["status"] == 1) {



                                $commentID = $commentData["comment"]["pk"];

                                $comment   = $commentData["comment"]["text"];



                            } else {

                                $data = array(

                                    "status" => 0,

                                    "error"  => "Yorum bulunamadı.",

                                    "data"   => $commentData

                                );



                                return $this->json($data);

                            }



                            $data = array(

                                "bayiID"         => $this->bayi["bayiID"],

                                "islemTip"       => "commentlike",

                                "mediaID"        => $mediaData["media_id"],

                                "likedComment"   => $comment,

                                "likedCommentID" => $commentID,

                                "username"       => $parse[1],

                                "krediTotal"     => $quantity,

                                "krediLeft"      => $quantity,

                                "tutar"          => $price

                            );



                            $service = new ApiService();

                            $orderID = $service->addData($data);



                        }



                    } else {

                        $arrErrors = "Parametreler eksik!";

                    }

                } else if($service == 8) {



                    if(!empty($link) && !empty($quantity)) {



                        if(!$quantity > 0) {

                            $arrErrors = "Adet'i hatalı girdiniz.";

                        }

                        if($quantity > $this->bayi["canliYayinMaxKredi"]) {

                            $arrErrors = "Girdiğiniz adet, girebileceğiniz max adetten büyük!";

                        }



                        $price = 0;

                        if($this->bayi["canliYayinPrice"] > 0) {

                            $price = ($this->bayi["canliYayinPrice"] * $quantity) / 1000;

                        }



                        if($this->bayi["bakiye"] < $price) {

                            $arrErrors = "Bakiyeniz bu işlem için yetersiz!";

                        }



                        if(empty($arrErrors)) {



                            $username = str_replace("/", "", parse_url($link)["path"]);

                            $userData = $this->instagramReaction->objInstagram->getUserInfoByName($username);



                            if($userData["status"] != "ok") {

                                $data = array(

                                    "status" => 0,

                                    "error"  => "Kullanıcı bulunamadı!"

                                );



                                return $this->json($data);

                            }



                            $broadcast = $this->instagramReaction->objInstagram->getliveInfoByName($userData["user"]["pk"]);



                            if(!isset($broadcast["broadcast"]["id"])) {

                                $sonuc = array(

                                    "status"  => "error",

                                    "code"    => "nolimitdefined",

                                    "message" => "Canlı Yayın Bulunamadı. Kişinin aktif canlı yayını bulunamadı!",

                                    "users"   => array()

                                );



                                return $this->json($sonuc);

                            }



                            $data = array(

                                "bayiID"      => $this->bayi["bayiID"],

                                "islemTip"    => "canliyayin",

                                "userID"      => $userData["user"]["pk"]."",

                                "userName"    => $userData["user"]["username"],

                                "broadcastID" => $broadcast["broadcast"]["id"],

                                "krediTotal"  => $quantity,

                                "krediLeft"   => $quantity,

                                "tutar"       => $price

                            );



                            $service = new ApiService();

                            $orderID = $service->addData($data);



                        }



                    } else {

                        $arrErrors = "Parametreler eksik!";

                    }

                } else {

                    $arrErrors = "Geçersiz talep";

                }



                if(!empty($arrErrors)) {

                    $data = array(

                        "status" => 0,

                        "error"  => $arrErrors

                    );

                } else {

                    $data = array("order" => $orderID);

                }



                return $this->json($data);





            } else if($action == "status") {



                $order  = isset($_REQUEST["order"]) ? $_REQUEST["order"] : "";

                $orders = isset($_REQUEST["orders"]) ? array_map('intval', explode(',', $_REQUEST["orders"])) : "";



                if($order) {



                    $status = "";



                    $s = $this->db->row("SELECT bayiIslemID,userID,userName,islemTip,mediaID,mediaCode,imageUrl,krediTotal,krediLeft,isActive,eklenmeTarihi,sonKontrolTarihi,minuteDelay,krediDelay,start_count,talepPrice FROM bayi_islem WHERE bayiIslemID=:bayiislemid AND bayiID=:bayiid", array(

                        "bayiislemid" => $order,

                        "bayiid"      => $this->bayi["bayiID"]

                    ));



                    if($s["krediLeft"] <= 0) {

                        $status = "Completed";

                    } else if(strtotime($s["eklenmeTarihi"]) < strtotime("-4 hours")) {

                        $status = "Partial";

                    } else if($s["krediLeft"] > 0) {

                        $status = "In progress";

                    }



                    $sorgu = array(

                        "charge"      => $s["talepPrice"] > 0 ? (($s["krediTotal"] - $s["krediLeft"]) * $s["talepPrice"]) / 1000 : 0,

                        "start_count" => isset($s["start_count"]) ? $s["start_count"] : 0,

                        "status"      => $status,

                        "remains"     => $s["krediLeft"],

                        "currency"    => "TRY"

                    );



                } else if($orders) {

                    $sorgu = $this->db->query("SELECT bayiIslemID,userID,userName,islemTip,mediaID,mediaCode,imageUrl,krediTotal,krediLeft,isActive,eklenmeTarihi,sonKontrolTarihi,minuteDelay,krediDelay,start_count,talepPrice FROM bayi_islem WHERE bayiIslemID IN (" . implode(",", $orders) . ") AND bayiID=:bayiid", array(

                        "bayiid" => $this->bayi["bayiID"]

                    ));



                    foreach($sorgu AS $s) {



                        $status = "";



                        if($s["krediLeft"] <= 0) {

                            $status = "Completed";

                        } else if(strtotime($s["eklenmeTarihi"]) < strtotime("-4 hours")) {

                            $status = "Partial";

                        } else if($s["krediLeft"] > 0) {

                            $status = "In progress";

                        }



                        $arrayData[$s["bayiIslemID"]] = array(

                            "charge"      => $s["talepPrice"] > 0 ? (($s["krediTotal"] - $s["krediLeft"]) * $s["talepPrice"]) / 1000 : 0,

                            "start_count" => isset($s["start_count"]) ? $s["start_count"] : 0,

                            "status"      => $status,

                            "remains"     => $s["krediLeft"],

                            "currency"    => "TRY"

                        );

                    }



                    $sorgu = $arrayData;

                } else {

                    $sorgu = array(

                        "status" => 0,

                        "error"  => "Hatalı işlem"

                    );

                }



                return $this->json($sorgu);



            } else if($action == "balance") {



                return $this->json(array(

                                       "balance"  => $this->bayi["bakiye"],

                                       "currency" => "TRY"

                                   ));



            } else {

                $data = array(

                    "status" => 0,

                    "error"  => "Geçersiz talep"

                );

            }





            return $this->json($data);

        }



    }