<?php
$pdo = new PDO('mysql:host=localhost;dbname=livraison_db', 'vinted', 's24EJIlOje');
$stmt = $pdo->prepare("SELECT * FROM points_livraison WHERE id = ?");
$stmt->execute([$_GET['id']]);
$point = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode($point);
?>