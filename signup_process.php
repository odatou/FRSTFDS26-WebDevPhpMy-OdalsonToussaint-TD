<?php

$errors = [];
$success_data = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Kouch 1: Verifikasyon si tout champs yo ranpli
    $fields = ['username', 'email', 'password', 'password_confirm', 'terms'];
    foreach ($fields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = "Le champ $field est obligatoire.";
        }
    }

    if (empty($errors)) {
        // Netwaye done yo pou sekirite (XSS Prevention)
        $username = htmlspecialchars($_POST['username']);
        $email = htmlspecialchars($_POST['email']);
        $password = $_POST['password'];
        $password_confirm = $_POST['password_confirm'];

        // Kouch 2: Validation teknik
        
        // Longè username (3-20)
        if (strlen($username) < 3 || strlen($username) > 20) {
            $errors[] = "Le nom d'utilisateur doit contenir entre 3 et 20 caracteres.";
        }

        // Format Email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Le format de l'email n'est pas valide.";
        }

        // Longè password (min 8)
        if (strlen($password) < 8) {
            $errors[] = "Le mot de passe doit faire au moins 8 caractères.";
        }

        // Korespondans password
        if ($password !== $password_confirm) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }
    }
}

// Afichaj rezilta
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultat de l'inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5">
    <div class="container">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <h4>Erreurs rencontrées :</h4>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
                <a href="signup_form.html" class="btn btn-secondary">Retour au formulaire</a>
            </div>
        <?php else: ?>
            <div class="alert alert-success">
                <h4> Inscription réussie !</h4>
                <p>Voici le résumé des données reçues (sécurisées avec <code>htmlspecialchars()</code>) :</p>
                <ul>
                    <li><strong>Pseudo :</strong> <?php echo $username; ?></li>
                    <li><strong>Email :</strong> <?php echo $email; ?></li>
                </ul>
                <hr>
                <p><em>Note pédagogique : La fonction <code>htmlspecialchars()</code> a été utilisée pour empêcher les attaques XSS en convertissant les caractères spéciaux en entités HTML.</em></p>
                <a href="signup_form.html" class="btn btn-primary">Retour</a>
            </div>
        <?php
         endif;
         ?>
    </div>
</body>
</html>

