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

    public static function getUserById($id)
    {
        $user = new User();
        if(!$user->loadFromId($id))
            return null;
        return $user;
    }

    public static function getAllUsers()
    {
        $db = MyDB::getDB();
        $result = $db->query("SELECT * FROM user");
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $user = new User();
            $user->loadFromResult($row);
            $users[] = $user;
        }

        return $users;
    }

    public static function isExist($id){
        $db = MyDB::getDB();
        $stmt = $db->prepare("SELECT id FROM user WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }


    public function setColor($color)
    {
        # if color is format #rrggbb
        if (preg_match('/^#[a-f0-9]{6}$/i', $color)) {
            # set color uppercase
            $this->color = strtoupper($color);
            return true;
        }
        return false;
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
        $this->password = $password;
        return true;
    }

    function getCostLevelUpIndustry()
    {
        # [costIndustry, costEnergy]
        if ($this->levelIndustry < 10) {
            $factor = pow(2, $this->levelIndustry);
            return [200 * $factor, 10 * $factor];
        }else
            return null;
    }

    function levelUpIndustry(){
        $cost = $this->getCostLevelupIndustry();
        if($cost != null && $this->nbIndustry >= $cost[0] && $this->nbEnergy >= $cost[1]){
            $this->nbIndustry -= $cost[0];
            $this->nbEnergy -= $cost[1];
            $this->levelIndustry++;
            return true;
        }
        return false;
    }

    function getCostLevelUpEnergy(){
        # [costIndustry, costEnergy]
        if ($this->levelEnergy < 10) {
            $factor = pow(2, $this->levelEnergy);
            return [100 * $factor, 0];
        }else{
            return null;
        }
    }

    function levelUpEnergy(){
        $cost = $this->getCostLevelupEnergy();
        if($cost != null && $this->nbIndustry >= $cost[0] && $this->nbEnergy >= $cost[1]){
            $this->nbIndustry -= $cost[0];
            $this->nbEnergy -= $cost[1];
            $this->nbEnergy+=200*pow(2, $this->levelEnergy);
            $this->levelEnergy++;
            return true;
        }
        return false;
    }

    public function save(){
        # update levelIndustry, levelEnergy, nbIndustry, nbEnergy, nbCannon, nbOffensiveTroop, nbLogisticTroop
        MyDB::query("UPDATE user SET levelIndustry = ?, levelEnergy = ?, nbIndustry = ?, nbEnergy = ?, nbCannon = ?, nbOffensiveTroop = ?, nbLogisticTroop = ? WHERE id = ?", [$this->levelIndustry, $this->levelEnergy, $this->nbIndustry, $this->nbEnergy, $this->nbCannon, $this->nbOffensiveTroop, $this->nbLogisticTroop, $this->id]);
        return true;
    }

    public function create()
    {
        # security check count user is less than 100
        $result = MyDB::getDB()->query("SELECT COUNT(*) FROM user");
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['COUNT(*)'] >= 100) {
                return false;
            }
        }

        # test if user already exist
        if (MyDB::query("SELECT id FROM user WHERE name LIKE ?", [$this->name])->fetch_row()) {
            return false;
        }

        $x = rand(0, 500);
        $y = rand(0, 500);
        $max = 20;
        while (MyDB::query("SELECT id FROM user WHERE x=? AND y=?", [$x, $y])->fetch_row() && $max > 0){
            $x = rand(0, 500);
            $y = rand(0, 500);
            $max--;
        }
        if($max == 0){
            die("TOO USERS");
        }
        # create user
        $res = MyDB::query("INSERT INTO user (x, y, name, password, color, levelIndustry, levelEnergy, nbIndustry, nbEnergy, nbCannon, nbOffensiveTroop, nbLogisticTroop) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [$x, $y, $this->name, password_hash($this->password, ALGO_HASH), $this->color, 0, 0, 500, 0, 0, 0, 0]);
        if (!$res) {
            $this->id = MyDB::getDB()->insert_id;
            return true;
        }
        return false;
    }

    public function login()
    {
        # login user
        $user = MyDB::query("SELECT * FROM user WHERE name LIKE ?", [$this->name])->fetch_assoc();
        if ($user && password_verify($this->password, $user['password'])) {
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

    function loadFromId($id){
        $res = MyDB::query("SELECT * FROM user WHERE id = ?", [$id]);
        if ($res && $res=$res->fetch_assoc()) {
            $this->loadFromResult($res);
            return true;
        }
        return false;
    }

    static public function updateNbIndustryAll(){
        MyDB::getDB()->multi_query("SET @delta = (SELECT TIMESTAMPDIFF(SECOND, last_update, NOW()) FROM last_update);
UPDATE user SET nbIndustry = nbIndustry + @delta * (POW(2, levelIndustry) * 5 - 5) WHERE nbIndustry + @delta * POW(2, levelIndustry) * 5 - 5  < 2000000000;
UPDATE last_update SET last_update = NOW() WHERE 1;");
        do {
            if ($result = MyDB::getDB()->store_result()) {
                $result->free();
            }
        } while (MyDB::getDB()->more_results() && MyDB::getDB()->next_result());
    }

    public function getNbCannon()
    {
        return $this->nbCannon;
    }

    public function getNbOffensiveTroop()
    {
        return $this->nbOffensiveTroop;
    }

    public function getNbLogisticTroop()
    {
        return $this->nbLogisticTroop;
    }

    public function setNbCannon($nbCannon)
    {
        $this->nbCannon = $nbCannon;
    }

    public function setNbOffensiveTroop($nbOffensiveTroop)
    {
        $this->nbOffensiveTroop = $nbOffensiveTroop;
    }

    public function setNbLogisticTroop($nbLogisticTroop)
    {
        $this->nbLogisticTroop = $nbLogisticTroop;
    }

    public function setNbIndustry($nbIndustry)
    {
        $this->nbIndustry = $nbIndustry;
    }

    public function cannonPurchase($nbCannon)
    {
        $cost = $this->getCostCannon($nbCannon);
        if($cost != null && $this->nbIndustry >= $cost[0] && $this->nbEnergy >= $cost[1]){
            $this->nbIndustry -= $cost[0];
            $this->nbEnergy -= $cost[1];
            $this->nbCannon += $nbCannon;
            return true;
        }
        return false;
    }

    private function getCostCannon($nbCannon)
    {
        if($nbCannon + $this->nbCannon > 2000000000){
            return null;
        }
        return [15 * $nbCannon,2 * $nbCannon];
    }

    public function offensiveTroopPurchase($nbOffensiveTroop)
    {
        $cost = $this->getCostOffensiveTroop($nbOffensiveTroop);
        if($cost != null && $this->nbIndustry >= $cost[0] && $this->nbEnergy >= $cost[1]){
            $this->nbIndustry -= $cost[0];
            $this->nbEnergy -= $cost[1];
            $this->nbOffensiveTroop += $nbOffensiveTroop;
            return true;
        }
        return false;
    }

    private function getCostOffensiveTroop($nbOffensiveTroop)
    {
        if($nbOffensiveTroop + $this->nbOffensiveTroop > 2000000000){
            return null;
        }
        return [10 * $nbOffensiveTroop, 0];
    }

    public function logisticTroopPurchase($nbLogisticTroop)
    {
        $cost = $this->getCostLogisticTroop($nbLogisticTroop);
        if($cost != null && $this->nbIndustry >= $cost[0] && $this->nbEnergy >= $cost[1]){
            $this->nbIndustry -= $cost[0];
            $this->nbEnergy -= $cost[1];
            $this->nbLogisticTroop += $nbLogisticTroop;
            return true;
        }
        return false;
    }

    private function getCostLogisticTroop($nbLogisticTroop)
    {
        if($nbLogisticTroop + $this->nbLogisticTroop > 2000000000){
            return null;
        }
        return [10 * $nbLogisticTroop, 0];
    }

    public function getX(){
        return $this->x;
    }

    public function getY(){
        return $this->y;
    }

    public function getId(){
        return $this->id;
    }
    const PURCHASES = [
        "cannon",
        "offensiveTroop",
        "logisticTroop"
    ];
    /**
     * type: "cannon", "offensiveTroop", "logisticTroop"
     * @return bool
     */
    public function purchase($type, $nb){
        if($nb <= 0){
            return false;
        }
        switch($type){
            case "cannon":
                return $this->cannonPurchase($nb);
            case "offensiveTroop":
                return $this->offensiveTroopPurchase($nb);
            case "logisticTroop":
                return $this->logisticTroopPurchase($nb);
        }
        return false;
    }

    const UPGRADES = [
        "industry",
        "energy"
    ];
    public function levelUp($type){
        switch($type){
            case "industry":
                return $this->levelUpIndustry();
            case "energy":
                return $this->levelUpEnergy();
        }
        return false;
    }

    public function attack($idDefender, $nbCannon, $nbOffensiveTroop, $nbLogisticTroop){
        if (!User::isExist($idDefender) or ($nbLogisticTroop == 0 and $nbCannon == 0 and $nbOffensiveTroop == 0) or $idDefender == $this->id or $nbCannon < 0 or $nbOffensiveTroop < 0 or $nbLogisticTroop < 0 or $nbCannon > $this->nbCannon or $nbOffensiveTroop > $this->nbOffensiveTroop or $nbLogisticTroop > $this->nbLogisticTroop) {
            return false;
        }
        $attackEvent = new AttackEvent();
        if($attackEvent->create($this->id, $idDefender, $nbCannon, $nbOffensiveTroop, $nbLogisticTroop)){
            $this->nbCannon -= $nbCannon;
            $this->nbOffensiveTroop -= $nbOffensiveTroop;
            $this->nbLogisticTroop -= $nbLogisticTroop;
            return true;
        }
        return false;
    }

    function loadFromResult($user)
    {
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
    }

    function getScores(){
        return $this->nbCannon*15 + $this->nbOffensiveTroop*10 + $this->nbLogisticTroop*10 + -200*(1-pow(2, $this->levelEnergy))+ -200*(1-pow(2, $this->levelIndustry));
    }

    function getLevelIndustry(){
        return $this->levelIndustry;
    }
    function getLevelEnergy(){
        return $this->levelEnergy;
    }
    function getNbIndustry(){
        return $this->nbIndustry;
    }
    function getNbEnergy(){
        return $this->nbEnergy;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    public static function isUpdate($id): bool
    {
        $st = MyDB::query("SELECT is_update FROM user WHERE id = ?", [$id]);
        return is_bool($st) or $st->fetch_assoc()['is_update'];
    }

    public static function setUpdate($id, $isUpdate = false){
        return MyDB::query("UPDATE user SET is_update = ? WHERE id = ?", [$isUpdate, $id]);
    }

}