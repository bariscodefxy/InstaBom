<?php

    namespace App\Controllers\Admin;

    use App\Models\Notification;
    use Wow\Net\Response;
    use Wow;

    class BayilikController extends BaseController {

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

            $this->navigation->add("Bayiler", Wow::get("project/adminPrefix") . "/bayilik");


            if(!intval($page) > 0) {
                $page = 1;
            }

            $limitCount = 50;
            $limitStart = (($page * $limitCount) - $limitCount);

            $sqlWhere = "WHERE 1=1";
            $arrSorgu = array();

            $q = $this->request->query->q;
            if(!empty($q)) {
                $sqlWhere      .= " AND (username LIKE :q)";
                $arrSorgu["q"] = "%" . $q . "%";
            }

            $isActive = $this->request->query->isActive;
            if(!is_null($isActive) && $isActive !== "") {
                $sqlWhere             .= " AND isActive = :isActive";
                $arrSorgu["isActive"] = $isActive;
            }

            $bayiler = $this->db->query("SELECT * FROM bayi " . $sqlWhere . " LIMIT :limitStart,:limitCount", array_merge($arrSorgu, array(
                "limitStart" => $limitStart,
                "limitCount" => $limitCount
            )));

            $totalRows    = $this->db->single("SELECT COUNT(bayiID) FROM bayi " . $sqlWhere, $arrSorgu);
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

            return $this->view($bayiler);
        }


        function BayiDetayAction($id) {
            if(intval($id) == 0) {
                return $this->notFound();
            }
            $bayi = $this->db->row("SELECT * FROM bayi WHERE bayiID=:bayiID", array("bayiID" => intval($id)));
            if(empty($bayi)) {
                return $this->notFound();
            }

            if(isset($this->request->query->changeapi)) {
                $this->db->query("UPDATE bayi SET apiKey=:apikey WHERE bayiID=:bayiID", array(
                    "bayiID" => $bayi["bayiID"],
                    "apikey" => md5(microtime() . rand(0, 999999))
                ));

                return $this->redirectToUrl($this->request->referrer);
            }


            if($this->request->method == "POST") {

                switch($this->request->query->formType) {
                    case "saveBayiDetails":
                        $this->db->query("UPDATE bayi SET username=:username,password=:password,begeniMaxKredi=:begeniMaxKredi, takipMaxKredi=:takipMaxKredi, yorumMaxKredi=:yorumMaxKredi,yorumBegeniMaxKredi=:yorumBegeniMaxKredi,canliYayinMaxKredi=:canliYayinMaxKredi,videoMaxKredi=:videoMaxKredi,saveMaxKredi=:saveMaxKredi, storyMaxKredi=:storyMaxKredi, otoBegeniMaxKredi=:otoBegeniMaxKredi,gunlukBegeniLimit=:gunlukBegeniLimit,gunlukTakipLimit=:gunlukTakipLimit,gunlukYorumLimit=:gunlukYorumLimit,gunlukYorumBegeniLimit=:gunlukYorumBegeniLimit,gunlukCanliYayinLimit=:gunlukCanliYayinLimit,gunlukVideoLimit=:gunlukVideoLimit,gunlukSaveLimit=:gunlukSaveLimit,gunlukStoryLimit=:gunlukStoryLimit,toplamOtoBegeniLimit=:toplamOtoBegeniLimit,gunlukBegeniLimitLeft=:gunlukBegeniLimitLeft, gunlukTakipLimitLeft=:gunlukTakipLimitLeft, gunlukYorumLimitLeft=:gunlukYorumLimitLeft,gunlukYorumBegeniLimitLeft=:gunlukYorumBegeniLimitLeft,gunlukCanliYayinLimitLeft=:gunlukCanliYayinLimitLeft,gunlukVideoLimitLeft=:gunlukVideoLimitLeft,gunlukSaveLimitLeft=:gunlukSaveLimitLeft, gunlukStoryLimitLeft=:gunlukStoryLimitLeft,toplamOtoBegeniLimitLeft=:toplamOtoBegeniLimitLeft, sonaErmeTarihi=:sonaErmeTarihi,notlar=:notlar,isActive=:isActive, begeniGender=:begeniGender, takipGender=:takipGender, yorumGender=:yorumGender,yorumBegeniGender=:yorumBegeniGender,canliYayinGender=:canliYayinGender,otoBegeniGender=:otoBegeniGender, otoBegeniMaxGun=:otoBegeniMaxGun,smmActive=:smmactive,bakiye=:bakiye,komisyonOran=:komisyon,begeniPrice=:begeniprice,takipPrice=:takipprice,yorumPrice=:yorumprice,yorumBegeniPrice=:yorumbegeniprice,canliYayinPrice=:canliYayinPrice,videoPrice=:videoprice,savePrice=:saveprice,storyPrice=:storyprice WHERE bayiID=:bayiID", array(
                            "bayiID"                     => $bayi["bayiID"],
                            "username"                   => $this->request->data->username,
                            "password"                   => $this->request->data->password,
                            "begeniMaxKredi"             => $this->request->data->begeniMaxKredi,
                            "takipMaxKredi"              => $this->request->data->takipMaxKredi,
                            "yorumMaxKredi"              => $this->request->data->yorumMaxKredi,
                            "yorumBegeniMaxKredi"        => $this->request->data->yorumBegeniMaxKredi,
                            "canliYayinMaxKredi"         => $this->request->data->canliYayinMaxKredi,
                            "videoMaxKredi"              => $this->request->data->videoMaxKredi,
                            "saveMaxKredi"               => $this->request->data->saveMaxKredi,
                            "storyMaxKredi"              => $this->request->data->storyMaxKredi,
                            "otoBegeniMaxKredi"          => $this->request->data->otoBegeniMaxKredi,
                            "gunlukBegeniLimit"          => $this->request->data->gunlukBegeniLimit,
                            "gunlukTakipLimit"           => $this->request->data->gunlukTakipLimit,
                            "gunlukYorumLimit"           => $this->request->data->gunlukYorumLimit,
                            "gunlukYorumBegeniLimit"     => $this->request->data->gunlukYorumBegeniLimit,
                            "gunlukCanliYayinLimit"      => $this->request->data->gunlukCanliYayinLimit,
                            "gunlukVideoLimit"           => $this->request->data->gunlukVideoLimit,
                            "gunlukSaveLimit"            => $this->request->data->gunlukSaveLimit,
                            "gunlukStoryLimit"           => $this->request->data->gunlukStoryLimit,
                            "toplamOtoBegeniLimit"       => $this->request->data->toplamOtoBegeniLimit,
                            "gunlukBegeniLimitLeft"      => $this->request->data->gunlukBegeniLimitLeft,
                            "gunlukTakipLimitLeft"       => $this->request->data->gunlukTakipLimitLeft,
                            "gunlukYorumLimitLeft"       => $this->request->data->gunlukYorumLimitLeft,
                            "gunlukYorumBegeniLimitLeft" => $this->request->data->gunlukYorumBegeniLimitLeft,
                            "gunlukCanliYayinLimitLeft"  => $this->request->data->gunlukCanliYayinLimitLeft,
                            "gunlukVideoLimitLeft"       => $this->request->data->gunlukVideoLimitLeft,
                            "gunlukSaveLimitLeft"        => $this->request->data->gunlukSaveLimitLeft,
                            "gunlukStoryLimitLeft"       => $this->request->data->gunlukStoryLimitLeft,
                            "toplamOtoBegeniLimitLeft"   => $this->request->data->toplamOtoBegeniLimitLeft,
                            "sonaErmeTarihi"             => date("Y-m-d H:i:s", strtotime($this->request->data->sonaErmeTarihi)),
                            "notlar"                     => $this->request->data->notlar,
                            "isActive"                   => intval($this->request->data->isActive) == 1 ? 1 : 0,
                            "begeniGender"               => intval($this->request->data->begeniGender) == 1 ? 1 : 0,
                            "takipGender"                => intval($this->request->data->takipGender) == 1 ? 1 : 0,
                            "yorumGender"                => intval($this->request->data->yorumGender) == 1 ? 1 : 0,
                            "yorumBegeniGender"          => intval($this->request->data->yorumBegeniGender) == 1 ? 1 : 0,
                            "canliYayinGender"           => intval($this->request->data->canliYayinGender) == 1 ? 1 : 0,
                            "otoBegeniGender"            => intval($this->request->data->otoBegeniGender) == 1 ? 1 : 0,
                            "otoBegeniMaxGun"            => $this->request->data->otoBegeniMaxGun,
                            "smmactive"                  => $this->request->data->smmactive == 1 ? "aktif" : "pasif",
                            "bakiye"                     => floatval(str_replace(",", ".", $this->request->data->smmbakiye)),
                            "komisyon"                   => intval($this->request->data->smmkomisyon),
                            "begeniprice"                => $this->request->data->begeniPrice ? floatval(str_replace(",", ".", $this->request->data->begeniPrice)) : 0,
                            "takipprice"                 => $this->request->data->takipPrice ? floatval(str_replace(",", ".", $this->request->data->takipPrice)) : 0,
                            "yorumprice"                 => $this->request->data->yorumPrice ? floatval(str_replace(",", ".", $this->request->data->yorumPrice)) : 0,
                            "yorumbegeniprice"           => $this->request->data->yorumBegeniPrice ? floatval(str_replace(",", ".", $this->request->data->yorumBegeniPrice)) : 0,
                            "canliYayinPrice"            => $this->request->data->canliYayinPrice ? floatval(str_replace(",", ".", $this->request->data->canliYayinPrice)) : 0,
                            "videoprice"                 => $this->request->data->videoPrice ? floatval(str_replace(",", ".", $this->request->data->videoPrice)) : 0,
                            "saveprice"                  => $this->request->data->savePrice ? floatval(str_replace(",", ".", $this->request->data->savePrice)) : 0,
                            "storyprice"                 => $this->request->data->storyPrice ? floatval(str_replace(",", ".", $this->request->data->storyPrice)) : 0
                        ));

                        $objNotification             = new Notification();
                        $objNotification->title      = "Değişiklikler Kaydedildi.";
                        $objNotification->messages[] = $bayi["username"] . " bayisinde yaptığınız değişiklikler kaydedildi.";
                        $objNotification->type       = $objNotification::PARAM_TYPE_SUCCESS;
                        $this->notifications[]       = $objNotification;

                        return $this->redirectToUrl($this->request->referrer);

                        break;
                    default:
                        return $this->notFound();
                }
            }

            return $this->view($bayi);
        }

        function BayiSilAction($id) {
            if(intval($id) == 0) {
                return $this->notFound();
            }
            $bayi = $this->db->row("SELECT * FROM bayi WHERE bayiID=:bayiID", array("bayiID" => intval($id)));
            if(empty($bayi)) {
                return $this->notFound();
            } else {
                $this->db->query("DELETE FROM bayi WHERE bayiID=:bayiID", array("bayiID" => intval($id)));
                $this->db->query("DELETE FROM bayi_islem WHERE bayiID NOT IN(SELECT bayiID FROM bayi)");
                $this->db->query("DELETE FROM uye_otobegenipaket_gonderi WHERE bayiIslemID IN (SELECT bayiIslemID FROM bayi_islem WHERE bayiID NOT IN(SELECT bayiID FROM bayi))");

            }

            return $this->redirectToUrl(Wow::get("project/adminPrefix") . "/bayilik");
        }

        function BayiEkleAction() {

            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "saveBayiEkle":
                        $this->db->query("INSERT INTO bayi SET username=:username,password=:password,begeniMaxKredi=:begeniMaxKredi, takipMaxKredi=:takipMaxKredi, yorumMaxKredi=:yorumMaxKredi,yorumBegeniMaxKredi=:yorumBegeniMaxKredi,canliYayinMaxKredi=:canliYayinMaxKredi,videoMaxKredi=:videoMaxKredi,saveMaxKredi=:saveMaxKredi,storyMaxKredi=:storyMaxKredi, otoBegeniMaxKredi=:otoBegeniMaxKredi,gunlukBegeniLimit=:gunlukBegeniLimit,gunlukTakipLimit=:gunlukTakipLimit,gunlukYorumLimit=:gunlukYorumLimit,gunlukYorumBegeniLimit=:gunlukYorumBegeniLimit,gunlukCanliYayinLimit=:gunlukCanliYayinLimit,gunlukVideoLimit=:gunlukVideoLimit,gunlukSaveLimit=:gunlukSaveLimit,gunlukStoryLimit=:gunlukStoryLimit,toplamOtoBegeniLimit=:toplamOtoBegeniLimit,gunlukBegeniLimitLeft=:gunlukBegeniLimitLeft, gunlukTakipLimitLeft=:gunlukTakipLimitLeft, gunlukYorumLimitLeft=:gunlukYorumLimitLeft,gunlukYorumBegeniLimitLeft=:gunlukYorumBegeniLimitLeft,gunlukCanliYayinLimitLeft=:gunlukCanliYayinLimitLeft,gunlukVideoLimitLeft=:gunlukVideoLimitLeft,gunlukSaveLimitLeft=:gunlukSaveLimitLeft,gunlukStoryLimitLeft=:gunlukStoryLimitLeft,toplamOtoBegeniLimitLeft=:toplamOtoBegeniLimitLeft, notlar=:notlar,sonaErmeTarihi=:sonaErmeTarihi,isActive=:isActive,begeniGender=:begeniGender,takipGender=:takipGender,yorumGender=:yorumGender,yorumBegeniGender=:yorumBegeniGender,canliYayinGender=:canliYayinGender,otoBegeniGender=:otoBegeniGender, otoBegeniMaxGun=:otoBegeniMaxGun,smmActive=:smmactive,bakiye=:bakiye,komisyonOran=:komisyon,apiKey=:apikey,begeniPrice=:begeniprice,takipPrice=:takipprice,yorumPrice=:yorumprice,yorumBegeniPrice=:yorumbegeniprice,canliYayinPrice=:canliYayinPrice,videoPrice=:videoprice,savePrice=:saveprice,storyPrice=:storyprice", array(
                            "username"                   => $this->request->data->username,
                            "password"                   => $this->request->data->password,
                            "begeniMaxKredi"             => $this->request->data->begeniMaxKredi,
                            "takipMaxKredi"              => $this->request->data->takipMaxKredi,
                            "yorumMaxKredi"              => $this->request->data->yorumMaxKredi,
                            "yorumBegeniMaxKredi"        => $this->request->data->yorumBegeniMaxKredi,
                            "canliYayinMaxKredi"         => $this->request->data->canliYayinMaxKredi,
                            "videoMaxKredi"              => $this->request->data->videoMaxKredi,
                            "saveMaxKredi"               => $this->request->data->saveMaxKredi,
                            "storyMaxKredi"              => $this->request->data->storyMaxKredi,
                            "otoBegeniMaxKredi"          => $this->request->data->otoBegeniMaxKredi,
                            "gunlukBegeniLimit"          => $this->request->data->gunlukBegeniLimit,
                            "gunlukTakipLimit"           => $this->request->data->gunlukTakipLimit,
                            "gunlukYorumLimit"           => $this->request->data->gunlukYorumLimit,
                            "gunlukYorumBegeniLimit"     => $this->request->data->gunlukYorumBegeniLimit,
                            "gunlukCanliYayinLimit"      => $this->request->data->gunlukCanliYayinLimit,
                            "gunlukVideoLimit"           => $this->request->data->gunlukVideoLimit,
                            "gunlukSaveLimit"            => $this->request->data->gunlukSaveLimit,
                            "gunlukStoryLimit"           => $this->request->data->gunlukStoryLimit,
                            "toplamOtoBegeniLimit"       => $this->request->data->toplamOtoBegeniLimit,
                            "gunlukBegeniLimitLeft"      => $this->request->data->gunlukBegeniLimitLeft,
                            "gunlukTakipLimitLeft"       => $this->request->data->gunlukTakipLimitLeft,
                            "gunlukYorumLimitLeft"       => $this->request->data->gunlukYorumLimitLeft,
                            "gunlukYorumBegeniLimitLeft" => $this->request->data->gunlukYorumBegeniLimitLeft,
                            "gunlukCanliYayinLimitLeft"  => $this->request->data->gunlukCanliYayinLimitLeft,
                            "gunlukVideoLimitLeft"       => $this->request->data->gunlukVideoLimitLeft,
                            "gunlukSaveLimitLeft"        => $this->request->data->gunlukSaveLimitLeft,
                            "gunlukStoryLimitLeft"       => $this->request->data->gunlukStoryLimitLeft,
                            "toplamOtoBegeniLimitLeft"   => $this->request->data->toplamOtoBegeniLimitLeft,
                            "sonaErmeTarihi"             => date("Y-m-d H:i:s", strtotime($this->request->data->sonaErmeTarihi)),
                            "notlar"                     => $this->request->data->notlar,
                            "isActive"                   => intval($this->request->data->isActive) == 1 ? 1 : 0,
                            "begeniGender"               => intval($this->request->data->begeniGender) == 1 ? 1 : 0,
                            "takipGender"                => intval($this->request->data->takipGender) == 1 ? 1 : 0,
                            "yorumGender"                => intval($this->request->data->yorumGender) == 1 ? 1 : 0,
                            "yorumBegeniGender"          => intval($this->request->data->yorumBegeniGender) == 1 ? 1 : 0,
                            "canliYayinGender"           => intval($this->request->data->canliYayinGender) == 1 ? 1 : 0,
                            "otoBegeniGender"            => intval($this->request->data->otoBegeniGender) == 1 ? 1 : 0,
                            "otoBegeniMaxGun"            => $this->request->data->otoBegeniMaxGun,
                            "smmactive"                  => $this->request->data->smmactive == 1 ? "aktif" : "pasif",
                            "bakiye"                     => floatval(str_replace(",", ".", $this->request->data->smmbakiye)),
                            "komisyon"                   => intval($this->request->data->smmkomisyon),
                            "apikey"                     => md5(microtime() . rand(0, 999999)),
                            "begeniprice"                => $this->request->data->begeniPrice ? floatval(str_replace(",", ".", $this->request->data->begeniPrice)) : 0,
                            "takipprice"                 => $this->request->data->takipPrice ? floatval(str_replace(",", ".", $this->request->data->takipPrice)) : 0,
                            "yorumprice"                 => $this->request->data->yorumPrice ? floatval(str_replace(",", ".", $this->request->data->yorumPrice)) : 0,
                            "yorumbegeniprice"           => $this->request->data->yorumBegeniPrice ? floatval(str_replace(",", ".", $this->request->data->yorumBegeniPrice)) : 0,
                            "canliYayinPrice"            => $this->request->data->canliYayinPrice ? floatval(str_replace(",", ".", $this->request->data->canliYayinPrice)) : 0,
                            "videoprice"                 => $this->request->data->videoPrice ? floatval(str_replace(",", ".", $this->request->data->videoPrice)) : 0,
                            "saveprice"                  => $this->request->data->savePrice ? floatval(str_replace(",", ".", $this->request->data->savePrice)) : 0,
                            "storyprice"                 => $this->request->data->storyPrice ? floatval(str_replace(",", ".", $this->request->data->storyPrice)) : 0
                        ));

                        $objNotification             = new Notification();
                        $objNotification->title      = "Yeni Bayi Eklendi.";
                        $objNotification->messages[] = $this->request->data->username . " bayisi başarılı bir şekilde eklendi.";
                        $objNotification->type       = $objNotification::PARAM_TYPE_SUCCESS;
                        $this->notifications[]       = $objNotification;

                        return $this->redirectToUrl(Wow::get("project/adminPrefix") . "/bayilik/bayi-detay/" . $this->db->lastInsertId());

                        break;
                    default:
                        return $this->notFound();
                }
            }

            return $this->view();
        }

    }