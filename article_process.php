<?php
// Rekiperasyon done yo
$titre = $_POST['titre'] ?? '';
$contenu = $_POST['contenu'] ?? '';
$auteur = $_POST['auteur'] ?? '';
$categorie = $_POST['categorie'] ?? '';
$date = $_POST['date'] ?? '';

$errors = [];

// --- ETAPE 5: VALIDATION ---

// Couche 1: Existence (Èske yo vide?)
if (empty(trim($titre))) { $errors[] = "Le titre est obligatoire"; }
if (empty(trim($contenu))) { $errors[] = "Le contenu est obligatoire"; }
if (empty(trim($auteur))) { $errors[] = "L'auteur est obligatoire"; }
if (empty($categorie)) { $errors[] = "La catégorie est obligatoire"; }
if (empty($date)) { $errors[] = "La date est obligatoire"; }

// Couche 2: Format & Longueur
if (!empty(trim($titre)) && (strlen($titre) < 5 || strlen($titre) > 100)) {
    $errors[] = "Le titre doit avoir entre 5 et 100 caractères";
}
if (!empty(trim($contenu)) && (strlen($contenu) < 20 || strlen($contenu) > 1000)) {
    $errors[] = "Le contenu doit avoir entre 20 et 1000 caractères";
}
if (!empty(trim($auteur)) && (strlen($auteur) < 3 || strlen($auteur) > 50)) {
    $errors[] = "L'auteur doit avoir entre 3 et 50 caractères";
}

// Validation Date (pa nan fitir)
if (!empty($date)) {
    $dateObj = DateTime::createFromFormat('Y-m-d', $date);
    if (!$dateObj) {
        $errors[] = "La date n'est pas valide";
    } elseif ($dateObj > new DateTime()) {
        $errors[] = "La date ne peut pas être dans le futur";
    }
}

// Validation Catégorie
$categories_valides = ['Tutoriels', 'Ressources', 'Actualités'];
if (!empty($categorie) && !in_array($categorie, $categories_valides)) {
    $errors[] = "La catégorie sélectionnée n'est pas valide";
}

$article_valide = empty($errors);

// --- ETAPE 6: SECURITE (XSS) ---
$titre_safe = htmlspecialchars($titre);
$contenu_safe = htmlspecialchars($contenu);
$auteur_safe = htmlspecialchars($auteur);
$categorie_safe = htmlspecialchars($categorie);
$date_safe = htmlspecialchars($date);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Resultat de la publication</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5">
    <div class="container">
        <?php if (!$article_valide): ?>
            <div class="alert alert-danger">
                <strong> Erreurs detectees :</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <a href="javascript:history.back()" class="btn btn-outline-danger btn-sm">Corriger le formulaire</a>
            </div>
        <?php else: ?>
            <div class="alert alert-success">
                <h5>Article cree avec succes !</h5>
                <div class="card mt-3">
                    <div class="card-body">
                        <h6><?= $titre_safe ?></h6>
                        <p><?= nl2br($contenu_safe) ?></p>
                        <small>Par <strong><?= $auteur_safe ?></strong> | Categorie: <?= $categorie_safe ?> | Publié le <?= $date_safe ?></small>
                    </div>
                </div>
                <div class="mt-3">
                    <small class="text-muted">
                        Sécurite: Toutes les données affichées ont été nettoyees avec htmlspecialchars()
                    </small>
                </div>
            </div>
            <a href="article_form.html" class="btn btn-primary">Ajouter un autre article</a>
        <?php endif; ?>
    </div>
</body>
</html>

