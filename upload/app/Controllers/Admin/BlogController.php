<?php

    namespace App\Controllers\Admin;

    use App\Models\Notification;
    use Wow;
    use Wow\Net\Response;

    class BlogController extends BaseController {

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
            //Yeni Blog İçeriği Kayıt Modu
            if($this->request->method == "POST") {
                $baslik = $this->request->data->baslik;
                $slug   = $this->helper->slug($baslik);

                $slugControl = $this->db->row("SELECT * FROM blog WHERE seoLink=:seoLink", ["seoLink" => $slug]);
                if(!empty($slugControl)) {
                    $slug = $slug . "-" . time();
                }

                $this->db->query("INSERT INTO blog (baslik,seoLink) VALUES (:baslik,:seoLink)", array("baslik"  => $baslik,
                                                                                                      "seoLink" => $slug
                ));
                $lastInsertID = $this->db->lastInsertId();
                if(intval($lastInsertID) > 0) {
                    $objNotification             = new Notification();
                    $objNotification->type       = $objNotification::PARAM_TYPE_SUCCESS;
                    $objNotification->title      = "Yeni Blog İçeriği Kaydedildi.";
                    $objNotification->messages[] = "Aşağıda eksik kısamları tamamlayabilirsiniz.";
                    $this->notifications[]       = $objNotification;

                    return $this->redirectToUrl(Wow::get("project/adminPrefix") . "/blog/blog-duzenle/" . $lastInsertID);
                }
            }

            //Blog Silme Modu
            if(intval($this->request->query->deleteBlogID) > 0) {
                $rowsAffected = $this->db->query("DELETE FROM blog WHERE blogID=:blogID", array("blogID" => $this->request->query->deleteBlogID));
                if($rowsAffected > 0) {
                    $objNotification             = new Notification();
                    $objNotification->type       = $objNotification::PARAM_TYPE_DANGER;
                    $objNotification->title      = "Blog İçeriği Silindi.";
                    $objNotification->messages[] = "Silmek istediğiniz blog başarılı bir şekilde silindi.";
                    $this->notifications[]       = $objNotification;

                    return $this->redirectToUrl(Wow::get("project/adminPrefix") . "/blog");
                }

            }


            $blogs = $this->db->query("SELECT * FROM blog");

            $this->navigation->add("Blog", Wow::get("project/adminPrefix") . "/blog");


            return $this->view($blogs);
        }

        function BlogDuzenleAction($id) {
            if(!intval($id) > 0) {
                return $this->notFound();
            }
            $id   = intval($id);
            $blog = $this->db->row("SELECT * FROM blog WHERE blogID=:blogID", array("blogID" => $id));
            if(empty($blog)) {
                return $this->notFound();
            }

            if($this->request->method == "POST") {
                $baslik   = $this->request->data->baslik;
                $icerik   = $this->request->data->icerik;
                $anaResim = $this->request->data->anaResim;
                $seoLink  = $this->request->data->seoLink;
                $isActive = intval($this->request->data->isActive) == 1 ? 1 : 0;
                $this->db->query("UPDATE blog SET baslik=:baslik,icerik=:icerik,anaResim=:anaResim,seoLink=:seoLink,isActive=:isActive WHERE blogID=:blogID", array(
                    "blogID"   => $blog["blogID"],
                    "baslik"   => $baslik,
                    "icerik"   => $icerik,
                    "anaResim" => $anaResim,
                    "seoLink"  => $seoLink,
                    "isActive" => $isActive
                ));

                $objNotification             = new Notification();
                $objNotification->type       = $objNotification::PARAM_TYPE_SUCCESS;
                $objNotification->title      = "Değişiklikler Kaydedildi.";
                $objNotification->messages[] = "Blog içeriğinde yaptığınız değişiklikler kaydedildi.";
                $this->notifications[]       = $objNotification;

                return $this->redirectToUrl($this->request->referrer);
            }

            $this->navigation->add("Blog", Wow::get("project/adminPrefix") . "/blog");
            $this->navigation->add($blog["baslik"], Wow::get("project/adminPrefix") . "/blog/blog-duzenle/" . $blog["blogID"]);


            return $this->view($blog);
        }
    }