<?php

    namespace App\Controllers\Admin;

    use Wow\Net\Response;

    class HomeController extends BaseController {

        function onActionExecuting() {
            if(($actionResponse = parent::onActionExecuting()) instanceof Response) {
                return $actionResponse;
            }
            //Üye girişi kontrolü.
            if(($pass = $this->middleware("logged")) instanceof Response) {
                return $pass;
            }
        }


        function IndexAction() {
            $data               = array();
            $data["genderUser"] = $this->db->query("SELECT gender,COUNT(uyeID) 'toplamSayi' FROM uye GROUP BY gender ORDER BY toplamSayi DESC");

            $data["aktiveUser"] = $this->db->query("SELECT isActive,COUNT(uyeID) 'toplamSayi' FROM uye GROUP BY isActive ORDER BY toplamSayi DESC");

            $data["aktiveUserAbility"]            = array();
            $data["aktiveUserAbility"]["follow"]  = $this->db->query("SELECT gender,COUNT(uyeID) 'toplamSayi' FROM uye WHERE isActive=1 AND isUsable=1 AND canFollow=1 GROUP BY gender ORDER BY toplamSayi DESC");
            $data["aktiveUserAbility"]["like"]    = $this->db->query("SELECT gender,COUNT(uyeID) 'toplamSayi' FROM uye WHERE isActive=1 AND isUsable=1 AND canLike=1 GROUP BY gender ORDER BY toplamSayi DESC");
            $data["aktiveUserAbility"]["comment"] = $this->db->query("SELECT gender,COUNT(uyeID) 'toplamSayi' FROM uye WHERE isActive=1 AND isUsable=1 AND canComment=1 GROUP BY gender ORDER BY toplamSayi DESC");
            $data["aktiveUserAbility"]["story"]   = $this->db->query("SELECT gender,COUNT(uyeID) 'toplamSayi' FROM uye WHERE isActive=1 AND isUsable=1 AND canStoryView=1 GROUP BY gender ORDER BY toplamSayi DESC");
			$data["aktiveUserAbility"]["live"]   = $this->db->query("SELECT gender,COUNT(uyeID) 'toplamSayi' FROM uye WHERE isActive=1 AND isUsable=1 AND canLive=1 GROUP BY gender ORDER BY toplamSayi DESC");

            $data["typeUser"] = $this->db->query("SELECT isWebCookie,COUNT(uyeID) 'toplamSayi' FROM uye GROUP BY isWebCookie ORDER BY toplamSayi DESC");

            return $this->view($data);
        }
    }