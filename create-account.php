<?php include 'classes/db.php'; ?>
<?php  
	if (isset($_POST['create_user'])) {
		$username = $_POST['username'];
		$email = $_POST['email'];
		$password = $_POST['password'];

		//validation
		if(DB::query("SELECT username FROM users WHERE username = :username", $name = [':username' => $username])) {
			echo 'username already exsist!';
		} elseif (strlen($username) < 3 || strlen($username) > 32) {
			echo "Invalid Username";
		} elseif (!preg_match('/[a-zA-Z0-9_]+/', $username)) {
			echo "Invalid Username";
		} elseif (DB::query("SELECT email FROM users WHERE email = :email", $name = [':email' => $email])) {
			echo "Email already exsist!";
		} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			echo "Invalid Email";
		} elseif (strlen($password) < 6 || strlen($username) > 60) {
			echo "Invalid Password";
		}
		else {
			DB::query("INSERT INTO users(username,email,password) VALUES(:username,:email,:password)", $name = [':username'=>$username, ':email'=>$email, ':password'=>password_hash("$password", PASSWORD_BCRYPT, ["cost" => 12])]);
			echo 'User Created';
		}
	}
?>
<h1>Register</h1>
<form action="" method="POST">
	<input type="text" name="username" placeholder="Username ..."><p />
	<input type="email" name="email" placeholder="Email ..."><p />
	<input type="password" name="password" placeholder="Password ..."><p />
	<input type="submit" name="create_user" value="Submit">
</form>