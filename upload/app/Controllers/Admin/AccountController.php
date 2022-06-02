<?php

    namespace App\Controllers\Admin;

    use App\Models\LogonPerson;
    use Wow\Net\Response;
    use App\Models\Notification;
    use Wow;

    class AccountController extends BaseController {

        /**
         * Override onStart
         */
        function onActionExecuting() {
            if(($pass = parent::onActionExecuting()) instanceof Response) {
                return $pass;
            }

            //Üye girişi kontrolü.
            if(($pass = $this->middleware("logged")) instanceof Response) {
                return $pass;
            }

        }

        function IndexAction() {

            if(isset($this->request->data->username)) {
                $arrErrors = array();
                $username  = $this->request->data->username ? $this->request->data->username : "";
                $password  = $this->request->data->oldpass ? $this->request->data->oldpass : "";

                if(!empty($username) && !empty($password)) {

                    if($this->logonPerson->member["password"] == $password) {

                        $bb = $this->db->row("SELECT adminID FROM admin WHERE username = :username AND adminID <> :adminid", array(
                            "username" => $username,
                            "adminid"  => $this->logonPerson->member["adminID"]
                        ));

                        if(empty($bb)) {
                            $this->db->query("UPDATE admin SET username=:uname WHERE adminID=:adminid", array(
                                "uname"   => $username,
                                "adminid" => $this->logonPerson->member["adminID"]
                            ));

                        } else {
                            $arrErrors[] = "Yapmak istediğiniz admin kullanıcı adı kullanılmaktadır.";
                        }

                        $newpass   = $this->request->data->newpass ? $this->request->data->newpass : "";
                        $renewpass = $this->request->data->renewpass ? $this->request->data->renewpass : "";

                        if((!empty($newpass) && !empty($renewpass))) {

                            if($newpass == $renewpass) {
                                $this->db->query("UPDATE admin SET password = :passw WHERE adminID = :adminid", array(
                                    "passw"   => $newpass,
                                    "adminid" => $this->logonPerson->member["adminID"]
                                ));
                            } else {
                                $arrErrors[] = "Girdiğiniz yeni şifreler birbiri ile uyuşmamaktadır.";
                            }

                        }

                    } else {
                        $arrErrors[] = "Girmiş olduğunuz eski şifreniz hatalıdır.";
                    }

                } else {

                    $arrErrors[] = "Kullanıcı adı ve eski şifrenizi mutlaka girmeniz gerekmektedir.";

                }

                $objNotification = new Notification();
                if(!empty($arrErrors)) {
                    $objNotification->title    = "Hesap Değişikliği Hatası!";
                    $objNotification->type     = $objNotification::PARAM_TYPE_DANGER;
                    $objNotification->messages = $arrErrors;
                    $this->notifications[]     = $objNotification;
                } else {
                    $this->logonPerson         = new LogonPerson();
                    $objNotification->title    = "Hesap Değişikliği Başarılı!";
                    $objNotification->type     = $objNotification::PARAM_TYPE_SUCCESS;
                    $objNotification->messages = array("Hesap bilgileriniz başarılı bir şekilde değiştirilmiştir.");
                    $this->notifications[]     = $objNotification;
                }

                return $this->redirectToUrl($this->request->referrer);
            }

            return $this->view();
        }

        function LogoutAction() {
            $this->logonPerson = new LogonPerson();

            return $this->redirectToUrl(Wow::get("project/adminPrefix"));
        }

    }