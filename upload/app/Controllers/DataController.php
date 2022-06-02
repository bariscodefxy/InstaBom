<?php

    namespace App\Controllers;

    use Wow;

    class DataController extends BaseController {

        public function LoginAction() {

            $username  = $this->request->data->username ? $this->request->data->username : "";
            $password  = $this->request->data->password ? $this->request->data->password : "";
            $deviceID  = $this->request->data->deviceID ? $this->request->data->deviceID : "";
            $phoneID   = $this->request->data->phoneID ? $this->request->data->phoneID : "";
            $csrfToken = $this->request->data->csrfToken ? $this->request->data->csrfToken : "";

            $instagram = new \MobilInstagram();
            $data      = $instagram->MobileLogin($username, $password, $deviceID, $phoneID, $csrfToken);

            return $this->json($data);

        }

    }