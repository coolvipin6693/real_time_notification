<?php

class LoadConfig {
	
	private static $configArray;
	
	static function init()
    {
		$configFile = "/home/yogesh/config/config_pubsub.ini";	 // Set this path as per the location of your config.ini file
		self::$configArray =  parse_ini_file ($configFile, true);

		if(!self::$configArray) {
			throw new Exception("Could not load environment config settings. Aborting...");
			exit;
		}		
	}	
	
	static function getConfigArray() {
		return self::$configArray;
	}	

    static function getDbName() { 
        return self::$configArray['database']['db_name'];
    }

	static function getPusher_AppID() { 
		return self::$configArray['pusher']['app_id'];
	}
    static function getPusher_AppKey() { 
        return self::$configArray['pusher']['app_key'];
    }
    static function getPusher_AppSecret() { 
        return self::$configArray['pusher']['app_secret'];
    }

}

LoadConfig::init();

?>
