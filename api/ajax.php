<?php

include_once 'includes.php';

class Ajax {

  const TABLE = "fiboot_global";

  private $user;
  private $rep;


	function __construct() {
    $input = file_get_contents("php://input");
    $post = json_decode($input);

    Sql::start();
    User::find();
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

      case "connect":
        if ($post->type.strchr("account") && $data->username && $data->password) {

        }
      break;

      case "disconnect":
        $this->user->disconnect();
      break;

      case "get":
        $data_keys = array_keys((array)$data);
        $result = $this->query_get($post->type, $data_keys[0], $data->id);

        if ($result) {
          $this->rep->data = $result;
          $this->rep->ok();
        } else {
          $this->rep->nok("La récupération de $post->type a échoué");
        }
      break;

      case "list":
        $query = new Query(EQueryCommand::SELECT, $post->type);
        $query->setOrder("id", EQueryOrder::ASC);

        $res = $query->exec();
        $this->rep->data = Sql::assoc_tab($res);
        $this->rep->ok('ok');

        // $results = $this->query_list($post->type);
        // if ($results) {
        //   $this->rep->data = $results;
        //   $this->rep->ok();
        // } else {
        //   $this->rep->nok("La récupération de list $post->type a échoué");
        // }
      break;

      case "add":
        $_SESSION['connected'] = true;
        $_SESSION['id'] = intval($post->type);
        $this->rep->ok("ok");
      break;

      case "save":
        // $this->db_import();
        $results = $this->query_list(null, false, " ORDER BY `creation_date` DESC limit 10");
        $this->rep->data = $results;
        $this->rep->ok();
      break;

      case 'update':
        $this->rep->update($_SESSION['connected'] ? true : false, "id : ". ($_SESSION['connected'] ? $_SESSION['id'] : 0));
      break;

      case "delete":
        session_destroy();
        $this->rep->ok("ok");
      break;

      default:
        $this->rep->nok("Aucune action");
    }

    // SEND REPSONSE
    Sql::close();
    $this->send();
  }

  function send() {
    $this->rep->query_count = Sql::query_count();
    $this->rep->elapsed_time = Sql::get_elapsed_time();

    header('Content-Type: application/json');
    echo json_encode($this->rep);
  }


  /* -----------------
  *    SQL ACTIONS
  ----------------- */

  function query_get($type, $option = null) {
    $query = $this->start_query($type, false, $option);
    $res = Sql::query($query);

    if (Sql::row_number($res) === 1) {
      $arr = Sql::fetch_array($res);
      return new Data($arr);
    }
    return null;
  }

  function query_list($type, $force = false, $option = null) {
    $query = $this->start_query($type, $force, $option);
    $res = Sql::query($query);

    $results = array();
    while ($arr = Sql::fetch_array($res)) {
      $results[] = new Data($arr);
    }
    return $results;
  }


  /* -----------------
  *       TMPS
  ----------------- */

  function db_import() {
    $res = Sql::query("SELECT * FROM `fiboot_timeline`");
    while ($row = Sql::fetch_array($res)) {
      $data = '{date_start: "'.$row['start'].'"';
      $data .= ', date_end: "'.$row['end'].'"';
      $data .= "}";
      $query = "INSERT INTO `". self::TABLE ."` (`id`, `account_id`, `name`, `data`, `type`, `creation_date`, `public`) VALUES ('".(1000 + intval($row['id']))."', '".$row['account_id']."', '".addslashes($row['content'])."', '".$data."', 'timeline_event', '".$row['date_created']."', '".$row['public']."')";
      Sql::query($query);
    }
    $this->rep->data = Sql::get_queries();
    $this->rep->ok();
  }
}

new Ajax;
exit;

?>
