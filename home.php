<?php
ob_start();
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
require_once __DIR__ . '/vendor/autoload.php';
require_once 'initdb.php';
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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Book search...</title>
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

<div class="container py-3">
    <div class="row">

        <form class="col-md-6 offset-md-3" action="<?= ($_SERVER['PHP_SELF']) ?>" method="GET">
            <div class="form-group">
                <input type="text" class="form-control" title="search-box" id="search-text" name="search-text"
                       placeholder="Type Book or Author...">
            </div>
            <div class="form-group text-center">
                <button class="btn btn-secondary" type="submit" name="search-button"> search <i
                            class="fa fa-long-arrow-right" aria-hidden="true"></i></button>
                <a href="newbook.php" class="btn btn-primary">Add Book</a>
            </div>
        </form>
    </div>

    <?php if (isset($_GET['search-button']) && !empty($_GET['search-text'])):
        $resultSQL = $dbh->prepare(<<<'SQL'
    SELECT a.first_name, a.last_name, b.title, b.description, b.image_url FROM book_catalog.book AS b
    JOIN book_catalog.author_book AS ab ON ab.book_id = b.id
    JOIN book_catalog.author AS a ON ab.author_id = a.id
    WHERE a.first_name LIKE :search OR a.last_name LIKE :search OR b.title LIKE :search;
SQL
        );
        $search = '%' . $_GET['search-text'] . '%';

        $resultSQL->execute(['search' => $search]);
        $results = $resultSQL->fetchAll();
        if (empty($results)):?>
            <h4>Book not found</h4>
            <p>Please contact the library at:</p>
            <p><i class="fa fa-phone" aria-hidden="true"></i> 0043 681 812 546 54</p>
        <?php else:
            foreach ($results as $result):
                ?>

                <br>
                <h4><?php echo $result['title'] . ' / ' . $result['first_name'] . ' ' . $result['last_name'] ?></h4>
                <div class="card">
                    <div class="row">

                        <div class="col-md-8 px-3">
                            <div class="card-block">
                                <p class="card-text"><?php echo $result['description'] ?></p>
                            </div>

                        </div>
                        <div class="col-md-4">
                            <img src="<?php echo $result['image_url'] ?>" class="w-100">
                        </div>

                    </div>
                </div>
            <?php endforeach;endif;endif; ?>
</div>
</body>
<script>
    window.onload = function () {
        document.getElementById("search-text").focus();
    }
</script>
</html>
<?php ob_end_flush(); ?>
