<?php

    namespace App\Controllers\Admin;

    use Wow\Net\Response;
    use App\Models\Notification;
    use Wow;

    class SayfalarController extends BaseController {

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
            $pages = $this->db->query("SELECT * FROM sayfa");

            $this->navigation->add("Sayfalar", Wow::get("project/adminPrefix")."/sayfalar");


            return $this->view($pages);
        }

        function SayfaDuzenleAction($id) {
            if(!intval($id) > 0) {
                return $this->notFound();
            }
            $id   = intval($id);
            $page = $this->db->row("SELECT * FROM sayfa WHERE id=:id", array("id" => $id));
            if(empty($page)) {
                return $this->notFound();
            }

            if($this->request->method == "POST") {
                $pageInfo    = serialize(array(
                                             "title"       => $this->request->data->title,
                                             "description" => $this->request->data->description,
                                             "keywords"    => $this->request->data->keywords
                                         ));
                $pageContent = $this->request->data->pageContent;
                $this->db->query("UPDATE sayfa SET pageInfo=:pageInfo,pageContent=:pageContent WHERE id=:id", array(
                    "id"          => $page["id"],
                    "pageInfo"    => $pageInfo,
                    "pageContent" => $pageContent
                ));

                $objNotification             = new Notification();
                $objNotification->type       = $objNotification::PARAM_TYPE_SUCCESS;
                $objNotification->title      = "Değişiklikler Kaydedildi.";
                $objNotification->messages[] = "Sayfada yaptığınız değişiklikler kaydedildi.";
                $this->notifications[]       = $objNotification;

                return $this->redirectToUrl($this->request->referrer);
            }

            $this->navigation->add("Sayfalar", Wow::get("project/adminPrefix")."/sayfalar");
            $this->navigation->add($page["page"], Wow::get("project/adminPrefix")."/sayfalar/sayfa-duzenle/" . $page["id"]);


            return $this->view($page);
        }
    }