<?php

    namespace App\Controllers\Admin;

    use App\Models\Notification;
    use Wow;
    use Wow\Net\Response;

    class SettingsController extends BaseController {

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

                $data = $this->request->data->ayar;
                if(is_array($data) && !empty($data)) {
                    $arrAyar = array();
                    foreach($data as $key => $value) {
                        $arrAyar[$key] = $value;
                    }

                    file_put_contents("./app/Config/system-settings.php", json_encode($arrAyar));

                    if($arrAyar["securityKey"] != Wow::get("ayar/securityKey")) {
                        $oldKey = "scKey=" . Wow::get("ayar/securityKey");
                        $newKey = "scKey=" . $arrAyar["securityKey"];
                        $this->db->query("UPDATE cron SET url=REPLACE(url,:oldKey,:newKey) WHERE (url LIKE :site OR url LIKE :ip)", [
                            "oldKey" => $oldKey,
                            "newKey" => $newKey,
                            "site"   => '%' . preg_replace('/(?:www\.)?(.*)\/?$/i', '$1', $_SERVER["HTTP_HOST"]) . '%',
                            "ip"     => $_SERVER['SERVER_ADDR']
                        ]);
                    }

                    $objNotification             = new Notification();
                    $objNotification->type       = $objNotification::PARAM_TYPE_SUCCESS;
                    $objNotification->title      = "Değişiklikler Kaydedildi.";
                    $objNotification->messages[] = "Ayarlarda yaptığınız değişiklikler kaydedildi.";
                    $this->notifications[]       = $objNotification;
                }

                return $this->redirectToUrl($this->request->referrer);

            }

            return $this->view();
        }

        function CronAction() {
            $data = $this->db->query("SELECT * FROM cron ORDER BY cronID ASC");

            return $this->view($data);
        }


        function OneCronAction($id) {

            $data = array();

            if(!empty($id)) {

                $data = $this->db->row("SELECT * FROM cron WHERE cronID = :cronID", array("cronID" => $id));

            }

            return $this->json($data);


        }


        function SaveUpdateCronAction() {

            $cronID     = $this->request->data->cronID ? $this->request->data->cronID : "";
            $cronBaslik = $this->request->data->cronBaslik ? $this->request->data->cronBaslik : "";
            $cronUrl    = $this->request->data->cronUrl ? $this->request->data->cronUrl : "";
            $cronSaniye = $this->request->data->cronSaniye ? $this->request->data->cronSaniye : "";
            $cronDurum  = in_array($this->request->data->cronDurum, array(
                "0",
                "1"
            )) ? $this->request->data->cronDurum : 0;

            if(empty($cronID)) {

                $this->db->query("INSERT INTO cron (baslik,url,calismaSikligi,isActive) VALUES (:baslik,:url,:calisma,:isactive)", array(
                    "baslik"   => $cronBaslik,
                    "url"      => $cronUrl,
                    "calisma"  => $cronSaniye,
                    "isactive" => $cronDurum
                ));

            } else {

                $this->db->query("UPDATE cron SET baslik=:baslik,url=:url,calismaSikligi=:calisma,isActive=:isactive WHERE cronID = :cronID", array(
                    "baslik"   => $cronBaslik,
                    "url"      => $cronUrl,
                    "calisma"  => $cronSaniye,
                    "isactive" => $cronDurum,
                    "cronID"   => $cronID
                ));

            }

            return $this->redirectToUrl($this->request->referrer);

        }

    }