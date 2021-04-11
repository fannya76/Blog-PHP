<?php

// CONNEXION A LA BDD A TRAVERS PDO (PHP Data Objects)
$user = 'root';
$pass = '';
$pdo = new PDO('mysql:host=localhost;dbname=td10bis', $user, $pass); // dbname : nom de notre bdd, ici td10