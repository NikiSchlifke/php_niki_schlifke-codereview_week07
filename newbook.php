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
    <title>New Book</title>
</head>
<body>
<nav class="navbar navbar-light bg-faded" role="navigation">

    <ul class="nav d-inline-flex justify-content-start">

        <li class="nav-item" >
            <a class="navbar-brand" href="#">
                <h4>Book Database</h4>
            </a>
        </li>
        <li class="nav-item"><a href="home.php" class="nav-link">find book</a></li>
        <li class="nav-item"><a href="newbook.php" class="nav-link">add book</a></li>
    </ul>

    <ul class="nav d-inline-flex justify-content-end">

        <li class="nav-item">
            <span class="navbar-text">Hi' <?php echo $_SESSION['user']; ?></span>

        </li>
        <li class="nav-item">
            <a class="nav-link" href="logout.php?logout">Sign Out</a>
        </li>
    </ul>

</nav>
<div class="container">
    <form action="newbook.php" method="POST">
        <div class="form-group">
            <label for="newbook-first-name">Author: first name</label>
            <input class="form-control" type="text" id="newbook-first-name" name="newbook-first-name">
        </div>
        <div class="form-group">
            <label for="newbook-last-name">Author: last name</label>
            <input class="form-control" type="text" id="newbook-last-name" name="newbook-last-name">
        </div>
        <div class="form-group">
            <label for="newbook-title">Book: title</label>
            <input class="form-control" type="text" id="newbook-title" name="newbook-title">
        </div>
        <div class="form-group">
            <label for="newbook-description">Book: description</label>
            <textarea class="form-control" id="newbook-description" name="newbook-description"></textarea>
        </div>
        <div class="form-group text-center">
            <button class="form-control btn btn-secondary" type="submit" name="newbook-submit">Add Book</button>
        </div>
    </form>

    <?php
    if (isset($_POST['newbook-submit'])):
        $authorFirstName = trim($_POST['newbook-first-name']);
        $authorLastName = trim($_POST['newbook-last-name']);
        $bookTitle = trim($_POST['newbook-title']);
        $bookDescription = trim($_POST['newbook-description']);
        $isValid = true;

        if (!preg_match('/^\w[\w ]*$/', $authorFirstName)):
            $isValid = false;
            ?>
            <div class="alert alert-danger">Please correctly enter the author's first name.</div>
        <?php endif;
        if (!preg_match('/^\w[\w ]*$/', $authorLastName)):
            $isValid = false;
            ?>
            <div class="alert alert-danger">Please correctly enter the author's last name.</div>
        <?php endif;
        if (!preg_match('/^\w[\w ]*$/', $bookTitle)):
            $isValid = false;
            ?>
            <div class="alert alert-danger">Please correctly enter the books's title.</div>
        <?php endif;
        if (!preg_match('/^\w[\w ]*$/', $bookDescription)):
            $isValid = false;
            ?>
            <div class="alert alert-danger">Please correctly enter the book's description</div>
        <?php endif; ?>

        <?php
        $authorFirstName = htmlspecialchars(ucfirst($authorFirstName));
        $authorLastName = htmlspecialchars(ucfirst($authorLastName));
        $bookTitle = htmlspecialchars($bookTitle);
        $bookDescription = htmlspecialchars($bookTitle);

        $existingAuthorSQL = $dbh->prepare(<<<'SQL'
        SELECT a.id AS author_id FROM book_catalog.author AS a
        WHERE a.last_name = :lastName AND a.first_name = :firstName;
SQL
        );

        $existingAuthorSQL->execute(['firstName' => $authorFirstName, 'lastName' => $authorLastName]);
        $author = $existingAuthorSQL->fetch();
        if ($author) {
            $authorId = $author['id'];
        } else {
            $newAuthorSQL = $dbh->prepare(<<<'SQL'
INSERT INTO book_catalog.author (first_name, last_name) VALUES (:firstName, :lastName);
SQL
            );
            $newAuthorSQL->execute(['firstName' => $authorFirstName, 'lastName' => $authorLastName]);
            $authorId = $dbh->lastInsertId();
        }

        $existingBookSQL = $dbh->prepare(<<<'SQL'
        SELECT b.id FROM book_catalog.author AS a
        LEFT JOIN book_catalog.author_book AS ab ON a.id = ab.author_id
        LEFT JOIN book_catalog.book AS b ON b.id = ab.book_id
        WHERE a.id = :authorId AND b.title = :bookTitle;
SQL
        );
        $existingBookSQL->execute(['authorId' => $authorId, 'bookTitle' => $bookTitle]);
        $book = $existingBookSQL->fetch();
        if ($book):
            ?>
            <div class="alert alert-danger">A book by this author with the same name already exists.</div><?php
        else:
            $newBookSQL = $dbh->prepare(<<<'SQL'
INSERT INTO book_catalog.book (title, description) VALUES (:bookTitle, :bookDescription);
SQL
            );
            $newBookAuthorSQL = $dbh->prepare(<<<'SQL'
INSERT INTO book_catalog.author_book (book_id, author_id) VALUES (:bookId, :authorId);
SQL
            );
            $newBookSQL->execute(['bookTitle' => $bookTitle, 'bookDescription' => $bookDescription]);
            $bookId = $dbh->lastInsertId();
            $newBookAuthorSQL->execute(['bookId' => $bookId, 'authorId' => $authorId]);
            ?>
            <div class="alert alert-success">Book added to database.</div><?php
        endif;
    endif;
    ?>
</div>
</body>
</html>
<?php ob_end_flush(); ?>
