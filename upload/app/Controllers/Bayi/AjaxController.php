<?php



    namespace App\Controllers\Bayi;



    use App\Models\LogonPerson;



    class AjaxController extends BaseController {



        function KeepSessionAction() {

            if($this->logonPerson->isLoggedIn()) {

                /**

                 * @var LogonPerson $adminLogonPerson

                 */

                $adminLogonPerson = (isset($_SESSION["AdminLogonPerson"]) && $_SESSION["AdminLogonPerson"] instanceof LogonPerson) ? (object)$_SESSION["AdminLogonPerson"] : new LogonPerson();

                if(!$adminLogonPerson->isLoggedIn()) {

                    $this->db->query("UPDATE bayi SET lastActivity=NOW(),lastSession=:lastSession,ipAdresi=:ipAdresi WHERE bayiID =:bayiID", array(

                        "bayiID"      => $this->logonPerson->member->bayiID,

                        "lastSession" => session_id(),

                        "ipAdresi"    => $this->request->ip

                    ));

                }

            }



            return $this->json(["status" => "ok"]);

        }



    }