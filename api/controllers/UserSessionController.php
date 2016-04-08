<?php

	require_once(dirname(__DIR__).'/KLogger.php');
	require_once(dirname(__DIR__).'/Config/Constants.php');
	require_once(dirname(__DIR__).'/Config/LoadConfig.php');

	class UserSessionController{

		public $log;

		function __construct(){
			$this->log = KLogger::logger();
		}

		function validate_user_id( $conn, $user_id ){

			$this->log->info("Validating User ID : ".$user_id);

			try{
				$db = $conn->mydb;
				$this->log->info("Database Selected --- ");

				$users_collection = $db->createCollection("users");
			    $this->log->info("Collection Users Selected Successfully");

			   	$user_record = array( 
	        		"email_id" => $user_id
	    		);
				
				$doc = $users_collection->findOne($user_record);
				if( !empty($doc) ){
				    $this->log->info("User Matching Record found successfully in users collection");	
					$responseObj = array('status' => true    
						);
				}else{
			    	$this->log->info("User Matching Document not found in the Users Collection : ".$e);	
					$responseObj = array('status' => false
    					);					
				}

			}catch(Exception $e){
		    	$this->log->info("Exception Occured while Matching Document not found in the Users Collection : ".$e);	
				$responseObj = array('status' => false
    				);	
			}
			
			return $responseObj;

		}

		function create_new_user( $conn, $email_id ){

			$this->log->info("Creating New User with Email ID : ".$email_id);

			// Create New Document in Users Collection
			try{
				$db = $conn->mydb;
				$this->log->info("Database Selected --- ");

				$users_collection = $db->createCollection("users");
			    $this->log->info("Collection Users Selected Successfully");

			   	$user_record = array( 
	        		"email_id" => $email_id 
	        		// "name" => "test user 1" 
	      			// "number" => 9013440592,
	    		);
		
			    $users_collection->insert($user_record);
			    $this->log->info("New Documents inserted successfully into users collection");	

				$responseObj = array('status' => true    
					);
			}catch(Exception $e){
			    $this->log->info("Exception occured in inserting new Document in the User Collection : ".$e);	
				$responseObj = array('status' => false
    				);	
			}
			
			return $responseObj;

		}

		function create_new_question( $conn, $user_id, $question_text, $category ){

			$this->log->info("Creating New Question for User ID : ".$user_id.", Question : ".$question_text.", category : ".$category);

			// Create New Document in Questions Collection
			try{
				$db = $conn->mydb;
				$this->log->info("Database Selected --- ");

				$questions_collection = $db->createCollection("questions");
			    $this->log->info("Collection Questions Selected Successfully");

			    $watchers_list = array();
			    array_push($watchers_list, $user_id);
			    $answers = array();

			   	$question_record = array( 
	        		"question_text" => $question_text,
	        		"category" => $category, 
	      			"user_id" => $user_id,
	      			"watchers" => $watchers_list,
	      			"answers" => $answers
	    		);
		
			    $questions_collection->insert($question_record);
			    $this->log->info("New Documents inserted successfully into questions collection");	

				$responseObj = array('status' => true    
					);
			}catch(Exception $e){
			    $this->log->info("Exception occured in inserting new Document in the Questions Collection : ".$e);	
				$responseObj = array('status' => false
    				);	
			}
			
			return $responseObj;

		}

	}

?>
