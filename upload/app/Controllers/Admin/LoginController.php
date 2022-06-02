<?php

    namespace App\Controllers\Admin;

    use App\Models\LogonPerson;
    use Wow\Net\Response;
    use Wow;

    class LoginController extends BaseController {

        function onActionExecuting() {
            if(($actionResponse = parent::onActionExecuting()) instanceof Response) {
                return $actionResponse;
            }
            //Üye girişi kontrolü.
            if(($pass = $this->middleware("logged")) instanceof Response) {
                return $pass;
            }
        }

        function BayiAction($id) {
            $bayi = $this->db->row("SELECT * FROM bayi WHERE isActive=1 AND sonaErmeTarihi>NOW() AND bayiID=:bayiID", ["bayiID" => $id]);
            if(empty($bayi)) {
                return $this->notFound();
            }
            $bayiLogonPerson = new LogonPerson();
            $bayiLogonPerson->setLoggedIn(TRUE);
            $bayiLogonPerson->setMemberData($bayi);
            $_SESSION["BayiLogonPerson"] = $bayiLogonPerson;

            return $this->redirectToUrl(Wow::get("project/resellerPrefix"));
        }

        function UyeAction($id) {
            $uye = $this->db->row("SELECT * FROM uye WHERE isWebCookie=0 AND uyeID=:uyeID", ["uyeID" => $id]);
            if(empty($uye)) {
                return $this->notFound();
            }
            $uyeLogonPerson = new LogonPerson();
            $uyeLogonPerson->setLoggedIn(TRUE);
            $uyeLogonPerson->setMemberData($uye);
            $_SESSION["LogonPerson"] = $uyeLogonPerson;

            return $this->redirectToUrl("/account");
        }

    }