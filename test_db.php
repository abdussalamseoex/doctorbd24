<?php
$db = new PDO('sqlite:database/database.sqlite');
$res = $db->query('SELECT title FROM blog_posts LIMIT 5')->fetchAll(PDO::FETCH_COLUMN);
print_r($res);
