<?php

    namespace App\Controllers;

    use App\Libraries\InstagramReaction;
    use RollingCurl\Request as RollingCurlRequest;
    use RollingCurl\RollingCurl;
    use Wow;
    use Wow\Core\Controller;
    use Wow\Database\Database;
    use InstagramWeb;
    use Instagram;

    class CronJobController extends Controller {

        /**
         * @var Instagram $objInstagram
         */
        public $objInstagram;
        /**
         * @var InstagramWeb $objInstagramWeb
         */
        public $objInstagramWeb;

        function IndexAction() {

            $db = Database::getInstance();

            $crons = $db->query("SELECT * FROM cron WHERE isActive=1 AND (calismaSikligi<= 60 OR TIMESTAMPDIFF(SECOND,sonCalismaTarihi,NOW()) >= calismaSikligi)");

            if(intval(date("i")) % 5 == 0) {
                $this->rrmdir(Wow::get("project/cookiePath") . "/anti-flood");
            }

            if(!empty($crons)) {
                $rollingCurl = new RollingCurl();
                $ua          = md5("");
                $ck          = md5("" . "_" . preg_replace('/(?:www\.)?(.*)\/?$/i', '$1', $_SERVER["HTTP_HOST"]) . "_" . $ua);
                $cv          = substr(md5(Wow::get("") . date("H")), 0, 26);
                foreach($crons AS $cron) {
                    $rollingCurl->get($cron["url"], ['Accept-Language: iw-IW'], [
                        CURLOPT_USERAGENT => md5(""),
                        CURLOPT_COOKIE    => $ck . "=" . $cv,
                        CURLOPT_ENCODING  => ''
                    ]);
                    $db->query("UPDATE cron SET sonCalismaTarihi=NOW() WHERE cronID = :cronID", array("cronID" => $cron["cronID"]));
                }
                $rollingCurl->setCallback(function(RollingCurlRequest $request, RollingCurl $rollingCurl) {
                    $rollingCurl->clearCompleted();
                    $rollingCurl->prunePendingRequestQueue();
                });
                $rollingCurl->setSimultaneousLimit(50);
                $rollingCurl->execute();
            }

            return $this->json(array("status" => "success"));
        }

        private function rrmdir($dir) {
            if(is_dir($dir)) {
                $objects = scandir($dir);
                foreach($objects as $object) {
                    if($object != "." && $object != "..") {
                        if(is_dir($dir . "/" . $object)) {
                            $this->rrmdir($dir . "/" . $object);
                        } else {
                            unlink($dir . "/" . $object);
                        }
                    }
                }
                rmdir($dir);
            }
        }

    }