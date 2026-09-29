<?php
/**
 * One-time setup script.
 * Creates the database and tables, loads demo data, and creates the
 * uploads folder. Delete this file once your demo is up and running.
 */
require_once 'includes/config.php';

$done = false;
$error = null;
$log = [];

function runSqlFile(PDO $pdo, string $path, array &$log): void {
    $sql = file_get_contents($path);
    if ($sql === false) {
        throw new RuntimeException("Impossible de lire $path");
    }
    // Strip full-line comments, then split into individual statements.
    $lines = preg_split('/\r\n|\r|\n/', $sql);
    $lines = array_filter($lines, fn($l) => !preg_match('/^\s*--/', $l));
    $clean = implode("\n", $lines);

    $statements = array_filter(array_map('trim', explode(';', $clean)));
    foreach ($statements as $statement) {
        $pdo->exec($statement);
    }
    $log[] = basename($path) . ' : ' . count($statements) . ' instruction(s) exécutée(s).';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Connect without selecting a database yet, so schema.sql can create it.
        $dsn = 'mysql:host=' . DB_HOST . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        runSqlFile($pdo, __DIR__ . '/database/schema.sql', $log);
        runSqlFile($pdo, __DIR__ . '/database/seed.sql', $log);

        // Create the uploads folder structure.
        $uploadsDir = __DIR__ . '/uploads/patients';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
            $log[] = 'Dossier uploads/patients/ créé.';
        } else {
            $log[] = 'Dossier uploads/patients/ déjà présent.';
        }

        $htaccess = __DIR__ . '/uploads/.htaccess';
        if (!file_exists($htaccess)) {
            file_put_contents($htaccess, "php_flag engine off\nOptions -Indexes\n<IfModule mod_authz_core.c>\n  Require all denied\n  <FilesMatch \"\\.(jpg|jpeg|png|pdf|doc|docx)$\">\n    Require all granted\n  </FilesMatch>\n</IfModule>\n<IfModule !mod_authz_core.c>\n  Order deny,allow\n  Deny from all\n  <FilesMatch \"\\.(jpg|jpeg|png|pdf|doc|docx)$\">\n    Allow from all\n  </FilesMatch>\n</IfModule>\n");
            $log[] = 'uploads/.htaccess créé.';
        }

        $done = true;
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation – DentaFlow</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body style="background:var(--gray-50);">
    <div style="max-width:560px;margin:60px auto;padding:0 var(--space-4);">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Installation de DentaFlow</h3>
            </div>
            <div class="card-body">
                <?php if ($done): ?>
                    <p style="color:var(--success);font-weight:600;margin-bottom:var(--space-4);">
                        Installation terminée avec succès.
                    </p>
                    <ul style="margin-bottom:var(--space-5); padding-left: var(--space-5); color: var(--gray-700);">
                        <?php foreach ($log as $line): ?>
                            <li><?= e($line) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <p class="text-muted" style="margin-bottom:var(--space-5);">
                        Pensez à supprimer <code>install.php</code> maintenant que la base est prête.
                    </p>
                    <a href="index.php" class="btn btn-primary btn-block">Ouvrir le tableau de bord</a>
                <?php else: ?>
                    <?php if ($error): ?>
                        <p style="color:var(--danger);font-weight:600;margin-bottom:var(--space-4);">
                            Erreur : <?= e($error) ?>
                        </p>
                        <p class="text-muted" style="margin-bottom:var(--space-4);">
                            Vérifiez les identifiants MySQL dans <code>includes/config.php</code>
                            (hôte, utilisateur, mot de passe) puis réessayez.
                        </p>
                    <?php endif; ?>
                    <p class="text-muted" style="margin-bottom:var(--space-5);">
                        Ceci va créer la base <code><?= e(DB_NAME) ?></code>, ses tables, y charger des
                        données de démonstration, et créer le dossier des pièces jointes.
                        Si la base existe déjà, ses tables seront <strong>recréées</strong>
                        (les données existantes seront perdues).
                    </p>
                    <form method="POST">
                        <button type="submit" class="btn btn-primary btn-block">Lancer l'installation</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>