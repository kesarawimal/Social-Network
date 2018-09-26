<?php  
	class Comment
	{
		public static function createcomment($body,$user_id,$post_id)
		{
			if (DB::query("INSERT INTO comments(body,user_id,post_id) VALUES(:body,:user_id,:post_id)", $name = [':body'=>$body, ':user_id'=>$user_id, ':post_id'=>$post_id])) {
				return TRUE;
			} else  {
				return FALSE;
			}		
		}


		public static function showcomments($post_id)
		{
			if (DB::query("SELECT users.username,comments.body FROM comments,users WHERE comments.user_id = users.id AND post_id = :post_id", $name = [':post_id'=>$post_id])) {

				return DB::query("SELECT users.username,comments.body FROM comments,users WHERE comments.user_id = users.id AND post_id = :post_id", $name = [':post_id'=>$post_id]);

			} else  {
				return FALSE;
			}		
		}
	}
?>