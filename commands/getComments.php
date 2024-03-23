<?php
session_start();
$config = require 'config.php';

$host = $config['database']['host'];
$db = $config['database']['name'];
$user = $config['database']['user'];
$pass = $config['database']['password'];
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$pdo = new PDO($dsn, $user, $pass);

$postID = $_GET['postID'];

$comments = getComments($pdo, $postID);
foreach ($comments as $comment) {
    $date = DateTime::createFromFormat('Y-m-d H:i:s', $comment['CreatedAt']);
    $formattedDate = $date->format('F j, Y, g:i A');
    echo '<div class="card">
        <div class="card-body">
        <h5 class="card-title">' . htmlspecialchars($comment['Username']) . ' <small class="badge bg-secondary text-white float-end text-wrap" style="max-width: 200px; font-size: 0.5rem;">' . htmlspecialchars($formattedDate) . '</small></h5>
        <p class="card-text">' . htmlspecialchars($comment['Content']) . '</p>';

    if ((isset($_SESSION['user']) && $_SESSION['user'] == $comment['Username']) || (isset($_SESSION['IsAdmin']) && $_SESSION['IsAdmin'])) {
        echo '
        <form action="commands/deleteComment.php" method="post">
            <input type="hidden" name="CommentID" value="' . $comment['CommentID'] . '">
            <input type="hidden" name="PostID" value="' . $postID . '">
            <button type="submit" class="btn btn-danger">Delete Comment</button>
        </form>';
    }

    echo '
        </div>
    </div>
    ';
}
function getComments($pdo, $postID)
{
    try {
        // SQL query to select the comments, the username of the user who made the comment, and the timestamp
        $sql = "SELECT Comment.*, User.Username, Comment.CreatedAt FROM Comment INNER JOIN User ON Comment.UserID = User.UserID WHERE PostID = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$postID]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("PDO error: " . $e->getMessage());
    }
}
