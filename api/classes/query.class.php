<?php

abstract class EQueryOrder {
  const ASC = "ASC";
  const DESC = "DESC";
}

abstract class EQueryCommand {
  const SELECT = 0;
  const INSERT_INTO = 1;
  const UPDATE = 2;
  const DELETE = 3;
}

abstract class EComparator {
  const EQUAL = "=";
  const SUPERIOR = ">";
  const INFERIOR = "<";
  const MORE = ">=";
  const LESS = "<=";
  const DIFFERENT = "<>";
  const LIKE = "LIKE";
}

class QueryParam {
  public $key;
  private $param;

  function __construct($key, $comparator, $value) {
    $this->key = $key;
    $this->param = "`$key` $comparator '". addslashes($value) ."'";
  }

  function or_query($query_param) {
    $new_param = $query_param->get();
    $this->param = "($this->param OR $new_param)";
  }
  function get() {
    return $this->param;
  }
}


class Query {

  const TABLE = "fiboot_global";

  private $type;
  private $params;
  private $key_values;
  private $order;
  private $limit;


	function __construct($command = EQueryCommand::SELECT, $type = null) {
    $this->command = $command;
    $this->type = $type;
    $this->params = array();
    $this->key_values = array();
    $this->order = "";
    $this->limit = "";
  }


  public function add_param($param) {
    array_push($this->params, $param);
  }

  public function set_order($field, $order = EQueryOrder::DESC) {
    $this->order = "ORDER BY `$field` $order";
  }

  public function set_limit($limit) {
    $this->limit = "LIMIT $limit";
  }

  public function add_keyvalue($key, $value) {
    $key_value = array(
      "key" => $key,
      "value" => $value
    );
    array_push($this->key_values, $key_value);
  }


  private function get_query_params() {
    $params_query = "";
    foreach ($this->params as $key => $value) {
      $params_query .= ($key > 0 ? " AND " : "") . $value->get();
    }
    return $params_query;
  }

  private function add_public_param($force) {
    if (!$force) {
      $public_param = new QueryParam('public', EComparator::EQUAL, 1);
      $user = USER::get();
      if ($user) {
        $user_param = new QueryParam('account_id', EComparator::EQUAL, $user->id);
        $public_param->or_query($user_param);
      }
      $this->add_param($public_param);
    }
  }


  public function exec($force = false) {
    if ($this->type) {
      $this->add_param(new QueryParam('type', EComparator::EQUAL, $this->type));
    }

    $user = USER::get();
    $query = null;

    switch ($this->command) {

      case EQueryCommand::SELECT:
        $this->add_public_param($force);
        $query_params = $this->get_query_params();
        $query = "SELECT * FROM `". self::TABLE ."` WHERE $query_params $this->order $this->limit";
      break;

      case EQueryCommand::INSERT_INTO:
        if ($user) {
          $this->add_keyvalue("account_id", $user->id);
          $query_keys = "";
          $query_values = "";
          foreach ($this->key_values as $key => $value) {
            $query_keys .= ($key > 0 ? ", " : "") . "`$key`";
            $query_values .= ($key > 0 ? ", " : "") . "'". addslashes($value) ."'";
          }
          $query = "INSERT INTO `". self::TABLE ."` ($query_keys) VALUES ($query_values)";
        } else {
          // TODO: rep
        }
      break;

      case EQueryCommand::UPDATE:
        if ($user && count($this->params) > 0) {
          $this->add_param(new QueryParam('account_id', EComparator::EQUAL, $user->id));
          $query_params = $this->get_query_params();
          $query_keyvalues = "";
          foreach ($this->key_values as $key => $value) {
            $qp = new QueryParam($this->key_values[$key], EComparator::EQUAL, $value);
            $query_keyvalues .= ($key > 0 ? ", " : "") . $qp->get();
          }
          $query = "UPDATE `". self::TABLE ."` SET $query_keyvalues WHERE $query_params";
        }
      break;

      case EQueryCommand::DELETE:
        $this->add_public_param($force);
        $query_params = $this->get_query_params();
        $query = "DELETE FROM `". self::TABLE ."` WHERE $query_params";
      break;
    }

    return SQL::query("$query;");
  }

}

?>
