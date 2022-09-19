<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Home</title>
</head>
<body>
    <h1>Home</h1>
    <p>Home page content</p>
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
