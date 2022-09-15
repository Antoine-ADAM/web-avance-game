<?php
require_once "db.php";
class AttackEvent
{
    private $id;
    private $idAttacker;
    private $idDefender;
    private $finalDateTime;
    private $startDateTime;
    private $nbCannon;
    private $nbOffensiveTroop;
    private $nbLogisticTroop;
    # 0 is in progress, 1 is won for attacker, 2 is lost for attacker, 3 battle without winner
    private $status;
    private $nbCannonLossAttacker;
    private $nbOffensiveTroopLossAttacker;
    private $nbLogisticTroopLossAttacker;
    private $nbIndustrySteal;
    private $nbCannonLossDefender;
    private $nbOffensiveTroopLossDefender;
    private $nbLogisticTroopLossDefender;
    private $defenderX;
    private $defenderY;
    private $attackerX;
    private $attackerY;

    function start($idAttacker, $idDefender, $nbCannon, $nbOffensiveTroop, $nbLogisticTroop)
    {
        $this->idAttacker = $idAttacker;
        $this->idDefender = $idDefender;
        $this->nbCannon = $nbCannon;
        $this->nbOffensiveTroop = $nbOffensiveTroop;
        $this->nbLogisticTroop = $nbLogisticTroop;
        #I don't use the relativity convention in sql to avoid "joins" everywhere
        $res = MyDB::getDB()->query("SELECT x, y FROM user WHERE id = $idDefender");
        if ($res) {
            $user = $res->fetch_assoc();
            $this->defenderX = $user['x'];
            $this->defenderY = $user['y'];
        }
        $res = MyDB::getDB()->query("SELECT x, y FROM user WHERE id = $idAttacker");
        if ($res) {
            $user = $res->fetch_assoc();
            $this->attackerX = $user['x'];
            $this->attackerY = $user['y'];
        }
        $this->startDateTime = date('Y-m-d H:i:s');
        $this->finalDateTime = date('Y-m-d H:i:s', time()+round(pow(pow($this->attackerX-$this->defenderX, 2)+pow($this->attackerY-$this->defenderY, 2), 0.5))*5);
        $this->status = 0;
        $res = MyDB::getDB()->query("INSERT INTO attack_event (idAttacker, idDefender, finalDateTime, startDateTime, nbCannon, nbOffensiveTroop, status, defenderX, defenderY, attackerX, attackerY) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [$this->idAttacker, $this->idDefender, $this->finalDateTime, $this->startDateTime, $this->nbCannon, $this->nbOffensiveTroop, $this->status, $this->defenderX, $this->defenderY, $this->attackerX, $this->attackerY]);
        return $res!=false;
    }
    function createTable()
    {
        MyDB::getDB()->query("CREATE TABLE IF NOT EXISTS attack_event (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            idAttacker INT(6) UNSIGNED NOT NULL,
            idDefender INT(6) UNSIGNED NOT NULL,
            finalDateTime DATETIME NOT NULL,
            startDateTime DATETIME NOT NULL,
            nbCannon INT(6) UNSIGNED NOT NULL,
            nbOffensiveTroop INT(6) UNSIGNED NOT NULL,
            nbLogisticTroop INT(6) UNSIGNED NOT NULL,
            status INT(1) UNSIGNED NOT NULL,
            nbCannonLossAttacker INT(6) UNSIGNED,
            nbOffensiveTroopLossAttacker INT(6) UNSIGNED,
            nbLogisticTroopLossAttacker INT(6) UNSIGNED,
            nbIndustrySteal INT(6) UNSIGNED,
            nbCannonLossDefender INT(6) UNSIGNED,
            nbOffensiveTroopLossDefender INT(6) UNSIGNED,
            nbLogisticTroopLossDefender INT(6) UNSIGNED,
            defenderX INT(6) UNSIGNED NOT NULL,
            defenderY INT(6) UNSIGNED NOT NULL,
            attackerX INT(6) UNSIGNED NOT NULL,
            attackerY INT(6) UNSIGNED NOT NULL
        )");
    }
    function loadFromResponseSql($response){
        $this->id = $response['id'];
        $this->idAttacker = $response['idAttacker'];
        $this->idDefender = $response['idDefender'];
        $this->finalDateTime = $response['finalDateTime'];
        $this->startDateTime = $response['startDateTime'];
        $this->nbCannon = $response['nbCannon'];
        $this->nbOffensiveTroop = $response['nbOffensiveTroop'];
        $this->nbLogisticTroop = $response['nbLogisticTroop'];
        $this->status = $response['status'];
        $this->nbCannonLossAttacker = $response['nbCannonLossAttacker'];
        $this->nbOffensiveTroopLossAttacker = $response['nbOffensiveTroopLossAttacker'];
        $this->nbLogisticTroopLossAttacker = $response['nbLogisticTroopLossAttacker'];
        $this->nbIndustrySteal = $response['nbIndustrySteal'];
        $this->nbCannonLossDefender = $response['nbCannonLossDefender'];
        $this->nbOffensiveTroopLossDefender = $response['nbOffensiveTroopLossDefender'];
        $this->nbLogisticTroopLossDefender = $response['nbLogisticTroopLossDefender'];
        $this->defenderX = $response['defenderX'];
        $this->defenderY = $response['defenderY'];
        $this->attackerX = $response['attackerX'];
        $this->attackerY = $response['attackerY'];
    }
    function loadFromId($id)
    {
        $res = MyDB::getDB()->query("SELECT * FROM attack_event WHERE id = $id");
        if ($res) {
            $this->loadFromResponseSql($res->fetch_assoc());
            return true;
        }
        return false;
    }

    function attackCalculation($nbCannon, $nbOffensiveTroop, &$pointAttack){
        $pointAttack = 0;
        $pointAttack += $nbCannon*7;
        $pointAttack += $nbOffensiveTroop*rand(1, 5);
    }

    function lossCalculation(&$nbCannon, &$nbOffensiveTroop, &$nbLogisticTroop, $pointAttack){
        $pointDefense = 0;
        $pointDefense += $nbCannon*7;
        $pointDefense += $nbOffensiveTroop*5;
        $pointDefense += $nbLogisticTroop*5;
        $ratio = $pointAttack/$pointDefense;
        $nbCannon = round($nbCannon*(1-$ratio));
        $nbOffensiveTroop = round($nbOffensiveTroop*(1-$ratio));
        $nbLogisticTroop = round($nbLogisticTroop*(1-$ratio));
    }

    function attack(){
        $step = 5;
        $userDefender = new User();
        $userDefender->loadFromId($this->idDefender);
        $nbCannonDefender = $userDefender->getNbCannon();
        $nbOffensiveTroopDefender = $userDefender->getNbOffensiveTroop();
        $nbLogisticTroopDefender = $userDefender->getNbLogisticTroop();
        $nbCannonAttacker = $this->nbCannon;
        $nbOffensiveTroopAttacker = $this->nbOffensiveTroop;
        $nbLogisticTroopAttacker = $this->nbLogisticTroop;
        while ($this->status == 0) {
            $step--;
            $this->attackCalculation($nbCannonAttacker, $nbOffensiveTroopAttacker, $powerAttack);
            $this->attackCalculation($nbCannonDefender, $nbOffensiveTroopDefender, $powerDefense);
            $this->lossCalculation($nbCannonAttacker, $nbOffensiveTroopAttacker, $nbLogisticTroopAttacker, $powerDefense);
            $this->lossCalculation($nbCannonDefender, $nbOffensiveTroopDefender, $nbLogisticTroopDefender, $powerAttack);
            if ($nbCannonDefender <= 0 && $nbOffensiveTroopDefender <= 0) {
                $this->status = 1;
                $this->nbCannonLossDefender = $userDefender->getNbCannon();
                $this->nbOffensiveTroopLossDefender = $userDefender->getNbOffensiveTroop();
                $this->nbLogisticTroopLossDefender = $userDefender->getNbLogisticTroop();
                $this->nbCannonLossAttacker = $this->nbCannon - $nbCannonAttacker;
                $this->nbOffensiveTroopLossAttacker = $this->nbOffensiveTroop - $nbOffensiveTroopAttacker;
                $this->nbLogisticTroopLossAttacker = $this->nbLogisticTroop - $nbLogisticTroopAttacker;
                $maxIndustrySteal = $nbLogisticTroopAttacker*50;
                if ($maxIndustrySteal > $userDefender->getNbIndustry()) {
                    $this->nbIndustrySteal = $userDefender->getNbIndustry();
                    $userDefender->setNbIndustry(0);
                } else {
                    $userDefender->setNbIndustry($userDefender->getNbIndustry() - $maxIndustrySteal);
                    $this->nbIndustrySteal = $maxIndustrySteal;
                }
                $userDefender->setNbCannon($nbCannonDefender);
                $userDefender->setNbOffensiveTroop($nbOffensiveTroopDefender);
                $userDefender->setNbLogisticTroop($nbLogisticTroopDefender);
                $userDefender->save();
                $userAttacker = new User();
                $userAttacker->loadFromId($this->idAttacker);
                $userAttacker->setNbCannon($userAttacker->getNbCannon() + $this->nbCannonLossAttacker);
                $userAttacker->setNbOffensiveTroop($userAttacker->getNbOffensiveTroop() + $this->nbOffensiveTroopLossAttacker);
                $userAttacker->setNbLogisticTroop($userAttacker->getNbLogisticTroop() + $this->nbLogisticTroopLossAttacker);
                $userAttacker->setNbIndustry($userAttacker->getNbIndustry() + $this->nbIndustrySteal);
                $userAttacker->save();
            }elseif ($nbCannonAttacker <= 0 && $nbOffensiveTroopAttacker <= 0) {
                $this->status = 2;
                $this->nbCannonLossDefender = $userDefender->getNbCannon() - $nbCannonDefender;
                $this->nbOffensiveTroopLossDefender = $userDefender->getNbOffensiveTroop() - $nbOffensiveTroopDefender;
                $this->nbLogisticTroopLossDefender = $userDefender->getNbLogisticTroop() - $nbLogisticTroopDefender;
                $this->nbCannonLossAttacker = $this->nbCannon;
                $this->nbOffensiveTroopLossAttacker = $this->nbOffensiveTroop;
                $this->nbLogisticTroopLossAttacker = $this->nbLogisticTroop;
                $userDefender->setNbCannon($nbCannonDefender);
                $userDefender->setNbOffensiveTroop($nbOffensiveTroopDefender);
                $userDefender->setNbLogisticTroop($nbLogisticTroopDefender);
                $userDefender->save();
            }elseif ($step <= 0) {
                $this->status = 3;
                $this->nbCannonLossDefender = $userDefender->getNbCannon() - $nbCannonDefender;
                $this->nbOffensiveTroopLossDefender = $userDefender->getNbOffensiveTroop() - $nbOffensiveTroopDefender;
                $this->nbLogisticTroopLossDefender = $userDefender->getNbLogisticTroop() - $nbLogisticTroopDefender;
                $this->nbCannonLossAttacker = $this->nbCannon - $nbCannonAttacker;
                $this->nbOffensiveTroopLossAttacker = $this->nbOffensiveTroop - $nbOffensiveTroopAttacker;
                $this->nbLogisticTroopLossAttacker = $this->nbLogisticTroop - $nbLogisticTroopAttacker;
                $userDefender->setNbCannon($nbCannonDefender);
                $userDefender->setNbOffensiveTroop($nbOffensiveTroopDefender);
                $userDefender->setNbLogisticTroop($nbLogisticTroopDefender);
                $userDefender->save();
                $userAttacker = new User();
                $userAttacker->loadFromId($this->idAttacker);
                $userAttacker->setNbCannon($userAttacker->getNbCannon() + $this->nbCannonLossAttacker);
                $userAttacker->setNbOffensiveTroop($userAttacker->getNbOffensiveTroop() + $this->nbOffensiveTroopLossAttacker);
                $userAttacker->setNbLogisticTroop($userAttacker->getNbLogisticTroop() + $this->nbLogisticTroopLossAttacker);
                $userAttacker->save();
            }

        }
    }

    static function updateEvent(){
        $res = MyDB::getDB()->query("SELECT * FROM attack_event WHERE finalDateTime < NOW()");
        while ($res) {
            $event = new AttackEvent();
            $event->loadFromResponseSql($res->fetch_assoc());
            $event->attack();
        }
    }

    static function getAllEvent(){
        $res = MyDB::getDB()->query("SELECT * FROM attack_event");
        $events = [];
        while ($res) {
            $event = new AttackEvent();
            $event->loadFromResponseSql($res->fetch_assoc());
            $events[] = $event;
        }
        return $events;
    }


}