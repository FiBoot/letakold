<?php
include_once 'includes.php';

class Ajax {

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
          $data->id = $result;
          $this->rep->data = $this->_get($data);
          $this->rep->ok("Création réussie");
        } else {
          $this->rep->nok("Création échouée");
        }
      break;

      case "save":
        $result = $this->_update($data);
        if ($result > 0) {
          $this->rep->ok("Sauvegarde réussie");
        } else {
          $this->rep->nok("Sauvegarde échouée");
        }
      break;

      case "delete":
        $result = $this->_delete($data);
        if ($result > 0) {
          $this->rep->ok("Suppression réussie");
        } else {
          $this->rep->nok("Suppression échouée");
        }
      break;

      case "connect":
        if ($data->username && $data->password) {
          if (USER::connect($data->username, $data->password)) {
            $user = USER::get();
            $user->data = null;
            $this->rep->data = $user;
            $this->rep->ok("Connecté en tant que $user->name.");
          } else {
            $this->rep->nok("Utilisateur ou mot de passe incorrect.");
          }
        } else {
          $this->rep->nok();
        }
      break;

      case "autoconnect":
        $user = USER::get();
        if ($user) {
          $user->data = null;
          $this->rep->data = $user;
          $this->rep->ok("Connecté en tant que $user->name.");
        } else {
          $this->rep->nok();
        }
      break;

      case "disconnect":
        $wc = USER::disconnect();
        $this->rep->update($wc);
      break;

case "import":
db_import();
$this->rep->data = Sql::get_queries();
$this->rep->ok();
break;

      default:
        $this->rep->nok("Aucune action");

    }

  }


  // Retourne la ligne recherchée
  private function _get($data) {
    if ($data->id) {
      $query = new Query(EQueryCommand::SELECT, $data->type);
      $query->add_param('id', EComparator::EQUAL, $data->id);
      $res = $query->exec();
      if (SQL::row_number($res) === 1) {
        return SQL::fetch_assoc($res);
      }
    }
    return null;
  }

  // Retourne le tableau recherché
  private function _list($data) {
    $query = new Query(EQueryCommand::SELECT, $data->type);
    $query->set_order($data->order, $data->asc ? EQueryOrder::ASC : EQueryOrder::DESC);
    $query->set_limit($data->limit);
    $res = $query->exec();
    return SQL::assoc_tab($res);
  }

  // Retourne l'ID de l'objet créé
  private function _create($data) {
    if ($data->item) {
      $item = new Data((array)$data->item);
      $query = new Query(EQueryCommand::INSERT);
      $query->exec($item);
      return SQL::last_insert_id();
    }
    return null;
  }

  // Retourne le nombre de lignes afféctées (-1 echec)
  private function _update($data) {
    if ($data->item) {
      $item = new Data((array)$data->item);
      $query = new Query(EQueryCommand::UPDATE);
      $query->exec($item);
      return SQL::affected_row();
    }
    return null;
  }

  // Retourne le nombre de lignes afféctées
  private function _delete($data) {
    if ($data->item) {
      $item = new Data((array)$data->item);
      $query = new Query(EQueryCommand::DELETE);
      $query->exec($item);
      return SQL::affected_row();
    }
    return null;
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

function db_import() {
  $res = Sql::query("SELECT * FROM `fiboot_dndsheets`");
  while ($row = Sql::fetch_assoc($res)) {
    $data = "{";
    $data .= '"sheet": "'.str_replace('↵	', '\\n', addslashes(addslashes($row['sheet']))).'"';
    $data .= "}";

    $name = "name";
    $id = 2000+intval($row['id']);
    $type = "dndsheet";

    $now = date('Y-m-d G:i:s');
    $query = "INSERT INTO `fiboot_global` (`id`, `account_id`, `name`, `data`, `type`, `creation_date`, `last_update`, `public`) VALUES ('".$id."', '".$row['account_id']."', '".str_replace("'", "\'", $row[$name])."', '".$data."', '$type', '".$row['date_created']."', '$now', '".$row['public']."')";
    Sql::query($query);
  }
}

new Ajax;
exit;

?>
