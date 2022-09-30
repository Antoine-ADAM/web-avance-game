<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
      <div class="container-fluid">
        <div class="text-light mx-auto h2">Home</div>
      </div>
    </nav>
    <!-- Surcouche MYSQLI pour la fonction query pour renforcer la sécurité -->
    <div class="text-center p-4">
        <?php Alert::displayAlerts() ?>
    </div>
    <div class="row justify-content-center mt-5 w-100">
        <div class="col-4">
            <form action="<?= Pages::toURL(Pages::LOGIN) ?>" style="text-align: right;" method="post">
                <h3>Login</h3>
                <div class="form-outline mb-4">
                    <input type="text" name="name" placeholder="Name" pattern="^[a-zA-Z0-9_]{3,16}$" required>
                </div>

                <div class="form-outline mb-4">
                    <input type="password" name="password" placeholder="Password" minlength="8" required>
                </div>

                <input type="submit" value="Login" class="btn btn-primary btn-block mb-4">

            </form>
        </div>
        <div class="col-4 text-left">
            <form onsubmit="return check()" action="<?= Pages::toURL(Pages::REGISTER) ?>" method="post" required>
                <h3>Register</h3>
                <div class="form-outline mb-4">
                    <input type="text" name="name" placeholder="Name" pattern="^[a-zA-Z0-9_]{3,16}$" required>
                </div>
                <div class="form-outline mb-4">
                    <input type="password" id="password" name="password" placeholder="Password" minlength="8" onkeyup="check()" required>
                </div>
                <div class="form-outline mb-4">
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm password" minlength="8" onkeyup="check()" required>
                </div>
                <div class="form-outline mb-4">
                    <span id='message'></span>
                </div>
                <div class="form-outline mb-4">
                    <input type="color" name="color" placeholder="color" required>
                </div>
                <input type="submit" value="Register" class="btn btn-primary btn-block mb-4">
            </form>
        </div>
    </div>
    <script type="text/javascript">
        var check = function() {
          if (document.getElementById('password').value ==
            document.getElementById('confirm_password').value) {
            document.getElementById('message').style.color = 'green';
            document.getElementById('message').innerHTML = 'matching';
            return true;
          } else {
            document.getElementById('message').style.color = 'red';
            document.getElementById('message').innerHTML = 'not matching';
            return false;
          }
        }
    </script>
</body>
</html>
