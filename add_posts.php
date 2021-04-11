<?php

// LES REQUETES PREPAREES SONT EN QLQ SORTE DES REQUETES DYNAMIQUES
// CAR ELLES PRENNENT DES VALEURS VARIABLES EN PARAMETRES, soit avec un point d'interrogation ou :nomDeChamp

// différentes méthodes avec execute : https://www.php.net/manual/fr/pdostatement.execute.php

require 'pdo_connect.php';


// Récup des catégories
$categ_query = $pdo->query("SELECT * FROM category");
$categ = $categ_query->fetchAll(PDO::FETCH_ASSOC);
// conversion du tab assoc indexé $categ en tab assoc unidimensionnel
$categ_uni = array_column($categ, 'name', 'id');

if (isset($_POST['submit'])) {
    if (!empty($_POST['post'])) {

        // on vérifie que le fichier a bien été téléchargé
        try {
            if ($_FILES['img_url']['error'] == 1) {
                throw new Exception("Problème lors de l'upload");
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        
        // On déplace le fichier uploadé car par défaut il est uploadé dans un fichier temp et est effacé à la fin du script si pas déplacé ou renommé
        move_uploaded_file($_FILES['img_url']['tmp_name'], 'uploads/'.basename($_FILES['img_url']['name']));

        // vérif si nom de catégorie existe déjà (tout en minuscule car in_array sensible à la casse)
        $categ_name = $_POST['post']['category'];
        $categ_name = strtolower($categ_name);

        if (in_array($categ_name, $categ_uni)) {  // si la catégorie existe déjà, on ne l'insère pas
            $categ_id = array_search($categ_name, $categ_uni);  // on récupère l'id associé à la categorie

            // insertion du titre, contenu et id de catégorie
            $query = $pdo->prepare("INSERT INTO post (title, content, id_category) VALUES (:title, :content, :id_category)");
            $query->execute(array(
                'title' => $_POST['post']['title'],
                'content' => $_POST['post']['content'],
                'id_category' => $categ_id
            ));

            if ($query->rowCount() > 0) {  // rowCount renvoie le nb de lignes ajoutées OU supprimés (donc nb positif)
                header('Location: /');
                exit;
            } else {
                echo "oups, probleme";
            }
        } else {   // si c'est une nouvelle catégorie, on l'insère :

            // inserton de new posts récupérés depuis index.php

            // insertion de la catégorie
            $categ_query = $pdo->prepare("INSERT INTO category (name) VALUES (:name)");
            $categ_query->execute(array('name' => $categ_name));

            // recup de l'id inséré
            $lastInsertId = $pdo->lastInsertId();

            // insertion du titre, contenu et id de catégorie
            $query = $pdo->prepare("INSERT INTO post (title, content, id_category) VALUES (:title, :content, :id_category)");
            $query->execute(array(
                'title' => $_POST['post']['title'],
                'content' => $_POST['post']['content'],
                'id_category' => $lastInsertId
            ));

            if ($query->rowCount() > 0) {  // rowCount renvoie le nb de lignes ajoutées OU supprimés (donc nb positif)
                header('Location: /');
                exit;
            } else {
                echo "oups, probleme";
            }
        }
    }
}
