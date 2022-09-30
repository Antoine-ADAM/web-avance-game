<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Game</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>

<nav class="navbar navbar-dark bg-dark">
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

<div id="info">
    <div class="container row">
        <ul class="col-3">
            <li>Industry level: <?= $user->getLevelIndustry() ?></li>
            <li>Energy level: <?= $user->getLevelEnergy() ?></li>
        </ul>
        <ul class="col-3">
            <li>Canoon amount: <?= $user->getNbCannon() ?></li>
            <li>Offensive troop amount: <?= $user->getNbOffensiveTroop() ?></li>
            <li>Logistic troop amount: <?= $user->getNbLogisticTroop() ?></li>
        </ul>
        <ul class="col-3">
            <li>Industry amount: <?= $user->getNbIndustry() ?></li>
            <li>Energy amount: <?= $user->getNbEnergy() ?></li>
        </ul>
    </div>
</div>

<div id="screen-left" class="container p-3">
    <u><b>Shop</b></u>
    <form action="<?= Pages::toURL(Pages::PURCHASE) ?>" method="post">
        <label>15I/2E</label>
        <input type="hidden" name="type" value="cannon">
        <input type="number" name="nb" value="0" class="form-control">
        <input type="submit" value="Buy cannon" class="btn btn-outline-secondary">
    </form>
    <form action="<?= Pages::toURL(Pages::PURCHASE) ?>" method="post">
        <label>10I/0E</label>
        <input type="hidden" name="type" value="offensiveTroop">
        <input type="number" name="nb" value="0" class="form-control">
        <input type="submit" value="Buy offensive troop" class="btn btn-outline-secondary">
    </form>
    <form action="<?= Pages::toURL(Pages::PURCHASE) ?>" method="post">
        <label>10I/0E</label>
        <input type="hidden" name="type" value="logisticTroop">
        <input type="number" name="nb" value="0" class="form-control">
        <input type="submit" value="Buy logistic troop" class="btn btn-outline-secondary">
    </form>
    <hr>
    <u><b>Level Up</b></u>
    
    <label>
        <?php
            if(!is_null($user->getCostLevelUpIndustry())){
                echo $user->getCostLevelUpIndustry()[0] ?>I/<?php  echo $user->getCostLevelUpIndustry()[1] 
                ?>E
                <form action="<?= Pages::toURL(Pages::LEVEL_UP) ?>" method="post">
                    <input type="hidden" name="type" value="industry">
                    <input type="submit" value="Upgrade industry" class="btn btn-outline-secondary">
                </form>
                <?php
            }else{
                echo "Max level reached !";
            }
        ?>
    </label>


    <label>
        <?php
            if(!is_null($user->getCostLevelUpEnergy())){
                echo $user->getCostLevelUpEnergy()[0] ?>I/<?php  echo $user->getCostLevelUpEnergy()[1] 
                ?>E
                <form action="<?= Pages::toURL(Pages::LEVEL_UP) ?>" method="post">
                    <input type="hidden" name="type" value="energy">
                    <input type="submit" value="Upgrade energy" class="btn btn-outline-secondary">
                </form>
                <?php
            }else{
                echo "Max level reached !";
            }
        ?>
    </label>
    
</div>


<div id="screen">
  <?php foreach ($users as $user): ?>
    <div
       class="player_dot"
       style="top: <?=$user->getY() * 3; ?>px; left: <?=$user->getY() * 3; ?>px; background-color: <?=$user->getColor(); ?>;">
    </div>
  <?php endforeach; ?>
</div>

<div id="screen-right" class="container p-3">

    <u><b>Ongoing attack</b></u><br>
    None
    <hr>
    <u><b>Current defense</b></u><br>
    None<br>
    <hr>
    <u><b>Send troops</b></u><br>
    <form action="<?= Pages::toURL(Pages::ATTACK) ?>" method="post">
        <select name="idDefender" id="bgfdshdsvt">
            <?php
            foreach ($users as $user){
                echo "<option value=\"".$user->getId()."\">".$user->getName()."</option>";
            }
            ?>
        </select>
        <input type="number" name="nbCannon" placeholder="Cannon amount" class="form-control">
        <input type="number" name="nbOffensiveTroop" placeholder="Offensive Troop amount" class="form-control">
        <input type="number" name="nbLogisticTroop" placeholder="Logistic Troop amount" class="form-control">
        <input type="submit" value="Attack" class="btn btn-outline-secondary">
    </form>
    <hr>
      <div class="bg-light px-0 border border-dark" style="height: 150px;">
        <div class="overflow-auto h-100">
          <div class="">01:22:44<p>Vous avez attaqué Lui</p></div><hr>
          <div class="">01:22:44<p>Vous avez attaqué Lui</p></div><hr>
          <div class="">01:22:44<p>Vous avez attaqué Lui</p></div><hr>
          <div class="">01:22:44<p>Vous avez attaqué Lui</p></div>
        </div>
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
        /*echo "<td>" . $attackEvent->getDefenderX() . "</td>";
        echo "<td>" . $attackEvent->getDefenderY() . "</td>";
        echo "<td>" . $attackEvent->getAttackerX() . "</td>";
        echo "<td>" . $attackEvent->getAttackerY() . "</td>";*/
        echo "</tr>";
    }
    ?>
</table> -->

</body>
</html>