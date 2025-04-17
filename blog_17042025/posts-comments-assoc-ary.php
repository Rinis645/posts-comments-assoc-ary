<?php

$hostname = "localhost";
$username = "user27032025";
$dbname = "php27032025";
$password = "password";

$conn = new mysqli($hostname, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Savienojums neizdevās: " . $conn->connect_error);
}

$sql_posts = "SELECT * FROM posts"; 
$result_posts = $conn->query($sql_posts);


$posts_with_comments = [];

if ($result_posts->num_rows > 0) {
    while($post = $result_posts->fetch_assoc()) {
        $post_id = $post['id']; 
     
        $sql_comments = "SELECT * FROM comments WHERE post_id = $post_id"; // Pieņemot, ka ir 'comments' tabula
        $result_comments = $conn->query($sql_comments);

        $comments = [];
        if ($result_comments->num_rows > 0) {
            
            while($comment = $result_comments->fetch_assoc()) {
                $comments[] = $comment;
            }
        }

     
        $posts_with_comments[] = [
            'post' => $post,
            'comments' => $comments
        ];
    }
} else {
    echo "Nav atrasti ieraksti posts tabulā.";
}


?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posti un Komentāri</title>
    <style>
        ul { list-style-type: none; }
        li { margin-bottom: 10px; }
    </style>
</head>
<body>
    <h1>Posti un komentāri</h1>
    <ul>
        <?php
        
        foreach ($posts_with_comments as $post_data) {
            $post = $post_data['post'];
            $comments = $post_data['comments'];
            echo "<li>";
            echo "<strong>" . htmlspecialchars($post['title']) . "</strong><br>";
            echo "<p>" . htmlspecialchars($post['content']) . "</p>";
            
        
            if (!empty($comments)) {
                echo "<ul>";
                foreach ($comments as $comment) {
                    echo "<li>";
                    echo htmlspecialchars($comment['comment']);
                    echo "</li>";
                }
                echo "</ul>";
            }
            echo "</li>";
        }
        ?>
    </ul>
</body>
</html>

<?php
$conn->close();
?>
