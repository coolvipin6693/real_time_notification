<?php
	require_once(dirname(__DIR__).'/vendor/autoload.php');

	require_once(dirname(__DIR__).'/KLogger.php');
	require_once(dirname(__DIR__).'/Config/Constants.php');
	require_once(dirname(__DIR__).'/Config/LoadConfig.php');

	class NotificationController{

		public $log;

		function __construct(){
			$this->log = KLogger::logger();
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

			    	array_push($notifications_list, $question_obj);
			    }

				$responseObj = array('status' => true,
									'notifications_list' => $notifications_list    
					);
			}catch(Exception $e){
			    $this->log->info("Exception occured in getting Notifications List : ".$e);	
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
