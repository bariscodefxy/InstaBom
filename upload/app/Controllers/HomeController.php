<?php

    namespace App\Controllers;

    use ApiService;
    use App\Libraries\InstagramReaction;
    use Constants;
    use RollingCurl\RollingCurl;
    use RollingCurl\Request as RollingCurlRequest;
    use Signatures;
    use Utils;
    use Wow;
    use Instagram;


    class HomeController extends BaseController {

        function IndexAction() {
            $d             = $this->helper->getPageDetail(1);
            $d["pageInfo"] = unserialize($d["pageInfo"]);
            $this->view->set('title', $d["pageInfo"]["title"]);
            $this->view->set('description', $d["pageInfo"]["description"]);
            $this->view->set('keywords', $d["pageInfo"]["keywords"]);

            return $this->view($d);
        }

    }