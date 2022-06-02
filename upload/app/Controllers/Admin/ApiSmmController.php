<?php

    namespace App\Controllers\Admin;

    use App\Libraries\InstagramReaction;
    use Constants;
    use Exception;
    use RollingCurl\RollingCurl;
    use Signatures;
    use SmmApi;
    use Utils;
    use Wow;
    use Wow\Database\Database;
    use Wow\Net\Response;
    use Instagram;
    use RollingCurl\Request as RollingCurlRequest;
    use InstagramWeb;

    class ApiSmmController extends BaseController {

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
         * @var array $data
         */
        protected $data;

        function onActionExecuting() {
            if(($actionResponse = parent::onActionExecuting()) instanceof Response) {
                return $actionResponse;
            }
            //Üye girişi kontrolü.
            if(($pass = $this->middleware("logged")) instanceof Response) {
                return $pass;
            }

            if(!$this->request->ajax) {
                $this->data["api"] = SmmApi::postDataSmm("api-check");
                if(!empty(Wow::get("ayar/InstaWebSmmAuth"))) {
                    if(isset($this->data["api"]["status"]) && $this->data["api"]["status"] == 0) {
                        $fileData                    = json_decode(file_get_contents("app/Config/system-settings.php"), TRUE);
                        $fileData["InstaWebSmmAuth"] = "";
                        file_put_contents("app/Config/system-settings.php", json_encode($fileData));

                        return $this->redirectToUrl(Wow::get("project/adminPrefix")."/api-smm");
                    }
                }
            }

        }

        function onActionExecuted() {
            if(($actionResponse = parent::onActionExecuted()) instanceof Response) {
                return $actionResponse;
            }

            @$_SESSION["apiData"] = $this->data["api"];

        }

        function IndexAction() {

            $this->view->set('title', "Anasayfa - SMM Api Servisleri");

            return $this->view($this->data);
        }

        function CikisYapAction() {

            $fileData                    = json_decode(file_get_contents("app/Config/system-settings.php"), TRUE);
            $fileData["InstaWebSmmAuth"] = "";
            file_put_contents("app/Config/system-settings.php", json_encode($fileData));

            return $this->redirectToUrl(Wow::get("project/adminPrefix")."/api-smm");
        }

        function PostDataAction() {

            if($this->request->method == "POST") {

                $type = $this->request->data->type ? $this->request->data->type : "";

                if(empty($type) || !in_array($type, array(
                        "register",
                        "login",
                        "authregister"
                    ))) {
                    return $this->json(array(
                                           "status" => 0,
                                           "error"  => "Tip belirtilmedi yada hatalı tip gönderildi."
                                       ));
                }

                $kullaniciAdi = $this->request->data->kullaniciAdi ? $this->request->data->kullaniciAdi : "";
                $sifre        = $this->request->data->sifre ? $this->request->data->sifre : "";
                $sifretekrar  = $this->request->data->sifretekrar ? $this->request->data->sifretekrar : "";

                if($type == "register") {
                    return $this->json(SmmApi::registerSmm($kullaniciAdi, $sifre, $sifretekrar));
                } else if($type == "login") {
                    return $this->json(SmmApi::loginSmm($kullaniciAdi, $sifre));
                } else {

                    $auth = $this->request->data->auth ? $this->request->data->auth : "";

                    if(empty($auth)) {

                        return $this->json(array(
                                               "status" => 0,
                                               "error"  => "Auth kodu hatalı yada boş tekrar deneyiniz."
                                           ));

                    } else {

                        $fileData                    = json_decode(file_get_contents("app/Config/system-settings.php"), TRUE);
                        $fileData["InstaWebSmmAuth"] = $auth;
                        file_put_contents("app/Config/system-settings.php", json_encode($fileData));

                        return $this->json(array("status" => 1));
                    }


                }

            }
            return $this->json($this->data);
        }


        function YeniIslemAction() {

            $this->view->set('title', "Yeni İşlem - SMM Api Servisleri");

            if(isset($this->data["api"]["status"]) && $this->data["api"]["status"] == 0) {
                return $this->redirectToUrl(Wow::get("project/adminPrefix")."/api-smm");
            }

            if(isset($this->request->query->okey)) {

                setcookie("okey", 1, time() + 3600);

                return $this->redirectToUrl(Wow::get("project/adminPrefix")."/api-smm/yeni-islem");

            }


            if($this->request->ajax) {

                $data = SmmApi::postDataSmm("order", $this->request->data->getData());

                if(isset($data["status"]) && $data["status"] == 1) {

                    $talepID = isset($data["talepID"]) ? $data["talepID"] : "";

                    if(!empty($talepID)) {
                        $this->db->query("INSERT INTO smm_talepler (talepID) VALUES(:talepID)", array("talepID" => $talepID));
                    }

                }

                return $this->json($data);

            }

            $this->data["servisler"] = SmmApi::postDataSmm("service-list");

            return $this->view($this->data);
        }

        function TaleplerAction($id = 1) {

            $this->view->set('title', "Talepler - SMM Api Servisleri");

            if(isset($this->data["api"]["status"]) && $this->data["api"]["status"] == 0) {
                return $this->redirectToUrl(Wow::get("project/adminPrefix")."/api-smm");
            }

            $sayfa         = $id == 1 ? 0 : ($id * 100) - 100;
            $this->data["sayfa"] = $id;

            if($this->request->ajax) {

                $talepler = $this->db->query("SELECT talepID FROM smm_talepler ORDER BY talepID DESC LIMIT {$sayfa},100");

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

            return $this->view($this->data);
        }
    }