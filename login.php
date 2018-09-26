<?php include 'classes/db.php'; ?>
<?php 	 
	if (isset($_POST['login'])) {
		$username = $_POST['username'];
		$password = $_POST['password'];

		if (DB::query("SELECT username FROM users WHERE username = :username", $name = [':username' => $username])) {
			
			if (password_verify($password, DB::query("SELECT password FROM users WHERE username = :username", $name = [':username' => $username])[0]['password'])) {
				echo "User is Logedin";
				$cstrong = TRUE;
				$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
				$user_id = DB::query("SELECT id FROM users WHERE username = :username", $name = [':username' => $username])[0]['id'];
				DB::query("INSERT INTO login_token(token,user_id) VALUES(:token,:user_id)", $name = [':token'=>sha1($token), ':user_id'=>$user_id]);
				setcookie("snid", $token, time() + 60*60*24*7, '/', NULL, NULL, TRUE);
				setcookie("snid_", '1', time() + 60*60*24*3, '/', NULL, NULL, TRUE);
			} else {
				echo "Password is Incorrect!";
			}
		} else {
			echo "User is not Registerd";
		}
	}
?>
<h1>Login</h1>
<form action="" method="POST">
	<input type="text" name="username" placeholder="Username ..."><p />
	<input type="password" name="password" placeholder="Password ..."><p />
	<input type="submit" name="login" value="Submit">
</form>