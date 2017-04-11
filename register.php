<?php
ob_start();
session_start();
require_once 'initdb.php';
require_once 'formHelpers.php';
require_once 'config.php';
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
<nav class="navbar navbar-light bg-faded" role="navigation">

    <ul class="nav">
        <li class="nav-item mr-auto" >
            <a class="navbar-brand" href="#">
                <h4>Book Database</h4>
            </a>
        </li>
        <li class="nav-item">
            <span class="navbar-text">Hi' <?php echo $_SESSION['user']; ?></span>

        </li>
        <li class="nav-item ml-auto">
            <a class="nav-link" href="logout.php?logout">Sign Out</a>
        </li>
    </ul>

</nav>
<div class="container">
    <h1>Register</h1>
    <form action="<?= ($_SERVER['PHP_SELF']) ?>" method="POST">
        <div class="form-group">
            <label for="user-name">Username</label>
            <input type="text" class="form-control" id="user-name" name="user-name">
        </div>
        <div class="form-group">
            <label for="first-name">First Name</label>
            <input type="text" class="form-control" id="first-name" name="first-name">
        </div>
        <div class="form-group">
            <label for="last-name">Last Name</label>
            <input type="text" class="form-control" id="last-name" name="last-name">
        </div>
        <div class="form-group">
            <label for="e-mail">E-Mail Address</label>
            <input type="text" class="form-control" id="e-mail" name="e-mail">
        </div>
        <div class="form-group">
            <label for="password1">Password</label>
            <input type="password" class="form-control" id="password1" name="password1">
        </div>
        <div class="form-group">
            <label for="password2">Confirm Password</label>
            <input type="password" class="form-control" id="password2" name="password2">
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" class="form-control" id="address" name="address">
        </div>
        <div class="form-group">
            <label for="city">City</label>
            <input type="text" class="form-control" id="city" name="city">
        </div>
        <div class="form-group">
            <label for="zip-code">ZIP Code</label>
            <input type="text" class="form-control" id="zip-code" name="zip-code">
        </div>
        <div class="form-group">
            <label for="country">Country</label>
            <input type="text" class="form-control" id="country" name="country">
        </div>
        <button type="submit" name="submit-register" class="btn btn-primary">Submit</button>
    </form>


    <?php
    // Register form submission handling.
    if (isset(
        $_POST['submit-register'])) {
        $isValidationSuccessful = true;
        // input validation
        $values = [];
        $values[':userName'] = validateWithMessage(
            $_POST['user-name'],
            '/^\w*$/',
            'Please provide your user name.',
            $isValidationSuccessful
        );
        $values[':eMail'] = validateWithMessage(
            $_POST['e-mail'],
            '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/',
            'Please provide a valid E-Mail Address',
            $isValidationSuccessful
        );

        if ( $_POST['password1'] != $_POST['password2'] ) {
            validationAlert('Your password confirmation doesn\'t match.');
            $isValidationSuccessful = false;
        }
        $values[':passHash'] = crypt(validateWithMessage(
            $_POST['password1'],
            '/^.{4,255}$/',
            'Please provide your password with at least 6 characters.',
            $isValidationSuccessful
        ), DB_SALT);
        $values[':address'] = validateWithMessage(
            $_POST['address'],
            '/^[\w\d ]*/',
            'Please provide your street address.',
            $isValidationSuccessful
        );
        $values[':city'] = validateWithMessage(
            $_POST['city'],
            '/^\w+$/',
            'Please provide your city.',
            $isValidationSuccessful
        );
        $values[':zipCode'] = validateWithMessage(
            $_POST['zip-code'],
            '/^\w+$/',
            'Please provide your zip code.',
            $isValidationSuccessful
        );
        $values[':country'] = validateWithMessage(
            $_POST['country'],
            '/^\w+$/',
            'Please provide your country.',
            $isValidationSuccessful
        );
        if ($isValidationSuccessful) {
            $sql = <<<'SQL'
INSERT INTO book_catalog.user (name, first_name, last_name, email, address, city, zip_code, country, pass_hash) 
VALUES (:userName, :firstName, :lastName, :eMail, :address, :city, :zipCode, :country, :passHash);
SQL;
            $sth = $dbh->prepare($sql);
            $sth->execute($values);
            $_SESSION['user'] = $values[':userName'];
            header("Location: home.php");
        }
    }



    ?>
</div>
</body>
</html>
<?php ob_end_flush(); ?>
