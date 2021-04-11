<?php
require 'pdo_connect.php';

// $dbh = new PDO('pgsql:host=localhost;dbname=test', $user, $pass); si le sgbd et postgresql


// Récup des posts du blog fictif
$query = $pdo->query("SELECT * FROM post");
$posts = $query->fetchAll(PDO::FETCH_ASSOC);

// Récup des catégories
$categ_query = $pdo->query("SELECT * FROM category");
$categ = $categ_query->fetchAll(PDO::FETCH_ASSOC);
// conversion du tab assoc indexé $categ en tab assoc unidimensionnel
$categ_uni = array_column($categ, 'name', 'id');

// POUR MODIFIER UN ARTICLE
if (!empty($_GET['id'])) {   // le bouton Edit renvoie à cette même page avec l'id en GET
    $query = $pdo->prepare("SELECT * FROM post WHERE id = :id");
    $query->execute(array('id' => $_GET['id']));
    $editPost = $query->fetch(PDO::FETCH_ASSOC); // tableau associatif du résultat de la requête, càd tab assoc de la ligne de l'id
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoblogBis</title>

    <style>
        body {
            font-family: sans-serif;
        }

        th {
            background-color: dodgerblue;
        }

        td,
        th {
            border: 1px solid #333;
            padding: 10px;
        }
    </style>

</head>

<body>
    <h2>TABLEAU DES ARTICLES</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>TITRE</th>
            <th>CONTENU</th>
            <th>Categorie</th>
            <th>ID de Categorie</th>
            <th colspan="2">ACTIONS</th>
        </tr>

        <?php
        foreach ($posts as $key => $post) { ?>
            <tr>
                <td><?= $post['id'] ?></td>
                <td><?= $post['title'] ?></td>
                <td><?= $post['content'] ?></td>
                <td><?= $categ_uni[$post['id_category']] ?></td>
                <td><?= $post['id_category'] ?></td>

                <td><a href="/?id=<?= $post['id'] ?>">Editer</a></td>
                <td>
                    <form action="delete_post.php" method="POST" onsubmit="return confirm('Vous confirmez la suppression ?')">
                        <input type="hidden" name="id" value="<?= $post['id'] ?>">
                        <input type="hidden" name="id_category" value="<?= $post['id_category'] ?>">

                        <button type="submit">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
    <br>

    <h3>Ajouter ou Modifier un article :</h3>

    <form action="<?= !empty($editPost['id']) ? 'edit_post.php' : 'add_posts.php' ?>" method="POST" enctype="multipart/form-data">

        <?php if (!empty($editPost['id'])) { ?>
            <input type="hidden" name="post[id]" value="<?= $editPost['id'] ?>">
            <input type="hidden" name="post[id_category]" value="<?= $editPost['id_category'] ?>">
        <?php } ?>

        <label>Titre</label><br>
        <input type="text" name="post[title]" value="<?= !empty($editPost['title']) ? $editPost['title'] : '' ?>"><br>

        <label>Contenu</label><br>
        <textarea name="post[content]" rows="10" cols="30"><?= !empty($editPost['content']) ? $editPost['content'] : '' ?></textarea><br>

        <label>Categorie</label><br>
        <input type="text" name="post[category]" value="<?= !empty($editPost['id_category']) ? $categ_uni[$editPost['id_category']] : '' ?>"><br>

        <label>Uploader une image</label><br>
        <input type="file" name="img_url"><br>

        <input type="submit" name="submit" value="Enregistrer">
    </form>

</body>

</html>