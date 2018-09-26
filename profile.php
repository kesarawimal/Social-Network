<?php include 'classes/db.php'; ?>
<?php include 'classes/login.php'; ?>
<?php  
	if (!Login::isloggedin()) {
		die('Not logged in!');
	}
	$follower_id = Login::isloggedin();
	if (isset($_GET['username'])) {
		if (DB::query("SELECT username FROM users WHERE username = :username", $name = [':username' => $_GET['username']])) {

			$username = DB::query("SELECT username FROM users WHERE username = :username", $name = [':username' => $_GET['username']])[0]['username'];
			$user_id = DB::query("SELECT id FROM users WHERE username = :username", $name = [':username' => $_GET['username']])[0]['id'];
			$verified = DB::query("SELECT verified FROM users WHERE username = :username", $name = [':username' => $_GET['username']])[0]['verified'];
			

			if (isset($_POST['follow'])) {
				if (!DB::query("SELECT follower_id FROM followers WHERE user_id = :user_id AND follower_id=:follower_id", $name = [':user_id' => $user_id, ':follower_id' => $follower_id])) {
					DB::query("INSERT INTO followers(user_id, follower_id) VALUES (:user_id,:follower_id)", $name = [':user_id' => $user_id, ':follower_id' => $follower_id]);

					//verification
					if (DB::query("SELECT verified FROM users WHERE id = :id", $name = [':id' => $follower_id])[0]['verified'] == 1) {
						DB::query("UPDATE users SET verified = :verified WHERE id=:id", $name = [':verified' => 1, ':id' => $user_id]);
					}
					echo "You are followed!";
				} else {
					//$isfollow = TRUE;
					echo "User Already followed!";
				}
			}

			if (isset($_POST['unfollow'])) {
				if (DB::query("SELECT follower_id FROM followers WHERE user_id = :user_id AND follower_id=:follower_id", $name = [':user_id' => $user_id, ':follower_id' => $follower_id])) {
					
					DB::query("DELETE FROM followers WHERE user_id = :user_id AND follower_id=:follower_id", $name = [':user_id' => $user_id, ':follower_id' => $follower_id]);
					echo "You are Unfollowed!";
				} else {
					//$isfollow = FALSE;
					echo "User Not followed!";
				}
			}
			//check profile verified or not!
			$isfollow = DB::query("SELECT follower_id FROM followers WHERE user_id = :user_id AND follower_id=:follower_id", $name = [':user_id' => $user_id, ':follower_id' => $follower_id]);

			//post submition
			if (isset($_POST['post'])) {
				$body = $_POST['body'];
				if (strlen($body) < 3 || strlen($body) > 160) {
					echo "Invalid Post Body!";
				} else {
					DB::query("INSERT INTO posts(body,user_id) VALUES(:body,:user_id)", $name = [':body'=>$body, ':user_id'=>$follower_id]);
					echo 'Post Created';
				}
			}

			

			//like button check
			if (isset($_POST['like'])) {
				if (!DB::query("SELECT * FROM post_likes WHERE user_id = :user_id AND post_id = :post_id", $name = [':user_id' => $follower_id, 'post_id' => $_GET['post_id']])) {

					DB::query("INSERT INTO post_likes(user_id,post_id) VALUES(:user_id,:post_id)", $name = [':user_id' => $follower_id, 'post_id' => $_GET['post_id']]);
					DB::query("UPDATE posts SET likes = likes + 1 WHERE id=:id", $name = ['id' => $_GET['post_id']]);
				} else {
					DB::query("DELETE FROM post_likes WHERE user_id = :user_id AND post_id = :post_id", $name = [':user_id' => $follower_id, 'post_id' => $_GET['post_id']]);
					DB::query("UPDATE posts SET likes = likes - 1 WHERE id=:id", $name = ['id' => $_GET['post_id']]);
				}
			}

 
			//posts retriew
			$dbposts = DB::query("SELECT * FROM posts WHERE user_id = :user_id ORDER BY id DESC", $name = [':user_id' => $user_id]);
			$posts = '';
			foreach ($dbposts as $key) {
				$isliked = DB::query("SELECT * FROM post_likes WHERE user_id = :user_id AND post_id = :post_id", $name = [':user_id' => $follower_id, 'post_id' => $key['id']]);
				if ($isliked) {
					$posts .= htmlspecialchars($key['body']) . "<br />" . '<form action="profile.php?username=' . $username . '&post_id=' . $key['id'] . '" method="POST"><input type="submit" name="like" value="Unlike"><span>  '.$key['likes']. ' likes</span></form>' . "<hr />"; 
				} else {
					$posts .= htmlspecialchars($key['body']) . "<br />" . '<form action="profile.php?username=' . $username . '&post_id=' . $key['id'] . '" method="POST"><input type="submit" name="like" value="Like"><span>  '.$key['likes']. ' likes</span></form>' . "<hr />"; 
				}
			}


		} else {
			die('User Not found');
		}
	} else {
		die('User Not found');
	}
?>

<h1><?php echo $username; ?>'s Profile <?php if($verified) echo "- Verified"; ?></h1>
<?php if ($user_id != $follower_id) { ?>
	<form action="profile.php?username=<?php echo $username; ?>" method="POST">
		<?php if ($isfollow == TRUE) { ?>
			<input type="submit" name="unfollow" value="Unfollow">
		<?php } else { ?>
			<input type="submit" name="follow" value="Follow">
		<?php } ?>
	</form>
<?php } ?>

<?php if ($user_id == $follower_id) { ?>
<form action="profile.php?username=<?php echo $username; ?>" method="POST">
	<textarea name="body" cols="30" rows="15"></textarea>
	<input type="submit" name="post">
</form>
<?php } ?>
<div>
	<?php echo $posts; ?>
</div>