<?php
$pdo = new PDO('mysql:host=localhost;dbname=livraison_db', 'vinted', 's24EJIlOje');
$stmt = $pdo->query("SELECT * FROM points_livraison");
$points = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($points);
?>
