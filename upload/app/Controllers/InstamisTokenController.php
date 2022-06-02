<?php

    namespace App\Controllers;

    use App\Libraries\InstagramReaction;
    use App\Models\LogonPerson;
    use Exception;
    use Wow;
    use Wow\Net\Response;
    use Instagram;


    class InstamisTokenController extends BaseController {

        private $token      = NULL;
        private $memberdata = NULL;


        function onActionExecuting() {
            $actionResponse = parent::onActionExecuting();
            if($actionResponse instanceof Response) {
                return $actionResponse;
            }

            if($this->logonPerson->isLoggedIn()) {
                return $this->redirectToUrl("/tools");
            }

            $this->token = $this->request->query->token ? $this->request->query->token : "";

            $_SESSION["instamisToken"] = $this->token;

            if(!empty($this->token)) {
                $this->memberdata = $this->db->row("SELECT * FROM uye WHERE instamisToken=:token", array("token" => $this->token));
            } else {
                return $this->redirectToUrl("/home");
            }
        }


        function IndexAction() {

            if(!empty($this->memberdata)) {
                $this->logonPerson->setLoggedIn(TRUE);
                $this->logonPerson->setMemberData($this->memberdata);
                $_SESSION["LogonPerson"] = $this->logonPerson;
                new Instagram($this->memberdata["kullaniciAdi"], $this->memberdata["sifre"], $this->memberdata["instaID"]);

                return $this->redirectToUrl("/tools");
            }

            return $this->json(array(
                                   "hata"  => "Bir hata oluştu.",
                                   "data"  => $this->memberdata,
                                   "token" => $this->token
                               ));

        }

    }