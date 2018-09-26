<?php  
	class Login
	{
		public static function isloggedin()
		{
			if (isset($_COOKIE['snid'])) {
				$cookie_token = sha1($_COOKIE['snid']);
				if (DB::query("SELECT token FROM login_token WHERE token = :token", $name = [':token' => $cookie_token])) {
					$user_id = DB::query("SELECT * FROM login_token WHERE token = :token", $name = [':token' => $cookie_token])[0]['user_id'];
					if (isset($_COOKIE['snid_'])) {
						return $user_id;
					} else {
						$cstrong = TRUE;
						$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
						DB::query("INSERT INTO login_token(token,user_id) VALUES(:token,:user_id)", $name = [':token'=>sha1($token), ':user_id'=>$user_id]);
						DB::query("DELETE FROM login_token WHERE token = :token", $name = [':token'=>$cookie_token]);

						setcookie("snid", $token, time() + 60*60*24*7, '/', NULL, NULL, TRUE);
						setcookie("snid_", '1', time() + 60*60*24*3, '/', NULL, NULL, TRUE);

						return $user_id;
						}
					
				}
			}
			return FALSE;
		}
	}
?>