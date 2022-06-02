<?php

    namespace App\Controllers\Admin;

    class AjaxController extends BaseController {

        function KeepSessionAction() {
            return $this->json(["status" => "ok"]);
        }

    }