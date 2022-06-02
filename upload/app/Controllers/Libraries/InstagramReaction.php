<?php

    namespace App\Libraries;

    use Exception;
    use Wow\Database\Database;
    use Instagram;
    use InstagramWeb;

    class InstagramReaction {

        /**
         * @var Instagram $objInstagram
         */
        public $objInstagram;
        /**
         * @var InstagramWeb $objInstagramWeb
         */
        public $objInstagramWeb;

        /**
         * @var Database $db
         */
        protected $db;
        /**
         * @var array $user
         */
        protected $user;

        /**
         * Instantiate Class By UserID
         *
         * @param $userID
         */
        function __construct($userID) {
            $this->db = Database::getInstance();

            $user = $this->db->row("SELECT * FROM uye WHERE uyeID=:uyeID", array("uyeID" => intval($userID)));

            if(empty($user)) {
                throw new Exception($userID . " IDli üye bulunamadı!");
            }
            $this->user         = $user;
            $this->objInstagram = new Instagram($this->user["kullaniciAdi"], $this->user["sifre"], $this->user["instaID"]);
        }

        /**
         * @param string $mediaID
         * @param string $mediaCode
         *
         * @return bool
         */
        public function like($mediaID, $mediaCode) {
            //Aktif olmayan bir kullanıcıya bişeyler beğendirmeye çalışmamalısınız! Sonuç her zaman FALSE olacaktır.
            if($this->user["isActive"] == 0) {
                return FALSE;
            }
            //Web Cookie ise
            if($this->user["isWebCookie"] == 1) {
                //Eğer kullanıcı __construct metodundan geçmiş olmasına rağmen hala isLoggedIn FALSE geliyorsa bu kullanıcının cookie dosyaları eksik anlamına gelir. Bu durumda bu kullanıcı pasif edeceğiz.
                if(!$this->objInstagramWeb->isLoggedIn()) {
                    $this->db->query("UPDATE uye SET isActive=0, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                    return FALSE;
                }

                $checkMedia = $this->objInstagramWeb->mediaInfo($mediaCode);
                if(isset($checkMedia["status"]) && $checkMedia["status"] != 'ok') {
                    $this->db->query("UPDATE uye SET isActive=2, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                    return FALSE;
                } elseif(isset($checkMedia["media"]) && isset($checkMedia["media"]["likes"]["viewer_has_liked"]) && $checkMedia["media"]["likes"]["viewer_has_liked"] == 1) {
                    $this->db->query("UPDATE uye SET isActive=1, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                    return FALSE;
                } elseif(isset($checkMedia["media"])) {
                    $this->db->query("UPDATE uye SET isActive=1, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                    $data = $this->objInstagramWeb->like($mediaID);
                    if(isset($data["status"]) && $data["status"] == 'ok') {
                        //Ve nihayet beğeni gerçekleşti.
                        return TRUE;
                    } else {
                        $this->db->query("UPDATE uye SET isActive=2, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                        return FALSE;
                    }
                } else {
                    $this->db->query("UPDATE uye SET isActive=2, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                    return FALSE;
                }


            } //User password ise
            else {

                //Eğer kullanıcı __construct metodundan geçmiş olmasına rağmen hala isLoggedIn FALSE geliyorsa bu kullanıcının cookie dosyaları eksik anlamına gelir. Bu durumda tekrar login denemesi yapmamız gerek.
                if(!$this->objInstagram->isLoggedIn()) {
                    try {
                        $this->objInstagram->login(TRUE);
                    } //Logini tazeleyemiyorsak bu kullanıcı pasif demektir. Malum kişi şifre değiştirmiş demektir. Pasif edelim.
                    catch(Exception $e) {
                        $this->db->query("UPDATE uye SET isActive=2, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                        return FALSE;
                    }
                }


                $checkMedia = $this->objInstagram->getMediaInfo($mediaID);

                //Aktif Kullanıcı ile ilgili medyaya erişemez ve giriş gerekli hatası alırsak logini tazalemeyi deneyelim.
                if($checkMedia["status"] == "fail" && $checkMedia["message"] == "login_required") {
                    try {
                        $result = $this->objInstagram->login(TRUE);
                    } //Logini tazeleyemiyorsak bu kullanıcı pasif demektir. Malum kişi şifre değiştirmiş demektir. Pasif edelim.
                    catch(Exception $e) {
                        $result = array("status" => "fail");
                    }
                    if($result["status"] != "ok") {
                        $this->db->query("UPDATE uye SET isActive=2 WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                        return FALSE;
                    }
                } elseif($checkMedia["status"] == "fail" && $checkMedia["message"] == "checkpoint_required") {
                    $this->db->query("UPDATE uye SET isActive=2 WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                    return FALSE;
                }

                $this->db->query("UPDATE uye SET isActive=1, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));
                //Bu gönderiyi daha önce zaten beğenmişse yapacak birşey yok. FALSE
                if(isset($checkMedia["items"][0]["has_liked"]) && $checkMedia["items"][0]["has_liked"] == 1) {
                    return FALSE;
                } else {
                    $data = $this->objInstagram->like($mediaID);
                    if(isset($data["status"]) && $data["status"] == 'ok') {
                        //Ve nihayet beğeni gerçekleşti.
                        return TRUE;
                    } else {
                        return FALSE;
                    }
                }
            }

            return FALSE;
        }


        public function follow($userID, $userName) {
            //Aktif olmayan bir kullanıcıya bişeyler yaptırmaya çalışmamalısınız! Sonuç her zaman FALSE olacaktır.
            if($this->user["isActive"] == 0) {
                return FALSE;
            }
            //Web Cookie ise
            if($this->user["isWebCookie"] == 1) {
                //Eğer kullanıcı __construct metodundan geçmiş olmasına rağmen hala isLoggedIn FALSE geliyorsa bu kullanıcının cookie dosyaları eksik anlamına gelir. Bu durumda bu kullanıcı pasif edeceğiz.
                if(!$this->objInstagramWeb->isLoggedIn()) {
                    $this->db->query("UPDATE uye SET isActive=0, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                    return FALSE;
                }

                $checkUser = $this->objInstagramWeb->getUsernameInfo($userName);

                if(isset($checkUser["status"]) && $checkUser["status"] != 'ok') {
                    $this->db->query("UPDATE uye SET isActive=2, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                    return FALSE;
                } elseif(isset($checkUser["user"]) && isset($checkUser["user"]["followed_by_viewer"]) && $checkUser["user"]["followed_by_viewer"] == 1) {
                    $this->db->query("UPDATE uye SET isActive=1, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                    return FALSE;
                } elseif(isset($checkUser["user"])) {
                    $this->db->query("UPDATE uye SET isActive=1, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                    $data = $this->objInstagramWeb->follow($userID);
                    if(isset($data["status"]) && $data["status"] == 'ok') {
                        //Ve nihayet takip gerçekleşti.
                        return TRUE;
                    } else {
                        //Takip işlemlerinde geçici bloka düşme olayı şimdilik rafta.
                        //$this->db->query("UPDATE uye SET isActive=2, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));
                        $this->db->query("UPDATE uye SET sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                        return FALSE;
                    }
                } else {
                    $this->db->query("UPDATE uye SET isActive=2, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                    return FALSE;
                }


            } //User password ise
            else {

                //Eğer kullanıcı __construct metodundan geçmiş olmasına rağmen hala isLoggedIn FALSE geliyorsa bu ullanıcının cookie dosyaları eksik anlamına gelir. Bu durumda tekrar login denemesi yapmamız gerek.
                if(!$this->objInstagram->isLoggedIn()) {
                    try {
                        $this->objInstagram->login(TRUE);
                    } //Logini tazeleyemiyorsak bu kullanıcı pasif demektir. Malum kişi şifre değiştirmiş demektir. Pasif edelim.
                    catch(Exception $e) {
                        $this->db->query("UPDATE uye SET isActive=2, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                        return FALSE;
                    }
                }


                $checkUser = $this->objInstagram->userFriendship($userID);

                //Aktif Kullanıcı ile ilgili kullanıcıyı erişemez ve giriş gerekli hatası alırsak logini tazalemeyi deneyelim.
                if($checkUser["status"] == "fail" && $checkUser["message"] == "login_required") {
                    try {
                        $result = $this->objInstagram->login(TRUE);
                    } //Logini tazeleyemiyorsak bu kullanıcı pasif demektir. Malum kişi şifre değiştirmiş demektir. Pasif edelim.
                    catch(Exception $e) {
                        $result = array("status" => "fail");
                    }
                    if($result["status"] != "ok") {
                        $this->db->query("UPDATE uye SET isActive=2, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                        return FALSE;
                    }
                } elseif($checkUser["status"] == "fail" && $checkUser["message"] == "checkpoint_required") {
                    $this->db->query("UPDATE uye SET isActive=2, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                    return FALSE;
                }

                //Bu kullanıcıyı zaten takip ediyorsa yapacak birşey yok. FALSE
                if(isset($checkUser["following"]) && $checkUser["following"] == 1) {
                    $this->db->query("UPDATE uye SET isActive=1, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                    return FALSE;
                } else {
                    $data = $this->objInstagram->follow($userID);
                    $this->db->query("UPDATE uye SET isActive=1, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));
                    if(isset($data["status"]) && $data["status"] == 'ok') {
                        //Ve nihayet takip gerçekleşti.
                        $this->db->query("UPDATE uye SET isActive=1, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                        return TRUE;
                    } else {
                        //Takip işlemlerinde geçici bloka düşme olayı şimdilik rafta.
                        //$this->db->query("UPDATE uye SET isActive=2, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));
                        $this->db->query("UPDATE uye SET sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                        return FALSE;
                    }
                }
            }

            return FALSE;
        }


        /**
         * @param string $mediaID
         * @param string $mediaCode
         * @param string $commentText
         *
         * @return bool
         */
        public function comment($mediaID, $mediaCode, $commentText) {
            //Aktif olmayan bir kullanıcıya bişeyler beğendirmeye çalışmamalısınız! Sonuç her zaman FALSE olacaktır.
            if($this->user["isActive"] == 0) {
                return FALSE;
            }
            //Web cookie ise
            if($this->user["isWebCookie"] == 1) {

                //Eğer kullanıcı __construct metodundan geçmiş olmasına rağmen hala isLoggedIn FALSE geliyorsa bu kullanıcının cookie dosyaları eksik anlamına gelir. Bu durumda bu kullanıcı pasif edeceğiz.
                if(!$this->objInstagramWeb->isLoggedIn()) {
                    $this->db->query("UPDATE uye SET isActive=0, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                    return FALSE;
                }


                $checkMedia = $this->objInstagramWeb->mediaInfo($mediaCode);
                if(isset($checkMedia["status"]) && $checkMedia["status"] != 'ok') {
                    $this->db->query("UPDATE uye SET isActive=2, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                    return FALSE;
                } elseif(isset($checkMedia["media"])) {
                    $data = $this->objInstagramWeb->comment($mediaID, $commentText);
                    if(isset($data["status"]) && $data["status"] == 'ok') {
                        //Ve nihayet yorum gerçekleşti.
                        $this->db->query("UPDATE uye SET isActive=1, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                        return TRUE;
                    } else {
                        $this->db->query("UPDATE uye SET isActive=2, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                        return FALSE;
                    }
                } else {
                    $this->db->query("UPDATE uye SET isActive=2, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                    return FALSE;
                }


            } //User password ise
            else {

                //Eğer kullanıcı __construct metodundan geçmiş olmasına rağmen hala isLoggedIn FALSE geliyorsa bu ullanıcının cookie dosyaları eksik anlamına gelir. Bu durumda tekrar login denemesi yapmamız gerek.
                if(!$this->objInstagram->isLoggedIn()) {
                    try {
                        $this->objInstagram->login(TRUE);
                    } //Logini tazeleyemiyorsak bu kullanıcı pasif demektir. Malum kişi şifre değiştirmiş demektir. Pasif edelim.
                    catch(Exception $e) {
                        $this->db->query("UPDATE uye SET isActive=2, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                        return FALSE;
                    }
                }


                $checkMedia = $this->objInstagram->getMediaInfo($mediaID);

                //Aktif Kullanıcı ile ilgili medyaya erişemez ve giriş gerekli hatası alırsak logini tazalemeyi deneyelim.
                if($checkMedia["status"] == "fail" && $checkMedia["message"] == "login_required") {
                    try {
                        $result = $this->objInstagram->login(TRUE);
                    } //Logini tazeleyemiyorsak bu kullanıcı pasif demektir. Malum kişi şifre değiştirmiş demektir. Pasif edelim.
                    catch(Exception $e) {
                        $result = array("status" => "fail");
                    }
                    if($result["status"] != "ok") {
                        $this->db->query("UPDATE uye SET isActive=2, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                        return FALSE;
                    }
                } elseif($checkMedia["status"] == "fail" && $checkMedia["message"] == "checkpoint_required") {
                    $this->db->query("UPDATE uye SET isActive=2 WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                    return FALSE;
                }

                $data = $this->objInstagram->comment($mediaID, $commentText);
                $this->db->query("UPDATE uye SET isActive=1, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));
                if(isset($data["status"]) && $data["status"] == 'ok') {
                    //Ve nihayet yorum gerçekleşti.
                    return TRUE;
                } else {
                    return FALSE;
                }

            }

            return FALSE;
        }

        function validateUser() {
            //Web Cookie ise
            if($this->user["isWebCookie"] == 1) {
                //Eğer kullanıcı __construct metodundan geçmiş olmasına rağmen hala isLoggedIn FALSE geliyorsa bu kullanıcının cookie dosyaları eksik anlamına gelir. Bu durumda bu kullanıcı pasif edeceğiz.
                if(!$this->objInstagramWeb->isLoggedIn()) {
                    $this->db->query("UPDATE uye SET isActive=0,isNeedLogin=0, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                    return FALSE;
                }

                $checkUser = $this->objInstagramWeb->isValid();
                if($checkUser) {
                    $this->db->query("UPDATE uye SET isActive=1,isNeedLogin=0,canFollow=1,canLike=1,canComment=1, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                    return TRUE;
                } else {

                    $this->db->query("UPDATE uye SET isActive=0,isNeedLogin=0, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                    return FALSE;
                }


            } //User password ise
            else {

                //Eğer kullanıcı __construct metodundan geçmiş olmasına rağmen hala isLoggedIn FALSE geliyorsa bu ullanıcının cookie dosyaları eksik anlamına gelir. Bu durumda tekrar login denemesi yapmamız gerek.
                try {
                    $this->objInstagram->login(TRUE);
                } //Logini tazeleyemiyorsak bu kullanıcı pasif demektir. Malum kişi şifre değiştirmiş demektir. Pasif edelim.
                catch(Exception $e) {
                    $this->db->query("UPDATE uye SET isActive=0,isNeedLogin=0, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                    return FALSE;
                }

                $checkUser = $this->objInstagram->getMediaInfo("1644818823288800567_6304564234");
                //Aktif Kullanıcı ile ilgili kullanıcıyı erişemez ve giriş gerekli hatası alırsak logini tazalemeyi deneyelim.
                if($checkUser["status"] == "fail" && ($checkUser["message"] == "login_required" || $checkUser["message"] == "checkpoint_required")) {
                    try {
                        $result = $this->objInstagram->login(TRUE);
                    } //Logini tazeleyemiyorsak bu kullanıcı pasif demektir. Malum kişi şifre değiştirmiş demektir. Pasif edelim.
                    catch(Exception $e) {
                        $result = array("status" => "fail");
                    }
                    if($result["status"] != "ok") {
                        $this->db->query("UPDATE uye SET isActive=0,isNeedLogin=0, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                        return FALSE;
                    } else {
                        $this->db->query("UPDATE uye SET isActive=1,isNeedLogin=0,canFollow=1,canLike=1,canComment=1, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                        return TRUE;
                    }
                } elseif($checkUser["status"] == "ok") {
                    $this->db->query("UPDATE uye SET isActive=1,isNeedLogin=0,canFollow=1,canLike=1,canComment=1, sonOlayTarihi = NOW() WHERE uyeID=:uyeID", array("uyeID" => $this->user["uyeID"]));

                    return TRUE;
                }

            }

            return FALSE;
        }


        public function getMediaData($permalink) {
            $handle = curl_init();

            curl_setopt($handle, CURLOPT_URL, "https://api.instagram.com/publicapi/oembed/?url=" . $permalink);
            curl_setopt($handle, CURLOPT_POST, FALSE);
            curl_setopt($handle, CURLOPT_BINARYTRANSFER, FALSE);
            curl_setopt($handle, CURLOPT_HEADER, TRUE);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($handle, CURLOPT_FOLLOWLOCATION, TRUE);

            $response = curl_exec($handle);

            $hlength  = curl_getinfo($handle, CURLINFO_HEADER_SIZE);
            $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
            $body     = substr($response, $hlength);

            // If HTTP response is not 200, return false
            if($httpCode != 200) {
                return FALSE;
            }

            return json_decode($body, TRUE);
        }

    }