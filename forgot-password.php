<?php include 'classes/db.php'; ?>
<?php if (isset($_GET['token'])) { 
	$token = sha1($_GET['token']);
	if (isset($_POST['change-password'])) {
		$newpassword = $_POST['newpassword'];
		$verify_newpassword = $_POST['verify_newpassword'];
		if (DB::query("SELECT token FROM password_token WHERE token = :token", $name = [':token' => $token])) {
			$user_id = DB::query("SELECT user_id FROM password_token WHERE token = :token", $name = [':token' => $token])[0]['user_id'];
			if (strlen($newpassword) < 6 || strlen($newpassword) > 60) {
					echo "Invalid New Password";
				} elseif ($newpassword != $verify_newpassword) {
					echo "Invalid Confirm Password";
				} else {
					DB::query("UPDATE users SET password=:password WHERE id=:id", $name = ['password' => password_hash("$newpassword", PASSWORD_BCRYPT, ["cost" => 12]), ':id' => $user_id]);
					echo "password changed";
					DB::query("DELETE FROM password_token WHERE token = :token", $name = [':token'=>$token]);
				}

		} else {
			echo "Invalid token!";
		}
	}
?>
	
<h1>Change your password</h1>
<form action="" method="POST">
	<input type="password" name="newpassword" placeholder="new password..."><p />
	<input type="password" name="verify_newpassword" placeholder="confirm new password..."><p />
	<input type="submit" name="change-password">
</form>

<?php } else { ?>
<?php  
	if (isset($_POST['submit-email'])) {
		$email = $_POST['email'];
		if ($user_id = DB::query("SELECT id FROM users WHERE email = :email", $name = [':email' => $email])) {
			$user_id = DB::query("SELECT id FROM users WHERE email = :email", $name = [':email' => $email])[0]['id'];
			$cstrong = TRUE;
			$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
			DB::query("INSERT INTO password_token(token,user_id) VALUES(:token,:user_id)", $name = [':token'=>sha1($token), ':user_id'=>$user_id]);
			echo $token;
		} else {
			echo "email invalid!";
		}
	}
?>
<h1>forgot your password</h1>
<form action="" method="POST">
	<input type="email" name="email" placeholder="someone@someone.someone"><p />
	<input type="submit" name="submit-email">
</form>
<?php } ?>