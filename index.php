<?php
// Connexion a la base de données
require_once 'config/database.php';

// --- Recuperation filtres GET ---
$selectedAuthor   = $_GET['author'] ?? null;
$selectedCategory = $_GET['category'] ?? null;

// --- Construction requete SQL principale ---
$sql = "
SELECT
    articles.id,
    articles.title AS titre,
    articles.content AS contenu,
    authors.name AS auteur,
    DATE(articles.created_at) AS date,
    categories.name AS categorie
FROM articles
INNER JOIN authors ON articles.author_id = authors.id
INNER JOIN categories ON articles.category_id = categories.id
WHERE 1
";

$params = [];

// Filtre par auteur
if ($selectedAuthor) {
    $sql .= " AND authors.name = :author";
    $params['author'] = $selectedAuthor;
}

// Filtre par categorie
if ($selectedCategory) {
    $sql .= " AND categories.name = :category";
    $params['category'] = $selectedCategory;
}

// Tri par date décroissante
$sql .= " ORDER BY articles.created_at DESC";

// Execution requête
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$filteredArticles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- Recuperation auteurs pour boutons ---
$authorsStmt = $pdo->query("SELECT DISTINCT name FROM authors");
$authors = $authorsStmt->fetchAll(PDO::FETCH_COLUMN);

// --- Récupération catégories pour boutons ---
$categoriesStmt = $pdo->query("SELECT name FROM categories");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_COLUMN);

// --- Fonction affichage article ---
function displayArticle($article) {
    return "
    <div class='card mb-3'>
        <div class='card-body'>
            <h5 class='card-title'>" . htmlspecialchars($article['titre']) . "</h5>
            <p class='card-text'>" . htmlspecialchars($article['contenu']) . "</p>
            <small class='text-muted'>
                Par " . htmlspecialchars($article['auteur']) . " - " . htmlspecialchars($article['date']) . "
            </small>
            <span class='badge bg-info ms-2'>" . htmlspecialchars($article['categorie']) . "</span>
        </div>
    </div>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Blog d'Articles</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">

<div class="container bg-white p-4 shadow-sm rounded">
    <h1 class="mb-4">Blog d'Articles</h1>

    <!-- Filtres par auteur -->
    <div class="mb-3">
        <strong>Auteurs :</strong>
        <a href="index.php" class="btn btn-secondary btn-sm me-2">Tous</a>
        <?php foreach ($authors as $author): ?>
            <a href="index.php?author=<?= urlencode($author) ?>"
               class="btn btn-outline-primary btn-sm me-2 <?= ($selectedAuthor === $author ? 'active' : '') ?>">
                <?= htmlspecialchars($author) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <hr>

    <!-- Filtres par catégorie -->
    <div class="mb-3">
        <strong>Categories :</strong>
        <a href="index.php" class="btn btn-secondary btn-sm me-2">Toutes</a>
        <?php foreach ($categories as $category): ?>
            <a href="index.php?category=<?= urlencode($category) ?>"
               class="btn btn-outline-success btn-sm me-2 <?= ($selectedCategory === $category ? 'active' : '') ?>">
                <?= htmlspecialchars($category) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Compteur -->
    <p class="text-muted">
        <?= count($filteredArticles) ?> article(s)
        <?php if ($selectedAuthor): ?>
            de l'auteur <strong><?= htmlspecialchars($selectedAuthor) ?></strong>
        <?php endif; ?>
    </p>

    <!-- Affichage articles -->
    <?php if (empty($filteredArticles)): ?>
        <div class="alert alert-info">Aucun article trouvé.</div>
    <?php else: ?>
        <?php foreach ($filteredArticles as $article): ?>
            <?= displayArticle($article); ?>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

</body>
</html>
