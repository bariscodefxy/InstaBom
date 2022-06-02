<?php



    namespace App\Controllers\Bayi;



    use App\Models\LogonPerson;

    use SmmApi;

    use Wow\Net\Response;

    use Wow;



    class ApiController extends BaseController {



        private $key  = NULL;

        private $bayi = NULL;



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



            $this->key = isset($this->request->query->key) ? $this->request->query->key : "";



            $this->bayi = $this->db->row("SELECT * FROM bayi WHERE apiKey=:apikey", array("apikey" => $this->key));



            if(empty($this->bayi)) {

                return $this->json(array(

                                       "status" => 0,

                                       "error"  => "Api Hatalıdır. Lütfen kontrol ederek tekrar deneyiniz."

                                   ));

            }



        }



        function V2Action() {



            $data = array();



            $action = isset($this->request->query->action) ? $this->request->query->action : "";



            if($action == "services") {



                return $this->json(SmmApi::postDataSmm("service-list"));





            } else if($action == "add") {



            } else if($action == "status") {



            } else if($action == "balance") {



            } else {

                $data = array(

                    "status" => 0,

                    "errpr"  => "Geçersiz talep"

                );

            }





            return $this->json($data);

        }



    }