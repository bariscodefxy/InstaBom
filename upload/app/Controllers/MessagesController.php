<?php
    namespace App\Controllers;

    use Wow\Net\Response;
    use App\Libraries\InstagramReaction;
    use Wow\Template\View;

    class MessagesController extends BaseController {

        /**
         * @var InstagramReaction $instagramReaction
         */
        private $instagramReaction;


        /**
         * Override onStart
         */
        function onActionExecuting() {
            if(($pass = parent::onActionExecuting()) instanceof Response) {
                return $pass;
            }

            //Navigation
            $this->navigation->add("Mesajlar", "/messages");

            //Üye girişi kontrolü.
            if(($pass = $this->middleware("logged")) instanceof Response) {
                return $pass;
            }

            $this->instagramReaction = new InstagramReaction($this->logonPerson->member->uyeID);

        }


        function IndexAction() {

            $inbox = $this->instagramReaction->objInstagram->getV2Inbox();
            if($inbox["status"] != "ok") {
                return $this->notFound();
            }

            unset($_SESSION["NonReadThreadCount"]);

            return $this->view($inbox, "messages/inbox");
        }

        function ThreadsAction() {
            $inbox = $this->instagramReaction->objInstagram->getV2Inbox();
            if($inbox["status"] != "ok") {
                return $this->notFound();
            }

            return $this->partialView($inbox);
        }

        function ThreadAction($id) {
            $thread = $this->instagramReaction->objInstagram->directThread($id);

            $messages = $this->view->getContent("messages/thread-messages", $thread, TRUE);
            $users    = $this->view->getContent("messages/thread-users", $thread, TRUE);

            return $this->json(array(
                                   "messages" => $messages,
                                   "users"    => $users
                               ));
        }

        function SendMessageAction() {
            if($this->request->method != "POST") {
                return $this->notFound();
            }

            $recipients = explode(",", $this->request->data->recipients);
            $message    = $this->request->data->message;
            $mediaid    = intval($this->request->data->mediaid);
            if($mediaid > 0) {
                $media = $this->instagramReaction->objInstagram->getMediaInfo($mediaid);
                if($media["status"] != "ok") {
                    $mediaid = 0;
                }
            }


            if(file_exists($this->request->files->file['tmp_name']) || is_uploaded_file($this->request->files->file['tmp_name'])){
                $uploadError = NULL;
                if($this->request->files->file["error"] === UPLOAD_ERR_OK) {
                    $info = getimagesize($this->request->files->file["tmp_name"]);
                    if($info === FALSE) {
                        $uploadError = "Sadece resim yüklenebilir!";
                    } else {
                        if(($info[2] !== IMAGETYPE_JPEG) && ($info[2] !== IMAGETYPE_PNG)) {
                            $uploadError = "Sadece jpg,png resimleri yüklenebilir!";
                        } else {
                            if($this->request->data->type == "onetoone") {
                                foreach($recipients as $recipient) {
                                    $this->instagramReaction->objInstagram->direct_photo($recipient, $this->request->files->file["tmp_name"], $message);
                                }
                            } else {
                                $this->instagramReaction->objInstagram->direct_photo($recipients, $this->request->files->file["tmp_name"], $message);
                            }
                        }
                    }
                } else {
                    $uploadError = "Yükleme başarısız oldu!";
                }

                if($uploadError){
                    return $this->json(array("status" => "fail","error"=>$uploadError));
                }

            }
            elseif($mediaid == 0 && !empty($message)) {


                if($this->request->data->type == "onetoone") {
                    foreach($recipients as $recipient) {
                        $this->instagramReaction->objInstagram->direct_message($recipient, $message);
                    }
                } else {
                    $this->instagramReaction->objInstagram->direct_message($recipients, $message);
                }

            } else {
                if($this->request->data->type == "onetoone") {
                    foreach($recipients as $recipient) {
                        $this->instagramReaction->objInstagram->direct_share($mediaid, $recipient, $message);
                    }
                } else {
                    $this->instagramReaction->objInstagram->direct_share($mediaid, $recipients, $message);
                }
            }

            return $this->json(array("status" => "success"));
        }

        function NewAction() {
            $model = array(
                "recipients" => array(),
                "media"      => array()
            );

            $allRecipients = array();
            $recipients    = $this->request->query->recipients;
            if(!empty($recipients)) {
                $splittedRecipients = explode(",", $recipients);
                foreach($splittedRecipients as $rec) {
                    $recInfo = $this->instagramReaction->objInstagram->getUserInfoById($rec);
                    if($recInfo["status"] == "ok") {
                        $allRecipients[] = $recInfo;
                    }
                }
            }
            $model["recipients"] = $allRecipients;

            $mediaid = intval($this->request->query->mediaid);
            if($mediaid > 0) {
                $media = $this->instagramReaction->objInstagram->getMediaInfo($mediaid);
                if($media["status"] == "ok") {
                    $model["media"] = $media;
                }
            }

            return $this->partialView($model);
        }

        function SearchRecipientsAction() {
            $searchText = $this->request->query->q;
            $data       = $this->instagramReaction->objInstagram->searchUsers($searchText);

            return $this->partialView($data);
        }

    }