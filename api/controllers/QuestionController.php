<?php
	require_once(dirname(__DIR__).'/vendor/autoload.php');

	require_once(dirname(__DIR__).'/KLogger.php');
	require_once(dirname(__DIR__).'/Config/Constants.php');
	require_once(dirname(__DIR__).'/Config/LoadConfig.php');

	class QuestionController{

		public $log;

		function __construct(){
			$this->log = KLogger::logger();
		}

		function watch_question( $conn, $user_id, $question_id ){

			$this->log->info("Adding User ID : ".$user_id." to list of watchers for question id : ".$question_id);

			try{
				$db = $conn->mydb;
				$this->log->info("Database Selected --- ");

				$questions_collection = $db->createCollection("questions");
			    $this->log->info("Collection Questions Selected Successfully");
		
				$mongo_id = new MongoId($question_id);

				$questions_collection->update(array('_id' => $mongo_id,array('$push' => array("watchers" => $user_id))));
			    $this->log->info("New Watcher added successfully to question : ".$question_id);	

				$question_record = $collection->findOne(array("_id" => $mongo_id));
    			$question_owner_id = $question_record["user_id"] . "\n";

			    // Trigger Notification for NEW_WATCHER_EVENT
			    $this->persist_notification( $question_id, "NEW_WATCHER_ADDED : ".$user_id, $question_owner_id );
				$this->triggerPusherNotification( $question_id, "NEW_WATCHER", $user_id );

				$responseObj = array('status' => true    
					);
			}catch(Exception $e){
			    $this->log->info("Exception occured in inserting new Document in the Questions Collection : ".$e);	
				$responseObj = array('status' => false
    				);	
			}
			
			return $responseObj;

		}

		function add_answer( $conn, $user_id, $question_id, $answer_string ){
			
			$this->log->info("Adding New answer by User ID : ".$user_id." to list question id : ".$question_id);

			try{
				$db = $conn->mydb;
				$this->log->info("Database Selected --- ");

				$questions_collection = $db->createCollection("questions");
			    $this->log->info("Collection Questions Selected Successfully");

				$answer_obj = array(
						"user_id" => $user_id,
						"answer_string" => $answer_string,
						"answer_id" => $this->generateAnswerID( $user_id, $question_id )
					);		
				$questions_collection->update(array("_id"=> new MongoId($question_id) ),array('$push' => array("answers" => $answer_obj)));
			    
			    $this->log->info("New Answer added successfully to question : ".$question_id);	

				$question_record = $questions_collection->findOne(array("_id" => new MongoId($question_id)));
    			$question_owner_id = $question_record["user_id"] . "\n";

			    // Trigger Notification for NEW_ANSWER_EVENT
   			    $this->persist_notification( $question_id, "NEW_ANSWER_ADDED_BY_USER : ".$user_id, $question_owner_id );
				$this->triggerPusherNotification( $question_id, "NEW_ANSWER", $answer_obj );

				$responseObj = array('status' => true    
					);
			}catch(Exception $e){
			    $this->log->info("Exception occured in inserting new Answer in the Questions Collection : ".$e);	
				$responseObj = array('status' => false
    				);	
			}
			
			return $responseObj;

		}

		function modify_answer( $conn, $user_id, $question_id, $answer_id , $new_answer_string ){
			
			$this->log->info("Modifying answer : ".$answer_id." by User ID : ".$user_id." for question id : ".$question_id);

			try{
				$db = $conn->mydb;
				$this->log->info("Database Selected --- ");

				$questions_collection = $db->createCollection("questions");
			    $this->log->info("Collection Questions Selected Successfully");

				$new_answer_obj = array(
						"user_id" => $user_id,
						"answer_string" => $new_answer_string,
						"answer_id" => $answer_id
					);	

				// CHANGE THIS UPDATE CONDITION		
				$questions_collection->update(array("_id"=> new MongoId($question_id) ),array('$push' => array("answers" => $answer_obj)));
			    
			    $this->log->info("New Answer added successfully to question : ".$question_id);	

				$question_record = $collection->findOne(array("_id" => new MongoId($question_id)));
    			$question_owner_id = $question_record["user_id"] . "\n";

			    // Trigger Notification for NEW_ANSWER_EVENT
   			    $this->persist_notification( $question_id, "ANSWER_MODIFIED_BY_USER : ".$user_id, $question_owner_id );
				$this->triggerPusherNotification( $question_id, "ANSWER_MODIFIED", $new_answer_obj );

				$responseObj = array('status' => true    
					);
			}catch(Exception $e){
			    $this->log->info("Exception occured in modifying Answer in the Questions Collection : ".$e);	
				$responseObj = array('status' => false
    				);	
			}
			
			return $responseObj;

		}

		function get_question_list( $conn, $user_id ){
			
			$this->log->info("Fetching Questions List");

			try{
				$db = $conn->mydb;
				$this->log->info("Database Selected --- ");

				$questions_collection = $db->createCollection("questions");
			    $this->log->info("Collection Questions Selected Successfully");
		   
			    $cursor = $questions_collection->find();
				
				$questions_list = array();

			    foreach ($cursor as $document){

			    	if( $user_id == $document['user_id'] ){
			    		$is_owner = true;
			    	}else{
			    		$is_owner = false;
			    	}

			    	if( in_array($user_id, $document['watchers']) ){
			    		$is_watcher = true;
			    	}else{
			    		$is_watcher = false;
			    	}

			    	$question_obj = array('id' => $document["_id"]->{'$id'},
			    							'question_text' => $document["question_text"],
			    							'category' => $document["category"],
			    							'asked_by_user' => $document["user_id"],
			    							'answers' => $document["answers"],
			    							'is_owner' => $is_owner,
			    							'is_watcher' => $is_watcher,
			    		);

			    	array_push($questions_list, $question_obj);
			    }

				$responseObj = array('status' => true,
									'questions_list' => $questions_list    
					);
			}catch(Exception $e){
			    $this->log->info("Exception occured in getting Questions List : ".$e);	
				$responseObj = array('status' => false
    				);	
			}
			
			return $responseObj;

		}

		function generateAnswerID( $user_id, $question_id ){
			return $user_id."-".$question_id;
		}

		function persist_notification( $question_id, $notification_text, $question_owner_id ){

			$this->log->info("Creating New Notification for User ID : ".$question_owner_id.", Question : ".$question_id);

			// Create New Document in Notifications Collection
			try{
				$db = $conn->mydb;
				$this->log->info("Database Selected --- ");

				$notifications_collection = $db->createCollection("notifications");
			    $this->log->info("Notification Questions Selected Successfully");

			   	$notification_record = array( 
	        		"notification_text" => $notification_text, 
	      			"question_id" => $question_id,
	      			"question_owner_id" => $question_owner_id,
	      			"created_at" => date("Y-m-d h:i:sa")
	    		);
		
			    $notifications_collection->insert($notification_record);
			    $this->log->info("New Documents inserted successfully into notifications collection");	

				$responseObj = array('status' => true    
					);
			}catch(Exception $e){
			    $this->log->info("Exception occured in inserting new Document in the Notifications Collection : ".$e);	
				$responseObj = array('status' => false
    				);	
			}
			
			return $responseObj;
		}


		function get_notification_list(){
			
			$this->log->info("Fetching Notifications List");

			try{
				$db = $conn->mydb;
				$this->log->info("Database Selected --- ");

				$notifications_collection = $db->createCollection("notifications");
			    $this->log->info("Collection Notifications Selected Successfully");
		   
			    $cursor = $notifications_collection->find();
				
				$questions_list = array();

			    foreach ($cursor as $document){
			    	$question_obj = array('id' => $document["_id"]->{'$id'},
			    							'notification_text' => $document["notification_text"],
			    							'question_id' => $document["question_id"],
			    							'question_owner_id' => $document["question_owner_id"],
			    							'created_at' => $document["created_at"]
			    		);

			    	array_push($questions_list, $question_obj);
			    }

				$responseObj = array('status' => true,
									'questions_list' => $questions_list    
					);
			}catch(Exception $e){
			    $this->log->info("Exception occured in getting Questions List : ".$e);	
				$responseObj = array('status' => false
    				);	
			}
			
			return $responseObj;

		}

		function triggerPusherNotification( $channel_name, $event, $data_to_push ){	

		    $this->log->info("Sending Pusher Notification");	
			
			try{
				// $PUSHER_APP_ID = LoadConfig::getPusher_AppID();
				// $PUSHER_APP_SECRET = LoadConfig::getPusher_AppSecret();
				// $PUSHER_APP_KEY = LoadConfig::getPusher_AppKey();

				$PUSHER_APP_ID = '195774';
				$PUSHER_APP_SECRET = 'b1cbf6da736227789a93';
				$PUSHER_APP_KEY = '302a9c3ba67be4c3d5ee';

				$pusher = new Pusher($PUSHER_APP_KEY, $PUSHER_APP_SECRET, $PUSHER_APP_ID);
				$data['message'] = $data_to_push;
				$pusher->trigger($channel_name, $event, $data);

			}catch(Exception $e){
		    	$this->log->info("Error Occured in Sending Pusher Notification".$e);	
			}
			

		}


	}

?>
