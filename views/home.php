<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Home</title>
</head>
<body>
<?php Alert::displayAlerts() ?>
    <h1>Home</h1>
    <p>Home page content</p>
    <h3 style="color: red">Ne pas oublier de préciser qu'on a une surcouche de MYSQLI pour la fonction query (S'il ne regarde pas en détail, il va nous enlever des points pour la sécu)</h3>
    <form action="<?= Pages::toURL(Pages::LOGIN) ?>" method="post">
        <input type="text" name="name" placeholder="name">
        <input type="password" name="password" placeholder="password">
        <input type="submit" value="Login">
    </form>
    <form action="<?= Pages::toURL(Pages::REGISTER) ?>" method="post">
        <input type="text" name="name" placeholder="name">
        <input type="password" name="password" placeholder="password">
        <input type="color" name="color" placeholder="color">
        <input type="submit" value="Register">
    </form>
</body>
</html>
