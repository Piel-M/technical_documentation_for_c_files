<?php
/**
 * Génère la documentation utilisateur au format HTML à partir d'un fichier Markdown.
 *
 * @param string $inputFile Le chemin vers le fichier Markdown d'entrée.
 * @param string $outputFile Le chemin vers le fichier HTML de sortie.
 * @param string $version La version de la documentation.
 */

function generateUserDocumentation($inputFile, $outputFile, $version) {
    // Vérifie si le fichier Markdown existe
    if (!file_exists($inputFile)) {
        die("Le fichier Markdown spécifié n'existe pas.");
    }

    // Lit le contenu du fichier Markdown
    $markdownContent = file_get_contents($inputFile);

    // Convertit le Markdown en HTML (une implémentation simple)
    $htmlContent = parseMarkdown($markdownContent);

    // Initialise la variable $tableHtml à une chaîne vide
    $tableHtml = '';

    // Recherche des lignes de tableau en Markdown
    preg_match_all('/\|(.+?)\|/s', $markdownContent, $matches);

    // Si des lignes de tableau sont trouvées
    if (isset($matches[0]) && !empty($matches[0])) {
        // Crée une nouvelle balise de tableau HTML
        $tableHtml .= "<table>\n";

        // Boucle sur chaque ligne de tableau
        foreach ($matches[0] as $index => $markdownTable) {
            // Supprime les caractères "|" et divise la ligne en colonnes
            $columns = explode('|', trim($markdownTable));

            // Supprime les espaces inutiles autour du contenu de chaque cellule
            $columns = array_map('trim', $columns);

            // Supprime les éléments vides générés par les cellules vides
            $columns = array_filter($columns);

            // Ajoute une ligne de tableau HTML pour chaque colonne
            if ($index === 0) {
                // Si c'est la première ligne, considérez-la comme l'en-tête
                $tableHtml .= "<tr>\n";
                foreach ($columns as $column) {
                    $tableHtml .= "<th>$column</th>\n";
                }
                $tableHtml .= "</tr>\n";
            } else {
                // Sinon, considérez-le comme une ligne de données
                $tableHtml .= "<tr>\n";
                foreach ($columns as $column) {
                    $tableHtml .= "<td>$column</td>\n";
                }
                $tableHtml .= "</tr>\n";
            }

            // Remplace la ligne de tableau en Markdown par une chaîne vide (pour la retirer)
            $markdownContent = str_replace($markdownTable, '', $markdownContent);
        }

        // Ferme le tableau HTML
        $tableHtml .= "</table>\n";
    }

    // Génère le code HTML complet avec le titre et la version
    $fullHtml = <<<HTML
<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Documentation Utilisateur - Version $version</title>
    <style>
        /* Ajoutez ici votre CSS pour le formatage élégant */
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            margin: 20px;
            color: #333;
        }

        h1, h2, h3, h4 {
            background-color: #f2f2f2;
            padding: 8px;
            color: #333;
        }

        ul {
            list-style-type: square;
            padding-left: 20px;
        }

        li {
            margin-bottom: 8px;
        }

        ul ul {
            padding-left: 20px;
        }

        strong {
            font-weight: bold;
        }

        em {
            font-style: italic;
        }

        code {
            background-color: #f4f4f4;
            padding: 2px 4px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
        }

        a {
            color: #0066cc;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Documentation Utilisateur - Version $version</h1>
    $htmlContent
    <!--TABLE-->
    $tableHtml
</body>
</html>
HTML;

    // Écrit le code HTML généré dans le fichier de sortie
    file_put_contents($outputFile, $fullHtml);

    echo "\numentation utilisateur a été générée avec succès dans '$outputFile'.\n";
}

