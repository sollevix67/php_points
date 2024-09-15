<?php
$pdo = new PDO('mysql:host=localhost;dbname=livraison_db', 'vinted', 's24EJIlOje');

if ($_POST['id']) {
    $stmt = $pdo->prepare("UPDATE points_livraison SET code_point = ?, type_point = ?, nom_magasin = ?, adresse = ?, code_postal = ?, ville = ?, photo_streetview = ?, latitude = ?, longitude = ? WHERE id = ?");
    $stmt->execute([$_POST['code_point'], $_POST['type_point'], $_POST['nom_magasin'], $_POST['adresse'], $_POST['code_postal'], $_POST['ville'], $_POST['photo_streetview'], $_POST['latitude'], $_POST['longitude'], $_POST['id']]);
} else {
    $stmt = $pdo->prepare("INSERT INTO points_livraison (code_point, type_point, nom_magasin, adresse, code_postal, ville, photo_streetview, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['code_point'], $_POST['type_point'], $_POST['nom_magasin'], $_POST['adresse'], $_POST['code_postal'], $_POST['ville'], $_POST['photo_streetview'], $_POST['latitude'], $_POST['longitude']]);
}
?>
