<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include getPost.php
include 'commands/getPostByID.php';

// Check if PostID is set in the URL parameters
if (!isset($_GET['PostID'])) {
    die('PostID is not set');
}

$postID = $_GET['PostID'];

// Fetch the post details using getPost.php
$post = getPostByID($postID);

if (!$post) {
    die('Post not found');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="reset.css" />
    <link rel="stylesheet" href="post.css" />
    <title>View Post</title>
    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!--Navigation bar-->
    <div id="nav" style="height: 100px;"><?php include 'display_elements/nav.php'; ?></div>
    <!--End of Navigation bar-->
</head>

<body class="bg-secondary">
    <!-- Close button -->
    <a href="index.php" class="btn-close m-3 fs-2 position-absolute" style="top: 60px; left: -5px;" aria-label="Close"></a>

    <!-- Post Container -->
    <div class="container card mt-5 mx-auto w-75">
        <div class="row">
            <div class="col-md-12">
                <div class="card-body d-flex flex-column">
                    <!-- Post Title -->
                    <h2 class="card-title text-center">
                        <?php echo $post['PostTitle']; ?>
                    </h2>
                    <hr>
                    <!-- Post Image -->
                    <div class="col-12 col-md-8 mx-auto">
                        <img src="data:image/png;base64,<?php echo base64_encode($post['PostImage']); ?>" alt="Post Image" class="img-fluid p-3">
                    </div>
                    <hr>
                    <!-- Post Description -->
                    <p class="card-text text-justify mt-auto p-3 bg-light border rounded">
                        <?php echo $post['Description']; ?>
                    </p>
                    <hr>
                    <!-- Tags -->
                    <h5 class="mb-0">Tags</h5>
                    <div class="card-body d-flex flex-wrap  align-items-center">
                        <span class="text-start h6 text-body-emphasis opacity-75 rounded">
                            <?php 
                            $tags = isset($post['Tags']) ? $post['Tags'] : '';
                            foreach (explode(',', $tags) as $tag) : ?>
                                <span class="badge bg-primary text-white m-1 rounded-pill"><?php echo $tag; ?></span>
                            <?php endforeach; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End of Post Container -->

    <!-- Rating Slider -->
    <div class="container card p-3 mx-auto mt-4 mb-0 w-75">
        <label for="rating" class="mb-3">
            <h3 id="ratingDisplay">Rating: ?/5.5</h3>
        </label>
        <form method="post" action="commands/addRating.php" id="ratingForm">
            <input type="range" class="form-range" id="rating" name="rating" value="30" min="0" max="55" step="1" oninput="updateRatingDisplay(this.value)">
            <div class="d-flex justify-content-end mt-3">
                <button type="submit" class="btn btn-primary">Submit Rating</button>
            </div>
        </form>
    </div>
    <!-- End of Rating Slider -->
    
    <!-- Discussion Area -->
    <div class="container card w-75 p-3 mt-4 mb-5 mx-auto">
        <div class="row">
            <div class="col-md-12">
                <h3 class="mb-3">Discussion</h3>

                <!-- Comments -->
                <div id="comments" class="mt-3"></div>
                    <!-- Comments will be loaded here using Ajax -->

                <!-- Comment Form -->
                <form action="commands/submitComment.php" method="POST" id="commentForm">
                    <input type="hidden" name="postID" value="<?php echo $postID; ?>">
                    <div class="mb-3 mt-5">
                        <label for="comment">Leave a Comment:</label>
                        <textarea class="form-control" id="comment" name="content" rows="3"></textarea>
                    </div>
                    <p id="charCount"></p>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary">Submit Comment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End of Discussion Area -->

    <!-- Ajax -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(function () {
            $("#nav").load("../display_elements/nav.php");
        });
    </script>
    <script>
        function updateRatingDisplay(value) {
            const ratingDisplay = document.getElementById('ratingDisplay');
            const rating = value / 10;
            ratingDisplay.textContent = `Rating: ${rating.toFixed(1)}/5.5`;
        }
    </script>
    <script>
        $(document).ready(function() {
            function loadComments() {
                $.ajax({
                    url: 'commands/getComments.php',
                    type: 'GET',
                    data: { postID: <?php echo json_encode($_GET['PostID']); ?>, userID: <?php echo json_encode($_SESSION['user_id']); ?> },
                    success: function(data) {
                        $('#comments').html(data);
                    }
                });
            }

            loadComments(); // Load comments on page load

            setInterval(loadComments, 5000); // Reload comments every 5 seconds

            $('#commentForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: 'commands/submitComment.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function() {
                        loadComments(); // Reload comments after submitting a new one
                    }
                });
            });
        });
    </script>
    <script>
        // Comments Char Count Script
        const textarea = document.getElementById('comment');
        const charCountDisplay = document.getElementById('charCount');
        const maxChars = 1000;
    
        textarea.addEventListener('input', function () {
            const charCount = this.value.length;
    
            if (charCount > maxChars) {
                this.value = this.value.slice(0, maxChars);
            }
    
            charCountDisplay.textContent = `Character Count: ${charCount}/${maxChars}`;
        });
    </script>
    <script>
        // Checks if user is signed in before submitting a rating
        $(document).ready(function() {
            $('#ratingForm').on('submit', function(e) {
                <?php if (!isset($_SESSION['user_id'])): ?>
                    e.preventDefault();
                    alert('Sign in to submit a rating.');
                <?php endif; ?>
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#commentForm').on('submit', function(e) {
                var comment = $.trim($('#comment').val());

                // Check if the user is logged in
                <?php if (!isset($_SESSION['user_id'])): ?>
                    var isLoggedIn = false;
                <?php else: ?>
                    var isLoggedIn = true;
                <?php endif; ?>

                // Check if the comment is empty or if the user is not logged in
                if (!isLoggedIn || comment === '') {
                    e.preventDefault();

                    if (!isLoggedIn) {
                        alert('Sign in to submit a comment.');
                    } else {
                        alert('Comment cannot be empty.');
                    }
                }
            });
        });
    </script>
</body>

</html>