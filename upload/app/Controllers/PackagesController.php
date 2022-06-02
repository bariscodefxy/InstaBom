<?php
    namespace App\Controllers;


    class PackagesController extends BaseController {

        function IndexAction() {

            $d             = $this->helper->getPageDetail(2);
            $d["pageInfo"] = unserialize($d["pageInfo"]);

            $this->view->set('title', $d["pageInfo"]["title"]);
            $this->view->set('description', $d["pageInfo"]["description"]);
            $this->view->set('keywords', $d["pageInfo"]["keywords"]);

            $this->navigation->add("Paketler", "/packages");


            return $this->view($d);
        }


    }