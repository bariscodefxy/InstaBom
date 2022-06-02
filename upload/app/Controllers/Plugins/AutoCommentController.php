<?php

    namespace App\Controllers\Plugins;

    use Wow\Net\Response;
    use Wow;
    use App\Controllers\BaseController;

    /**
     * AutoComment Plugini
     * Medyalara otomatik yorum gönderimi sağlar.
     *
     * Kullanım için admin panelindeki cron url leri arasına,
     * /plugins/auto-comment/cron?scKey={SECURITY_KEY} eklenmelidir!!
     *
     * @package App\Controllers\Plugins
     */
    class AutoCommentController extends BaseController {

        /**
         * Override onStart
         */
        function onActionExecuting() {
            if(($pass = parent::onActionExecuting()) instanceof Response) {
                return $pass;
            }

            session_write_close();

            if($this->request->query->scKey != Wow::get("ayar/securityKey")) {
                return $this->notFound();
            }
        }

        function CronAction(){

        }

        function DoAction(){

        }

    }