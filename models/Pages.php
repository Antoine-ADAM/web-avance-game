<?php

class Pages
{
    # Pages
    const HOME = "home";
    const GAME = "game";

    # Actions
    const LOGIN = "login";
    const REGISTER = "register";
    const LOGOUT = "logout";
    const PURCHASE = "purchase";
    const ATTACK = "attack";


    static function toURL($page, $params = [])
    {
        $url = "index.php?page=$page";
        foreach ($params as $key => $value) {
            $url .= "&$key=$value";
        }
        return $url;
    }

    static function redirect($page, $params = [])
    {
        header("Location: " . Pages::toURL($page, $params));
        exit();
    }

    static function returnJSON($data){
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    static function render($page, $params = [])
    {
        extract($params);
        require_once "../views/$page";
    }
}