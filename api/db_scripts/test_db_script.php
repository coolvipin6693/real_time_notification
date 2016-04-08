<?php
	
	$conn = new MongoClient(); // connects to localhost:27017

	// Check connection
	if (!$conn) {
	    die("Connection failed: " . mysqli_connect_error());
	}else{
		echo "Connection to database successfully\n";				
	}

	// select a database
	$db = $conn->mydb;
	echo "Database gossip selected\n";

	$users_collection = $db->createCollection("users");
    echo "Collection Users Selected successfully\n";

	$questions_collection = $db->createCollection("questions");
    echo "Collection Users Selected successfully\n";


    //////////////// Inserting a new Document in a Collection ////////////////////
	$user_sample_record_one = array( 
      "email_id" => "test1@gmail.com", 
      "name" => "test user 1" 
      // "number" => 9013440592,
    );
	
	$user_sample_record_two = array( 
      "email_id" => "test2@gmail.com", 
      "name" => "test user 2" 
      // "number" => 9013440592,
    );

    $users_collection->insert($user_sample_record_one);
    $users_collection->insert($user_sample_record_two);
    echo "New Documents inserted successfully into users collection \n";

	$question_sample_record_one = array( 
      "question_text" => "What do you want motherfucker?", 
      "name" => "" 
      // "number" => 9013440592,
    );
	
	$question_sample_record_two = array( 
      "question_text" => "Nothing, Just Goofing Around !", 
      "name" => "test " 
      // "number" => 9013440592,
    );

    $users_collection->insert($user_sample_record_one);
    $users_collection->insert($user_sample_record_two);
    echo "New Documents inserted successfully into users collection \n";

    ///////////////////////////////////////////////////////////////////////////////

    ///////////////////////// Update a document of a collection ///////////////////
    
    // $collection->update(array("email_id"=>"test@gmail.com"), 
    // 	array('$set'=>array("email_id"=>"updated@gmail.com")));
    // echo "Document updated successfully\n";

    ///////////////////////////////////////////////////////////////////////////////

    ///////////////////// Delete Document of a collection /////////////////////////
    
    $collection->remove(array("email_id"=>"updated@gmail.com"),false);
    echo "Documents deleted successfully\n";
       
    ///////////////////////////////////////////////////////////////////////////////


    ///////////////////// List Documents of a Collection //////////////////////////

    $cursor = $collection->find();
    // iterate cursor to display title of documents
	
    foreach ($cursor as $document) {
    	echo $document["email_id"] . "\n";
    }

    ///////////////////////////////////////////////////////////////////////////////


?>