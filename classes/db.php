<?php  
	class DB
	{
		private static function connect()
		{
			$servername = "localhost";
			$username = "root";
			$password = "root";

			try {
			    $conn = new PDO("mysql:host=$servername;dbname=socialnetwork;charset=utf8", $username, $password);
			    // set the PDO error mode to exception
			    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			    return $conn;
			    }
			catch(PDOException $e)
			    {
			    echo "Connection failed: " . $e->getMessage();
			    }
		}

		public static function query($query, $params = array())
		{
			$statement = self::connect()->prepare($query);
			$statement->execute($params);

			if (explode(' ' , $query)[0] == 'SELECT') {
				$data = $statement->fetchAll();
				return $data;
			}
		}
	}
?>