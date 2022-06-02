<?php

    namespace App\Controllers\Bayi;



    use App\Models\LogonPerson;

    use Wow\Net\Response;

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



        function LogoutAction() {

            $this->logonPerson = new LogonPerson();



            return $this->redirectToUrl(Wow::get("project/resellerPrefix"));

        }



    }