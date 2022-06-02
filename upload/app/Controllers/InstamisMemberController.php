<?php

    namespace App\Controllers;

    use Settings;
    use Wow;
    use Wow\Net\Response;

    class InstamisMemberController extends BaseController {

        private $uyeID;
        private $deviceID;
        private $auth;

        function onActionExecuting() {
            if(($pass = parent::onActionExecuting()) instanceof Response) {
                return $pass;
            }

            $this->uyeID    = $this->request->data->uyeID ? $this->request->data->uyeID : "";
            $this->deviceID = $_SERVER["HTTP_DEVICEID"] ? $_SERVER["HTTP_DEVICEID"] : "";
            $this->auth     = $_SERVER["HTTP_AUTH"] ? $_SERVER["HTTP_AUTH"] : "";

            if(!empty($this->auth) && !empty($this->uyeID) && !empty($this->deviceID)) {

                $tokenID = $this->db->single("SELECT tokenID FROM token WHERE (uyeID=:uyeid OR tokenID=:tokenid) AND deviceID=:deviceid AND oAuth=:oauth", array(
                    "uyeid"    => $this->uyeID,
                    "tokenid"  => $this->uyeID,
                    "deviceid" => $this->deviceID,
                    "oauth"    => $this->auth
                ));

                if(empty($tokenID)) {

                    return $this->json(array(
                                           "status" => 0,
                                           "error"  => "Yetkilendirme hatası",
                                           "hata"   => 1
                                       ));
                }

            } else {
                return $this->json(array(
                                       "status" => 0,
                                       "error"  => "Yetkilendirme hatası",
                                       "hata"   => 2
                                   ));


            }

        }


        public function IndexAction() {

            $data = array();

            return $this->partialView($data);
        }


        public function LogoutAction() {

            $data = array();

            $this->db->query("UPDATE token SET loginStatus=0 WHERE uyeID=:uyeid AND deviceID=:deviceid", array(
                "uyeid"    => $this->uyeID,
                "deviceid" => $this->deviceID
            ));

            $data["uyeID"]    = $this->uyeID;
            $data["deviceID"] = $this->deviceID;

            return $this->json($data);
        }

    }