<?php
require_once 'initdb.php';
require_once 'formHelpers.php';
ob_start();
session_start();
if (isset($_SESSION['user'])) {
    header("Location: home.php");
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css"
          integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js"
            integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n"
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"
            integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb"
            crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js"
            integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn"
            crossorigin="anonymous"></script>
    <title>Book Database</title>

</head>
<body>
<nav class="navbar navbar-light bg-faded">
    <a class="navbar-brand" href="#">
        <h4>Book Database</h4>
    </a>
</nav>
<div class="container">
    <div class="container">
        <h1>Login</h1>
        <form action="<?= ($_SERVER['PHP_SELF']) ?>" method="POST">
            <div class="form-group">
                <label for="user-name">User Name</label>
                <input type="text" class="form-control" id="user-name" name="user-name">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <button type="submit" name="submit-login" class="btn btn-primary">Submit</button>
        </form>

        <?php
        // Register form submission handling.
        if (isset(
            $_POST['submit-login'])) {
            $isValidationSuccessful = true;
            // input validation
            $values = [];
            $values[':userName'] = validateWithMessage(
                $_POST['user-name'],
                '/^[\w ]*$/',
                'Please provide a valid user name',
                $isValidationSuccessful
            );

            $passwordFromPost = validateWithMessage(
                $_POST['password'],
                '/^.{4,255}$/',
                'Please provide your password with at least 6 characters.',
                $isValidationSuccessful
            );
            if ($isValidationSuccessful) {
                $sql = <<<'SQL'
SELECT pass_hash FROM book_catalog.user WHERE user.name = :userName;
SQL;
                $sth = $dbh->prepare($sql);
                $sth->execute([':userName' => $values[':userName']]);
                $userData = $sth->fetch(PDO::FETCH_ASSOC);
                if (crypt($passwordFromPost, $userData['pass_hash']) == $userData['pass_hash']) {
                    $_SESSION['user'] = $values[':userName'];
                    header("Location: home.php");
                } else {
                    echo validationAlert('Incorrect Credentials, Try again...');
                }
            }
        }

        ?>
    </div>

</body>
</html>
<?php ob_end_flush(); ?>
