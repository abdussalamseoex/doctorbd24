<?php
$db = new PDO('sqlite:database/database.sqlite');
$stmt = $db->query('SELECT slug, title FROM blog_posts LIMIT 5');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
