<?php

class Alert
{
    const TYPES = [
        "success" => "alert-success",
        "danger" => "alert-danger",
        "warning" => "alert-warning",
        "info" => "alert-info",
    ];
    const SUCCESS = "success";
    const WARNING = "warning";
    const INFO = "info";
    const ERROR = "danger";

    public static function pushAlert(string $message, string $type)
    {
        if (isset(self::TYPES[$type])) {
            if($_SESSION["alerts"]==null){
                $_SESSION["alerts"] = [];
            }
            $_SESSION["alerts"][] = [
                "message" => htmlspecialchars($message),
                "type" => self::TYPES[$type]
            ];
        }
    }

    public static function getAlerts(){
        if(!isset($_SESSION) OR !isset($_SESSION["alerts"])){
            return array();
        }
        $alerts = $_SESSION["alerts"];
        $_SESSION["alerts"] = [];
        return $alerts;
    }

    public static function displayAlerts(){
        foreach (self::getAlerts() as $alert){
            echo "<div class=\"alert ".$alert["type"]."\" role=\"alert\">".$alert["message"]."</div>";
        }
    }


}