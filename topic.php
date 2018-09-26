<?php include 'classes/db.php'; ?>
<?php include 'classes/login.php'; ?>
<?php include 'classes/comment.php'; ?>
<?php  
	if (!Login::isloggedin()) {
		die('Not Logged In');
	} elseif (!isset($_GET['topic'])) {
		die('topic not found');
	} else {

		$user_id = Login::isloggedin();


		//like button check
		if (isset($_POST['like'])) {
			if (!DB::query("SELECT * FROM post_likes WHERE user_id = :user_id AND post_id = :post_id", $name = [':user_id' => $user_id, 'post_id' => $_GET['post_id']])) {

				DB::query("INSERT INTO post_likes(user_id,post_id) VALUES(:user_id,:post_id)", $name = [':user_id' => $user_id, 'post_id' => $_GET['post_id']]);
				DB::query("UPDATE posts SET likes = likes + 1 WHERE id=:id", $name = ['id' => $_GET['post_id']]);
			} else {
				DB::query("DELETE FROM post_likes WHERE user_id = :user_id AND post_id = :post_id", $name = [':user_id' => $user_id, 'post_id' => $_GET['post_id']]);
				DB::query("UPDATE posts SET likes = likes - 1 WHERE id=:id", $name = ['id' => $_GET['post_id']]);
			}
		}

		//comment submision
		if (isset($_POST['comment_submit'])) {
			$body = $_POST['body'];
			$post_id = $_GET['post_id'];
			if (strlen($body) < 3 || strlen($body) > 160) {
				echo "Invalid Post Body!";
			} else {
				Comment::createcomment($body,$user_id,$post_id);
			}
		}

		function link_add($text) {
			$topic = "#" . $_GET['topic'];
			$words = explode(" ", $text);
			$string = '';
			foreach ($words as $word) {
				if (substr($word, 1) == "php") {
					if (substr($word, 0, 1) == "@") {
						$string .= '<a href="profile.php?username=' . substr($word, 1) . '">' . htmlspecialchars($word) . '</a> ';
					} elseif (substr($word, 0, 1) == "#") {
						$string .= '<a href="topic.php?topic=' . substr($word, 1) . '">' . htmlspecialchars($word) . '</a> ';
					} else {
						$string .= htmlspecialchars($word) . ' ';
					}
				} else {
				echo "%F0%9F%8C%90";
				}
			}
			return $string;
		}

		$dbposts = DB::query("SELECT users.username, posts.id, posts.body, posts.likes FROM posts,followers,users WHERE posts.user_id = followers.user_id AND users.id = posts.user_id AND followers.follower_id = :id ORDER BY posts.likes DESC", $name = [':id' => $user_id]);

		foreach ($dbposts as $post) {
			//echo htmlspecialchars($post['body']) . " ~ " . htmlspecialchars($post['username']) . "<br /> Likes " . $post['likes'] . "<br /><hr />";

			$isliked = DB::query("SELECT * FROM post_likes WHERE user_id = :user_id AND post_id = :post_id", $name = [':user_id' => $user_id, 'post_id' => $post['id']]);
			if ($isliked) {
				echo link_add($post['body']) . " ~ " . htmlspecialchars($post['username']) . "<br />" . '<form action="index.php?post_id=' . $post['id'] . '" method="POST"><input type="submit" name="like" value="Unlike"><span>  '.$post['likes']. ' likes</span></form>'; 

				echo '<form action="index.php?post_id=' . $post['id'] . '" method="POST"><textarea cols="40" rows="5" name="body"></textarea><input type="submit" name="comment_submit" value="Post"></form>';
				if (Comment::showcomments($post['id'])) {
					foreach (Comment::showcomments($post['id']) as $comment) {
					echo htmlspecialchars($comment['body']) . " ~ " . htmlspecialchars($comment['username']) . "<br />" . "<hr />";
					}
				}
				echo "<hr />";
				
			} else {
				echo link_add($post['body']) . " ~ " . htmlspecialchars($post['username']) . "<br />" . '<form action="index.php?post_id=' . $post['id'] . '" method="POST"><input type="submit" name="like" value="Like"><span>  '.$post['likes']. ' likes</span></form>'; 

				echo '<form action="index.php?post_id=' . $post['id'] . '" method="POST"><textarea cols="40" rows="5" name="body"></textarea><input type="submit" name="comment_submit" value="Post"></form>' . "<hr />";

				if (Comment::showcomments($post['id'])) {
					foreach (Comment::showcomments($post['id']) as $comment) {
					echo htmlspecialchars($comment['body']) . " ~ " . htmlspecialchars($comment['username']) . "<br />" . "<hr />";
					}
				}
				echo "<hr />";
			}
		}
	}
?>
