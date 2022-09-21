<?php
require_once 'models/User.php';
require_once 'models/Pages.php';
require_once 'models/AttackEvent.php';


function init(){
    //date_default_timezone_set('Europe/Paris'); comment for compare with db
    session_start();
    if (isset($_SESSION["id"])){
        $user = User::getUserById($_SESSION["id"]);
        if ($user != null){
            User::updateNbIndustryAll();
            AttackEvent::updateEvent();
            $_SESSION["user"] = $user;
        }
    }
}

function isLogged(){
    return isset($_SESSION["user"]);
}

function getJsonRequest($issetKeys = []){
    $json = json_decode(file_get_contents('php://input'), true);
    foreach ($issetKeys as $key){
        if (!isset($json[$key])){
            Pages::returnJSON(["error" => "Missing $key"]);
        }
    }
    return $json;
}

function createAccount(){
    if(isLogged())
        Pages::redirect(Pages::GAME);
    $color = $_POST['color'];
    $name = $_POST['name'];
    $password = $_POST['password'];
    $user = new User();
    var_dump($color, $name, $password);
    if(isset($color) && isset($name) && isset($password) && $user->setColor($color) && $user->setName($name) && $user->setPassword($password) && $user->create()){
        $_SESSION["id"] = $user->getId();
        Pages::redirect(Pages::GAME);
    }
    Pages::redirect(Pages::HOME);
}
function login(){
    if(isLogged())
        Pages::redirect(Pages::GAME);
    $name = $_POST['name'];
    $password = $_POST['password'];
    $user = new User();
    if(isset($name) && isset($password) && $user->setName($name) && $user->setPassword($password) && $user->login()){
        $_SESSION["id"] = $user->getId();
        Pages::redirect(Pages::GAME);
    }
    Pages::redirect(Pages::HOME);
}

function logout(){
    session_destroy();
    Pages::redirect(Pages::HOME);
}

function purchase(){
    if(!isLogged())
        Pages::redirect(Pages::HOME);
    if(!isset($_POST['type']) || !isset($_POST['nb']))
        Pages::redirect(Pages::GAME);
    $user = $_SESSION["user"];
    $type = $_POST["type"];
    $nb = $_POST["nb"];
    if(is_numeric($nb) && $user->purchase($type, intval($nb)) && $user->save()){
        Pages::redirect(Pages::GAME);
    }
    Pages::redirect(Pages::GAME);
}
function levelUp(){
    if(!isLogged())
        Pages::redirect(Pages::HOME);
    if(!isset($_POST['type']))
        Pages::redirect(Pages::GAME);
    $user = $_SESSION["user"];
    $type = $_POST["type"];
    if($user->levelUp($type) && $user->save()){
        Pages::redirect(Pages::GAME);
    }
    Pages::redirect(Pages::GAME);
}

function attack(){
    if(!isLogged())
        Pages::redirect(Pages::HOME);
    $user = $_SESSION["user"];
    if(!isset($_POST['idDefender']) || !isset($_POST['nbCannon']) || !isset($_POST['nbOffensiveTroop']) || !isset($_POST['nbLogisticTroop']))
        Pages::redirect(Pages::GAME);
    $idDefender = $_POST["idDefender"];
    $nbCannon = $_POST["nbCannon"];
    $nbOffensiveTroop = $_POST["nbOffensiveTroop"];
    $nbLogisticTroop = $_POST["nbLogisticTroop"];
    if(is_numeric($idDefender) && is_numeric($nbCannon) && is_numeric($nbOffensiveTroop) && is_numeric($nbLogisticTroop) &&
        $user->attack(intval($idDefender), intval($nbCannon), intval($nbOffensiveTroop), intval($nbLogisticTroop)) && $user->save()){
        Pages::redirect(Pages::GAME);
    }
    Pages::redirect(Pages::GAME);
}


function home(){
    if(isLogged())
        Pages::redirect(Pages::GAME);
    Pages::render("home.php");
}

function game(){
    if(!isLogged())
        Pages::redirect(Pages::HOME);
    $user = $_SESSION["user"];
    $users = User::getAllUsers();
    $attackEvents = AttackEvent::getAttackEventByUserId($user->getId());
    Pages::render("game.php", ["user" => $user, "users" => $users, "attackEvents" => $attackEvents]);
}