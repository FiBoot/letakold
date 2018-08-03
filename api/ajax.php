<?php

include_once 'includes.php';

class Ajax {

  const TABLE = "fiboot_global";

  private $rep;

	function __construct() {
    $input = file_get_contents("php://input");
    $post = json_decode($input);

    $this->rep = new Response;

    if ($post) {
      $this->rep->action = $post->action;
      SQL::start();
      $this->exec($post->action, $post->data);
      SQL::close();
      $this->send($post->data->debug);
    }
  }


  function exec($action, $data) {

    switch ($action) {

      case 'test':
        $d = new Data((array)$data->item);
        $d->encode_data();
        $this->rep->data = $d;
        $this->rep->ok('oui');
      break;

      case "get":
        $result = $this->_get($data);
        $this->rep->data = $result;
        $this->rep->update($result ? true : false);
      break;

      case "list":
        $result = $this->_list($data);
        $this->rep->data = $result;
        $this->rep->update($result ? true : false);
      break;

      case 'new':
        $result = $this->_create($data);
        if ($result) {
          $item = $this->_get($data);
          $this->rep->data = $item;
          $this->rep->update($item ? true : false);
        } else {
          $this->rep->nok();
        }
      break;

      case "save":

      break;

      case "delete":

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

  }


  private function _get($data) {
    if ($data->id) {
      $query = new Query(EQueryCommand::SELECT, $data->type);
      $query->add_param('id', EComparator::EQUAL, $data->id);
      $res = $query->exec($data->force);
      if (SQL::row_number($res) === 1) {
        return SQL::fetch_assoc($res);
      }
    }
    return null;
  }

  private function _list($data) {
    $query = new Query(EQueryCommand::SELECT, $data->type);
    $query->set_order($data->orderby, $data->asc ? EQueryOrder::ASC : EQueryOrder::DESC);
    $query->set_limit($data->limit);
    $res = $query->exec($data->force);
    return SQL::assoc_tab($res);
  }

  private function _create($data) {
    if ($data->item) {
      $item = new Data((array)$data->item);
      $query = new Query(EQueryCommand::INSERT);
      return $query->exec($data->force, $item);
    }
    return null;
  }

  private function _upodate($data) {

  }

  private function _delete($data) {

  }


  private function send($debug) {
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
