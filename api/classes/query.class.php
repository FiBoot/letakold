<?php

abstract class EQueryOrder {
  const ASC = "ASC";
  const DESC = "DESC";
}

abstract class EQueryCommand {
  const SELECT = 0;
  const INSERT = 1;
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

  function __construct($key, $comparator = EComparator::EQUAL, $value) {
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

class KeyValue {
  public $key;
  public $value;
  function __construct($key, $value) {
    $this->key = $key;
    $this->value = $value;
  }
}


class Query {

  const TABLE = "fiboot_global";

  private $force;

  private $type;
  private $params;
  private $keyvalues;
  private $item;
  private $order;
  private $limit;


	function __construct($command = EQueryCommand::SELECT, $type = null) {
    $this->force = false;

    $this->command = $command;
    $this->type = $type;
    $this->params = array();
    $this->item = null;
    $this->order = "";
    $this->limit = "";
  }


  public function force() {
    $this->force = true;
  }

  public function add_param($field, $comparator, $value) {
    $param = new QueryParam($field, $comparator, $value);
    array_push($this->params, $param);
  }

  public function add_query_param($query_param) {
    array_push($this->params, $query_param);
  }

  public function add_keyvalue($key, $value) {
    $keyvalue = new KeyValue($key, $value);
    array_push($this->keyvalues, $keyvalue);
  }

  public function set_item($item) {
    $this->item = $item;
  }

  public function set_order($field, $order = EQueryOrder::ASC) {
    if ($field) {
      $this->order = "ORDER BY `$field` $order";
    }
  }

  public function set_limit($limit) {
    if ($limit) {
      $this->limit = "LIMIT $limit";
    }
  }


  private function get_query_params() {
    $pq = "";
    foreach ($this->params as $key => $value) {
      $pq .= ($key > 0 ? " AND " : "") . $value->get();
    }
    return (count($pq) > 0) ? $pq : "true";
  }

  private function add_public_param($user) {
    if (!($this->force || $user->admin)) {
      $public_param = new QueryParam('public', EComparator::EQUAL, 1);
      if ($user) {
        $user_param = new QueryParam('account_id', EComparator::EQUAL, $user->id);
        $public_param->or_query($user_param);
      }
      $this->add_query_param($public_param);
    }
  }

  private function add_private_param($user) {
    if (!($this->force || $user->admin)) {
      $this->add_param("account_id", EComparator::EQUAL, $user->id);
    }
  }

  private function build_insert_keyvalue() {
    $qkv = (object)array("keys" => "", "values" => "");
    foreach ($this->keyvalues as $key => $kv) {
      $qkv->keys .= ($key > 0 ? ", " : "") . "`$kv->key`";
      $qkv->values .= ($key > 0 ? ", " : "") . "'". addslashes($kv->value) ."'";
    }
    return $qkv;
  }

  private function build_update_keyvalue() {
    $qkv = "";
    foreach ($this->keyvalues as $key => $kv) {
      $qp = new QueryParam($kv->key, EComparator::EQUAL, $kv->value);
      $qkv .= ($key > 0 ? ", " : "") . $qp->get();
    }
    return $qkv;
  }


  public function exec($item = null) {
    if ($this->type) {
      $this->add_param('type', EComparator::EQUAL, $this->type);
    }

    $user = USER::get();
    $query = null;

    switch ($this->command) {

      case EQueryCommand::SELECT:
        $this->add_public_param($user);
        $query_params = $this->get_query_params();
        $query = rtrim("SELECT * FROM `". self::TABLE ."` WHERE $query_params $this->order $this->limit");
      break;

      case EQueryCommand::INSERT:
        if ($user && $item) {
          $item->accout_id = $user->id;
          $now = date('Y-m-d G:i:s');
          $item->creation_date = $now;
          $item->last_update = $now;
          $insert_query = $item->insert_query();
          $query = "INSERT INTO `". self::TABLE ."` $insert_query";
        }
      break;

      case EQueryCommand::UPDATE:
        if ($user && $item) {
          $this->add_param('id', EComparator::EQUAL, $item->id);
          $this->add_private_param($user);
          $query_params = $this->get_query_params();
          $update_query = $item->update_query();
          $query = "UPDATE `". self::TABLE ."` SET $update_query WHERE $query_params";
        }
      break;

      case EQueryCommand::DELETE:
        if ($user && $item) {
          $this->add_param('id', EComparator::EQUAL, $item->id);
          $this->add_private_param($user);
          $query_params = $this->get_query_params();
          $query = "DELETE FROM `". self::TABLE ."` WHERE $query_params";
        }
      break;
    }

    return $query ? SQL::query($query) : null;
  }

}

?>
