<?php

	namespace App\Libraries;

	use Wow\Database\Database;
	use DateTime;

	class Helpers {
		/**
		 * @var Database $db Database Object
		 */
		protected $db;


		public function __construct() {
			$this->db = Database::getInstance();
		}


		/**
		 * @param string $date   Date
		 * @param string $format Date Format
		 *
		 * @return bool
		 */
		public function is_date($date,$format = "d.m.Y") {
			$d = DateTime::createFromFormat($format,$date);

			return ($d && $d->format($format) === $date) ? TRUE : FALSE;
		}

		/**
		 * @param string $email Email
		 *
		 * @return bool
		 */
		public function is_email($email) {
			return filter_var($email,FILTER_VALIDATE_EMAIL);
		}

		/**
		 * @param string $gsm Gsm Number
		 *
		 * @return bool
		 */
		public function is_gsm($gsm) {
			return preg_match("/^\d+$/",$gsm) && strlen($gsm) == 12 && substr($gsm,0,3) == "905";
		}

        public static function deleteDir($dirPath) {
            if(substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
                $dirPath .= '/';
            }
            $files = glob($dirPath . '*', GLOB_MARK);
            foreach($files as $file) {
                if(is_dir($file)) {
                    self::deleteDir($file);
                } else {
                    unlink($file);
                }
            }
            rmdir($dirPath);
        }

		/**
		 * Dedect the user's device is mobile
		 *
		 * @return bool
		 */
		public function is_mobile() {
			return (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$_SERVER['HTTP_USER_AGENT']) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($_SERVER['HTTP_USER_AGENT'],0,4))) ? TRUE : FALSE;
		}

		/**
		 * @param string $gsmNumber Gsm Number To Recover
		 *
		 * @return null|string
		 */
		public function recoverGsmNumber($gsmNumber) {
			$returnPhone = preg_replace("/[^\d]/","",$gsmNumber);
			if(strlen($returnPhone) == 10) {
				$returnPhone = "90".$returnPhone;
			}
			if(strlen($returnPhone) == 11) {
				$returnPhone = "9".$returnPhone;
			}

			return strlen($returnPhone) == 12 && substr($returnPhone,0,3) == "905" ? $returnPhone : NULL;
		}


        /**
         * Create a slug of given string
         *
         * @param $string
         *
         * @return string
         */
        function slug($string) {
            $arrFrom = array(
                "ı",
                "Ğ",
                "ğ",
                "Ü",
                "ü",
                "Ş",
                "ş",
                "İ",
                "Ö",
                "ö",
                "Ç",
                "ç"
            );
            $arrTo   = array(
                "i",
                "G",
                "g",
                "U",
                "u",
                "S",
                "s",
                "I",
                "O",
                "o",
                "C",
                "c"
            );
            $string  = str_replace($arrFrom, $arrTo, $string);

            return strtolower(trim(preg_replace('~[^0-9a-z]+~i', '-', html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($string, ENT_QUOTES, 'UTF-8')), ENT_QUOTES, 'UTF-8')), '-'));
        }


		public function getPageDetail($pageID) {
			$d = $this->db->row("SELECT * FROM sayfa WHERE id = :id LIMIT 1",array("id" => $pageID));

			return $d;
		}


		public function blogExcerpt($blog,$limit = 160) {
			$icerik     = strip_tags($blog);
			$icerik     = str_replace(array(
				                          "\t",
				                          "\r",
				                          "\n"
			                          ),' ',$icerik);
			$icerik_bol = explode(' ',$icerik);
			$icerik     = '';
			for($i = 0;$i < count($icerik_bol);$i++) {
				if($icerik_bol[ $i ] != '') {
					$icerik .= trim($icerik_bol[ $i ]).' ';
				}
			}
			if(preg_match('/(.*?)\s/i',substr($icerik,$limit),$dizi)) {
				$icerik = trim(substr($icerik,0,$limit+strlen($dizi[0])));
			}

			return $icerik;
		}


		public function makeKeyword($keyword) {
			return trim(implode(",",explode(" ",$keyword)));
		}


		public function getMediaID($permalink) {
			$json = file_get_contents("https://api.instagram.com/oembed?url=".$permalink);
			$json = json_decode($json,TRUE);

			return $json;
		}

		public function tarihFark($tarih1,$tarih2,$ayrac) {
			$result = 0;
			list($y1,$a1,$g1) = explode($ayrac,$tarih1);
			list($y2,$a2,$g2) = explode($ayrac,$tarih2);
			$t1_timestamp = mktime('0','0','0',$a1,$g1,$y1);
			$t2_timestamp = mktime('0','0','0',$a2,$g2,$y2);
			if($t1_timestamp > $t2_timestamp) {
				$result = ($t1_timestamp-$t2_timestamp)/86400;
			} elseif($t2_timestamp > $t1_timestamp) {
				$result = ($t2_timestamp-$t1_timestamp)/86400;
			}

			return $result;
		}
	}