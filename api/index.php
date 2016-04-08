<?php

	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: POST, GET");

	require 'Slim/Slim.php';
	require_once 'KLogger.php';
	
	include 'db_scripts/db_connect.php';

  	include 'controllers/QuestionController.php';
	include 'controllers/UserSessionController.php';

	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();



/////////////////////// REGISTER NEW USER /////////////////////////////////////////////////////

	$app->post('/registerUser/', function(){

		if( isset($_POST['email_id']) ) {
			$email_id = $_POST['email_id'];
		}
		else{
			$responseObj = array('status' => false,
    			   'message' => "Insufficient Parameters",
    			);
			echo json_encode($responseObj);
			exit();
		}

		$log = KLogger::logger();
		$log->info("Creating New User Entry");

		$dbConnectionObject = new DBconnect();
		$DBConnection = $dbConnectionObject->ConnectToDB();

		$userSessionObject = new UserSessionController();
		$new_user_record = $userSessionObject->create_new_user( $DBConnection, $email_id );

		if( $new_user_record['status'] ){
			$log->info("User Created Successfully !");
			
			$responseObj = array('status' => true,
				   'message' => 'Successfully created new user.',
				);
		}
		else{
			$log->info("Error in Creating New User");

			$responseObj = array('status' => false,
				   'message' => 'Could not create new user.',
				);
		}

		$responseObj = json_encode($responseObj);
		echo $responseObj;

		exit();	

	});

///////////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////// ASK NEW QUESTION BY A USER /////////////////////////////////////////////////

	$app->post('/askQuestion/', function(){

		if( isset($_POST['user_id']) && isset($_POST['question_string']) && isset($_POST['category']) ) {
			$user_id = $_POST['user_id'];
			$question_text = $_POST['question_string'];
			$category = $_POST['category'];
		}
		else{
			$responseObj = array('status' => false,
    			   'message' => "Insufficient Parameters",
    			);
			echo json_encode($responseObj);
			exit();
		}

		$log = KLogger::logger();
		$log->info("Creating New Question Entry");

		$dbConnectionObject = new DBconnect();
		$DBConnection = $dbConnectionObject->ConnectToDB();

		$userSessionObject = new UserSessionController();
		$new_question_record = $userSessionObject->create_new_question( $DBConnection, $user_id, $question_text, $category );

		if( $new_question_record['status'] ){
			$log->info("Question Added Successfully !");

			$responseObj = array('status' => true,
				   'message' => 'Added New Question Successfully.',
				);
		}
		else{
			$log->info("Error in Creating New Question");

			$responseObj = array('status' => false,
				   'message' => 'Could not add new Question.',
				);
		}

		$responseObj = json_encode($responseObj);
		echo $responseObj;

		exit();	

	});

/////////////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////// ADD A WATCHER TO A QUESTION ///////////////////////////////////////

	$app->post('/watchQuestion/', function(){
		$log = KLogger::logger();
		if( isset($_POST['user_id']) && isset($_POST['question_id']) ) {

			$user_id = $_POST['user_id'];
			$question_id = $_POST['question_id'];
		}
		else{
			$responseObj = array('status' => false,
    			   'message' => "Insufficient Parameters",
    			);
			echo json_encode($responseObj);
			exit();
		}


		$log->info("Adding new watcher to question : ".$question_id);

		$dbConnectionObject = new DBconnect();
		$DBConnection = $dbConnectionObject->ConnectToDB();

		$questionObject = new QuestionController();
		$watch_question_response = $questionObject->watch_question( $DBConnection, $user_id, $question_id );

		if( $watch_question_response['status'] ){
			$log->info("Watcher Added Successfully !");
			$responseObj = array('status' => true,
				   'message' => 'Successfully Added the User as a watcher to the Question.',
				);
		}
		else{
			$log->info("Error in Creating New Watcher for the Question");

			$responseObj = array('status' => false,
				   'message' => 'Could not add new watcher to the Question.',
				);
		}

		$responseObj = json_encode($responseObj);
		echo $responseObj;

		exit();	


	});

/////////////////////////////////////////////////////////////////////////////////////////////////////////////


