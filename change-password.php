<?php include 'classes/db.php'; ?>
<?php include 'classes/login.php'; ?>
<?php  
	if (!Login::isloggedin()) {
		die("Not Logged In");
	} else {
		if (isset($_POST['change-password'])) {
			$oldpassword = $_POST['oldpassword'];
			$newpassword = $_POST['newpassword'];
			$verify_newpassword = $_POST['verify_newpassword'];

			if (password_verify($oldpassword, DB::query("SELECT password FROM users WHERE id = :id", $name = [':id' => Login::isloggedin()])[0]['password'])) {
				if (strlen($newpassword) < 6 || strlen($newpassword) > 60) {
					echo "Invalid New Password";
				} elseif ($newpassword != $verify_newpassword) {
					echo "Invalid Confirm Password";
				} else {
					DB::query("UPDATE users SET password=:password WHERE id=:id", $name = ['password' => password_hash("$newpassword", PASSWORD_BCRYPT, ["cost" => 12]), ':id' => Login::isloggedin()]);
					echo "password changed";
				}
			} else {
				echo "old password is wrong";
			}
		}
	}
?>
<h1>Change your password</h1>
<form action="" method="POST">
	<input type="password" name="oldpassword" placeholder="old password..."><p />
	<input type="password" name="newpassword" placeholder="new password..."><p />
	<input type="password" name="verify_newpassword" placeholder="confirm new password..."><p />
	<input type="submit" name="change-password">
</form>