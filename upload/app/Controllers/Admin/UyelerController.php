<?php

    namespace App\Controllers\Admin;

    use App\Models\Notification;
    use Wow;
    use Wow\Net\Response;

    class UyelerController extends BaseController {

        function onActionExecuting() {
            if(($actionResponse = parent::onActionExecuting()) instanceof Response) {
                return $actionResponse;
            }
            //Üye girişi kontrolü.
            if(($pass = $this->middleware("logged")) instanceof Response) {
                return $pass;
            }
        }

        function IndexAction($page = 1) {
            $this->navigation->add("Üyeler", Wow::get("project/adminPrefix") . "/uyeler");


            if(!intval($page) > 0) {
                $page = 1;
            }

            $limitCount = 50;
            $limitStart = (($page * $limitCount) - $limitCount);

            $sqlWhere = "WHERE 1=1";
            $arrSorgu = array();

            $q = trim($this->request->query->q);
            if(!empty($q)) {
                $sqlWhere       .= " AND (kullaniciAdi LIKE :q OR fullName LIKE :q2)";
                $arrSorgu["q"]  = "%" . $q . "%";
                $arrSorgu["q2"] = "%" . $q . "%";
            }

            $isWebCookie = $this->request->query->isWebCookie;
            if(!is_null($isWebCookie) && $isWebCookie !== "") {
                $sqlWhere                .= " AND isWebCookie = :isWebCookie";
                $arrSorgu["isWebCookie"] = $isWebCookie;
            }

            $isActive = $this->request->query->isActive;
            if(!is_null($isActive) && $isActive !== "") {
                $sqlWhere             .= " AND isActive = :isActive";
                $arrSorgu["isActive"] = $isActive;
            }

            $uyeler = $this->db->query("SELECT * FROM uye " . $sqlWhere . " ORDER BY uyeID DESC LIMIT :limitStart,:limitCount", array_merge($arrSorgu, array(
                "limitStart" => $limitStart,
                "limitCount" => $limitCount
            )));


            $totalRows    = $this->db->single("SELECT COUNT(uyeID) FROM uye " . $sqlWhere, $arrSorgu);
            $previousPage = $page > 1 ? $page - 1 : NULL;
            $nextPage     = $totalRows > $limitStart + $limitCount ? $page + 1 : NULL;
            $totalPage    = ceil($totalRows / $limitCount);
            $endIndex     = ($limitStart + $limitCount) <= $totalRows ? ($limitStart + $limitCount) : $totalRows;
            $pagination   = array(
                "recordCount"  => $totalRows,
                "pageSize"     => $limitCount,
                "pageCount"    => $totalPage,
                "activePage"   => $page,
                "previousPage" => $previousPage,
                "nextPage"     => $nextPage,
                "startIndex"   => $limitStart + 1,
                "endIndex"     => $endIndex
            );
            $this->view->set("pagination", $pagination);

            return $this->view($uyeler);
        }


        function UyeDetayAction($id) {
            if(intval($id) == 0) {
                return $this->notFound();
            }
            $user = $this->db->row("SELECT * FROM uye WHERE uyeID=:uyeID", array("uyeID" => intval($id)));
            if(empty($user)) {
                return $this->notFound();
            }

            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "saveUserDetails":
                        $this->db->query("UPDATE uye SET begeniKredi=:begeniKredi,takipKredi=:takipKredi,yorumKredi=:yorumKredi,storyKredi=:storyKredi,videoKredi=:videoKredi,saveKredi=:saveKredi,yorumBegeniKredi=:yorumBegeniKredi,canliYayinKredi=:canliYayinKredi,isUsable=:isUsable, isBayi=:isBayi WHERE uyeID=:uyeID", array(
                            "uyeID"            => $user["uyeID"],
                            "begeniKredi"      => intval($this->request->data->begeniKredi),
                            "takipKredi"       => intval($this->request->data->takipKredi),
                            "yorumKredi"       => intval($this->request->data->yorumKredi),
                            "storyKredi"       => intval($this->request->data->storyKredi),
                            "videoKredi"       => intval($this->request->data->videoKredi),
                            "saveKredi"        => intval($this->request->data->saveKredi),
                            "yorumBegeniKredi" => intval($this->request->data->yorumBegeniKredi),
                            "canliYayinKredi"  => intval($this->request->data->canliYayinKredi),
                            "isUsable"         => $this->request->data->isUsable == 1 ? 1 : 0,
                            "isBayi"           => $this->request->data->isBayi == 1 ? 1 : 0
                        ));

                        $objNotification             = new Notification();
                        $objNotification->title      = "Değişiklikler Kaydedildi.";
                        $objNotification->messages[] = $user["kullaniciAdi"] . " kullanıcısında yaptığınız değişiklikler kaydedildi.";
                        $objNotification->type       = $objNotification::PARAM_TYPE_SUCCESS;
                        $this->notifications[]       = $objNotification;

                        return $this->redirectToUrl($this->request->referrer);

                        break;
                    default:
                        return $this->notFound();
                }
            }

            $userPaket = $this->db->query("SELECT * FROM uye_otobegenipaket WHERE instaID=:instaID ORDER BY id DESC", array("instaID" => $user["instaID"]));

            $data = array(
                "uye"   => $user,
                "paket" => $userPaket
            );

            return $this->view($data);
        }

        function UyeSilAction($id) {
            $uye = $this->db->row("SELECT * FROM uye WHERE uyeID=:uyeID", ["uyeID" => $id]);
            if(empty($uye)) {
                return $this->notFound();
            }
            $cookiePath = Wow::get("project/cookiePath") . "instagramv3/" . substr($uye["instaID"], -1) . "/" . $uye["instaID"] . ".iwb";
            if(file_exists($cookiePath)) {
                unlink($cookiePath);
            }
            $this->db->query("DELETE FROM uye WHERE uyeID = :uyeID", ["uyeID" => $uye["uyeID"]]);
            $objNotification             = new Notification();
            $objNotification->title      = "Üye Silindi.";
            $objNotification->messages[] = $uye["kullaniciAdi"] . " nickli üye silindi.";
            $objNotification->type       = $objNotification::PARAM_TYPE_DANGER;
            $this->notifications[]       = $objNotification;

            return $this->redirectToUrl(Wow::get("project/adminPrefix") . "/uyeler");
        }
    }