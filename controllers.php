<?php
require_once 'models/User.php';
require_once 'models/Pages.php';
require_once 'models/AttackEvent.php';
require_once 'models/Alert.php';
require_once 'models/Message.php';


function init(){
    //date_default_timezone_set('Europe/Paris'); comment for compare with db
    session_start();
    if (isset($_SESSION["id"])){
        if(isset($_GET["page"]) && $_GET["page"] == Pages::IS_UPDATE){
            return;
        }
        $user = User::getUserById($_SESSION["id"]);
        if ($user != null){
            User::updateNbIndustryAll();
            AttackEvent::updateEvent();
            $_SESSION["user"] = $user;
            User::setUpdate($_SESSION["id"], true);
        }
    }
}

function isLogged(){
    return isset($_SESSION["id"]);
}

function checkLogged(){
    if (!isLogged()){
        Alert::pushAlert("You must be logged in to access this page", Alert::ERROR);
        Pages::redirect(Pages::HOME);
    }
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
    if(isset($color) && isset($name) && isset($password) && $user->setColor($color) && $user->setName($name) && $user->setPassword($password) && $user->create()){
        $_SESSION["id"] = $user->getId();
        Alert::pushAlert("Account created", Alert::SUCCESS);
        Pages::redirect(Pages::GAME);
    }
    Alert::pushAlert("Error while creating account (username is already taken)", Alert::ERROR);
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
    Alert::pushAlert("Error while logging in (username or password is incorrect)", Alert::ERROR);
    Pages::redirect(Pages::HOME);
}

function logout(){
    session_destroy();
    Pages::redirect(Pages::HOME);
}

function purchase(){
    checkLogged();
    if(!isset($_POST['type']) || !isset($_POST['nb'])) {
        Alert::pushAlert("Error while purchasing", Alert::ERROR);
        Pages::redirect(Pages::GAME);
    }
    $user = $_SESSION["user"];
    $type = $_POST["type"];
    $nb = $_POST["nb"];
    if(is_numeric($nb) && $user->purchase($type, intval($nb)) && $user->save()){
        Alert::pushAlert("Purchase successful", Alert::SUCCESS);
        Message::pushMessage($user->getId(), Message::TYPE_PERSONAL_NOTIFICATION, "You have purchased $nb $type");
        Pages::redirect(Pages::GAME);
    }
    Alert::pushAlert("Error while purchasing (you didn't have enough resources or you reached the purchase limit)", Alert::ERROR);
    Pages::redirect(Pages::GAME);
}
function levelUp(){
    checkLogged();
    if(!isset($_POST['type'])) {
        Alert::pushAlert("Error while leveling up", Alert::ERROR);
        Pages::redirect(Pages::GAME);
    }
    $user = $_SESSION["user"];
    $type = $_POST["type"];
    if($user->levelUp($type) && $user->save()){
        Alert::pushAlert("Level up successful", Alert::SUCCESS);
        Message::pushMessage($user->getId(), Message::TYPE_PERSONAL_NOTIFICATION, "You have leveled up your $type");
        Pages::redirect(Pages::GAME);
    }
    Alert::pushAlert("Error while leveling up (you didn't have enough resources or you reached the level limit 9)", Alert::ERROR);
    Pages::redirect(Pages::GAME);
}

function attack(){
    checkLogged();
    $user = $_SESSION["user"];
    if(!isset($_POST['idDefender']) || !isset($_POST['nbCannon']) || !isset($_POST['nbOffensiveTroop']) || !isset($_POST['nbLogisticTroop'])) {
        Alert::pushAlert("Error while attacking", Alert::ERROR);
        Pages::redirect(Pages::GAME);
    }
    $idDefender = $_POST["idDefender"];
    $nbCannon = $_POST["nbCannon"];
    $nbOffensiveTroop = $_POST["nbOffensiveTroop"];
    $nbLogisticTroop = $_POST["nbLogisticTroop"];
    if(is_numeric($idDefender) && is_numeric($nbCannon) && is_numeric($nbOffensiveTroop) && is_numeric($nbLogisticTroop) &&
        $user->attack(intval($idDefender), intval($nbCannon), intval($nbOffensiveTroop), intval($nbLogisticTroop)) && $user->save()){
        Alert::pushAlert("Attack successful", Alert::SUCCESS);
        Message::pushMessage($idDefender, Message::TYPE_PERSONAL_NOTIFICATION, "You have been attacked by ".$user->getName()." ! with ".$nbCannon." cannons, ".$nbOffensiveTroop." offensive troops and ".$nbLogisticTroop." logistic troops");
        Message::pushMessage($user->getId(), Message::TYPE_PERSONAL_NOTIFICATION, "You have attacked ".User::getUserById($idDefender)->getName()." ! with ".$nbCannon." cannons, ".$nbOffensiveTroop." offensive troops and ".$nbLogisticTroop." logistic troops");
        User::setUpdate($idDefender);
        sleep(1);
        Pages::redirect(Pages::GAME);
    }
    Alert::pushAlert("Error while attacking (you didn't have enough resources)", Alert::ERROR);
    Pages::redirect(Pages::GAME);
}

function message(){
    checkLogged();
    $user = $_SESSION["user"];
    if(!isset($_POST['message'])) {
        Alert::pushAlert("Error while sending message", Alert::ERROR);
        Pages::redirect(Pages::GAME);
    }
    $message = $_POST["message"];
    if (Message::pushMessage($user->getId(), Message::TYPE_CHAT_ALL, $message)){
        Alert::pushAlert("Message sent", Alert::SUCCESS);
        User::setUpdateAll($user->getId(), false);
        Pages::redirect(Pages::GAME);
    }
    Alert::pushAlert("Error while sending message (0<length<255)", Alert::ERROR);
    Pages::redirect(Pages::GAME);
}

function isUpdate(){
    if(!isLogged())
        Pages::returnJSON(["status" => "error", "message" => "You are not logged"]);
    if (User::isUpdate($_SESSION["id"])){
        Pages::returnJSON(["status" => "update", "message" => ""]);
    }
    Pages::returnJSON(["status" => "noUpdate", "message" => ""]);

}


function home(){
    if(isLogged())
        Pages::redirect(Pages::GAME);
    Pages::render("home.php");
}

function game(){
    checkLogged();
    $user = $_SESSION["user"];
    $users = User::getAllUsers();
    $attackEvents = AttackEvent::getAttackEventByUserId($user->getId());
    $messages = Message::getMessagesForUser($user->getId());
    $usersById = [];
    foreach ($users as $u){
        $usersById[$u->getId()] = $u;
    }
    Pages::render("game.php", ["user" => $user, "users" => $users, "attackEvents" => $attackEvents, "messages" => $messages, "usersById" => $usersById]);
}