function parseMarkdown($markdownContent) {
    // Remplace les titres de niveau 1 à 4
    $markdownContent = preg_replace('/^# (.*$)/m', '<h1>$1</h1>', $markdownContent);
    $markdownContent = preg_replace('/^## (.*$)/m', '<h2>$1</h2>', $markdownContent);
    $markdownContent = preg_replace('/^### (.*$)/m', '<h3>$1</h3>', $markdownContent);
    $markdownContent = preg_replace('/^#### (.*$)/m', '<h4>$1</h4>', $markdownContent);

    // Remplace les listes non ordonnées
    $markdownContent = preg_replace('/^\s*- (.*)/m', '<ul><li>$1</li></ul>', $markdownContent);
    $markdownContent = preg_replace('/^\s{4}- (.*)/m', '<ul><ul><li>$1</li></ul></ul>', $markdownContent);

    // Ajoute des puces aux listes
    $markdownContent = preg_replace('/^\s*\*\s(.*)/m', '<ul><li>$1</li></ul>', $markdownContent);
    $markdownContent = preg_replace('/^\s{4}\*\s(.*)/m', '<ul><ul><li>$1</li></ul></ul>', $markdownContent);

    // Traite les sous-listes de manière générique
    $markdownContent = preg_replace_callback('/^(\s*)- (.*)/m', function ($matches) {
        $indentation = $matches[1];
        $item = $matches[2];
        $level = strlen($indentation) / 4; // Chaque niveau de sous-liste ajoute 4 espaces
        $listTag = str_repeat('<ul>', $level) . '<li>';
        return $listTag . $item . str_repeat('</li></ul>', $level);
    }, $markdownContent);

    // Traite les balises de code en ligne
    $markdownContent = preg_replace('/`([^`]*)`/', '<code>$1</code>', $markdownContent);

    // Traite les textes en gras
    $markdownContent = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $markdownContent);

    // Traite les textes en italique
    $markdownContent = preg_replace('/_(.*?)_/', '<em>$1</em>', $markdownContent);

    // Traite les liens
    $markdownContent = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2">$1</a>', $markdownContent);

    // Ajoute la gestion des tableaux
    $markdownContent = parseTableMarkdown($markdownContent);

    return $markdownContent;
}

function parseTableMarkdown($markdownContent) {
    // Recherche des lignes de tableau en Markdown
    preg_match_all('/\|(.+?)\|/s', $markdownContent, $matches);

    // Si des lignes de tableau sont trouvées
    if (isset($matches[0]) && !empty($matches[0])) {
        // Initialise les en-têtes de tableau à une chaîne vide
        $tableHeaders = '';
        // Initialise les lignes de données à une chaîne vide
        $tableData = '';

        // Extrait les en-têtes de la première ligne de tableau
        $headers = explode('|', trim($matches[0][0]));
        $headers = array_map('trim', $headers);
        $headers = array_filter($headers);

        // Boucle sur chaque ligne de données à partir de la deuxième ligne
        for ($i = 1; $i < count($matches[0]); $i++) {
            // Supprime les caractères "|" et divise la ligne en colonnes
            $columns = explode('|', trim($matches[0][$i]));
            $columns = array_map('trim', $columns);
            $columns = array_filter($columns);

            // Ajoute une ligne de tableau HTML pour chaque colonne
            $tableData .= "<tr>\n";
            foreach ($columns as $columnIndex => $column) {
                // Utilise les en-têtes de tableau pour créer les balises th ou td appropriées
                $cellTag = $i === 1 ? 'th' : 'td';
                $tableData .= "<$cellTag>{$column}</$cellTag>\n";
            }
            $tableData .= "</tr>\n";
        }

        // Ajoute les en-têtes de tableau HTML
        $tableHeaders = "<tr>\n";
        foreach ($headers as $header) {
            $tableHeaders .= "<th>{$header}</th>\n";
        }
        $tableHeaders .= "</tr>\n";
    }

    // Remplace le marqueur de tableau en Markdown par le tableau HTML généré
    $markdownContent = str_replace('<!--TABLE-->', "<table>\n$tableHeaders$tableData</table>\n", $markdownContent);

    // Remplace les lignes horizontales par des balises <hr>
    $markdownContent = preg_replace('/^-----/m', '<hr>', $markdownContent);

    return $markdownContent;
}

$patterns_config = [
    "CLIENT" => "/CLIENT=(.*)/",
    "PRODUIT" => "/PRODUIT=(.*)/",
    "VERSION" => "/VERSION=(\d+\.\d+\.\d+)/",
];

$config = file_get_contents('./config');

//Recuperation de la version dans le fichier config
$match = preg_match($patterns_config['VERSION'], $config, $matches);
if ($match) {
    $version = $matches[1];
};

echo $version;
$inputFile = "doc-utilisateur.md";

$outputFile = "DOC_USER-$version.html";

// Génère la documentation utilisateur
generateUserDocumentation($inputFile, $outputFile, $version);
?>