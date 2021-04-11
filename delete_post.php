<?php
require 'pdo_connect.php';

// SUPPRESSION AVEC $_GET pas très secure car n'importe qui peut supprimer depuis l'url

if (isset($_POST['id'])) {

    // on peut mettre une condition si on veut supprimer aussi la catégoie si et slmt si il y a une seule entrée de cette catégorie dans post
    $entrees = $pdo->query("SELECT COUNT(*) FROM post WHERE id_category = '$_POST[id_category]'");
    $entrees->execute();
    $nb_entrees = $entrees->fetchColumn();

    // var_dump($nb_entrees);

    if ($nb_entrees == '1') {
        // echo 'y\'en a qu\'un';
 
        $query = $pdo->prepare("DELETE FROM post WHERE id = :id");
        $query->execute(array('id' => $_POST['id']));
        
        $query_delete_categ = $pdo->prepare("DELETE FROM category WHERE id = :id");
        $query_delete_categ->execute(array('id' =>  $_POST['id_category']));

        if ($query->rowCount() > 0) {
            header('Location: /');
            exit;
        } else {
            echo "oups, probleme1";
        }
    } else {

        $query = $pdo->prepare("DELETE FROM post WHERE id = :id");
        $query->execute(array('id' => $_POST['id']));

        if ($query->rowCount() > 0) {
            header('Location: /');
            exit;
        } else {
            echo "oups, probleme2";
        }
    }
}
