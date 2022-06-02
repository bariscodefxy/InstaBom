<?php

    namespace App\Controllers\Admin;

    use App\Models\Notification;
    use Wow;
    use Wow\Net\Response;
    use ZipArchive;

    class WizardController extends BaseController {

        function onActionExecuting() {
            if(($actionResponse = parent::onActionExecuting()) instanceof Response) {
                return $actionResponse;
            }
            //Üye girişi kontrolü.
            if(($pass = $this->middleware("logged")) instanceof Response) {
                return $pass;
            }

            //Extension Test
            if(!extension_loaded('zip')) {
                return $this->view(NULL, "admin/wizard/extension-needed");
            }
        }

        function ImportAction() {
            if($this->request->method == "POST") {
                switch($this->request->query->formType) {
                    case "uploadIwp":
                        $uploads_dir = Wow::get("project/cookiePath") . "import/";

                        if(!file_exists($uploads_dir)) {
                            mkdir($uploads_dir, 0755, TRUE);
                        }

                        foreach($this->request->files->files["error"] as $key => $error) {
                            if($error == UPLOAD_ERR_OK) {
                                $tmp_name     = $this->request->files->files["tmp_name"][$key];
                                $name         = $this->request->files->files["name"][$key];
                                $splittedName = explode(".", $name);
                                if(count($splittedName) > 1) {
                                    $extension = $splittedName[count($splittedName) - 1];
                                    if($extension == "iwp") {
                                        move_uploaded_file($tmp_name, $uploads_dir . strtolower($name));
                                    }
                                }
                            }
                        }

                        return $this->json(["status" => "success"]);
                        break;
                    default:

                        $iwpPath = Wow::get("project/cookiePath") . "import/" . $this->request->data->packageName . ".iwp";

                        $exportPath = Wow::get("project/cookiePath") . "import/" . md5(time());
                        $cookiePath = Wow::get("project/cookiePath");
                        $zip        = new ZipArchive;
                        if($zip->open($iwpPath) === TRUE) {
                            $allInstaIDs = $this->db->column("SELECT instaID FROM uye");
                            $this->db->CloseConnection();

                            $zip->extractTo($exportPath);
                            $zip->close();

                            $acUS       = json_decode(file_get_contents($exportPath . "/users.json"), TRUE);
                            $addedCount = 0;
                            $sqlCols    = [
                                "instaID",
                                "profilFoto",
                                "fullName",
                                "kullaniciAdi",
                                "sifre",
                                "takipEdilenSayisi",
                                "takipciSayisi",
                                "phoneNumber",
                                "email",
                                "gender",
                                "birthDay",
                                "isWebCookie"
                            ];
                            $sql        = "INSERT INTO uye (" . implode(",", $sqlCols) . ") VALUES ";
                            $sqlParams  = [];
                            $intLooper  = 0;
                            for($i = 0; $i < count($acUS); $i++) {
                                $c = $acUS[$i];
                                if(!in_array($c["instaID"], $allInstaIDs) && $c["instaID"] != 0) {
                                    $addedCount++;
                                    $sql .= $intLooper > 0 ? ',' : '';
                                    $sql .= '(';
                                    for($y = 0; $y < count($sqlCols); $y++) {
                                        $sql                                  .= $y > 0 ? ',' : '';
                                        $sql                                  .= ':' . $sqlCols[$y] . $intLooper;
                                        $sqlParams[$sqlCols[$y] . $intLooper] = $c[$sqlCols[$y]];
                                    }
                                    $sql .= ')';
                                    copy($exportPath . "/" . substr($c["instaID"], -1) . "/" . $c["instaID"] . ".iwb", $cookiePath . "instagramv3/" . substr($c["instaID"], -1) . "/" . $c["instaID"] . ".iwb");
                                    $intLooper++;
                                }
                                if(($intLooper % 150 == 0 || $i == count($acUS)-1) && !empty($sqlParams)) {
                                    $this->db->query($sql, $sqlParams);
                                    $sql       = "INSERT INTO uye (" . implode(",", $sqlCols) . ") VALUES ";
                                    $sqlParams = [];
                                    $intLooper = 0;
                                }
                            }


                            $objNotification = new Notification();
                            if($addedCount > 0) {
                                $objNotification->title    = "Import Tamamlandı";
                                $objNotification->type     = $objNotification::PARAM_TYPE_SUCCESS;
                                $objNotification->messages = ["Aktarım tamamlandı. " . $addedCount . " yeni data eklendi. Eklenen dataların tümünün çalışıp çalışmadığını aktarım esnasında denetlemiyoruz. Takipçi veya beğeni gönderimi yapıldıkça çalışmayan datalar pasif olarak işaretlenecektir."];
                            } else {
                                $objNotification->title    = "Uyarı";
                                $objNotification->type     = $objNotification::PARAM_TYPE_WARNING;
                                $objNotification->messages = ["Aktarım tamamlandı. Ancak paketten sisteminize hiç data aktarılmadı. Bunun sebebi pakette hiç data olmaması veya paketteki tüm dataların sisteminizde ekli olması olabilir!"];
                            }
                            $this->notifications[] = $objNotification;

                            unlink($iwpPath);
                            $this->rrmdir($exportPath);

                            return $this->redirectToUrl($this->request->referrer);
                        } else {
                            $objNotification           = new Notification();
                            $objNotification->title    = "Import Başarısız!";
                            $objNotification->type     = $objNotification::PARAM_TYPE_DANGER;
                            $objNotification->messages = [$iwpPath . " yolunda bir iwp dosyası bulunamadı!"];
                            $this->notifications[]     = $objNotification;

                            return $this->redirectToUrl($this->request->referrer);
                        }
                }
            }

            $data        = [];
            $iwpPackages = glob(Wow::get("project/cookiePath") . "import/*.iwp", GLOB_BRACE);
            $dPck        = [];
            foreach($iwpPackages as $p) {
                $pName  = str_replace(Wow::get("project/cookiePath") . "import/", "", $p);
                $dPck[] = substr($pName, 0, strlen($pName) - 4);
            }
            $data["iwpPackages"] = $dPck;


            return $this->view($data);
        }


        function ExportAction() {

            if($this->request->method == "POST") {
                $adet      = intval($this->request->data->adet);
                $gender    = intval($this->request->data->gender);
                $sql       = "SELECT instaID,profilFoto,fullName,kullaniciAdi,sifre,takipEdilenSayisi,takipciSayisi,phoneNumber,email,gender,birthDay,isWebCookie FROM uye WHERE isActive=1 and isUsable=1";
                $sqlParams = [];
                if($gender > 0) {
                    if($gender == 3) {
                        $sql .= " AND (gender = :gender OR gender IS NULL)";
                    } else {
                        $sql .= " AND gender = :gender";
                    }
                    $sqlParams["gender"] = $gender;
                }
                $sql .= " ORDER BY sonOlayTarihi DESC";
                if($this->request->query->formType == "sorgu") {
                    $acUs = $this->db->query($sql, $sqlParams);

                    return $this->json([
                                           "status" => "success",
                                           "adet"   => count($acUs)
                                       ]);
                }
                if($adet > 0) {
                    $sql               .= " LIMIT :adet";
                    $sqlParams["adet"] = $adet;
                }
                $acUs = $this->db->query($sql, $sqlParams);
                if(empty($acUs)) {
                    $objNotification           = new Notification();
                    $objNotification->title    = "Export Başarısız!";
                    $objNotification->type     = $objNotification::PARAM_TYPE_DANGER;
                    $objNotification->messages = ["Belirtilen kriterlerde hiç aktif ve kullanımda olmayan kullanıcı yok!"];
                    $this->notifications[]     = $objNotification;

                    return $this->redirectToUrl($this->request->referrer);
                }
                if(!file_exists(Wow::get("project/cookiePath") . "export/")) {
                    mkdir(Wow::get("project/cookiePath") . "export/", 0755, TRUE);
                }
                $randomIwpName     = md5(time());
                $temporaryFilePath = Wow::get("project/cookiePath") . "export/" . $randomIwpName;
                if(!file_exists($temporaryFilePath)) {
                    mkdir($temporaryFilePath, 0755, TRUE);
                }
                for($i = 0; $i < 10; $i++) {
                    if(!file_exists($temporaryFilePath . "/" . $i)) {
                        mkdir($temporaryFilePath . "/" . $i, 0755, TRUE);
                    }
                }
                $arrUsers = [];
                foreach($acUs as $c) {
                    $cPath = Wow::get("project/cookiePath") . "instagramv3/" . substr($c["instaID"], -1) . "/" . $c["instaID"] . ".iwb";
                    if(file_exists($cPath)) {
                        $newPath = $temporaryFilePath . "/" . substr($c["instaID"], -1) . "/" . $c["instaID"] . ".iwb";
                        copy($cPath, $newPath);
                        $arrUsers[] = $c;
                    }
                }

                file_put_contents($temporaryFilePath . "/users.json", json_encode($arrUsers));
                $z = new ZipArchive();
                $z->open($temporaryFilePath . "/" . $randomIwpName . ".iwp", ZIPARCHIVE::CREATE);
                foreach($arrUsers as $c) {
                    $z->addFile($temporaryFilePath . "/" . substr($c["instaID"], -1) . "/" . $c["instaID"] . ".iwb", substr($c["instaID"], -1) . "/" . $c["instaID"] . ".iwb");
                }
                $z->addFile($temporaryFilePath . "/users.json", "users.json");
                $z->close();
                $zipBuffer = file_get_contents($temporaryFilePath . "/" . $randomIwpName . ".iwp");


                $this->rrmdir($temporaryFilePath);

                return $this->file($zipBuffer, "application/zip", $randomIwpName . ".iwp", TRUE);

            }

            return $this->view();
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