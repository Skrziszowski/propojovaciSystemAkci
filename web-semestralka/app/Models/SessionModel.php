<?php

class SessionModel {
    public function __construct(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function addSession($name, $value){
        $_SESSION[$name] = $value;
    }

    public function readSession($name){
        if($this->isSession($name)){
            return $_SESSION[$name];
        }else{
            return null;
        }
    }

    public function isSession($name){
        return isset($_SESSION[$name]);
    }

    public function deleteSession($name){
        unset($_SESSION[$name]);
    }

}


?>