///////////////////////// GET QUESTIONS LIST ///////////////////////////////////////////////////

	$app->get('/getQuestionList/', function(){

		if( isset($_GET['user_id']) ) {
			$user_id = $_GET['user_id'];
		}
		else{
			$responseObj = array('status' => false,
    			   'message' => "Insufficient Parameters",
    			);
			echo json_encode($responseObj);
			exit();
		}

		$log = KLogger::logger();
		$log->info("Getting All Questions List");

		$dbConnectionObject = new DBconnect();
		$DBConnection = $dbConnectionObject->ConnectToDB();

		$userSessionObject = new UserSessionController();
		$user_record = $userSessionObject->validate_user_id( $DBConnection, $user_id );

		if( $user_record['status'] ){
			$log->info("User ID validated. Record Found !");
		}
		else{
			$log->info("Invalid User ID");

			$responseObj = array('status' => false,
				   'message' => 'Invalid User ID'
				);

			$responseObj = json_encode($responseObj);
			echo $responseObj;

			exit();	
		}

		$questionObject = new QuestionController();
		$question_list = $questionObject->get_question_list( $DBConnection, $user_id );

		if( $question_list['status'] ){
			$log->info("Got all questions !");
			$responseObj = array('status' => true,
				   'data' => $question_list['questions_list']
				);
		}
		else{
			$log->info("Error in Getting Questions List");

			$responseObj = array('status' => false,
				   'message' => 'Could not get questions list ! '
				);
		}

		$responseObj = json_encode($responseObj);
		echo $responseObj;

		exit();	

	});
///////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////// ANSWER QUESTION ////////////////////////////////////////////////////

	$app->post('/answerQuestion/', function(){

		if( isset($_POST['user_id']) && isset($_POST['question_id']) && isset($_POST['answer_string']) ) {
			$user_id = $_POST['user_id'];
			$question_id = $_POST['question_id'];
			$answer_string = $_POST['answer_string'];
		}
		else{
			$responseObj = array('status' => false,
    			   'message' => "Insufficient Parameters",
    			);
			echo json_encode($responseObj);
			exit();
		}

		$log = KLogger::logger();
		$log->info("Adding new answer to question : ".$question_id." by user ID : ".$user_id);

		$dbConnectionObject = new DBconnect();
		$DBConnection = $dbConnectionObject->ConnectToDB();

		$questionObject = new QuestionController();
		$add_answer_response = $questionObject->add_answer( $DBConnection, $user_id, $question_id, $answer_string );

		if( $new_question_record['status'] ){
			$log->info("Answer Added Successfully !");
			
			$responseObj = array('status' => true,
				   'message' => 'Successfully Added new watcher to the Question.',
				);
		}
		else{
			$log->info("Error in Creating New Answer for the Question");

			$responseObj = array('status' => false,
				   'message' => 'Could not add new watcher to the Question.',
				);

		}

		$responseObj = json_encode($responseObj);
		echo $responseObj;

		exit();	

	});

/////////////////////////////////////////////////////////////////////////////////////////
	
////////////////////////// ANSWER QUESTION ////////////////////////////////////////////////////

	$app->post('/modifyAnswer/', function(){

		if( isset($_POST['user_id']) && isset($_POST['question_id']) && isset($_POST['answer_id']) &&  isset($_POST['new_answer_string']) ) {
			$user_id = $_POST['user_id'];
			$question_id = $_POST['question_id'];
			$answer_id = $_POST['answer_id'];
			$new_answer_string = $_POST['new_answer_string'];
		}
		else{
			$responseObj = array('status' => false,
    			   'message' => "Insufficient Parameters",
    			);
			echo json_encode($responseObj);
			exit();
		}

		$log = KLogger::logger();
		$log->info("Modifying answer with id : ".$answer_id." for question : ".$question_id." by user ID : ".$user_id);

		$dbConnectionObject = new DBconnect();
		$DBConnection = $dbConnectionObject->ConnectToDB();

		$questionObject = new QuestionController();
		$add_answer_response = $questionObject->modify_answer( $DBConnection, $user_id, $question_id, $answer_id , $new_answer_string );

		if( $new_question_record['status'] ){
			$log->info("Answer Modified Successfully !");
			
			$responseObj = array('status' => true,
				   'message' => 'Successfully Modified Your Answer to the Question.',
				);
		}
		else{
			$log->info("Error in Modifying Answer for the Question");

			$responseObj = array('status' => false,
				   'message' => 'Could not modify answer to the Question.',
				);

		}

		$responseObj = json_encode($responseObj);
		echo $responseObj;

		exit();	

	});
///////////////////////////////////////////////////////////////////////////////////////////
	$app->run();
?>
