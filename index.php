<?php
require_once 'controllers.php';
require_once 'models/Pages.php';

init();
switch ($_GET['page']){
    case Pages::HOME:
        home();
        break;
    case Pages::GAME:
        game();
        break;
    case Pages::LOGIN:
        login();
        break;
    case Pages::REGISTER:
        createAccount();
        break;
    case Pages::LOGOUT:
        logout();
        break;
    case Pages::PURCHASE:
        purchase();
        break;
    case Pages::ATTACK:
        attack();
        break;
    case Pages::LEVEL_UP:
        levelUp();
        break;
    default:
        Pages::redirect(Pages::HOME);
        break;
}