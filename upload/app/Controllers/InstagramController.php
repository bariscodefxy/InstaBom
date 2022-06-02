<?php

	namespace App\Controllers;

	use Exception;
	use Settings;
	use Constants;
	use Wow;
	use Utils;

	class InstagramController{

		function TestAction(){
			exit();
		}

	}

	class Instagram{
		protected $username;            // Username
		protected $password;
		protected $username_id;         // Username ID
		protected $token;               // _csrftoken
		protected $isLoggedIn = FALSE;  // Session status
		protected $IGDataPath;          // Data storage path
		/**
		 * @var Settings
		 */
		public $settings;

		public function __construct($username,$password,$username_id = NULL,$forceUserIP = FALSE){
			$username = trim($username);
			$password = trim($password);
			if($username_id === NULL){
				try{
					$userData = file_get_contents("https://www.instagram.com/".$username."/?__a=1");
				} catch(Exception $e){
					$userData = "";
				}
				$userData = json_decode($userData,TRUE);
				if(!is_array($userData) || !isset($userData["user"]["id"])){
					throw new Exception("Invalid username!");
				}
				$username_id = $userData["user"]["id"];
			}

			$this->setUser($username,$password,$username_id,$forceUserIP);
		}

		public function setUser($username,$password,$username_id,$forceUserIP = FALSE){
			$this->username    = $username;
			$this->password    = $password;
			$this->username_id = $username_id;
			$this->IGDataPath  = Wow::get("project/cookiePath")."instagramv4/";
			$this->settings    = new Settings($this->IGDataPath.$username_id.'.iwb');
			$this->checkSettings($forceUserIP);
			if($this->settings->get('token') != NULL){
				$this->isLoggedIn  = TRUE;
				$this->username_id = $this->settings->get('username_id');
				$this->token       = $this->settings->get('token');
			} else {
				$this->isLoggedIn = FALSE;
			}
		}

		protected function checkSettings($forceUserIP = FALSE){
			$settingsCompare = $this->settings->get("sets");
			if($this->settings->get('ip') == NULL || $forceUserIP){
				$ipAdress = '78.'.rand(160,191).'.'.rand(1,255).'.'.rand(1,255);
				if($forceUserIP && !empty($_SERVER["REMOTE_ADDR"])){
					$ipAdress = $_SERVER["REMOTE_ADDR"];
				}
				$this->settings->set('ip',$ipAdress);
			}
			if($this->settings->get('username_id') == NULL){
				$this->settings->set('username_id',$this->username_id);
			}
			if($this->settings->get('web_user_agent') == NULL){
				$userAgents = explode(PHP_EOL,file_get_contents(Wow::get("project/cookiePath")."device/browsers.csv"));
				$agentIndex = rand(0,count($userAgents)-1);
				$userAgent  = $userAgents[ $agentIndex ];
				$this->settings->set('web_user_agent',$userAgent);
			}
			if($this->settings->get('asns') == NULL){
				$this->settings->set('asns',rand(1,INSTAWEB_MAX_ASNS));
			}
			if($settingsCompare !== $this->settings->get("sets")){
				$this->settings->save();
			}
		}


		function getData(){
			return array(
				"username"       => $this->username,
				"username_id"    => $this->username_id,
				"token"          => $this->token,
				"web_user_agent" => $this->settings->get('web_user_agent'),
				"ip"             => $this->settings->get('ip'),
				"web_cookie"     => $this->settings->get('web_cookie'),
				"asns"           => $this->settings->get('asns')
			);
		}


		public function comment($mediaId,$commentText){
			$arrMediaID = explode("_",$mediaId);
			$mediaId    = $arrMediaID[0];
			$postData   = 'comment_text='.$commentText;
			$headers    = array();
			$headers[]  = 'Referer: https://www.instagram.com/';
			$headers[]  = 'DNT: 1';
			$headers[]  = 'Origin: https://www.instagram.com/';
			$headers[]  = 'X-CSRFToken: '.trim($this->token);
			$headers[]  = 'X-Requested-With: XMLHttpRequest';
			$headers[]  = 'X-Instagram-AJAX: 1';
			$headers[]  = 'Connection: close';
			$headers[]  = 'Cache-Control: max-age=0';

			return $this->request("web/comments/$mediaId/add/",$headers,$postData)[1];
		}


		public function getUsernameInfo($username){
			$headers   = array();
			$headers[] = 'Referer: https://www.instagram.com/';
			$headers[] = 'DNT: 1';
			$headers[] = 'Origin: https://www.instagram.com/';
			$headers[] = 'X-CSRFToken: '.trim($this->token);
			$headers[] = 'X-Requested-With: XMLHttpRequest';
			$headers[] = 'X-Instagram-AJAX: 1';
			$headers[] = 'Connection: close';
			$headers[] = 'Cache-Control: max-age=0';

			return $this->request("$username/?__a=1",$headers)[1];
		}


		public function mediaInfo($mediaCode){
			$headers   = array();
			$headers[] = 'Referer: https://www.instagram.com/';
			$headers[] = 'DNT: 1';
			$headers[] = 'Origin: https://www.instagram.com/';
			$headers[] = 'X-CSRFToken: '.trim($this->token);
			$headers[] = 'X-Requested-With: XMLHttpRequest';
			$headers[] = 'X-Instagram-AJAX: 1';
			$headers[] = 'Connection: close';
			$headers[] = 'Cache-Control: max-age=0';

			return $this->request("p/$mediaCode/?__a=1",$headers)[1];
		}


		public function like($mediaId){
			$arrMediaID = explode("_",$mediaId);
			$mediaId    = $arrMediaID[0];
			$headers    = array();
			$headers[]  = 'Referer: https://www.instagram.com/instagram/';
			$headers[]  = 'DNT: 1';
			$headers[]  = 'Origin: https://www.instagram.com/';
			$headers[]  = 'X-CSRFToken: '.trim($this->token);
			$headers[]  = 'X-Requested-With: XMLHttpRequest';
			$headers[]  = 'X-Instagram-AJAX: 1';
			$headers[]  = 'Connection: close';
			$headers[]  = 'Cache-Control: max-age=0';

			return $this->request("web/likes/$mediaId/like/",$headers,TRUE)[1];
		}


		public function unlike($mediaId){
			$arrMediaID = explode("_",$mediaId);
			$mediaId    = $arrMediaID[0];
			$headers    = array();
			$headers[]  = 'Referer: https://www.instagram.com/';
			$headers[]  = 'DNT: 1';
			$headers[]  = 'Origin: https://www.instagram.com/';
			$headers[]  = 'X-CSRFToken: '.trim($this->token);
			$headers[]  = 'X-Requested-With: XMLHttpRequest';
			$headers[]  = 'X-Instagram-AJAX: 1';
			$headers[]  = 'Connection: close';
			$headers[]  = 'Cache-Control: max-age=0';

			return $this->request("web/likes/$mediaId/unlike/",$headers,TRUE)[1];
		}


		public function follow($userId){
			$headers   = array();
			$headers[] = 'Referer: https://www.instagram.com/instagram/';
			$headers[] = 'DNT: 1';
			$headers[] = 'Origin: https://www.instagram.com/';
			$headers[] = 'X-CSRFToken: '.trim($this->token);
			$headers[] = 'X-Requested-With: XMLHttpRequest';
			$headers[] = 'X-Instagram-AJAX: 1';
			$headers[] = 'Connection: close';
			$headers[] = 'Cache-Control: max-age=0';

			return $this->request("web/friendships/$userId/follow/",$headers,TRUE)[1];
		}


		public function unfollow($userId){
			$headers   = array();
			$headers[] = 'Referer: https://www.instagram.com/instagram/';
			$headers[] = 'DNT: 1';
			$headers[] = 'Origin: https://www.instagram.com/';
			$headers[] = 'X-CSRFToken: '.trim($this->token);
			$headers[] = 'X-Requested-With: XMLHttpRequest';
			$headers[] = 'X-Instagram-AJAX: 1';
			$headers[] = 'Connection: close';
			$headers[] = 'Cache-Control: max-age=0';

			return $this->request("web/friendships/$userId/unfollow/",$headers,TRUE)[1];
		}


		public function changeProfilePicture($photo){

			$bodies = [
				[
					'type'     => 'form-data',
					'name'     => 'profile_pic',
					'data'     => file_get_contents($photo),
					'filename' => 'profile_pic',
					'headers'  => [
						'Content-type: application/octet-stream',
						'Content-Transfer-Encoding: binary',
					],
				],
			];

			$seed = str_split('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
			shuffle($seed);
			$rand = '';
			foreach(array_rand($seed,16) as $k){
				$rand .= $seed[ $k ];
			}
			$boundary = 'WebKitFormBoundary'.$rand;

			$data    = $this->buildBody($bodies,$boundary);
			$headers = [
				'Connection: close',
				'Accept: */*',
				'Content-Type: multipart/form-data; boundary='.$boundary,
				'Content-Length: '.strlen($data),
				'Accept-Language: '.Constants::ACCEPT_LANGUAGE,
				'X-Forwarded-For: '.$this->settings->get('ip')
			];

			$headers[] = 'Referer: https://www.instagram.com/accounts/edit/';
			$headers[] = 'Origin: https://www.instagram.com/';
			$headers[] = 'X-CSRFToken: '.trim($this->token);
			$headers[] = 'X-Requested-With: XMLHttpRequest';
			$headers[] = 'X-Instagram-AJAX: 1';

			$endpoint = 'accounts/web_change_profile_picture/';
			$userAsns = Utils::generateAsns($this->settings->get("asns"));
			$ch       = curl_init();
			curl_setopt($ch,CURLOPT_URL,Constants::WEB_URL.$endpoint);
			curl_setopt($ch,CURLOPT_USERAGENT,$this->settings->get("web_user_agent"));
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
			curl_setopt($ch,CURLOPT_FOLLOWLOCATION,TRUE);
			curl_setopt($ch,CURLOPT_HEADER,TRUE);
			curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
			curl_setopt($ch,CURLOPT_VERBOSE,FALSE);
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
			curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
			curl_setopt($ch,CURLOPT_ENCODING,'');
			curl_setopt($ch,CURLOPT_COOKIE,$this->settings->get("web_cookie"));
			curl_setopt($ch,CURLOPT_PROXY,$userAsns[0]);
			curl_setopt($ch,CURLOPT_PROXYUSERPWD,$userAsns[1]);
			curl_setopt($ch,CURLOPT_POST,TRUE);
			curl_setopt($ch,CURLOPT_POSTFIELDS,$data);

			$resp       = curl_exec($ch);
			$header_len = curl_getinfo($ch,CURLINFO_HEADER_SIZE);
			$header     = substr($resp,0,$header_len);
			$upload     = json_decode(substr($resp,$header_len),TRUE,512,JSON_BIGINT_AS_STRING);
			$this->organizeCookies($header);

			curl_close($ch);

			return $upload;
		}


		protected function buildBody($bodies,$boundary){
			$body = '';
			foreach($bodies as $b){
				$body .= '--'.$boundary."\r\n";
				$body .= 'Content-Disposition: '.$b['type'].'; name="'.$b['name'].'"';
				if(isset($b['filename'])){
					$ext  = pathinfo($b['filename'],PATHINFO_EXTENSION);
					$body .= '; filename="'.'pending_media_'.number_format(round(microtime(TRUE)*1000),0,'','').'.'.$ext.'"';
				}
				if(isset($b['headers']) && is_array($b['headers'])){
					foreach($b['headers'] as $header){
						$body .= "\r\n".$header;
					}
				}

				$body .= "\r\n\r\n".$b['data']."\r\n";
			}
			$body .= '--'.$boundary.'--';

			return $body;
		}


		public function mailApprove($mailCode){
			return $this->request("accounts/confirm_email/".$mailCode."/?app_redirect=False",[]);
		}

		/**
		 * @return bool
		 */
		public function login(){
			$this->request('',NULL,NULL,FALSE);
			sleep(1);
			$oldCookies    = $this->settings->get('web_cookie') === NULL ? NULL : $this->settings->get('web_cookie');
			$arrOldCookies = [];
			if(!empty($oldCookies)){
				$parseCookies = explode(";",$oldCookies);
				foreach($parseCookies as $c){
					parse_str($c,$ck);
					$arrOldCookies = array_merge($arrOldCookies,$ck);
				}
			}
			$cookies                         = [];
			$cookies["ig_pr"]                = "1";
			$cookies["fbsr_124024574287414"] = NULL;
			$cookies["ig_vw"]                = rand(600,1860);
			$newCookies                      = array_merge($arrOldCookies,$cookies);
			$cookie_all                      = [];
			foreach($newCookies as $k => $v){
				$cookie_all[] = $k."=".urlencode($v);
			}
			$this->settings->set("web_cookie",implode(";",$cookie_all));
			$this->settings->save();


			$headers   = array();
			$headers[] = 'Referer: https://www.instagram.com/';
			$headers[] = 'Origin: https://www.instagram.com/';
			$headers[] = 'X-CSRFToken: '.trim($this->token);
			$headers[] = 'X-Requested-With: XMLHttpRequest';
			$headers[] = 'X-Instagram-AJAX: 1';
			$headers[] = 'Connection: close';
			$headers[] = 'Cache-Control: no-cache';


			$postParams    = http_build_query(["username" => $this->username,"password" => $this->password]);
			$doLogin       = $this->request('accounts/login/ajax/',$headers,$postParams,FALSE);
			$loginResponse = $doLogin[1];
			if($loginResponse["status"] == "ok" && $loginResponse["authenticated"]){
				return TRUE;
			} else {
				return FALSE;
			}
		}

		protected function request($endpoint,array $optionalheaders = array(),$post = NULL,$needsAuth = TRUE){

			if($needsAuth && !$this->isLoggedIn){
				throw new Exception("Not logged in\n");
			}

			$headers = array(
				'Connection: close',
				'Accept: */*',
				'Accept-Language: '.Constants::ACCEPT_LANGUAGE,
				'X-Forwarded-For: '.$this->settings->get("ip")
			);

			$headers = array_merge($headers,$optionalheaders);

			$userAsns = Utils::generateAsns($this->settings->get("asns"));
			$ch       = curl_init();
			curl_setopt($ch,CURLOPT_URL,Constants::WEB_URL.$endpoint);
			curl_setopt($ch,CURLOPT_USERAGENT,$this->settings->get("web_user_agent"));
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
			curl_setopt($ch,CURLOPT_FOLLOWLOCATION,TRUE);
			curl_setopt($ch,CURLOPT_HEADER,TRUE);
			curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
			curl_setopt($ch,CURLOPT_VERBOSE,FALSE);
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
			curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
			curl_setopt($ch,CURLOPT_ENCODING,'');
			curl_setopt($ch,CURLOPT_COOKIE,$this->settings->get("web_cookie"));
			curl_setopt($ch,CURLOPT_PROXY,$userAsns[0]);
			curl_setopt($ch,CURLOPT_PROXYUSERPWD,$userAsns[1]);
			if($post){
				curl_setopt($ch,CURLOPT_POST,TRUE);
				if(is_string($post)){
					curl_setopt($ch,CURLOPT_POSTFIELDS,$post);
				}
			}
			$resp       = curl_exec($ch);
			$header_len = curl_getinfo($ch,CURLINFO_HEADER_SIZE);
			$header     = substr($resp,0,$header_len);
			$body       = substr($resp,$header_len);
			$this->organizeCookies($header);
			curl_close($ch);

			return [
				$header,
				json_decode($body,TRUE,512,JSON_BIGINT_AS_STRING)
			];
		}


		public function isLoggedIn(){
			return $this->isLoggedIn;
		}


		public function isValid(){
			$headers   = array();
			$headers[] = 'Referer: https://www.instagram.com/';
			$headers[] = 'DNT: 1';
			$headers[] = 'Origin: https://www.instagram.com/';
			$headers[] = 'X-CSRFToken: '.trim($this->token);
			$headers[] = 'X-Requested-With: XMLHttpRequest';
			$headers[] = 'X-Instagram-AJAX: 1';
			$headers[] = 'Connection: close';
			$headers[] = 'Cache-Control: max-age=0';

			$header = $this->request("accounts/activity/?__a=1",$headers)[0];

			return strpos($header,"HTTP/1.1 200 OK") === FALSE ? FALSE : TRUE;
		}


		public function organizeCookies($headers){
			preg_match_all('/^Set-Cookie:\s*([^;]*)/mi',$headers,$matches);
			$cookies = [];
			foreach($matches[1] as $item){
				parse_str($item,$cookie);
				$cookies = array_merge($cookies,$cookie);
			}
			if(!empty($cookies)){
				$oldCookies    = $this->settings->get('web_cookie') === NULL ? NULL : $this->settings->get('web_cookie');
				$arrOldCookies = [];
				if(!empty($oldCookies)){
					$parseCookies = explode(";",$oldCookies);
					foreach($parseCookies as $c){
						parse_str($c,$ck);
						$arrOldCookies = array_merge($arrOldCookies,$ck);
					}
				}
				$newCookies = array_merge($arrOldCookies,$cookies);
				$cookie_all = [];
				foreach($newCookies as $k => $v){
					$cookie_all[] = $k."=".urlencode($v);
				}
				$this->settings->set("web_cookie",implode(";",$cookie_all));
				$this->settings->save();
			}
		}

	}