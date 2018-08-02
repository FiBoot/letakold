<?php

include_once 'includes.php';

class Ajax {

  const TABLE = "fiboot_global";

  private $user;
  private $rep;


	function __construct() {
    $input = file_get_contents("php://input");
    $post = json_decode($input);

    $this->rep = new Response;

    if ($post) {
      $this->rep->action = $post->action;
      $this->exec($post->action, $post->data);
    }
  }


  function exec($action, $data) {

    SQL::start();

    // PROCESS POST
    switch ($action) {

      case "get":
        if ($data->id) {
          $query = new Query(EQueryCommand::SELECT, $data->type);
          $query->add_param('id', EComparator::EQUAL, $data->id);
          $res = $query->exec($data->force);
          $res_count = SQL::row_number($res);
          if ($res_count === 1) {
            $this->rep->data = SQL::fetch_assoc($res);
            $this->rep->ok();
          } else {
            $this->rep->nok();
          }
        }
      break;

      case "list":
        $query = new Query(EQueryCommand::SELECT, $data->type);
        if ($data->orderby) {
          $query->set_order($data->orderby, $data->asc ? EQueryOrder::ASC : EQueryOrder::DESC);
        }
        if ($data->limit) {
          $query->set_limit($data->limit);
        }
        $res = $query->exec($data->force);
        $this->rep->data = SQL::assoc_tab($res);
        $this->rep->ok();
      break;

      case 'new':
        $query = new Query(EQueryCommand::INSERT);
        $query->add_keyvalue('type', $data->type);
        $query->add_keyvalue('name', $data->name);
        $query->add_keyvalue('data', $data->value);
        $query->add_keyvalue('public', $data->public);
        $query->add_keyvalue('last_update', date('Y-m-d G:i:s'));
        $res = $query->exec($data->force);
        $this->rep->update($res ? true : false);
      break;

      case "save":
        if ($data->id) {
          $query = new Query(EQueryCommand::UPDATE, $data->type);
          $query->add_param('id', EComparator::EQUAL, $data->id);
          $query->add_keyvalue('name', $data->name);
          $query->add_keyvalue('data', $data->value);
          $query->add_keyvalue('public', $data->public);
          $query->add_keyvalue('last_update', date('Y-m-d G:i:s'));
          $res = $query->exec($data->force);
          $this->rep->update($res ? true : false);
        }
      break;

      case "delete":
        if ($data->id) {
          $query = new Query(EQueryCommand::DELETE, $data->type);
          $query->add_param('id', EComparator::EQUAL, $data->id);
          $res = $query->exec($data->force);
          if ($res) {
            $afr = SQL::affected_row();
            $this->rep->update($afr > 0 ? true : false);
          } else {
            $this->rep->nok('no user');
          }
        }
      break;

      case "connect":
        if ($data->username && $data->password) {
          $res = USER::connect($data->username, md5($data->password));
          $msg = $res ? "Connection réussie" : "Nom d'utilisateur ou mot de passe incorrect";
          $this->rep->update($res, $msg);
        } else {
          $user = USER::get();
          $this->rep->update(
            $user ? true : false,
            $user ? "Connecté en tant que $user->name" : null
          );
        }
      break;

      case "disconnect":
        $wc = USER::disconnect();
        $this->rep->update($wc);
      break;

      default:
        $this->rep->nok("Aucune action");
    }

    // SEND REPSONSE
    SQL::close();
    $this->send($data->debug);
  }

  function send($debug) {
    if ($debug) {
      $this->rep->data = SQL::get_queries();
    }
    $this->rep->query_count = SQL::query_count();
    $this->rep->elapsed_time = SQL::get_elapsed_time();

    header('Content-Type: application/json');
    echo json_encode($this->rep);
  }

}

new Ajax;
exit;

?>
