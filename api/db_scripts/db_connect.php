<?php
	
	require_once(dirname(__DIR__).'/Config/LoadConfig.php');
	require_once(dirname(__DIR__).'/KLogger.php');


	class DBconnect{

		public $db_name;
		public $servername;
		public $username;
		public $password;

		function __construct(){

			$this->log = KLogger::logger();

			$this->db_name = LoadConfig::getDbName();
			$this->servername = LoadConfig::getDbHost();
			$this->username = LoadConfig::getDbUser();
			$this->password = LoadConfig::getDbPassword();

		}

		function ConnectToDB(){
			$conn = new MongoClient(); // connects to localhost:27017
			// $connection = new MongoClient( "mongodb://example.com" ); // connect to a remote host (default port: 27017)
			// $connection = new MongoClient( "mongodb://example.com:65432" ); // connect to a remote host at a given port

			// Check connection
			if (!$conn) {
			    die("Connection failed: " . mysqli_connect_error());
			}else{
				$this->log->info("Connection to database successfully...");				
			}

   			// select a database
   			$db = $conn->mydb;
			$this->log->info("Database mydb selected...");				


   			return $conn;
		}


	}
?>