<?php
    namespace App\Controllers;

    use Wow;

    class BlogController extends BaseController {

        function IndexAction($page = 1) {
            if(!intval($page) > 0) {
                $page = 1;
            }

            $d             = $this->helper->getPageDetail(3);
            $d["pageInfo"] = unserialize($d["pageInfo"]);

            $this->view->set('title', $d["pageInfo"]["title"]);
            $this->view->set('description', $d["pageInfo"]["description"]);
            $this->view->set('keywords', $d["pageInfo"]["keywords"]);

            $limitCount = Wow::get("ayar/birSayfadaGosterilecekBlogSayisi");
            $limitStart = (($page * $limitCount) - $limitCount);

            $blogSorgu = $this->db->query("SELECT * FROM blog WHERE isActive=1 ORDER BY registerDate DESC LIMIT :limitStart,:limitCount", array(
                "limitStart" => $limitStart,
                "limitCount" => $limitCount
            ));
            if(empty($blogSorgu)) {
                //return $this->redirectToUrl("/blog");
            }
            $d["blogList"] = $blogSorgu;
            $blogCount     = $this->db->single('SELECT COUNT(*) FROM blog WHERE isActive=1;');

            $previousPage = $page > 1 ? $page - 1 : NULL;
            $this->view->set("previousPage", $previousPage);
            $nextPage = $blogCount > $limitStart + $limitCount ? $page + 1 : NULL;
            $this->view->set("nextPage", $nextPage);

            $this->navigation->add("Blog", "/blog");


            return $this->view($d);
        }

        /**
         * @param string $seolink
         *
         * @return \Wow\Net\Response
         */
        function BlogDetailAction($seolink) {
            $s = $this->db->row("SELECT * FROM blog WHERE seoLink = :seolink", array("seolink" => $seolink));

            if(empty($s)) {
                return $this->redirectToUrl("/blog");
            }

            $otherBlogs      = $this->db->query("SELECT * FROM blog WHERE seoLink <> :seolink ORDER BY RAND() LIMIT :limitCount", array(
                "seolink"    => $seolink,
                "limitCount" => Wow::get("ayar/blogDetaydaGosterilecekBenzerBloglarSayisi")
            ));
            $s["otherBlogs"] = $otherBlogs;

            $this->view->set('title', $s["baslik"]);
            $this->view->set('description', $this->helper->blogExcerpt($s["icerik"]));
            $this->view->set('keywords', $this->helper->makeKeyword($s["baslik"]));

            $this->navigation->add("Blog", "/blog");
            $this->navigation->add($s["baslik"], "/blog/" . $s["seoLink"]);


            return $this->view($s);
        }

    }