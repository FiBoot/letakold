<?php
include "include.php";

if ($post) {

  switch ($post->action)
  {
    case "GET":
      $json['status'] = true;
      $json['message'] = 'oui '.$post->type;
    break;

    case "LIST":
      $json['status'] = false;
      $json['message'] = 'non '.$post->type;
    break;

    case "SAVE":
    break;

    case "DELETE":
    break;
  }

  /* SENDING RESPONSE */

  $sql->close();

  $json["sql"]["query_count"]    = $sql->query_count();
  $json["sql"]["elapsed_time"]   = $sql->get_elapsed_time();

  header('Content-Type: application/json');
  echo json_encode($json);

  exit;
}


/* MAIL FUNCTIONS */

function send_activation_mail($email, $key)
{
  $to = $email;
  $subject = "Activation de votre compte Letakol";
  $message = "<table><tr><td>Bienvenue sur Letakol/FiBoot :)</td></tr><tr><td>Voici votre lien d'activaiton:</td><td><a href='http://letakol.free.fr/fiboot/#/account/new/$key'>Activer mon compte</a></td></tr></table>";
  $headers  = "MIME-Version: 1.0"."\r\n";
  $headers .= "Content-type: text/html; charset=iso-8859-1"."\r\n";

  return mail($to, $subject, $message, $headers);
}

function send_newpassword_mail($email, $key)
{
  $to = $email;
  $subject = "Recuperation de votre mot de passe Letakol";
  $message = "<table><tr><td>Requête de récupération de mot de passe</td></tr><tr><td>Voici votre lien de génération d'un nouveau mot de passe: </td><td><a href='http://letakol.free.fr/fiboot/#/account/newpassword/$key'>Nouveau mot de passe</a></td></tr></table>";
  $headers  = "MIME-Version: 1.0"."\r\n";
  $headers .= "Content-type: text/html; charset=iso-8859-1"."\r\n";

  return mail($to, $subject, $message, $headers);
}

?>
