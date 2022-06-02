<?php

    namespace App\Controllers\Admin;

    use App\Libraries\InstagramReaction;
    use Constants;
    use Exception;
    use RollingCurl\RollingCurl;
    use Signatures;
    use Utils;
    use Wow;
    use Wow\Database\Database;
    use Wow\Net\Response;
    use Instagram;
    use RollingCurl\Request as RollingCurlRequest;
    use InstagramWeb;

    class BakimController extends BaseController {

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
            if($this->request->method == "POST") {
                if($this->request->data->delete_rows == 1) {
                    $this->db->query("DELETE FROM bayi_islem WHERE islemTip<>'autolike' AND TIMESTAMPDIFF(DAY,eklenmeTarihi,NOW()) > 10 OR (islemTip='autolike' AND TIMESTAMPDIFF(DAY,endDate,NOW()) > 10)");
                    $this->db->query("DELETE FROM uye_otobegenipaket_gonderi WHERE likeCountLeft=0 AND bayiIslemID IS NOT NULL AND bayiIslemID NOT IN(SELECT bayiIslemID FROM bayi_islem WHERE islemTip='autolike')");
                }

                return $this->json(["status" => "success"]);
            }
            $bayiIslem        = $this->db->single("SELECT COUNT(*) 'toplamRow' FROM bayi_islem WHERE islemTip<>'autolike' AND TIMESTAMPDIFF(DAY,eklenmeTarihi,NOW()) > 10 OR (islemTip='autolike' AND TIMESTAMPDIFF(DAY,endDate,NOW()) > 10)");
            $otoBegeniGonderi = $this->db->single("SELECT COUNT(*) 'toplamRow' FROM uye_otobegenipaket_gonderi WHERE likeCountLeft=0 AND bayiIslemID IN(SELECT bayiIslemID FROM bayi_islem WHERE islemTip<>'autolike' AND TIMESTAMPDIFF(DAY,eklenmeTarihi,NOW()) > 10 OR (islemTip='autolike' AND TIMESTAMPDIFF(DAY,endDate,NOW()) > 10))");

            $data = [
                "bayi_islem"                 => $bayiIslem,
                "uye_otobegenipaket_gonderi" => $otoBegeniGonderi
            ];

            return $this->view($data);
        }

    }