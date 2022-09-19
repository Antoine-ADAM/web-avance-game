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

    public static function getAttackEventByUserId($getId)
    {
        $db = MyDB::getDB();
        $sql = "SELECT * FROM attack_event WHERE idAttacker = ? OR idDefender = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ii", $getId, $getId);
        $stmt->execute();
        $result = $stmt->get_result();
        $attackEvents = [];
        while ($row = $result->fetch_assoc()) {
            $attackEvent = new AttackEvent();
            $attackEvent->loadFromResponseSql($row);
            $attackEvents[] = $attackEvent;
        }
        return $attackEvents;
    }

    function create($idAttacker, $idDefender, $nbCannon, $nbOffensiveTroop, $nbLogisticTroop)
    {
        if ($idAttacker == $idDefender) {
            return false;
        }
        $this->idAttacker = $idAttacker;
        $this->idDefender = $idDefender;
        $this->nbCannon = $nbCannon;
        $this->nbOffensiveTroop = $nbOffensiveTroop;
        $this->nbLogisticTroop = $nbLogisticTroop;
        #I don't use the relativity convention in sql to avoid "joins" everywhere
        $res = MyDB::query("SELECT x, y FROM user WHERE id = ?", [$idDefender]);
        if ($res && $user = $res->fetch_assoc()) {
            $this->defenderX = $user['x'];
            $this->defenderY = $user['y'];
        }
        $res = MyDB::query("SELECT x, y FROM user WHERE id = ?", [$idAttacker]);
        if ($res && $user = $res->fetch_assoc()) {
            $this->attackerX = $user['x'];
            $this->attackerY = $user['y'];
        }
        $this->startDateTime = date('Y-m-d H:i:s');
        $this->finalDateTime = date('Y-m-d H:i:s', time()+round(pow(pow($this->attackerX-$this->defenderX, 2)+pow($this->attackerY-$this->defenderY, 2), 0.5))*5);
        $this->status = 0;
        $res = MyDB::query("INSERT INTO attack_event (idAttacker, idDefender, finalDateTime, startDateTime, nbCannon, nbOffensiveTroop, nbLogisticTroop, status, defenderX, defenderY, attackerX, attackerY) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [$this->idAttacker, $this->idDefender, $this->finalDateTime, $this->startDateTime, $this->nbCannon, $this->nbOffensiveTroop, $this->nbLogisticTroop, $this->status, $this->defenderX, $this->defenderY, $this->attackerX, $this->attackerY]);
        return true;
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
        if($pointDefense==0){
            $nbLogisticTroop = 0;
            $nbOffensiveTroop = 0;
            $nbCannon = 0;
        }else{
            $ratio = $pointAttack/$pointDefense;
            $nbCannon = round($nbCannon*(1-$ratio));
            $nbOffensiveTroop = round($nbOffensiveTroop*(1-$ratio));
            $nbLogisticTroop = round($nbLogisticTroop*(1-$ratio));
            if ($nbCannon<0) $nbCannon = 0;
            if ($nbOffensiveTroop<0) $nbOffensiveTroop = 0;
            if ($nbLogisticTroop<0) $nbLogisticTroop = 0;
        }

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
                $userAttacker->setNbCannon($userAttacker->getNbCannon() + $nbCannonAttacker);
                $userAttacker->setNbOffensiveTroop($userAttacker->getNbOffensiveTroop() + $nbOffensiveTroopAttacker);
                $userAttacker->setNbLogisticTroop($userAttacker->getNbLogisticTroop() + $nbLogisticTroopAttacker);
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
                $this->nbIndustrySteal = 0;
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
                $this->nbIndustrySteal = 0;
                $userDefender->setNbCannon($nbCannonDefender);
                $userDefender->setNbOffensiveTroop($nbOffensiveTroopDefender);
                $userDefender->setNbLogisticTroop($nbLogisticTroopDefender);
                $userDefender->save();
                $userAttacker = new User();
                $userAttacker->loadFromId($this->idAttacker);
                $userAttacker->setNbCannon($userAttacker->getNbCannon() + $nbCannonAttacker);
                $userAttacker->setNbOffensiveTroop($userAttacker->getNbOffensiveTroop() + $nbOffensiveTroopAttacker);
                $userAttacker->setNbLogisticTroop($userAttacker->getNbLogisticTroop() + $nbLogisticTroopAttacker);
                $userAttacker->save();
            }

        }
        $this->save();
    }

    public function getNbCannonLossAttacker()
    {
        return $this->nbCannonLossAttacker;
    }

    public function getNbOffensiveTroopLossAttacker()
    {
        return $this->nbOffensiveTroopLossAttacker;
    }

    public function getNbLogisticTroopLossAttacker()
    {
        return $this->nbLogisticTroopLossAttacker;
    }

    public function getNbCannonLossDefender()
    {
        return $this->nbCannonLossDefender;
    }

    public function getNbOffensiveTroopLossDefender()
    {
        return $this->nbOffensiveTroopLossDefender;
    }

    public function getNbLogisticTroopLossDefender()
    {
        return $this->nbLogisticTroopLossDefender;
    }

    public function getNbIndustrySteal()
    {
        return $this->nbIndustrySteal;
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

    public function getIdAttacker()
    {
        return $this->idAttacker;
    }

    public function getIdDefender()
    {
        return $this->idDefender;
    }
    /**
     * 0 is in progress, 1 is won for attacker, 2 is lost for attacker, 3 battle without winner
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    static function updateEvent(){
        $res = MyDB::getDB()->query("SELECT * FROM attack_event WHERE status = 0 AND finalDateTime < CONVERT_TZ(NOW(), @@session.time_zone, '+00:00')");
        while ($res && $sub = $res->fetch_assoc()) {
            $event = new AttackEvent();
            $event->loadFromResponseSql($sub);
            $event->attack();
        }
    }

    static function getAllEvent(){
        $res = MyDB::getDB()->query("SELECT * FROM attack_event");
        $events = [];
        while ($res && $row = $res->fetch_assoc()) {
            $event = new AttackEvent();
            $event->loadFromResponseSql($row);
            $events[] = $event;
        }
        return $events;
    }

    function getFinalDateTime()
    {
        return (new DateTime($this->finalDateTime))->setTimezone(new DateTimeZone('Europe/Paris'));
    }
    function getStartDateTime()
    {
        return (new DateTime($this->startDateTime))->setTimezone(new DateTimeZone('Europe/Paris'));
    }
    function getId()
    {
        return $this->id;
    }
    /**
     * @return int
     */
    public function getDefenderX()
    {
        return $this->defenderX;
    }

    /**
     * @return int
     */
    public function getDefenderY()
    {
        return $this->defenderY;
    }

    /**
     * @return int
     */
    public function getAttackerX()
    {
        return $this->attackerX;
    }

    /**
     * @return int
     */
    public function getAttackerY()
    {
        return $this->attackerY;
    }

    public function actualPosition(){
        if($this->status != 0)
            return false;
        $start = new DateTime($this->startDateTime);
        $final = new DateTime($this->finalDateTime);
        $now = new DateTime();
        $diff = $now->diff($start);
        $diff2 = $final->diff($start)->format('%r%s');
        $percent = $diff->format('%r%s') / ($diff2);
        $x = round($this->attackerX + ($this->defenderX - $this->attackerX) * $percent);
        $y = round($this->attackerY + ($this->defenderY - $this->attackerY) * $percent);
        return [$x, $y];
    }

    private function save()
    {
        $res = MyDB::query("UPDATE attack_event SET status = ?, nbCannon = ?, nbOffensiveTroop = ?, nbLogisticTroop = ?, nbIndustrySteal = ?, nbCannonLossAttacker = ?, nbOffensiveTroopLossAttacker = ?, nbLogisticTroopLossAttacker = ?, nbCannonLossDefender = ?, nbOffensiveTroopLossDefender = ?, nbLogisticTroopLossDefender = ? WHERE id = ?", [$this->status, $this->nbCannon, $this->nbOffensiveTroop, $this->nbLogisticTroop, $this->nbIndustrySteal, $this->nbCannonLossAttacker, $this->nbOffensiveTroopLossAttacker, $this->nbLogisticTroopLossAttacker, $this->nbCannonLossDefender, $this->nbOffensiveTroopLossDefender, $this->nbLogisticTroopLossDefender, $this->id]);
        return true;
    }
}