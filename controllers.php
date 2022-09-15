<?php
require_once 'models/User.php';
require_once 'models/Pages.php';
require_once 'models/AttackEvent.php';


function init(){
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
        Pages::returnJSON(["error" => "not logged"]);
    $user = $_SESSION["user"];
    $type = $_POST['type'];
    $nb = $_POST['nb'];
    if(isset($type) && isset($nb) && is_numeric($nb) && $user->purchase($type, intval($nb)) && $user->update()){
        Pages::returnJSON(["success" => "purchase done"]);
    }
    Pages::returnJSON(["error" => "purchase failed"]);
}
function levelUp(){
    if(!isLogged())
        Pages::returnJSON(["error" => "not logged"]);
    $user = $_SESSION["user"];
    $type = $_POST['type'];
    if(isset($type) && $user->levelUp($type) && $user->update()){
        Pages::returnJSON(["success" => "level up done"]);
    }
    Pages::returnJSON(["error" => "level up failed"]);
}

function attack(){
    if(!isLogged())
        Pages::returnJSON(["error" => "not logged"]);
    $user = $_SESSION["user"];
    $idDefender = $_POST['idDefender'];
    $nbCannon = $_POST['nbCannon'];
    $nbOffensiveTroop = $_POST['nbOffensiveTroop'];
    $nbLogisticTroop = $_POST['nbLogisticTroop'];
    if(isset($idDefender) && isset($nbCannon) && isset($nbOffensiveTroop) && isset($nbLogisticTroop) &&
        is_int($idDefender) && is_int($nbCannon) && is_int($nbOffensiveTroop) && is_int($nbLogisticTroop) &&
        $user->attack($idDefender, $nbCannon, $nbOffensiveTroop, $nbLogisticTroop)){
        Pages::returnJSON(["success" => "attack done"]);
    }
    Pages::returnJSON(["error" => "attack failed"]);
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