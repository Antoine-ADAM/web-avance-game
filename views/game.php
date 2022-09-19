<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>game</title>
</head>
<body>
<div>
    <select name="type" id="fdsgfds">
        <?php
            foreach (User::PURCHASES as $type){
                echo '<option value="'.$type.'">'.$type."</option>";
            }
        ?>
    </select>
    <input type="number" name="nb" id="hgfdhgfd">
    <button onclick="purchase()">Purchase</button>
</div>
<hr>
<div>
    <select name="type" id="gfdsgfdsgf">
        <?php
        foreach (User::UPGRADES as $type){
            echo '<option value="'.$type.'">'.$type."</option>";
        }
        ?>
    </select>
    <button onclick="levelUp()">Level Up USE FETCH</button>
</div>

<hr>
<div>
    <select name="idDefender" id="bgfdshdsvt">
        <?php
        foreach ($users as $user){
            echo "<option value=\"".$user->getId()."\">".$user->getName()."</option>";
        }
        ?>
    </select>
    <input type="number" name="nbCannon" placeholder="nbCannon" id="aa1">
    <input type="number" name="nbOffensiveTroop" placeholder="nbOffensiveTroop" id="aa2">
    <input type="number" name="nbLogisticTroop" placeholder="nbLogisticTroop" id="aa3">
    <button onclick="attack()">Attack</button>
</div>
<script type="application/javascript">
    function levelUp(){
        fetch("<?= Pages::toURL(Pages::LEVEL_UP) ?>", {
            method: 'POST',
            body: JSON.stringify({type: document.getElementById("gfdsgfdsgf").value}),
            headers: {
                'Content-Type': 'application/json'
            }
        }).then(function(response) {
            console.log(response);
            return response.json();
        }).then(function(data) {
            alert(data);
            console.log(data);
        });
    }
    function purchase() {
        fetch("<?= Pages::toURL(Pages::PURCHASE) ?>", {
            method: 'POST',
            body: JSON.stringify({type: document.getElementById("fdsgfds").value, nb: document.getElementById("hgfdhgfd").value}),
            headers: {
                'Content-Type': 'application/json'
            }
        }).then(function(response) {
            console.log(response);
            return response.json();
        }).then(function(data) {
            alert(data);
            console.log(data);
        });
    }
    function attack() {
        fetch("<?= Pages::toURL(Pages::ATTACK) ?>", {
            method: 'POST',
            body: JSON.stringify({
                idDefender: document.getElementById("bgfdshdsvt").value,
                nbCannon: document.getElementById("aa1").value,
                nbOffensiveTroop: document.getElementById("aa2").value,
                nbLogisticTroop: document.getElementById("aa3").value
            }),
            headers: {
                'Content-Type': 'application/json'
            }
        }).then(function(response) {
            console.log(response);
            return response.json();
        }).then(function(data) {
            alert(data);
            console.log(data);
        });
    }
</script>
<hr>
<a href="<?= Pages::toURL(Pages::LOGOUT) ?>">Logout</a>
<hr>
<table>
    <tr>
        <th>id</th>
        <th>name</th>
        <th>color</th>
        <th>levelIndustry</th>
        <th>levelEnergy</th>
        <th>nbIndustry</th>
        <th>nbEnergy</th>
        <th>nbCannon</th>
        <th>nbOffensiveTroop</th>
        <th>nbLogisticTroop</th>
        <th>scores</th>
        <th>x</th>
        <th>y</th>
        <th>getCostLevelUpIndustry</th>
    </tr>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?= $user->getId() ?></td>
            <td><?= $user->getName() ?></td>
            <td><?= $user->getColor() ?></td>
            <td><?= $user->getLevelIndustry() ?></td>
            <td><?= $user->getLevelEnergy() ?></td>
            <td><?= $user->getNbIndustry() ?></td>
            <td><?= $user->getNbEnergy() ?></td>
            <td><?= $user->getNbCannon() ?></td>
            <td><?= $user->getNbOffensiveTroop() ?></td>
            <td><?= $user->getNbLogisticTroop() ?></td>
            <td><?= $user->getScores() ?></td>
            <td><?= $user->getX() ?></td>
            <td><?= $user->getY() ?></td>
            <td><?= $user->getCostLevelUpIndustry()[0] ?>I/<?= $user->getCostLevelUpIndustry()[1] ?>E</td>
        </tr>
    <?php endforeach; ?>
</table>
<hr>
<table>
    <tr>
        <th>id</th>
        <th>idAttacker</th>
        <th>idDefender</th>
        <th>finalDateTime</th>
        <th>startDateTime</th>
        <th>nbCannon</th>
        <th>nbOffensiveTroop</th>
        <th>nbLogisticTroop</th>
        <th>status</th>
        <th>nbCannonLossAttacker</th>
        <th>nbOffensiveTroopLossAttacker</th>
        <th>nbLogisticTroopLossAttacker</th>
        <th>nbIndustrySteal</th>
        <th>nbCannonLossDefender</th>
        <th>nbOffensiveTroopLossDefender</th>
        <th>nbLogisticTroopLossDefender</th>
        <th>actualPosition</th>
    </tr>
    <?php
    foreach ($attackEvents as $attackEvent) {
        echo "<tr>";
        echo "<td>" . $attackEvent->getId() . "</td>";
        echo "<td>" . $attackEvent->getIdAttacker() . "</td>";
        echo "<td>" . $attackEvent->getIdDefender() . "</td>";
        echo "<td>" . $attackEvent->getFinalDateTime() . "</td>";
        echo "<td>" . $attackEvent->getStartDateTime() . "</td>";
        echo "<td>" . $attackEvent->getNbCannon() . "</td>";
        echo "<td>" . $attackEvent->getNbOffensiveTroop() . "</td>";
        echo "<td>" . $attackEvent->getNbLogisticTroop() . "</td>";
        echo "<td>" . $attackEvent->getStatus() . "</td>";
        echo "<td>" . $attackEvent->getNbCannonLossAttacker() . "</td>";
        echo "<td>" . $attackEvent->getNbOffensiveTroopLossAttacker() . "</td>";
        echo "<td>" . $attackEvent->getNbLogisticTroopLossAttacker() . "</td>";
        echo "<td>" . $attackEvent->getNbIndustrySteal() . "</td>";
        echo "<td>" . $attackEvent->getNbCannonLossDefender() . "</td>";
        echo "<td>" . $attackEvent->getNbOffensiveTroopLossDefender() . "</td>";
        echo "<td>" . $attackEvent->getNbLogisticTroopLossDefender() . "</td>";
        echo "<td>x:" . ($attackEvent->getStatus()==0?$attackEvent->actualPosition()[0]." y:".$attackEvent->actualPosition()[1]:"FALSE") . "</td>";
        /*echo "<td>" . $attackEvent->getDefenderX() . "</td>";
        echo "<td>" . $attackEvent->getDefenderY() . "</td>";
        echo "<td>" . $attackEvent->getAttackerX() . "</td>";
        echo "<td>" . $attackEvent->getAttackerY() . "</td>";*/
        echo "</tr>";
    }
    ?>
</table>
<hr>
</body>
</html>