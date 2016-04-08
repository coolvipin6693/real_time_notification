<?php
	
	/* Finally, A light, permissions-checking logging class. 
	 * 
	 * Author	: Kenneth Katzgrau < katzgrau@gmail.com >
	 * Date	: July 26, 2008
	 * Comments	: Originally written for use with wpSearch
	 * Website	: http://codefury.net
	 * Version	: 1.0
	 *
	 * Usage: 
	 *		$log = new KLogger ( "log.txt" , KLogger::INFO );
	 *		$log->LogInfo("Returned a million search results");	//Prints to the log file
	 *		$log->LogFATAL("Oh dear.");				//Prints to the log file
	 *		$log->LogDebug("x = 5");					//Prints nothing due to priority setting
	*/
	// use backendModel\classes\LoadConfig;
	// include_once(__DIR__.'/../classes/LoadConfig.php');

	class KLogger
	{
		
		const DEBUG 	= 1;	// Most Verbose
		const INFO 		= 2;	// ...
		const WARN 		= 3;	// ...
		const ERROR 	= 4;	// ...
		const FATAL 	= 5;	// Least Verbose
		const OFF 		= 6;	// Nothing at all.
		
		const LOG_OPEN 		= 1;
		const OPEN_FAILED 	= 2;
		const LOG_CLOSED 	= 3;
		
		/* Public members: Not so much of an example of encapsulation, but that's okay. */
		public $Log_Status 	= KLogger::LOG_CLOSED;
		public $DateFormat	= "d M Y g:i:s A e";
		public $MessageQueue;
	
		private $log_file;
		private $priority = KLogger::DEBUG;
		
		public $file_handle;
		
		public function __construct( $filepath , $priority )
		{
			if ( $priority == KLogger::OFF ) return;
			
			$this->log_file = $filepath;
			$this->MessageQueue = array();
			$this->priority = $priority;
			
			if ( file_exists( $this->log_file ) )
			{
				if ( !is_writable($this->log_file) )
				{
					$this->Log_Status = KLogger::OPEN_FAILED;
					$this->MessageQueue[] = "The file exists, but could not be opened for writing. Check that appropriate permissions have been set.";
					return;
				}
			}
			else
			{
				fopen( $this->log_file , "w" );
			}
			
			if ( $this->file_handle = fopen( $this->log_file , "a" ) )
			{
				$this->Log_Status = KLogger::LOG_OPEN;
				$this->MessageQueue[] = "The log file was opened successfully.";
			}
			else
			{
				$this->Log_Status = KLogger::OPEN_FAILED;
				$this->MessageQueue[] = "The file could not be opened. Check permissions.";
			}
			//echo $this->file_handle;
			
			return;
		}
		
		public function __destruct()
		{
			if ( $this->file_handle )
				fclose( $this->file_handle );
		}
		
		public function info($line)
		{
			$backTrack = debug_backtrace();
			$fromLine = $backTrack[0]['line'];

			$this->Log( $line , KLogger::INFO,$fromLine );
		}
		
		public function debug($line)
		{
			$backTrack = debug_backtrace();
			$fromLine = $backTrack[0]['line'];
			//echo $this->file_handle;
			$this->Log( $line , KLogger::DEBUG,$fromLine );
		}
		
		public function warn($line)
		{
			$backTrack = debug_backtrace();
			$fromLine = $backTrack[0]['line'];
			$this->Log( $line , KLogger::WARN,$fromLine );	
		}
		
		public function error($line)
		{
			$backTrack = debug_backtrace();
			$fromLine = $backTrack[0]['line'];
			$this->Log( $line , KLogger::ERROR,$fromLine );		
		}

		public function fatal($line)
		{
			$backTrack = debug_backtrace();
			$fromLine = $backTrack[0]['line'];
			$this->Log( $line , KLogger::FATAL,$fromLine );
		}
		
		public function Log($line, $priority, $fromLine="Unknown")
		{
			if ( $this->priority <= $priority )
			{
				$status = $this->getTimeLine( $priority, $fromLine);
				//echo $this->file_handle;
				$this->WriteFreeFormLine ( "$status $line \n" );
			}
		}
		
		public function WriteFreeFormLine( $line )
		{
			if ( $this->Log_Status == KLogger::LOG_OPEN && $this->priority != KLogger::OFF )
			{
				// echo $this->file_handle;
			    if (fwrite( $this->file_handle , $line ) === false) {
			        $this->MessageQueue[] = "The file could not be written to. Check that appropriate permissions have been set.";
			    }
			}
		}
		
		private function getTimeLine( $level, $fromLine=null)
		{
			$time = date( $this->DateFormat );
			$logged_in_user_email = "Unknown";
			if(isset($_SESSION['appvigil_user']))
			{
				$user = (array)$_SESSION['appvigil_user'];
				$logged_in_user_email = $user['email'];
			}
			//$bt = debug_backtrace();
			//$caller = array_shift($bt);
			//$caller_file = $caller['file'];
			//$caller_line = $caller['line'];
			switch( $level )
			{
				case KLogger::INFO:
					return "[ INFO  ][$time][$logged_in_user_email][Line:$fromLine]";//return "[ $time ] - INFO  [$logged_in_user_email in Line: $fromLine]";
				case KLogger::WARN:
					return "[ WARN  ][$time][$logged_in_user_email][Line:$fromLine]";				
				case KLogger::DEBUG:
					return "[ DEBUG ][$time][$logged_in_user_email][Line:$fromLine]";				
				case KLogger::ERROR:
					return "[ ERROR ][$time][$logged_in_user_email][Line:$fromLine]";
				case KLogger::FATAL:
					return "[ FATAL ][$time][$logged_in_user_email][Line:$fromLine]";
				default:
					return "[ LOG   ][$time][$logged_in_user_email][Line:$fromLine]";
			}
		}
		
		public function logClose()
		{
				if ( $this->file_handle )
				fclose( $this->file_handle );
		}

		public static function logger()
		{
			$systemDate =  date('Y-m-d');
			return new KLogger (__DIR__."/log/$systemDate-Log.log" , 2 );
		}
		
	}


?>