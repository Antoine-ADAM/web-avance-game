<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Game</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>

<nav class="navbar navbar-dark bg-dark fixed-top">
  <div class="container-fluid">
    <span class="navbar-text">
      <?= $user->getName(); ?>
    </span>
    <div class="text-light mx-auto h2">Game</div>
    <span class="navbar-text">
      <a href="<?= Pages::toURL(Pages::LOGOUT) ?>">Logout</a>
    </span>
  </div>
</nav>

<div class="alert alert-info" style="display: none" id="isUpdate">
    An event has just occurred, refresh the page to see it !
</div>

<div id="info">
    <div class="container row">
        <ul class="col">
            <li>🏭 Industry level: <?= $user->getLevelIndustry() ?></li>
            <li>☢️ Energy level: <?= $user->getLevelEnergy() ?></li>
        </ul>
        <ul class="col">
            <li>Canoon amount: <?= $user->getNbCannon() ?>💣</li>
            <li>Offensive troop amount: <?= $user->getNbOffensiveTroop() ?>💪</li>
            <li>Logistic troop amount: <?= $user->getNbLogisticTroop() ?>🛡️</li>
        </ul>
        <ul class="col">
            <li>Industry amount: <?= $user->getNbIndustry() ?>🔧</li>
            <li>Energy amount: <?= $user->getNbEnergy() ?>⚡</li>
        </ul>
        <ul class="col">
            <li>📍 Coordinates:</li>
            x: <?=$user->getX()?>
            y: <?=$user->getY()?>
        </ul>
        <ul class="col">
            <div class="h-100"><?php Alert::displayAlerts() ?></div>
        </ul>
    </div>
</div>

<div id="screen-left" class="container p-3">
    <u><b>🛒 Shop</b></u>
    <form action="<?= Pages::toURL(Pages::PURCHASE) ?>" method="post">
        <label class="mt-2">15🔧 2⚡</label>
        <input type="hidden" name="type" value="cannon">
        <input type="number" name="nb" value="0" class="form-control">
        <input type="submit" value="Buy cannon" class="btn btn-outline-secondary mt-2">
    </form>
    <form action="<?= Pages::toURL(Pages::PURCHASE) ?>" method="post">
        <label class="mt-2">10🔧 0⚡</label>
        <input type="hidden" name="type" value="offensiveTroop">
        <input type="number" name="nb" value="0" class="form-control">
        <input type="submit" value="Buy offensive troop" class="btn btn-outline-secondary mt-2">
    </form>
    <form action="<?= Pages::toURL(Pages::PURCHASE) ?>" method="post">
        <label class="mt-2">10🔧 0⚡</label>
        <input type="hidden" name="type" value="logisticTroop">
        <input type="number" name="nb" value="0" class="form-control">
        <input type="submit" value="Buy logistic troop" class="btn btn-outline-secondary mt-2">
    </form>
    <hr>
    <u><b>🆙 Level Up</b></u>
    
    <div class="mt-2">
        <?php
            if(!is_null($user->getCostLevelUpIndustry())){
                echo $user->getCostLevelUpIndustry()[0] ?>🔧 <?php  echo $user->getCostLevelUpIndustry()[1] 
                ?>⚡
                <form action="<?= Pages::toURL(Pages::LEVEL_UP) ?>" method="post">
                    <input type="hidden" name="type" value="industry">
                    <input type="submit" value="Upgrade industry" class="btn btn-outline-secondary">
                </form>
                <?php
            }else{ ?>
                <button class="btn btn-outline-secondary disabled">Max Industry level reached !</button>
            <?php } ?>
    </div>
    <div class="mt-2">
        <?php
            if(!is_null($user->getCostLevelUpEnergy())){
                echo $user->getCostLevelUpEnergy()[0] ?>🔧 <?php  echo $user->getCostLevelUpEnergy()[1] 
                ?>⚡
                <form action="<?= Pages::toURL(Pages::LEVEL_UP) ?>" method="post">
                    <input type="hidden" name="type" value="energy">
                    <input type="submit" value="Upgrade energy" class="btn btn-outline-secondary">
                </form>
                <?php
            }else{ ?>
                <button class="btn btn-outline-secondary disabled">Max Energy level reached !</button>
            <?php } ?>
    </div>
    
</div>


<div id="screen">
  <?php foreach ($users as $userDot): ?>
    <div
       class="player_dot"
       style="top: <?=$userDot->getY() * 3; ?>px; left: <?=$userDot->getX() * 3; ?>px; background-color: <?=$userDot->getColor(); ?>;"
       title="<?=$userDot->getName()?>&#13;
       x: <?=$userDot->getX()?> y: <?=$userDot->getY()?>&#13;
       <?=$userDot->getLevelIndustry()?>🏭 <?=$userDot->getLevelEnergy()?>☢️&#13;
       <?=$userDot->getNbCannon()?>💣 <?=$userDot->getNbOffensiveTroop()?>💪 <?=$userDot->getNbLogisticTroop()?>🛡️">
    </div>
  <?php endforeach; ?>
