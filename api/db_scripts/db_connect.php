<?php
	
	require_once(dirname(__DIR__).'/Config/LoadConfig.php');
	require_once(dirname(__DIR__).'/KLogger.php');

	class DBconnect{

		public $db_name;
		public $servername;

		function __construct(){

			$this->log = KLogger::logger();

			$this->db_name = LoadConfig::getDbName();
		
		}

		function ConnectToDB(){
			$conn = new MongoClient(); // Connects to localhost:27017

			if (!$conn) {
			    die("Connection failed: " . mysqli_connect_error());
			}else{
				$this->log->info("Connection to database successfully...");				
			}

   			$db = $conn->mydb;
			$this->log->info("Database Selected Successfully");

   			return $conn;
		}

	}
?>