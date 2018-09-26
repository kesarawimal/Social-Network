<?php include 'classes/db.php'; ?>
<?php include 'classes/login.php'; ?>
<?php  
	if (!Login::isloggedin()) {
		die('Not logged in!');
	} else {
		$cookie_token = sha1($_COOKIE['snid']);
		if (isset($_POST['logout'])) {
			if (isset($_POST['alldevice'])) {
				DB::query("DELETE FROM login_token WHERE user_id = :user_id", $name = [':user_id'=>Login::isloggedin()]);
			} else {
				DB::query("DELETE FROM login_token WHERE token = :token", $name = [':token'=>$cookie_token]);
			}
			setcookie('snid', '1', time()-3600);
			setcookie('snid_', '1', time()-3600);
		}
	}
?>

<h1>Log out from your acount</h1>
<p>are you sure you want to log out?</p>
<form action="" method="POST">
	<p><input type="checkbox" name="alldevice" value="alldevice">logout from all devices</p>
	<input type="submit" name="logout" value="logout">
</form>