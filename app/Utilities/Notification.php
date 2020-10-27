<?php

namespace App\Utilities;

use App\Utilities\phpMQTT;

class Notification {

    private $mqtt_connection;

    //Hold the class instance
    private static $instance = null;

    private $host = "tailor.cloudmqtt.com"; 
    private $port = 11621;
    private $username = "gfuxuwzr";
    private $password = "Z4WwbATYPT6U";

   
    //establishing mqtt broker connection in the private constructor
    private function __construct(){

        $this->mqtt_connection = new phpMQTT($this->host,$this->port,"ClientID".rand());

    }
    
    public static function getInstance(){

        if(!self::$instance)
        {

            self::$instance = new Notification();
        }

        return self::$instance;
    }

    public function connect(){

        return $this->mqtt_connection->connect(true,NULL,$this->username,$this->password);
    }

    public function publish($topic,$message){

        if($this->connect()){

            $this->mqtt_connection->publish($topic,$message,1);
            $this->mqtt_connection->close();

        } else {

            echo "Fail or time out";
        }
    }
}