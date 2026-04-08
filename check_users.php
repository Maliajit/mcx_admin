<?php
$pdo = new PDO('sqlite:database/database.sqlite');
$result = $pdo->query('SELECT id, name, email FROM users')->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($result, JSON_PRETTY_PRINT);
