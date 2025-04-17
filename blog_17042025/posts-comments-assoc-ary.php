<?php

// Pieslēdzamies datubāzei
$pdo = new PDO('mysql:host=localhost;dbname=your_db_name;charset=utf8', 'your_user', 'your_password');

// SQL vaicājums, kas iegūst datus no posts un comments tabulām
$sql = "SELECT p.id AS post_id, p.title, p.content AS post_content, c.id AS comment_id, c.content AS comment_content 
        FROM posts p
        LEFT JOIN comments c ON p.id = c.post_id
        ORDER BY p.id, c.id";

// Veicam vaicājumu
$stmt = $pdo->query($sql);

// Iegūstam datus kā asociatīvo masīvu
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Algoritms, kas pārveido plakanā masīva datus uz hierarhisku asociatīvu masīvu
$posts = [];

foreach ($rows as $row) {
    // Ja posts vēl nav pievienots masīvā, pievienojam to
    if (!isset($posts[$row['post_id']])) {
        $posts[$row['post_id']] = [
            'id' => $row['post_id'],
            'title' => $row['title'],
            'content' => $row['post_content'],
            'comments' => []
        ];
    }
    
    // Pievienojam komentāru, ja tas ir pieejams
    if ($row['comment_id']) {
        $posts[$row['post_id']]['comments'][] = [
            'id' => $row['comment_id'],
            'content' => $row['comment_content']
        ];
    }
}

// Attēlojam datus kā hierarhisku HTML sarakstu
function renderHtml($posts) {
    $html = '<ul>';
    
    foreach ($posts as $post) {
        $html .= '<li>';
        $html .= '<strong>' . htmlspecialchars($post['title']) . '</strong>: ' . htmlspecialchars($post['content']);
        
        if (!empty($post['comments'])) {
            $html .= '<ul>';
            foreach ($post['comments'] as $comment) {
                $html .= '<li>' . htmlspecialchars($comment['content']) . '</li>';
            }
            $html .= '</ul>';
        }
        
        $html .= '</li>';
    }
    
    $html .= '</ul>';
    return $html;
}

// Izvadām HTML struktūru
echo renderHtml($posts);
?>
