<?php
include_once 'json.php';
include_once 'sql.class.php';
include_once 'data.class.php';
include_once 'response.class.php';

class Ajax {

  private $table = "fiboot_global";

  private $sql;
  private $rep;


	function __construct() {
    $input = file_get_contents("php://input");
    $post = json_decode($input);

    $this->sql = new Sql;
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
        $data_keys = array_keys((array)$data);
        $result = $this->get($post->type, $data_keys[0], $data->id);

        if ($result) {
          $this->rep->data = new Data($arr);
          $this->rep->ok();
        } else {
          $this->rep->nok("La récupération de $post->type a échoué");
        }
      break;

      case "list":
      break;

      case "add":
      array_keys($data);
      break;

      case "save":
        $count = 50000000;
        while ($count > 0) {
          $count -= 1;
        }
        $this->rep->ok("save ok");
      break;

      case 'update':
      break;

      case "delete":
        $this->rep->nok("del nok");
      break;

      default:
        $this->rep->nok("Aucune action");
    }

    // SEND REPSONSE
    $this->sql->close();
    $this->send();
  }

  function send() {
    $this->rep->query_count = $this->sql->query_count();
    $this->rep->elapsed_time = $this->sql->get_elapsed_time();

    header('Content-Type: application/json');
    echo json_encode($this->rep);
  }


  function get($type, $field, $value) {
    $field = $field && count($field) > 0 ? $field : "id";
    $query = "SELECT * FROM `$this->table` WHERE `type`=". $this->sql->protect($type)
           ." AND `$field`=". $this->sql->protect($value) .";";
    $res = $this->sql->query($query);

    if ($this->sql->row_number($res) === 1) {
      $arr = $this->sql->fetch_array($res);
      return new Data($arr);
    }
    return null;
  }

}

new Ajax;
exit;

?>
