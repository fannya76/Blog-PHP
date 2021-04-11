<?php
require 'pdo_connect.php';

// Récup des catégories
$categ_query = $pdo->query("SELECT * FROM category");
$categ = $categ_query->fetchAll(PDO::FETCH_ASSOC);
// conversion du tab assoc indexé $categ en tab assoc unidimensionnel
$categ_uni = array_column($categ, 'name', 'id');


if (isset($_POST['submit'])) {
    if (!empty($_POST['post'])) {
        // modification de posts récupérés depuis index.php

        // vérif si nom de catégorie existe déjà (tout en minuscule car in_array sensible à la casse)
        $categ_name = $_POST['post']['category'];
        $categ_name = strtolower($categ_name);

        if (in_array($categ_name, $categ_uni)) {  // si la catégorie existe déjà, on ne l'insère pas

            $categ_id = array_search($categ_name, $categ_uni);  // on récupère l'id associé à la categorie

            // insertion du titre, contenu et id de catégorie
            $query = $pdo->prepare("UPDATE post SET title = :title, content = :content, id_category = :id_category WHERE id = :id");
            $query->execute(array(
                'id' => $_POST['post']['id'],
                'title' => $_POST['post']['title'],
                'content' => $_POST['post']['content'],
                'id_category' => $categ_id
            ));

            header('Location: /');
           
            // si c'est une nouvelle catégorie, on l'insère :
        } else {
            // modification de la catégorie 

            $categ_query = $pdo->prepare("INSERT INTO category (name) VALUES (:name)");
            $categ_query->execute(array('name' => $categ_name));
            

            // insertion du titre, contenu et id de catégorie
            $query = $pdo->prepare("UPDATE post SET title = :title, content = :content, id_category = :id_category WHERE id = :id");
            $query->execute(array(
                'id' => $_POST['post']['id'],
                'title' => $_POST['post']['title'],
                'content' => $_POST['post']['content'],
                'id_category' => $categ_id
            ));

            if ($query->rowCount() > 0) {
                header('Location: /');
                exit;
            } else {
                echo "oups, probleme edition new categ";
            }
        }
    }
}
