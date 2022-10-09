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

<div class="alert alert-info" style="display: none; z-index: 1000000000" id="isUpdate">
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
            <li>Logistic troop amount: <?= $user->getNbLogisticTroop() ?>🚚</li>
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
        <input type="number" name="nb" min="0" max="1999999999" value="1" class="form-control">
        <input type="submit" value="Buy cannon" class="btn btn-outline-secondary mt-2">
    </form>
    <form action="<?= Pages::toURL(Pages::PURCHASE) ?>" method="post">
        <label class="mt-2">10🔧 0⚡</label>
        <input type="hidden" name="type" value="offensiveTroop">
        <input type="number" name="nb" min="0" max="1999999999" value="1" class="form-control">
        <input type="submit" value="Buy offensive troop" class="btn btn-outline-secondary mt-2">
    </form>
    <form action="<?= Pages::toURL(Pages::PURCHASE) ?>" method="post">
        <label class="mt-2">10🔧 0⚡</label>
        <input type="hidden" name="type" value="logisticTroop">
        <input type="number" min="0" max="1999999999" name="nb" value="1" class="form-control">
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
            onmouseover="infoPlayer(<?= $userDot->getId() ?>)"
       class="player_dot"
       style="top: <?=$userDot->getY() * 3; ?>px; left: <?=$userDot->getX() * 3; ?>px; background-color: <?=$userDot->getColor(); ?>;"
       title="<?=$userDot->getName()?>&#13;
       x: <?=$userDot->getX()?> y: <?=$userDot->getY()?>&#13;
       <?=$userDot->getLevelIndustry()?>🏭 <?=$userDot->getLevelEnergy()?>☢️&#13;
       <?=$userDot->getNbCannon()?>💣 <?=$userDot->getNbOffensiveTroop()?>💪 <?=$userDot->getNbLogisticTroop()?>🚚">
    </div>
  <div id="playerInfo<?= $userDot->getId() ?>" style="display: none;position: relative;top: <?=$userDot->getY() * 3+15; ?>px; left: <?=$userDot->getX() * 3+15; ?>px; border: solid 3px; border-color: <?=$userDot->getColor(); ?>;width: fit-content; padding: 10px; background-color: aliceblue; border-radius: 5px ">
    <div class="playerInfo">
      <div class="playerInfoName">
        <?=$userDot->getName()?>
      </div>
      <div class="playerInfoLevel">
        <?=$userDot->getLevelIndustry()?>🏭 <?=$userDot->getLevelEnergy()?>☢️
      </div>
      <div class="playerInfoTroop">
        <?=$userDot->getNbCannon()?>💣 <?=$userDot->getNbOffensiveTroop()?>💪 <?=$userDot->getNbLogisticTroop()?>🚚
      </div>
    </div>
  </div>
  <?php endforeach; ?>
    <?php foreach ($attackEvents as $a){
        if ($a->getStatus() == 0){
            $color = $usersById[$a->getAttackerId()]->getColor();
        ?>
    <div id="attack-<?= $a->getId() ?>" style="position: absolute; top: <?= $a->actualPosition()[1]*3-22?>px;left: <?= $a->actualPosition()[0]*3-27?>px;transform: translate(-50%, -50%) rotate(<?= $a->getAngle() ?>deg);height: 90px;width: 110px;">
        <div style="position: absolute;background-color: <?= $color ?>;border-radius: 110px;top: 10px;left: 10px;bottom: 10px;right: 10px;opacity: 0.3;box-shadow: 0 0 30px 30px <?= $color ?>"></div>
        <img src="public/img/fourmis.gif" alt="" height="60px" style="position: absolute;top: 30px;left: 0">
        <img src="public/img/fourmis.gif" alt="" height="60px" style="position: absolute;top: 0;left:30px">
        <img src="public/img/fourmis.gif" alt="" height="60px" style="position: absolute;top: 30px;left:60px">
    </div>
            <div id="attack-info-<?= $a->getId() ?>" style="position: absolute; top: <?= $a->actualPosition()[1]*3+50?>px;left: <?= $a->actualPosition()[0]*3-27?>px;transform: translate(-50%, -50%) rotate(<?= $a->getAngle() ?>deg);width: 110px;">
                <?=$a->getNbCannon()?>💣 <?=$a->getNbOffensiveTroop()?>💪 <?=$a->getNbLogisticTroop()?>🚚
            </div>
    <?php }} ?>
