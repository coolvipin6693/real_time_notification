<?php

class LoadConfig {
	
	private static $configArray;
	
	static function init()
    {
		$configFile = "/home/yogesh/config/config.ini";	
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
    static function getDbHost() { 
        return self::$configArray['database']['db_host'];
    }
    static function getDbUser() { 
        return self::$configArray['database']['db_user'];
    }
    static function getDbPassword() { 
        return self::$configArray['database']['db_pass'];
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
