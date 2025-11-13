<?php
$host = 'dpg-d4a7id0dl3ps739kge4g-a.frankfurt-postgres.render.com';
$user = 'restaurant_pos_db_nk05_user';
$pass = '3jDDib7H18qU5yc9FFRnv3IYTBHwC1nr';
$dbname = 'restaurant_pos_db_nk05';

try {
    $pdo = new PDO("pgsql:host=$host;port=5432;dbname=$dbname;", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ PostgreSQL connection failed: " . $e->getMessage());
}
?>
