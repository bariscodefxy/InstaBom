<?php
    namespace App\Controllers;


    class SitemapController extends BaseController {
        function IndexAction() {
            $blogData = $this->db->query("SELECT seoLink FROM blog ORDER BY blogID DESC");

            return $this->partialView($blogData);
        }
    }