<?php
require_once 'models/db.php';

const ALGO_HASH = PASSWORD_DEFAULT;

class User
{
    private $name;
    private $password;
    private $color;
    private $id;
    private $levelIndustry;
    private $levelEnergy;
    private $nbIndustry;
    private $nbEnergy;
    private $nbCannon;
    private $nbOffensiveTroop;
    private $nbLogisticTroop;
    private $x;
    private $y;



    public function setColor($color)
    {
        # if color is format #rrggbb
        if (preg_match('/^#[a-f0-9]{6}$/i', $color)) {
            # set color uppercase
            $this->color = strtoupper($color);
            return true;
        }
    }

    public function setName($name)
    {
        # verfiy name with regex [a-zA-Z0-9]{3,16}
        if (preg_match('/^[a-zA-Z0-9_]{3,16}$/', $name)) {
            $this->name = $name;
            return true;
        }
        return false;
    }

    public function setPassword($password)
    {
        if (strlen($password) < 8) {
            return false;
        }
        $this->password = password_hash($password, ALGO_HASH);
        return true;
    }

    function getCostLevelUpIndustry(){
        # [costIndustry, costEnergy]
        $factor = pow(2, $this->levelIndustry);
        return [200 * $factor, 10 * $factor];
    }

    function upgradeIndustry(){
        $cost = $this->getCostLevelupIndustry();
        if($this->nbIndustry >= $cost[0] && $this->nbEnergy >= $cost[1]){
            $this->nbIndustry -= $cost[0];
            $this->nbEnergy -= $cost[1];
            $this->levelIndustry++;
            return true;
        }
        return false;
    }

    function getCostLevelUpEnergy(){
        # [costIndustry, costEnergy]
        $factor = pow(2, $this->levelEnergy);
        return [100 * $factor, 0];
    }

    function upgradeEnergy(){
        $cost = $this->getCostLevelupEnergy();
        if($this->nbIndustry >= $cost[0] && $this->nbEnergy >= $cost[1]){
            $this->nbIndustry -= $cost[0];
            $this->nbEnergy -= $cost[1];
            $this->levelEnergy++;
            return true;
        }
        return false;
    }

    public function update(){
        # update levelIndustry, levelEnergy, nbIndustry, nbEnergy, nbCannon, nbOffensiveTroop, nbLogisticTroop
        return true == $db->query("UPDATE user SET levelIndustry = ?, levelEnergy = ?, nbIndustry = ?, nbEnergy = ?, nbCannon = ?, nbOffensiveTroop = ?, nbLogisticTroop = ? WHERE id = ?", [$this->levelIndustry, $this->levelEnergy, $this->nbIndustry, $this->nbEnergy, $this->nbCannon, $this->nbOffensiveTroop, $this->nbLogisticTroop, $this->id]);
    }

    public function create()
    {
        # test if user already exist
        if ($db->query("SELECT id FROM user WHERE name = ?", [$this->name])->fetch()) {
            return false;
        }

        $x = rand(0, 500);
        $y = rand(0, 500);
        $max = 20;
        while (!$db->query("SELECT id FROM user WHERE x=? AND y=?", [$x, $y])->fetch() && $max > 0){
            $x = rand(0, 500);
            $y = rand(0, 500);
            $max--;
        }
        if($max == 0){
            die("TOO USERS");
        }

        # create user
        $res = $db->query("INSERT INTO user (x, y, name, password, color, levelIndustry, levelEnergy, nbIndustry, nbEnergy, nbCannon, nbOffensiveTroop, nbLogisticTroop) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [$x, $y, $this->name, $this->password, $this->color, $this->levelIndustry, $this->levelEnergy, $this->nbIndustry, $this->nbEnergy, $this->nbCannon, $this->nbOffensiveTroop, $this->nbLogisticTroop]);
        if ($res) {
            $this->id = $res->fetch_assoc()['id'];
            return true;
        }
        return false;
    }

    public function login()
    {
        # login user
        $res = $db->query("SELECT * FROM user WHERE name = ? AND password ?", [$this->name, $this->password]);
        if ($res) {
            $user = $res->fetch_assoc();
            $this->id = $user['id'];
            $this->name = $user['name'];
            $this->password = $user['password'];
            $this->color = $user['color'];
            $this->levelIndustry = $user['levelIndustry'];
            $this->levelEnergy = $user['levelEnergy'];
            $this->nbIndustry = $user['nbIndustry'];
            $this->nbEnergy = $user['nbEnergy'];
            $this->nbCannon = $user['nbCannon'];
            $this->nbOffensiveTroop = $user['nbOffensiveTroop'];
            $this->nbLogisticTroop = $user['nbLogisticTroop'];
            $this->x = $user['x'];
            $this->y = $user['y'];
            return true;
        }
        return false;
    }

    static public function updateNbIndustryAll(){
        return true == $db->query("SET @delta = (SELECT TIMESTAMPDIFF(SECOND, last_update, NOW()) FROM last_update);
UPDATE user SET nbIndustry = nbIndustry + @delta * POW(2, levelIndustry);
UPDATE last_update SET last_update = NOW() WHERE 1;");
    }
}