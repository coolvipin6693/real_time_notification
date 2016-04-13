# Real Time Notification System

To demonstrate a real time Publish / Subscribe Notification system, this project illustrates a 
simple Q&A forum, wherein users can post questions and all the users can answer those questions.
Users can "watch" a question, so that whenever any "change" is there in that question, the 'watcher/subscriber' would be notified via an instant notification. The "change" refers to any new answer that has been added to the question. Also, whenever any new user answers a question or starts following [ watching ] a question, thw owner of that question receive a notification.

Currently, this project does not involve real authentication system for users, users can simply register using their email id and login using the same. No password based authentication ahs been implemented for demonstrating the Pub/Sub system. 


----------------------- DATABASE STRUCTURE -----------------------------------------

To persist all the information, local installation of MongoDB has been used.


// Collections Created //

	{ users, questions, notifications }


// Sample Document Format in 'questions' collection //

	{
		"_id" : ObjectId("570d12e2ed06b73a53229b26"),
		"answers" : [
			{
				"user_id" : "a@b.com",
				"answer_string" : "Sample Answer 1",
				"answer_id" : "a@b.com-570d12e2ed06b73a53229b26"
			},
			{
				"user_id" : "c@d.com",
				"answer_string" : "Sample Answer 2",
				"answer_id" : "c@d.com-570d12e2ed06b73a53229b26"
			},
			{
				"user_id" : "e@f.com",
				"answer_string" : "Sample Answer 3",
				"answer_id" : "e@f.com-570d12e2ed06b73a53229b26"
			}
		],
		"category" : "Test",
		"question_text" : "This is a Test Question",
		"user_id" : "owner@demo.com",
		"watchers" : [
			"watcher@demo.com"
		]
	}

	"_id" : MongoId of the document created,

	"answers" : [] json object array containing all the answers given by various users for this particular question. Each answer object contains three key-value pairs :
		"user_id" : email id of the user who has given this answer,
		"answer_string" : Text of the answer given by the user,
		"answer_id" : System Generated unique ID for each answer, in our case, it is simply made by appeding the user email id and the mongoId of the question in reference, by using a '-' as a delimiter.

	"category" : Refers to the category of the question asked,

	"question_text": Text string containing the question

	"user_id": email id of th user who asked this question,

	"watchers": [] json object array containing email ids of all the users who are subscribed to this question. By default, whenever a user asks a new question, he is automatically added to the watcher's list




// Sample Document format in 'users' collection //


	{
		"_id" : ObjectId("570d0b32ed06b72c32229b26"),
		"email_id" : "demo@test.com"
	}

	"_id" : MongoId of the document created,
	"email_id" : email id of the user


// Sample Document format in 'notifications' collection //

	{
		"_id" : ObjectId("570d0b32ed06b72c32229b26"),
		"email_id" : "shweta@wegilant.com"
	}

	"_id" : MongoId of the document created,
	"email_id" : email id of the user

----------------------- DATABASE STRUCTURE ENDS -----------------------------------------



---------------------------------------- API Info ---------------------------------------

	All the API end points are made by appending the BASE_URL along with the API_URL

	BASE_URL : 'http://www.domain.com/gossip/api'

	So, the final API endpint for all the requests would be :
	BASE_URL + API_URL


	Response Format for all the api requests :

		{
			    
		    'meta':
		    		{
		    			'status': "STATUS_CODE",
		    			'message':"RESPONSE_MESSAGE"
		    		},

			'data':[]
    	
    	}

    	"meta" will contain the status code for the response and message would contain the corresponding API response message.

    	"data" will contain any relevant data that needs to be sent to the client side for the requests 

    Information about the API endpoints :

		1)	API_URL : '/registerUser/'

			Request Type : POST
			Request Parameters : {'email_id'}

		2) API_URL : '/loginUser/'

		   Request Type : POST
		   Request Parameters : {'email_id'}

		3) API_URL : '/askQuestion/'

		   Request Type : POST
		   Request Parameters : {'user_id', 'question_string', 'category'}

		4) API_URL : '/watchQuestion/'

		   Request Type : POST
		   Request Parameters : {'user_id', 'question_id'}

		5) API_URL : '/getQuestionList/'

		   Request Type : GET
		   Request Parameters : {'user_id'}

		6) API_URL : '/answerQuestion/'

		   Request Type : POST
		   Request Parameters : {'user_id', 'question_id', 'answer_string'}	

		7) API_URL : '/modifyAnswer/'

		   Request Type : POST
		   Request Parameters : {'user_id', 'question_id', 'answer_id', 'new_answer_string'}		   	   
		8) API_URL : '/getNotificationList/'

		   Request Type : GET
		   Request Parameters : {'user_id'}


