<?php
    namespace App\Controllers;

    use App\Libraries\InstagramReaction;
    use App\Models\Notification;
    use Exception;
    use Wow;
    use Wow\Net\Response;
    use Instagram;


    class AdminController extends BaseController {

        /**
         * @var InstagramReaction $instagramReaction
         */
        private $instagramReaction;

        /**
         * Override onStart
         */
        function onActionExecuting() {

            if(($pass = parent::onActionExecuting()) instanceof Response) {
                return $pass;
            }

            //Admin girişi kontrolü.
            if(($pass = $this->middleware("admin")) instanceof Response) {
                return $pass;
            }

            $this->instagramReaction = new InstagramReaction($this->logonPerson->member->uyeID);

            //Navigation
            $this->navigation->add("Admin Paneli", "/admin");
        }

        function IndexAction() {
            $data               = array();
            $data["genderUser"] = $this->db->query("SELECT gender,COUNT(uyeID) 'toplamSayi' FROM uye GROUP BY gender ORDER BY toplamSayi DESC");

            $data["aktiveUser"] = $this->db->query("SELECT isActive,COUNT(uyeID) 'toplamSayi' FROM uye GROUP BY isActive ORDER BY toplamSayi DESC");

            $data["aktiveUserAbility"] = array();
            $data["aktiveUserAbility"]["follow"] = $this->db->query("SELECT gender,COUNT(uyeID) 'toplamSayi' FROM uye WHERE isActive=1 AND isAdmin=0 AND isUsable=1 AND canFollow=1 GROUP BY gender ORDER BY toplamSayi DESC");
            $data["aktiveUserAbility"]["like"] = $this->db->query("SELECT gender,COUNT(uyeID) 'toplamSayi' FROM uye WHERE isActive=1 AND isAdmin=0 AND isUsable=1 AND canLike=1 GROUP BY gender ORDER BY toplamSayi DESC");
            $data["aktiveUserAbility"]["comment"] = $this->db->query("SELECT gender,COUNT(uyeID) 'toplamSayi' FROM uye WHERE isActive=1 AND isAdmin=0 AND isUsable=1 AND canComment=1 GROUP BY gender ORDER BY toplamSayi DESC");


            $data["typeUser"] = $this->db->query("SELECT isWebCookie,COUNT(uyeID) 'toplamSayi' FROM uye GROUP BY isWebCookie ORDER BY toplamSayi DESC");

            return $this->view($data);
        }

        function SettingsAction() {
            if($this->request->method == "POST") {

                $data = $this->request->data->ayar;
                if(is_array($data) && !empty($data)) {
                    $arrAyar = array();
                    foreach($data as $key => $value) {
                        $arrAyar[$key] = $value;
                    }
                }
                file_put_contents("./app/Config/system-settings.php", json_encode($arrAyar));

                $objNotification             = new Notification();
                $objNotification->type       = $objNotification::PARAM_TYPE_SUCCESS;
                $objNotification->title      = "Değişiklikler Kaydedildi.";
                $objNotification->messages[] = "Ayarlarda yaptığınız değişiklikler kaydedildi.";
                $this->notifications[]       = $objNotification;

                return $this->redirectToUrl($this->request->referrer);

            }

            $data["crons"] = $this->db->query("SELECT * FROM cron ORDER BY cronID ASC");

            return $this->view($data);
        }

        function OneCronAction($id) {

            $data = array();

            if(!empty($id)) {

                $data = $this->db->row("SELECT * FROM cron WHERE cronID = :cronID", array("cronID" => $id));

            }

            return $this->json($data);


        }


        function SaveUpdateCronAction() {

            $cronID     = $this->request->data->cronID ? $this->request->data->cronID : "";
            $cronBaslik = $this->request->data->cronBaslik ? $this->request->data->cronBaslik : "";
            $cronUrl    = $this->request->data->cronUrl ? $this->request->data->cronUrl : "";
            $cronSaniye = $this->request->data->cronSaniye ? $this->request->data->cronSaniye : "";
            $cronDurum  = in_array($this->request->data->cronDurum, array(
                "0",
                "1"
            )) ? $this->request->data->cronDurum : 0;

            if(empty($cronID)) {

                $this->db->query("INSERT INTO cron (baslik,url,calismaSikligi,isActive) VALUES (:baslik,:url,:calisma,:isactive)", array(
                    "baslik"   => $cronBaslik,
                    "url"      => $cronUrl,
                    "calisma"  => $cronSaniye,
                    "isactive" => $cronDurum
                ));

            } else {

                $this->db->query("UPDATE cron SET baslik=:baslik,url=:url,calismaSikligi=:calisma,isActive=:isactive WHERE cronID = :cronID", array(
                    "baslik"   => $cronBaslik,
                    "url"      => $cronUrl,
                    "calisma"  => $cronSaniye,
                    "isactive" => $cronDurum,
                    "cronID"   => $cronID
                ));

            }

            return $this->redirectToUrl($this->request->referrer);

        }

        function IslemlerAction() {
            $this->navigation->add("İşlemler", "/admin/islemler");
            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "removePassiveUsers":
                        $passiveUsers = $this->db->column("SELECT kullaniciAdi FROM uye WHERE isActive=0");
                        foreach($passiveUsers as $username) {
                            $filePath  = Wow::get("project/cookiePath") . "instagram/" . $username . ".dat";
                            $filePath2 = Wow::get("project/cookiePath") . "instagram/" . $username . ".cnf";
                            if(file_exists($filePath)) {
                                unlink($filePath);
                            }
                            if(file_exists($filePath2)) {
                                unlink($filePath2);
                            }
                        }
                        $this->db->query("DELETE FROM uye WHERE isActive=0");

                        $allCookies = glob(Wow::get("project/cookiePath") . "instagram/*.dat", GLOB_BRACE);
                        $allUsers = $this->db->column("SELECT kullaniciAdi FROM uye");
                        foreach($allCookies as $cookieFile){
                            $arrCookieFileName = explode("/", $cookieFile);
                            $cookieFileName    = $arrCookieFileName[count($arrCookieFileName) - 1];
                            $username          = substr($cookieFileName, 0, strlen($cookieFileName) - 4);
                            if(!in_array($username,$allUsers)){
                                $filePath  = Wow::get("project/cookiePath") . "instagram/" . $username . ".dat";
                                $filePath2 = Wow::get("project/cookiePath") . "instagram/" . $username . ".cnf";
                                if(file_exists($filePath)) {
                                    unlink($filePath);
                                }
                                if(file_exists($filePath2)) {
                                    unlink($filePath2);
                                }
                            }
                        }

                        return $this->json("ok");
                        break;
                    case "addUserPass":
                        $userpass    = $this->request->data->userpass;
                        $expUserpass = explode(":", $userpass);
                        $sonuc       = array(
                            "status"   => "error",
                            "message"  => "user:password hatalı gönderildi",
                            "instaID"  => "0",
                            "userNick" => ""
                        );
                        if(count($expUserpass) > 1) {
                            $username = strtolower(trim($expUserpass[0]));
                            $password = $expUserpass[1];
                            try {
                                $i = new Instagram($username, $password);
                                $l = $i->login(TRUE);

                                if($l["status"] == "ok") {

                                    $userData = $i->getProfileData();
                                    $userInfo = $i->getSelfUsernameInfo();

                                    $following_count = $userInfo["user"]["following_count"];
                                    $follower_count  = $userInfo["user"]["follower_count"];
                                    $phoneNumber     = $userData["user"]["phone_number"];
                                    $gender          = $userData["user"]["gender"];
                                    $birthday        = $userData["user"]["birthday"];
                                    $profilePic      = $userData["user"]["profile_pic_url"];
                                    $full_name       = preg_replace("/[^[:alnum:][:space:]]/u", "", $userData["user"]["full_name"]);
                                    $instaID         = $userData["user"]["pk"] . "";
                                    $email           = $userData["user"]["email"];

                                    $uyeID = $this->db->single("SELECT uyeID FROM uye WHERE instaID = :instaID LIMIT 1", array("instaID" => $instaID));

                                    if(empty($uyeID)) {

                                        $this->db->query("INSERT INTO uye (instaID, profilFoto, fullName, kullaniciAdi, sifre, takipEdilenSayisi, takipciSayisi, phoneNumber, email, gender, birthDay, isOtoAktarim) VALUES(:instaID, :profilFoto, :fullName, :kullaniciAdi, :sifre, :takipEdilenSayisi, :takipciSayisi, :phoneNumber, :email, :gender, :birthDay, 1)", array(
                                            "instaID"           => $instaID,
                                            "profilFoto"        => $profilePic,
                                            "fullName"          => $full_name,
                                            "kullaniciAdi"      => $username,
                                            "sifre"             => $password,
                                            "takipEdilenSayisi" => $following_count,
                                            "takipciSayisi"     => $follower_count,
                                            "phoneNumber"       => $phoneNumber,
                                            "email"             => $email,
                                            "gender"            => $gender,
                                            "birthDay"          => $birthday
                                        ));
                                        $sonuc = array(
                                            "status"   => "success",
                                            "message"  => "Kullanıcı Eklendi",
                                            "instaID"  => $instaID,
                                            "userNick" => $username
                                        );
                                    } else {

                                        $this->db->query("UPDATE uye SET takipciSayisi = :takipciSayisi,takipEdilenSayisi = :takipEdilenSayisi,profilFoto = :profilFoto,fullName = :fullName, isActive=1, isWebCookie=0 WHERE instaID = :instaID", array(
                                            "takipciSayisi"     => $follower_count,
                                            "takipEdilenSayisi" => $following_count,
                                            "profilFoto"        => $profilePic,
                                            "fullName"          => $full_name,
                                            "instaID"           => $instaID
                                        ));

                                        $sonuc = array(
                                            "status"   => "error",
                                            "message"  => "Kullanıcı Zaten Ekli",
                                            "instaID"  => $instaID,
                                            "userNick" => $username
                                        );
                                    }

                                } else {
                                    $sonuc = array(
                                        "status"   => "error",
                                        "message"  => "Kullanıcı bulunamadı!",
                                        "instaID"  => "0",
                                        "userNick" => $username
                                    );
                                }
                            } catch(Exception $e) {
                                $sonuc = array(
                                    "status"   => "error",
                                    "message"  => "Login başarısız.",
                                    "instaID"  => "0",
                                    "userNick" => $username
                                );
                            }
                        }

                        return $this->json($sonuc);
                        break;
                    case "uploadCookies":
                        $uploads_dir = Wow::get("project/cookiePath") . "source/";
                        foreach($this->request->files->files["error"] as $key => $error) {
                            if($error == UPLOAD_ERR_OK) {
                                $tmp_name     = $this->request->files->files["tmp_name"][$key];
                                $name         = $this->request->files->files["name"][$key];
                                $splittedName = explode(".", $name);
                                if(count($splittedName) > 1) {
                                    $extension = $splittedName[count($splittedName) - 1];
                                    if($extension == "selco" || $extension == "dat" || $extension == "cnf") {
                                        move_uploaded_file($tmp_name, $uploads_dir . strtolower($name));
                                    }
                                }
                            }
                        }

                        return $this->json("ok");
                        break;
                }
            }

            $data                       = array();
            $sourceCookies              = glob(Wow::get("project/cookiePath") . "source/*.{selco,dat}", GLOB_BRACE);
            $data["countPassiveUsers"]  = $this->db->single("SELECT COUNT(*) FROM uye WHERE isActive=0");
            $data["countSourceCookies"] = count($sourceCookies);

            return $this->view($data);
        }

        function UyelerAction($page = 1) {
            $this->navigation->add("Üyeler", "/admin/uyeler");


            if(!intval($page) > 0) {
                $page = 1;
            }

            $limitCount = 50;
            $limitStart = (($page * $limitCount) - $limitCount);

            $sqlWhere = "WHERE 1=1";
            $arrSorgu = array();

            $q = $this->request->query->q;
            if(!empty($q)) {
                $sqlWhere .= " AND (kullaniciAdi LIKE :q OR fullName LIKE :q2)";
                $arrSorgu["q"]  = "%" . $q . "%";
                $arrSorgu["q2"] = "%" . $q . "%";
            }

            $isWebCookie = $this->request->query->isWebCookie;
            if(!is_null($isWebCookie) && $isWebCookie !== "") {
                $sqlWhere .= " AND isWebCookie = :isWebCookie";
                $arrSorgu["isWebCookie"] = $isWebCookie;
            }

            $isActive = $this->request->query->isActive;
            if(!is_null($isActive) && $isActive !== "") {
                $sqlWhere .= " AND isActive = :isActive";
                $arrSorgu["isActive"] = $isActive;
            }

            $uyeler = $this->db->query("SELECT * FROM uye " . $sqlWhere . " LIMIT :limitStart,:limitCount", array_merge($arrSorgu, array(
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
                        $this->db->query("UPDATE uye SET begeniKredi=:begeniKredi,takipKredi=:takipKredi,yorumKredi=:yorumKredi, isUsable=:isUsable, isAdmin=:isAdmin, isBayi=:isBayi WHERE uyeID=:uyeID", array(
                            "uyeID"       => $user["uyeID"],
                            "begeniKredi" => intval($this->request->data->begeniKredi),
                            "takipKredi"  => intval($this->request->data->takipKredi),
                            "yorumKredi"  => intval($this->request->data->yorumKredi),
                            "isUsable"    => $this->request->data->isUsable == 1 ? 1 : 0,
                            "isAdmin"     => $this->request->data->isAdmin == 1 ? 1 : 0,
                            "isBayi"      => $this->request->data->isBayi == 1 ? 1 : 0
                        ));

                        $objNotification             = new Notification();
                        $objNotification->title      = "Değişiklikler Kaydedildi.";
                        $objNotification->messages[] = $user["kullaniciAdi"] . " kullanıcısında yaptığınız değişiklikler kaydedildi.";
                        $objNotification->type       = $objNotification::PARAM_TYPE_SUCCESS;
                        $this->notifications[]       = $objNotification;

                        return $this->redirectToUrl($this->request->referrer);

                        break;
                    case "addPackage":
                        $startDate = date("Y-m-d H:i:s", strtotime($this->request->data->startDate));
                        $endDate   = date("Y-m-d H:i:s", strtotime($this->request->data->endDate));
                        $likeCount = intval($this->request->data->likeCount);
                        if($likeCount < 1) {
                            $likeCount = 1;
                        }
                        $minuteDelay = intval($this->request->data->minuteDelay);
                        if($minuteDelay < 1) {
                            $minuteDelay = 1;
                        }
                        $krediDelay = intval($this->request->data->krediDelay);
                        if($krediDelay < 1) {
                            $krediDelay = 100;
                        }
                        if($krediDelay < ceil($likeCount / 20)) {
                            $krediDelay = ceil($likeCount / 20);
                        }
                        if($krediDelay > 250) {
                            $krediDelay = 250;
                        }
                        $gender = NULL;
                        if(intval($this->request->data->gender) > 0) {
                            $gender = intval($this->request->data->gender) == 2 ? 2 : 1;
                        }
                        $this->db->query("INSERT INTO uye_otobegenipaket (uyeID,startDate,endDate,lastControlDate,likeCount,minuteDelay,krediDelay,isActive,gender) VALUES (:uyeID,:startDate,:endDate,NOW(),:likeCount,:minuteDelay,:krediDelay,1,:gender)", array(
                            "uyeID"       => $user["uyeID"],
                            "startDate"   => $startDate,
                            "endDate"     => $endDate,
                            "likeCount"   => $likeCount,
                            "minuteDelay" => $minuteDelay,
                            "krediDelay"  => $krediDelay,
                            "gender"      => $gender
                        ));
                        $objNotification             = new Notification();
                        $objNotification->title      = "Paket Tanımlandı.";
                        $objNotification->messages[] = $user["kullaniciAdi"] . " kullanıcısı için paket tanımlandı.";
                        $objNotification->type       = $objNotification::PARAM_TYPE_SUCCESS;
                        $this->notifications[]       = $objNotification;

                        return $this->redirectToUrl($this->request->referrer);
                        break;
                    case "packageDetails":
                        $paketID = intval($this->request->query->paketID);
                        $paket   = $this->db->row("SELECT * FROM uye_otobegenipaket WHERE id=:paketID", array("paketID" => $paketID));
                        if(empty($paket)) {
                            return $this->notFound();
                        }
                        $paketdetay = $this->db->query("SELECT * FROM uye_otobegenipaket_gonderi WHERE paketID=:paketID", array("paketID" => $paketID));
                        $data       = array(
                            "paket"      => $paket,
                            "paketdetay" => $paketdetay
                        );

                        return $this->partialView($data, "admin/uye-paket-detay");
                        break;
                    case "updatePackage":
                        $startDate = date("Y-m-d H:i:s", strtotime($this->request->data->startDate));
                        $endDate   = date("Y-m-d H:i:s", strtotime($this->request->data->endDate));
                        $likeCount = intval($this->request->data->likeCount);
                        if($likeCount < 1) {
                            $likeCount = 1;
                        }
                        $minuteDelay = intval($this->request->data->minuteDelay);
                        if($minuteDelay < 1) {
                            $minuteDelay = 1;
                        }
                        $krediDelay = intval($this->request->data->krediDelay);
                        if($krediDelay < 1) {
                            $krediDelay = 100;
                        }
                        if($krediDelay < ceil($likeCount / 20)) {
                            $krediDelay = ceil($likeCount / 20);
                        }
                        if($krediDelay > 250) {
                            $krediDelay = 250;
                        }
                        $gender = NULL;
                        if(intval($this->request->data->gender) > 0) {
                            $gender = intval($this->request->data->gender) == 2 ? 2 : 1;
                        }
                        $isActive = intval($this->request->data->isActive);
                        $this->db->query("UPDATE uye_otobegenipaket SET startDate = :startDate, endDate = :endDate, likeCount = :likeCount,minuteDelay=:minuteDelay,krediDelay=:krediDelay, isActive = :isActive, gender = :gender WHERE id=:id", array(
                            "id"          => $this->request->query->paketID,
                            "startDate"   => $startDate,
                            "endDate"     => $endDate,
                            "likeCount"   => $likeCount,
                            "isActive"    => $isActive,
                            "minuteDelay" => $minuteDelay,
                            "krediDelay"  => $krediDelay,
                            "gender"      => $gender
                        ));
                        $objNotification             = new Notification();
                        $objNotification->title      = "Paket Değişiklikleri Kaydedildi.";
                        $objNotification->messages[] = "Pakette yaptığınız değişiklikler kaydedildi.";
                        $objNotification->type       = $objNotification::PARAM_TYPE_SUCCESS;
                        $this->notifications[]       = $objNotification;

                        return $this->redirectToUrl($this->request->referrer);
                        break;
                    default:
                        return $this->notFound();
                }
            }

            $userPaket = $this->db->query("SELECT * FROM uye_otobegenipaket WHERE uyeID=:uyeID ORDER BY id DESC", array("uyeID" => $user["uyeID"]));

            $data = array(
                "uye"   => $user,
                "paket" => $userPaket
            );

            return $this->view($data);
        }


        function SayfalarAction() {
            $pages = $this->db->query("SELECT * FROM sayfa");

            $this->navigation->add("Sayfalar", "/admin/sayfalar");


            return $this->view($pages);
        }

        function SayfaDuzenleAction($id) {
            if(!intval($id) > 0) {
                return $this->notFound();
            }
            $id   = intval($id);
            $page = $this->db->row("SELECT * FROM sayfa WHERE id=:id", array("id" => $id));
            if(empty($page)) {
                return $this->notFound();
            }

            if($this->request->method == "POST") {
                $pageInfo    = serialize(array(
                                             "title"       => $this->request->data->title,
                                             "description" => $this->request->data->description,
                                             "keywords"    => $this->request->data->keywords
                                         ));
                $pageContent = $this->request->data->pageContent;
                $this->db->query("UPDATE sayfa SET pageInfo=:pageInfo,pageContent=:pageContent WHERE id=:id", array(
                    "id"          => $page["id"],
                    "pageInfo"    => $pageInfo,
                    "pageContent" => $pageContent
                ));

                $objNotification             = new Notification();
                $objNotification->type       = $objNotification::PARAM_TYPE_SUCCESS;
                $objNotification->title      = "Değişiklikler Kaydedildi.";
                $objNotification->messages[] = "Sayfada yaptığınız değişiklikler kaydedildi.";
                $this->notifications[]       = $objNotification;

                return $this->redirectToUrl($this->request->referrer);
            }

            $this->navigation->add("Sayfalar", "/admin/sayfalar");
            $this->navigation->add($page["page"], "/admin/sayfa-duzenle/" . $page["id"]);


            return $this->view($page);
        }

        function BlogAction() {
            //Yeni Blog İçeriği Kayıt Modu
            if($this->request->method == "POST") {
                $this->db->query("INSERT INTO blog (baslik) VALUES (:baslik)", array("baslik" => $this->request->data->baslik));
                $lastInsertID = $this->db->lastInsertId();
                if(intval($lastInsertID) > 0) {
                    $objNotification             = new Notification();
                    $objNotification->type       = $objNotification::PARAM_TYPE_SUCCESS;
                    $objNotification->title      = "Yeni Blog İçeriği Kaydedildi.";
                    $objNotification->messages[] = "Aşağıda eksik kısamları tamamlayabilirsiniz.";
                    $this->notifications[]       = $objNotification;

                    return $this->redirectToUrl("/admin/blog-duzenle/" . $lastInsertID);
                }
            }

            //Blog Silme Modu
            if(intval($this->request->query->deleteBlogID) > 0) {
                $rowsAffected = $this->db->query("DELETE FROM blog WHERE blogID=:blogID", array("blogID" => $this->request->query->deleteBlogID));
                if($rowsAffected > 0) {
                    $objNotification             = new Notification();
                    $objNotification->type       = $objNotification::PARAM_TYPE_DANGER;
                    $objNotification->title      = "Blog İçeriği Silindi.";
                    $objNotification->messages[] = "Silmek istediğiniz blog başarılı bir şekilde silindi.";
                    $this->notifications[]       = $objNotification;

                    return $this->redirectToUrl("/admin/blog");
                }

            }


            $blogs = $this->db->query("SELECT * FROM blog");

            $this->navigation->add("Blog", "/admin/blog");


            return $this->view($blogs);
        }

        function BlogDuzenleAction($id) {
            if(!intval($id) > 0) {
                return $this->notFound();
            }
            $id   = intval($id);
            $blog = $this->db->row("SELECT * FROM blog WHERE blogID=:blogID", array("blogID" => $id));
            if(empty($blog)) {
                return $this->notFound();
            }

            if($this->request->method == "POST") {
                $baslik   = $this->request->data->baslik;
                $icerik   = $this->request->data->icerik;
                $anaResim = $this->request->data->anaResim;
                $seoLink  = $this->request->data->seoLink;
                $isActive = intval($this->request->data->isActive) == 1 ? 1 : 0;
                $this->db->query("UPDATE blog SET baslik=:baslik,icerik=:icerik,anaResim=:anaResim,seoLink=:seoLink,isActive=:isActive WHERE blogID=:blogID", array(
                    "blogID"   => $blog["blogID"],
                    "baslik"   => $baslik,
                    "icerik"   => $icerik,
                    "anaResim" => $anaResim,
                    "seoLink"  => $seoLink,
                    "isActive" => $isActive
                ));

                $objNotification             = new Notification();
                $objNotification->type       = $objNotification::PARAM_TYPE_SUCCESS;
                $objNotification->title      = "Değişiklikler Kaydedildi.";
                $objNotification->messages[] = "Blog içeriğinde yaptığınız değişiklikler kaydedildi.";
                $this->notifications[]       = $objNotification;

                return $this->redirectToUrl($this->request->referrer);
            }

            $this->navigation->add("Blog", "/admin/blog");
            $this->navigation->add($blog["baslik"], "/admin/blog-duzenle/" . $blog["blogID"]);


            return $this->view($blog);
        }


        function BayilikAction($page = 1) {

            $this->navigation->add("Bayiler", "/admin/bayilik");


            if(!intval($page) > 0) {
                $page = 1;
            }

            $limitCount = 50;
            $limitStart = (($page * $limitCount) - $limitCount);

            $sqlWhere = "WHERE 1=1";
            $arrSorgu = array();

            $q = $this->request->query->q;
            if(!empty($q)) {
                $sqlWhere .= " AND (username LIKE :q)";
                $arrSorgu["q"] = "%" . $q . "%";
            }

            $isActive = $this->request->query->isActive;
            if(!is_null($isActive) && $isActive !== "") {
                $sqlWhere .= " AND isActive = :isActive";
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


            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "saveBayiDetails":
                        $this->db->query("UPDATE bayi SET username=:username,password=:password,begeniMaxKredi=:begeniMaxKredi, takipMaxKredi=:takipMaxKredi, yorumMaxKredi=:yorumMaxKredi, otoBegeniMaxKredi=:otoBegeniMaxKredi,gunlukBegeniLimit=:gunlukBegeniLimit,gunlukTakipLimit=:gunlukTakipLimit,gunlukYorumLimit=:gunlukYorumLimit,toplamOtoBegeniLimit=:toplamOtoBegeniLimit,gunlukBegeniLimitLeft=:gunlukBegeniLimitLeft, gunlukTakipLimitLeft=:gunlukTakipLimitLeft, gunlukYorumLimitLeft=:gunlukYorumLimitLeft,toplamOtoBegeniLimitLeft=:toplamOtoBegeniLimitLeft, sonaErmeTarihi=:sonaErmeTarihi,notlar=:notlar,isActive=:isActive, begeniGender=:begeniGender, takipGender=:takipGender, yorumGender=:yorumGender, otoBegeniGender=:otoBegeniGender WHERE bayiID=:bayiID", array(
                            "bayiID"                   => $bayi["bayiID"],
                            "username"                 => $this->request->data->username,
                            "password"                 => $this->request->data->password,
                            "begeniMaxKredi"           => $this->request->data->begeniMaxKredi,
                            "takipMaxKredi"            => $this->request->data->takipMaxKredi,
                            "yorumMaxKredi"            => $this->request->data->yorumMaxKredi,
                            "otoBegeniMaxKredi"        => $this->request->data->otoBegeniMaxKredi,
                            "gunlukBegeniLimit"        => $this->request->data->gunlukBegeniLimit,
                            "gunlukTakipLimit"         => $this->request->data->gunlukTakipLimit,
                            "gunlukYorumLimit"         => $this->request->data->gunlukYorumLimit,
                            "toplamOtoBegeniLimit"     => $this->request->data->toplamOtoBegeniLimit,
                            "gunlukBegeniLimitLeft"    => $this->request->data->gunlukBegeniLimitLeft,
                            "gunlukTakipLimitLeft"     => $this->request->data->gunlukTakipLimitLeft,
                            "gunlukYorumLimitLeft"     => $this->request->data->gunlukYorumLimitLeft,
                            "toplamOtoBegeniLimitLeft" => $this->request->data->toplamOtoBegeniLimitLeft,
                            "sonaErmeTarihi"           => date("Y-m-d H:i:s", strtotime($this->request->data->sonaErmeTarihi)),
                            "notlar"                   => $this->request->data->notlar,
                            "isActive"                 => intval($this->request->data->isActive) == 1 ? 1 : 0,
                            "begeniGender"             => intval($this->request->data->begeniGender) == 1 ? 1 : 0,
                            "takipGender"              => intval($this->request->data->takipGender) == 1 ? 1 : 0,
                            "yorumGender"              => intval($this->request->data->yorumGender) == 1 ? 1 : 0,
                            "otoBegeniGender"          => intval($this->request->data->otoBegeniGender) == 1 ? 1 : 0
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
            }


            return $this->redirectToUrl("/admin/bayilik");
        }

        function BayiEkleAction() {

            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "saveBayiEkle":
                        $this->db->query("INSERT INTO bayi SET username=:username,password=:password,begeniMaxKredi=:begeniMaxKredi, takipMaxKredi=:takipMaxKredi, yorumMaxKredi=:yorumMaxKredi, otoBegeniMaxKredi=:otoBegeniMaxKredi,gunlukBegeniLimit=:gunlukBegeniLimit,gunlukTakipLimit=:gunlukTakipLimit,gunlukYorumLimit=:gunlukYorumLimit,toplamOtoBegeniLimit=:toplamOtoBegeniLimit,gunlukBegeniLimitLeft=:gunlukBegeniLimitLeft, gunlukTakipLimitLeft=:gunlukTakipLimitLeft, gunlukYorumLimitLeft=:gunlukYorumLimitLeft,toplamOtoBegeniLimitLeft=:toplamOtoBegeniLimitLeft, notlar=:notlar,sonaErmeTarihi=:sonaErmeTarihi,isActive=:isActive, begeniGender=:begeniGender, takipGender=:takipGender, yorumGender=:yorumGender, otoBegeniGender=:otoBegeniGender", array(
                            "username"                 => $this->request->data->username,
                            "password"                 => $this->request->data->password,
                            "begeniMaxKredi"           => $this->request->data->begeniMaxKredi,
                            "takipMaxKredi"            => $this->request->data->takipMaxKredi,
                            "yorumMaxKredi"            => $this->request->data->yorumMaxKredi,
                            "otoBegeniMaxKredi"        => $this->request->data->otoBegeniMaxKredi,
                            "gunlukBegeniLimit"        => $this->request->data->gunlukBegeniLimit,
                            "gunlukTakipLimit"         => $this->request->data->gunlukTakipLimit,
                            "gunlukYorumLimit"         => $this->request->data->gunlukYorumLimit,
                            "toplamOtoBegeniLimit"     => $this->request->data->toplamOtoBegeniLimit,
                            "gunlukBegeniLimitLeft"    => $this->request->data->gunlukBegeniLimitLeft,
                            "gunlukTakipLimitLeft"     => $this->request->data->gunlukTakipLimitLeft,
                            "gunlukYorumLimitLeft"     => $this->request->data->gunlukYorumLimitLeft,
                            "toplamOtoBegeniLimitLeft" => $this->request->data->toplamOtoBegeniLimitLeft,
                            "sonaErmeTarihi"           => date("Y-m-d H:i:s", strtotime($this->request->data->sonaErmeTarihi)),
                            "notlar"                   => $this->request->data->notlar,
                            "isActive"                 => intval($this->request->data->isActive) == 1 ? 1 : 0,
                            "begeniGender"             => intval($this->request->data->begeniGender) == 1 ? 1 : 0,
                            "takipGender"              => intval($this->request->data->takipGender) == 1 ? 1 : 0,
                            "yorumGender"              => intval($this->request->data->yorumGender) == 1 ? 1 : 0,
                            "otoBegeniGender"          => intval($this->request->data->otoBegeniGender) == 1 ? 1 : 0
                        ));

                        $objNotification             = new Notification();
                        $objNotification->title      = "Yeni Bayi Eklendi.";
                        $objNotification->messages[] = $this->request->data->username . " bayisi başarılı bir şekilde eklendi.";
                        $objNotification->type       = $objNotification::PARAM_TYPE_SUCCESS;
                        $this->notifications[]       = $objNotification;

                        return $this->redirectToUrl("/admin/bayi-detay/" . $this->db->lastInsertId());

                        break;
                    default:
                        return $this->notFound();
                }
            }

            return $this->view();
        }

    }