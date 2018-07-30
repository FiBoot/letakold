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
          $query->add_param(new QueryParam($data->field, EComparator::EQUAL, $data->value));
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
        $query->set_order("creation_date");
        $res = $query->exec($data->force);
        $this->rep->data = SQL::assoc_tab($res);
        $queries=SQL::get_queries();
        $this->rep->ok($queries[0]);
      break;



      case 'new':
        $user = USER::get();
        $this->rep->data = $user;
        $this->rep->update($user ? true : false, 'user');
      break;



      case "save":
        if (!$data->field || !$data->value) {
          $this->rep->nok('n');
        } else {
          $query = new Query(EQueryCommand::SELECT);
          $query->add_param(new QueryParam($data->field, EComparator::EQUAL, $data->value));
          $res = $query->exec(true);
          $arr = new Data(SQL::fetch_assoc($res));
          $keys = array_keys((array)$arr->data);
          $str_data = "{";
          foreach ($arr->data as $key => $value) {
            $str_data .= "\"$key\": \"$value\", ";
          }
          $str_data[strlen($str_data) - 1] = '}';
          $this->rep->data = $str_data;
          $this->rep->ok();
        }
      break;



      case "delete":
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

  function db_import() {
    $res = SQL::query("SELECT * FROM `fiboot_timeline`");
    while ($row = SQL::fetch_array($res)) {
      $data = '{date_start: "'.$row['start'].'"';
      $data .= ', date_end: "'.$row['end'].'"';
      $data .= "}";
      $query = "INSERT INTO `". self::TABLE ."` (`id`, `account_id`, `name`, `data`, `type`, `creation_date`, `public`) VALUES ('".(1000 + intval($row['id']))."', '".$row['account_id']."', '".addslashes($row['content'])."', '".$data."', 'timeline_event', '".$row['date_created']."', '".$row['public']."')";
      SQL::query($query);
    }
    $this->rep->data = SQL::get_queries();
    $this->rep->ok();
  }
}

new Ajax;
exit;

?>
