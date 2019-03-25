<?php
$nom = $_POST['user_name'];                    //On récupère le pseudo et on le stocke dans une variable
$message = $_POST['user_message'];            //On fait de même avec le message

$json = file_get_contents('messages.json');
$messages = json_decode($json);

$m = new stdClass();
$m->user_name = $nom;
$m->user_message = $message;
$messages[] = $m;

file_put_contents('messages.json', json_encode($messages));
