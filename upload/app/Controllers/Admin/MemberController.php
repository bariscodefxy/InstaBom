<?php

    namespace App\Controllers\Admin;

    use App\Models\Notification;
    use Wow\Net\Response;
    use Wow;

    class MemberController extends BaseController {

        function onActionExecuting() {
            $actionResponse = parent::onActionExecuting();
            if($actionResponse instanceof Response) {
                return $actionResponse;
            }
            if($this->logonPerson->isLoggedIn()) {
                return $this->redirectToUrl(Wow::get("project/adminPrefix"));
            }
        }

        function IndexAction() {
            //Geri dönüş için mevcut bir url varsa bunu not edelim.
            if(!is_null($this->request->query->returnUrl)) {
                $_SESSION["ReturnUrl"] = $this->request->query->returnUrl;
            }

            if($this->request->method == "POST") {

                if($this->request->data->antiForgeryToken !== $_SESSION["AntiForgeryToken"]) {
                    return $this->notFound();
                }


                if(!empty(Wow::get("ayar/GoogleCaptchaSiteKey")) && !empty(Wow::get("ayar/GoogleCaptchaSecretKey"))) {
                    $url             = 'https://www.google.com/recaptcha/api/siteverify';
                    $data            = array(
                        'secret'   => Wow::get("ayar/GoogleCaptchaSecretKey"),
                        'response' => $_POST["g-recaptcha-response"]
                    );
                    $options         = array(
                        'http' => array(
                            'method'  => 'POST',
                            'content' => http_build_query($data)
                        )
                    );

                    $context         = stream_context_create($options);
                    @$verify          = file_get_contents($url, FALSE, $context);
                    $captcha_success = json_decode($verify);
                    if($captcha_success->success == FALSE) {
                        $arrErrors[]               = "Güvenlik doğrulamasını geçmeniz gerekmektedir.";
                        $objNotification           = new Notification();
                        $objNotification->title    = "Giriş Başarısız!";
                        $objNotification->type     = $objNotification::PARAM_TYPE_DANGER;
                        $objNotification->messages = $arrErrors;
                        $this->notifications[]     = $objNotification;

                        return $this->redirectToUrl($this->request->referrer);
                    }
                }

                $username = trim($this->request->data->username);
                $password = trim($this->request->data->password);

                if(empty($username) || empty($password)) {
                    $arrErrors[] = "Kullanıcı adınızı ve şifrenizi eksiksiz girin.";
                }


                $memberData = $this->db->row("SELECT * FROM admin WHERE username=:username AND password=:password", array(
                    "username" => $username,
                    "password" => $password
                ));
                if(empty($memberData)) {
                    sleep(8);
                    $arrErrors[] = "Kullanıcı adınızı ve/veya şifrenizi hatalı girdiniz !";
                }

                if(!empty($arrErrors)) {
                    $objNotification           = new Notification();
                    $objNotification->title    = "Giriş Başarısız !";
                    $objNotification->type     = $objNotification::PARAM_TYPE_DANGER;
                    $objNotification->messages = $arrErrors;
                    $this->notifications[]     = $objNotification;

                    return $this->redirectToUrl($this->request->referrer);
                }

                $this->db->query("UPDATE admin SET ipAdresi=:ipAdresi,sonGirisTarihi=NOW() WHERE adminID=:adminID", array(
                    "adminID"  => $memberData["adminID"],
                    "ipAdresi" => $this->request->ip
                ));
                $memberData = $this->db->row("SELECT * FROM admin WHERE adminID=:adminID", array(
                    "adminID" => $memberData["adminID"]
                ));

                $this->logonPerson->setLoggedIn(TRUE);
                $this->logonPerson->setMemberData($memberData);
                session_regenerate_id(TRUE);


                $returnUrl = Wow::get("project/adminPrefix");
                if(isset($_SESSION["ReturnUrl"])) {
                    $returnUrl = $_SESSION["ReturnUrl"];
                    unset($_SESSION["ReturnUrl"]);
                }

                return $this->redirectToUrl($returnUrl);

            }

            $_SESSION["AntiForgeryToken"] = md5(uniqid(mt_rand(), TRUE));

            return $this->view();
        }

    }