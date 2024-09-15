<?php
$pdo = new PDO('mysql:host=localhost;dbname=livraison_db', 'vinted', 's24EJIlOje');
$stmt = $pdo->prepare("DELETE FROM points_livraison WHERE id = ?");
$stmt->execute([$_POST['id']]);
?>