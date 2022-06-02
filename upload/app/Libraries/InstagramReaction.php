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
                throw new Exception($userID . " IDli uye bulunamadi!");
            }
            $this->user         = $user;
            $this->objInstagram = new Instagram($this->user["kullaniciAdi"], $this->user["sifre"], $this->user["instaID"]);
        }

        public function getMediaData($permalink) {
            $handle = curl_init();

            curl_setopt($handle, CURLOPT_URL, "https://i.instagram.com/publicapi/oembed/?url=" . $permalink);
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