</div>

<div id="screen-right" class="container p-3">

    <u><b>Ongoing attack</b></u><br>
    <?php
    $attackCountTmp = 0;
    foreach ($attackEvents as $attackEvent) {
        if($attackEvent->getIdAttacker() == $user->getId() AND $attackEvent->getStatus() == 0){
            echo "To: ".$attackEvent->getIdDefender();
            echo " ".$attackEvent->getStatus();
            $attackCountTmp++;
        }
    }
    if($attackCountTmp==0){
        echo "None";
    }
    ?><br>
    <hr>
    <!-- 0 is in progress, 1 is won for attacker, 2 is lost for attacker, 3 battle without winner -->
    <u><b>Current defense</b></u><br>
    <?php
    $attackCountTmp = 0;
    foreach ($attackEvents as $attackEvent) {
        if($attackEvent->getIdDefender() == $user->getId() AND $attackEvent->getStatus() == 0){
            echo "From: ".$attackEvent->getIdAttacker();
        }
    }
    if($attackCountTmp==0){
        echo "None";
    }
    ?><br>
    <hr>
    <u><b>⚔️ Send troops</b></u><br>
    <form action="<?= Pages::toURL(Pages::ATTACK) ?>" method="post">
        <select name="idDefender" class="mt-2">
            <?php
            foreach ($users as $user){
                echo "<option value=\"".$user->getId()."\">".$user->getName()."</option>";
            }
            ?>
        </select>
        <input type="number" name="nbCannon" placeholder="💣" class="form-control mt-2" required>
        <input type="number" name="nbOffensiveTroop" placeholder="💪" class="form-control mt-2" required>
        <input type="number" name="nbLogisticTroop" placeholder="🛡️" class="form-control mt-2" required>
        <input type="submit" value="Attack" class="btn btn-outline-secondary mt-2">
    </form>
    <hr>
      <div class="bg-light px-0 border border-dark" style="height: 125px;">
        <div class="overflow-auto h-100">
          <?php
            foreach($messages as $msg){
                if($msg->getType() == 0){
                    ?><div class="bg-white"><?php
                    echo '<u>'.$msg->getDate().'</u><br>';
                    echo $msg->getIdSender().': ';
                    echo $msg->getContent().'<hr class="mb-0" style="border-top: 2px solid #000000;">';
                    ?></div><?php
                }else if($msg->getType() == 1){
                    ?><div style="background-color: #f2f2f2;"><?php
                    echo '<u>'.$msg->getDate().'</u><br>';
                    echo $msg->getContent().'<hr class="mb-0" style="border-top: 2px solid #000000;">';
                    ?></div><?php
                }
            }
          ?>
        </div>
      </div>
      <div class="mt-2">
          <form action="<?= Pages::toURL(Pages::MESSAGE) ?>" method="post">
              <textarea style="max-width: 165px;max-height: 50px;" type="text" name="message"></textarea>
              <input type="submit" value="Send message" class="btn btn-outline-secondary">
          </form>
      </div>
  </div>
</div>

<!-- <table>
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
</table> -->

<!-- <table>
    <?php
    foreach ($attackEvents as $attackEvent) {
        echo "<tr>";
        echo "<td>" . $attackEvent->getId() . "</td>";
        echo "<td>" . $attackEvent->getIdAttacker() . "</td>";
        echo "<td>" . $attackEvent->getIdDefender() . "</td>";
        echo "<td>" . $attackEvent->getFinalDateTime()->format("Y-m-d H:i:s") . "</td>";
        echo "<td>" . $attackEvent->getStartDateTime()->format("Y-m-d H:i:s") . "</td>";
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
        echo "<td>" . $attackEvent->getDefenderX() . "</td>";
        echo "<td>" . $attackEvent->getDefenderY() . "</td>";
        echo "<td>" . $attackEvent->getAttackerX() . "</td>";
        echo "<td>" . $attackEvent->getAttackerY() . "</td>";
        echo "</tr>";
    }
    ?>
</table> -->
<script type="application/javascript">
    const id = setInterval(check, 2000);
    function check() {
        fetch('<?= Pages::toURL(Pages::IS_UPDATE) ?>')
            .then(response => response.json())
            .then(data => {
                if (data.status === "noUpdate") {
                    document.getElementById("isUpdate").style.display = "block";
                    clearInterval(id);
                    alert("An event has just occurred, refresh the page to see it !");
                }
            });
    }
</script>
</body>
</html>