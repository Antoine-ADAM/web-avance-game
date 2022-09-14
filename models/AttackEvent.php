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
    # 0 is in progress, 1 is won, 2 is lost
    private $status;
    private $nbCannonRemainingWinner;
    private $nbOffensiveTroopRemainingWinner;
    private $nbEnergySteal;
    private $nbIndustrySteal;
    private $nbCannonRemainingDefenderLoser;
    private $nbOffensiveTroopRemainingDefenderLoser;
    private $defenderX;
    private $defenderY;
    private $attackerX;
    private $attackerY;

    function start($idAttacker, $idDefender, $nbCannon, $nbOffensiveTroop, $defenderX, $defenderY, $attackerX, $attackerY)
    {
        $this->idAttacker = $idAttacker;
        $this->idDefender = $idDefender;
        $this->nbCannon = $nbCannon;
        $this->nbOffensiveTroop = $nbOffensiveTroop;
        $this->defenderX = $defenderX;
        $this->defenderY = $defenderY;
        $this->attackerX = $attackerX;
        $this->attackerY = $attackerY;
        $this->startDateTime = date('Y-m-d H:i:s');
        $this->finalDateTime = date('Y-m-d H:i:s', time()+round(pow(pow($attackerX-$defenderX, 2)+pow($attackerY-$defenderY, 2), 0.5))*5);
        $this->status = 0;
        $res = $db->query("INSERT INTO attack_event (idAttacker, idDefender, finalDateTime, startDateTime, nbCannon, nbOffensiveTroop, status, defenderX, defenderY, attackerX, attackerY) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [$this->idAttacker, $this->idDefender, $this->finalDateTime, $this->startDateTime, $this->nbCannon, $this->nbOffensiveTroop, $this->status, $this->defenderX, $this->defenderY, $this->attackerX, $this->attackerY]);
    }
    function createTable()
    {
        $db->query("CREATE TABLE IF NOT EXISTS attack_event (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            idAttacker INT(6) UNSIGNED NOT NULL,
            idDefender INT(6) UNSIGNED NOT NULL,
            finalDateTime DATETIME NOT NULL,
            startDateTime DATETIME NOT NULL,
            nbCannon INT(6) UNSIGNED NOT NULL,
            nbOffensiveTroop INT(6) UNSIGNED NOT NULL,
            status INT(6) UNSIGNED NOT NULL,
            nbCannonRemainingWinner INT(6) UNSIGNED,
            nbOffensiveTroopRemainingWinner INT(6) UNSIGNED,
            nbEnergySteal INT(6) UNSIGNED,
            nbIndustrySteal INT(6) UNSIGNED,
            nbCannonRemainingDefenderLoser INT(6) UNSIGNED,
            nbOffensiveTroopRemainingDefenderLoser INT(6) UNSIGNED,
            defenderX INT(6) UNSIGNED NOT NULL,
            defenderY INT(6) UNSIGNED NOT NULL,
            attackerX INT(6) UNSIGNED NOT NULL,
            attackerY INT(6) UNSIGNED NOT NULL,
            FOREIGN KEY (idAttacker) REFERENCES user(id),
            FOREIGN KEY (idDefender) REFERENCES user(id)
        )");
    }


}