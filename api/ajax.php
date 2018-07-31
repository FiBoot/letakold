<?php

include_once 'includes.php';

class Ajax {

  const TABLE = "fiboot_global";

  private $user;
  private $rep;


	function __construct() {
    $input = file_get_contents("php://input");
    $post = json_decode($input);

    SQL::start();
    $this->rep = new Response;

    if ($post) {
      $this->rep->set_request($post->action, $post->type);
      $this->exec($post);
    }
  }


  function exec($post) {

    $data = $post->data;

    // PROCESS POST
    switch ($post->action) {

      case "get":
        $query = new Query(EQueryCommand::SELECT, $post->type);
        if ($data->field && $data->value) {
          $query->add_param($data->field, EComparator::EQUAL, $data->value);
        }
        $res = $query->exec($data->force);
        $res_count = SQL::row_number($res);
        if ($res_count === 1) {
          $this->rep->data = SQL::fetch_assoc($res);
          $queries=SQL::get_queries();
          $this->rep->ok($queries[0]);
        } else {
          $this->rep->nok($res_count ? "More than 1 result found" : "No result found");
        }
      break;

      case "list":
        $query = new Query(EQueryCommand::SELECT, $post->type);
        $query->set_order("id");
        $res = $query->exec($data->force);
        $this->rep->data = SQL::assoc_tab($res);
        $queries = SQL::get_queries();
        $this->rep->ok($queries[0]);
      break;


      case 'new':
        $user = USER::get();
        $this->rep->data = $user;
        $this->rep->update($user ? true : false, 'user');
      break;


      case "save":
      break;


      case "delete":
        $query = new Query(EQueryCommand::DELETE, 'test');
        $query->add_param('id', EComparator::EQUAL, $data->value);
        $res = $query->exec($data->force);
        if ($res) {
          $queries = SQL::get_queries();
          $this->rep->data = $res;
          $this->rep->ok($queries[0]);
        } else {
          $this->rep->nok('no user');
        }
      break;


      case "connect":
        $res = USER::connect($data->field, md5($data->value));
        $msg = $res ? "Connection réussie" : "Nom d'utilisateur ou mot de passe incorrect";
        $this->rep->update($res, $msg);
      break;

      case "disconnect":
        USER::disconnect();
      break;

      default:
        $this->rep->nok("Aucune action");
    }

    // SEND REPSONSE
    SQL::close();
    $this->send();
  }

  function send() {
    $this->rep->query_count = SQL::query_count();
    $this->rep->elapsed_time = SQL::get_elapsed_time();

    header('Content-Type: application/json');
    echo json_encode($this->rep);
  }




  /* -----------------
  *    SQL IMPORT
  ----------------- */

  function quote_data() {
    $ignore_quotes = array("activated", "admin", "actif", "checked");

    $query = new Query(EQueryCommand::SELECT, $post->type);
    $query->set_order("id");
    $res = $query->exec(true);

    $reps = array();

    while ($arr = SQL::fetch_assoc($res)) {
      $row = new Data($arr);
      $keys = array_keys((array)$row->data);

      $str_data = "{";
      $first = true;
      foreach ($row->data as $key => $value) {
        $str_data .= ($first ? "" : ", ") . "\"$key\": ". (in_array($key, $ignore_quotes) ? $value : "'".addslashes($value)."'");
        $first = false;
      }
      $str_data .= "}";

      $query = new Query(EQueryCommand::UPDATE);
      $query->add_param('id', EComparator::EQUAL, $row->id);
      $query->add_keyvalue("data", $str_data);
      $reps[] = $query->exec();
    }
    $this->rep->data = $reps;
    $this->rep->ok('ok');
  }

  function db_import() {
    $res = SQL::query("SELECT * FROM `fiboot_dndsheets` ORDER BY `id` ASC");
    $query;
    while ($row = SQL::fetch_assoc($res)) {
      $data = '{"sheet": "'. addslashes($row['sheet']) .'"';
      $data .= "}";
      $query = "INSERT INTO `". self::TABLE ."` (`id`, `account_id`, `name`, `data`, `type`, `creation_date`, `public`) VALUES ('".$row['id']."', '".$row['account_id']."', '".addslashes($row['name'])."', '".$data."', 'dnd_sheet', '".$row['date_created']."', '".$row['public']."')";
      SQL::query($query);
    }
    $this->rep->data = $query;
    $this->rep->ok();
  }
}

new Ajax;
exit;

?>
