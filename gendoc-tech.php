<?php

    $patterns_config = [
        "CLIENT" => "/CLIENT=(.*)/",
        "PRODUIT" => "/PRODUIT=(.*)/",
        "VERSION" => "/VERSION=(\d+\.\d+\.\d+)/",
    ];

    $patterns_param_all = [
        "param" => '/\/\*\*(?s:(?!\/\*\*).)*\\\param/',
    ];

    //Paternes pour les fichiers sources
    $patterns_src = [
        "auteur" => '/\\\\author\s+(.*)/',
        "date" => '/\\\\date\s+(.*)/',
        "brief" => '/\\\\brief\s+(.*)/',
        "define" => '/\#define\s+(.*)/',
        "include" => '/#include <(.+)>/',
        "type" => '/typedef\s+(.*)/',
        "fonction" => '/\\\\fn\s+(.*)/',
        "param" => '/\\\\param\s+(.*)/',
        "return" => '/\\\\return\s+(.*)/',
        "briefF" => '/\\\\briefF\s+(.*)/',
    ];

    //Recupere tout les paths des fichier sources présent dans le meme dossier que le fichier gendoc-tech.php
    $files_paths = glob('*.c');
    //compte le nombre de fichiers sources
    $nb_c_files = count($files_paths);


    //CONFIG
    //Recupere le contenu du fichier config
    $config = file_get_contents('./config');

    //Recuperation du nom du client dans le fichier config
    $match = preg_match($patterns_config['CLIENT'], $config, $matches);
    if ($match) {
        $client = $matches[1];
    }

    //Recuperation du nom du produit dans le fichier config
    $match = preg_match($patterns_config['PRODUIT'], $config, $matches);
    if ($match) {
        $produit = $matches[1];
    };

    //Recuperation de la version dans le fichier config
    $match = preg_match($patterns_config['VERSION'], $config, $matches);
    if ($match) {
        $version = $matches[1];
    };



    //Creation du fichier DOC-TECH-'version'.html
    $doc_rendu = "DOC_TECH-$version.html";

    /*CODE GENERATION*/

    // Nombre de répétitions de la section
    $nombreRepetitions = count($files_paths);

    // Ouverture du fichier HTML en écriture
    $file = fopen("$doc_rendu", 'w');

    if ($file) {
        // Écriture de l'en-tête dans le fichier HTML
        fwrite($file, '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link rel="stylesheet" href="style.css">
                <title>Documentation Technique</title>
            </head>
            <body>
                <header>
                    <h1>
                        DOCUMENTATION TECHNIQUE
                    </h1>
                </header>
                <main>
                    <section>
                        <h3>
                            CLIENT : 
                        </h3>
                        <p>' . $client . '</p>
                        <h3>
                            PRODUIT :
                        </h3>
                        <p>' . $produit . '</p>
                        <h3>
                            VERSION :
                        </h3>
                        <p>' . $version . '</p>
                        <h3>
                            DATE DE GENERATION :
                        </h3>
                        <p> ' . date('d/m/Y') . ' </p>
                    </section>
        ');

        // Répétition de la section
        foreach ($files_paths as $i => $file_path) {

            // Récupération du contenu du fichier source
            $source = file_get_contents($file_path);

            //find \author in src and replace the first [Auteur] in $html by the name of the author
            $match = preg_match($patterns_src['auteur'], $source, $matches);
            $author = ($match) ? $matches[1] : '';

            $match = preg_match($patterns_src['date'], $source, $matches);
            $date = ($match) ? $matches[1] : '';

            $match = preg_match($patterns_src['brief'], $source, $matches);
            $brief = ($match) ? $matches[1] : '';

            fwrite($file, '
            <section>
                <h2>
                    Général :
                </h2>
                <p><strong>Auteur :</strong></p>
                <ul>' . $author . '</ul>
                <p><strong>Date :</strong></p>
                <ul>' . $date . '</ul>
                <p><strong>Description du programme : </strong>' . $brief . '
                </p>  
            </section>
            <section>
                <h2>
                Documentation fichier ' . ($i + 1) . ' :
                </h2>
                <h3>
                    Référence du fichier ' . $file_path . ' :
                </h3>
                <h3>Librairies :</h3>');

            //Trouve les #include et selon leur nombre les écrit dans l'html.
            $match = preg_match_all($patterns_src['include'], $source, $matches);
            foreach ($matches[1] as $include) {
                fwrite($file, '<p>#include  &lt;' . $include . '&gt;</p>');
            }

            fwrite($file, '
                <h3>Macros : </h3>
            ');

            //Trouve les #define et selon leur nombre les écrit dans l'html.
            $match = preg_match_all($patterns_src['define'], $source, $matches);
            foreach ($matches[0] as $define) {
                fwrite($file, '<p>' . $define . '</p>');
            }

            fwrite($file, '
                <h3>
                    Types :
                </h3>
            ');

            //Trouve les typedef et selon leur nombre les écrit dans l'html.
            $match = preg_match_all($patterns_src['type'], $source, $matches);
            foreach ($matches[0] as $type) {
                fwrite($file, '<p>' . $type . '</p>');
            }

            fwrite($file, '
                <h3>
                    Fonctions & Procédures :
                </h3>
            ');

            // Utilisation de preg_match_all pour compter le nombre d'occurrences de \param dans chaque bloc de commentaire
            preg_match_all('/\/\*\*(?s:(?!\/\*\*).)*\\\param/', $source, $params_src_all);
            // Afficher le nombre d'occurrences pour chaque bloc de commentaire
            foreach ($params_src_all[0] as $index => $nb_params) {
                //echo "Le nombre d'occurrences de \\param dans la section $index est : " . substr_count($nb_params, '\param') . PHP_EOL;
            }

            $match = preg_match_all($patterns_src['fonction'], $source, $matches);
            foreach ($matches[1] as $j => $function) {

                if (substr($function, 0, 4) == "void") {
                    fwrite($file, '<hr><p><h4>Procédure : </h4>' . $function . '</p>');
                } else {
                    fwrite($file, '<hr><p><h4>Fonction : </h4>' . $function . '</p>');
                }
                $matchb = preg_match_all($patterns_src['briefF'], $source, $matchesb);
                if ($matchb) {
                    fwrite($file, '<p> Description : ' . $matchesb[1][$j] . '</p>');
                }

                fwrite($file, ' <ul><strong>Paramètres :</strong></ul>');

                $matchp = preg_match_all($patterns_src['param'], $source, $matchesp);
                for ($k = 0; $k < substr_count($nb_params, '\param'); $k++) {
                    fwrite($file, '<li>' . $matchesp[0][$k] . '</li>');
                }

                $matchr = preg_match_all($patterns_src['return'], $source, $matchesr);
                if ($matchr && $matchesr[1][$j] != 'void') {
                    fwrite($file, '
                        <ul><strong>Retourne :</strong></ul>');
                    fwrite($file, '<li>' . $matchesr[1][$j] . '</li>');
                }
            }

            fwrite($file, '
            </section>
            ');
        }


        // Écriture du pied de page dans le fichier HTML
        fwrite($file, '
                </main>
                    <style>
                    body {
                        background-color: #f2f2f2;
                        font-family: Arial, sans-serif;
                        margin: 0;
                        padding: 0;
                    }
            
                    h1 {
                        padding: 10px;
                        background-color: #333;
                        color: #fff;
                        text-align: center;
                        margin: 20px 10px;
                        border-radius: 8px;
                    }
            
                    main {
                        padding: 20px; 
                    }
            
                    section {
                        border-radius: 8px;
                        background-color: #fff;
                        padding: 20px;
                        margin: 10px 0;
                        box-shadow: 0 4px 7px rgba(0, 0, 0, 0.5);
                        border: 5px solid #666;
                    }
            
                    h2 {
                        font-size: 24px;
                        color: #333;
                        margin-bottom: 15px;
                        border-bottom: 2px solid #ccc;
                        padding-bottom: 10px;
                    }
            
                    h3 {
                        font-size: 18px;
                        color: #444;
                        margin-top: 20px;
                        margin-bottom: 10px;
                    }
            
                    p {
                        color: #333;
                        line-height: 1.6;
                    }
            
                    ul {
                        margin-top: 5px;
                        margin-bottom: 10px;
                    }
            
                    ul li {
                        color: #666;
                    }
            
                    strong {
                        color: #333;
                    }
            
            
                </style>
            </body>
            </html>
        ');

        // Fermeture du fichier
        fclose($file);
        echo "La documentation technique a été générée avec succès dans \'DOC_TECH-$version.html\'.";
    } else {
        echo "Impossible d'ouvrir le fichier HTML en écriture.";
    } 



    /*/Repeter pour chaque fichier source 
    foreach($files_paths as $file_path) {
        

        //find \date in src and replace the first [Date] in $html by the date of the author
        $match = preg_match($patterns_src['date'], $source, $matches);
        if ($match) {
            $date = $matches[1];
            $html = preg_replace('/\[Date\]/', $date, $html, 1);
        }
    }*/

    

?>