</div>



<div id="screen-right" class="container p-3">

    <u><b>Ongoing attack</b></u><br>
    <?php
    $attackCountTmp = 0;
    foreach ($attackEvents as $attackEvent) {
        if($attackEvent->getIdAttacker() == $user->getId() AND $attackEvent->getStatus() == 0){
            echo "To: ".$usersById[$attackEvent->getIdDefender()]->getName();
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
            echo "From: ".$usersById[$attackEvent->getIdAttacker()]->getName();
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
            foreach ($users as $u){
                if($user->getId() != $u->getId()){
                    echo "<option value=\"".$u->getId()."\">".$u->getName()."</option>";
                }
            }
            ?>
        </select>
        <div class="input-group mt-2">
            <span class="input-group-text" id="label_attack_a">💣</span>
            <input type="number" name="nbCannon" min="0" max="1999999999" value="1" class="form-control" required>
        </div>
        <div class="input-group mt-2">
            <span class="input-group-text" id="label_attack_b">💪</span>
            <input type="number" name="nbOffensiveTroop" min="0" max="1999999999" value="1" class="form-control" required>
        </div>
        <div class="input-group mt-2">
            <span class="input-group-text" id="label_attack_b">🚚</span>
            <input type="number" name="nbLogisticTroop" min="0" max="1999999999" value="1" class="form-control" required>
        </div>
        
        <input type="submit" value="Attack" class="btn btn-outline-secondary mt-2">
    </form>
    <hr>
      <div class="bg-light px-0 border border-dark" style="height: 200px;">
        <div class="overflow-auto" style="height: inherit" id="messages">
          <?php
            foreach($messages as $msg){
                if($msg->getType() == 0){
                    ?>
                    <div class="bg-white">
                        <u><?=$msg->getDate()?></u><br>
                        <?=$usersById[$msg->getIdSender()]->getName()?>: <?=$msg->getContent()?>
                        <hr class="mb-0" style="border-top: 2px solid #000000;">
                    </div><?php
                }else if($msg->getType() == 1){
                    ?>
                    <div style="background-color: #f2f2f2;">
                    <u><?=$msg->getDate()?></u><br>
                    <?=$msg->getContent()?>
                    <hr class="mb-0" style="border-top: 2px solid #000000;">
                    </div>
                    <?php
                }
            }
          ?>
        </div>
      </div>
      <div class="mt-2">
          <form action="<?= Pages::toURL(Pages::MESSAGE) ?>" method="post">
              <input style="max-width: 165px;max-height: 50px;" type="text" name="message" class="form-control">
              <input type="submit" value="Send message" class="btn btn-outline-secondary mt-2">
          </form>
      </div>
</div>
<script type="application/javascript">
    console.log("test");
    let id = setInterval(check, 2000);
    function check() {
        fetch('<?= Pages::toURL(Pages::IS_UPDATE) ?>')
            .then(response => response.json())
            .then(data => {
                if (data.status === "noUpdate") {
                    noUpdate();
                }
            });
    }
    function noUpdate() {
        if(id===null){
            return;
        }
        document.getElementById("isUpdate").style.display = "block";
        clearInterval(id);
        id=null;
        if(confirm("An event has just occurred, do you want to refresh the page?")){
            location.reload();
        }
    }
    let user = {
        id: <?= $user->getId() ?>,
        x: <?= $user->getX() ?>,
        y: <?= $user->getY() ?>,
        color: "<?= $user->getColor() ?>",
        name: "<?= $user->getName() ?>",
        levelIndustry: <?= $user->getLevelIndustry() ?>,
        levelEnergy: <?= $user->getLevelEnergy() ?>,
        nbCannon: <?= $user->getNbCannon() ?>,
        nbOffensiveTroop: <?= $user->getNbOffensiveTroop() ?>,
        nbLogisticTroop: <?= $user->getNbLogisticTroop() ?>,
        nbIndustry: <?= $user->getNbIndustry() ?>,
        nbEnergy: <?= $user->getNbEnergy() ?>,
        costLevelUpIndustry: <?= json_encode($user->getCostLevelUpIndustry()) ?>,
        costLevelUpEnergy: <?= json_encode($user->getCostLevelUpEnergy()) ?>,
    };
    let users = [
        <?php foreach ($users as $u): ?>
        {
            id: <?= $u->getId() ?>,
            x: <?= $u->getX() ?>,
            y: <?= $u->getY() ?>,
            color: "<?= $u->getColor() ?>",
            name: "<?= $u->getName() ?>",
            levelIndustry: <?= $u->getLevelIndustry() ?>,
            levelEnergy: <?= $u->getLevelEnergy() ?>,
            nbCannon: <?= $u->getNbCannon() ?>,
            nbOffensiveTroop: <?= $u->getNbOffensiveTroop() ?>,
            nbLogisticTroop: <?= $u->getNbLogisticTroop() ?>,
        },
        <?php endforeach; ?>
    ];
    function infoPlayer(id){
        document.getElementById("playerInfo"+id).style.display = "block";
        for (let i = 0; i < users.length; i++) {
            if(users[i].id !== id){
                document.getElementById("playerInfo"+users[i].id).style.display = "none";
            }
        }

    }
    let attacks = [
        <?php foreach ($attackEvents as $a) {
        if ($a->getStatus() == 0) {
            echo '{';
            echo 'id: ' . $a->getId() . ',';
            echo 'startDate: Date.parse("' . $a->getStartDateTime()->format(DateTime::ATOM) . '"),';
            echo 'finalDate: Date.parse("' . $a->getFinalDateTime()->format(DateTime::ATOM) . '"),';
            echo 'startX: ' . $a->getAttackerX() . ',';
            echo 'startY: ' . $a->getAttackerY() . ',';
            echo 'finalX: ' . $a->getDefenderX() . ',';
            echo 'finalY: ' . $a->getDefenderY() . ',';
            echo 'moveX: ' . $a->getMovementPerSecond()[0] . ',';
            echo 'moveY: ' . $a->getMovementPerSecond()[1] . ',';
            echo '},';
        }
    }?>
    ];
    function reProcessPos() {
        for(let i = 0; i < attacks.length; i++){
            let dom = document.getElementById("attack-"+attacks[i].id);
            let x = attacks[i].startX + (attacks[i].finalX - attacks[i].startX) * (Date.now() - attacks[i].startDate) / (attacks[i].finalDate - attacks[i].startDate);
            let y = attacks[i].startY + (attacks[i].finalY - attacks[i].startY) * (Date.now() - attacks[i].startDate) / (attacks[i].finalDate - attacks[i].startDate);
            dom.style.top = y*3+22+"px";
            dom.style.left = x*3+27+"px";
        }
    }
    function update() {
        for(let i = 0; i < attacks.length; i++){
            let dom = document.getElementById("attack-"+attacks[i].id);
            let domInfo = document.getElementById("attack-info-"+attacks[i].id);
            if(Date.now() > attacks[i].finalDate){
                dom.style.display = "none";
                domInfo.style.display = "none";
                attacks.splice(i, 1);
                i--;
                noUpdate();
            }
            let x = parseFloat(dom.style.left);
            let y = parseFloat(dom.style.top);
            dom.style.top = (y+attacks[i].moveY)+"px";
            dom.style.left = (x+attacks[i].moveX)+"px";
            domInfo.style.top = (y+attacks[i].moveY+50)+"px";
            domInfo.style.left = (x+attacks[i].moveX+27)+"px";
        }
    }
    setInterval(update, 1000);

    function scrollbarPos(){
        var messageBody = document.getElementById("messages");
        messageBody.scrollTop = messageBody.scrollHeight - messageBody.clientHeight;
    }
    window.onload = scrollbarPos;

</script>
</body>
</html>