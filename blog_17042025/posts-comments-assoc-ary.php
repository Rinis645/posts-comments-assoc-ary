<?php
// 1. Savienojuma izveide ar MySQL datubāzi
$servername = "localhost"; // Mainiet uz savu servera adresi
$username = "user27032025";        // Mainiet uz savu lietotājvārdu
$password = "password";            // Mainiet uz savu paroli
$dbname = "php27032025"; // Mainiet uz savu datubāzes nosaukumu

// Savienojuma izveide
$conn = new mysqli($servername, $username, $password, $dbname);

// Pārbaudīt savienojumu
if ($conn->connect_error) {
    die("Savienojums neizdevās: " . $conn->connect_error);
}

// 2. Iegūt visus postus
$sql_posts = "SELECT * FROM posts"; // Pieņemot, ka ir 'posts' tabula
$result_posts = $conn->query($sql_posts);

// 3. Saglabāt postus un saistītos komentārus
$posts_with_comments = [];

if ($result_posts->num_rows > 0) {
    // Iegūt postus
    while($post = $result_posts->fetch_assoc()) {
        $post_id = $post['id']; // Pieņemot, ka ir 'id' kolonna postiem

        // Iegūt komentārus saistītus ar šo postu
        $sql_comments = "SELECT * FROM comments WHERE post_id = $post_id"; // Pieņemot, ka ir 'comments' tabula
        $result_comments = $conn->query($sql_comments);

        $comments = [];
        if ($result_comments->num_rows > 0) {
            // Iegūt komentārus
            while($comment = $result_comments->fetch_assoc()) {
                $comments[] = $comment;
            }
        }

        // Saglabāt postu un komentārus asociatīvā masīvā
        $posts_with_comments[] = [
            'post' => $post,
            'comments' => $comments
        ];
    }
} else {
    echo "Nav atrasti ieraksti posts tabulā.";
}

// 4. Attēlot datus HTML hierarhiskā sarakstā
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
        // Attēlot postus un to komentārus
        foreach ($posts_with_comments as $post_data) {
            $post = $post_data['post'];
            $comments = $post_data['comments'];
            echo "<li>";
            echo "<strong>" . htmlspecialchars($post['title']) . "</strong><br>";
            echo "<p>" . htmlspecialchars($post['content']) . "</p>";
            
            // Ja ir komentāri, tad attēlot tos
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
// 5. Aizvērt savienojumu ar datubāzi
$conn->close();
?>
