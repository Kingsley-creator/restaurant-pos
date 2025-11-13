<?php
$host = 'dpg-d4a7id0dl3ps739kge4g-a.frankfurt-postgres.render.com';
$db   = 'restaurant_pos_db_nk05';
$user = 'restaurant_pos_db_nk05_user';
$pass = '3jDDib7H18qU5yc9FFRnv3IYTBHwC1nr';
$port = '5432';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}
?>
