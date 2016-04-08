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
		
				$questions_collection->update(array("_id" => new MongoId($question_id)),array('$push' => array("watchers" => $user_id)));
			    $this->log->info("New Watcher added successfully to question : ".$question_id);	

			    // Trigger Notification for NEW_WATCHER_EVENT
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
						"answer_id" => generateAnswerID( $user_id, $question_id )
					);		
				$questions_collection->update(array("_id"=> new MongoId($question_id) ),array('$push' => array("answers" => $answer_obj)));
			    
			    $this->log->info("New Answer added successfully to question : ".$question_id);	

			    // Trigger Notification for NEW_ANSWER_EVENT
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

			    // Trigger Notification for NEW_ANSWER_EVENT
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
			    	$question_obj = array('id' => $document["_id"],
			    							'question_text' => $document["question_text"],
			    							'category' => $document["category"],
			    							'asked_by_user' => $document["user_id"],
			    							'watchers' => $document["watchers"],
			    							'answers' => $document["answers"]
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
