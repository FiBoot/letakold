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
    $this->param = "main.`$key` $comparator '". addslashes($value) ."'";
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

  private function get_table($table_name = "main") {
    $table = self::TABLE;
    return "`$table` `$table_name`";
  }

  private function get_inner_fields() {
    $fields = "user.name as owner";
    foreach (array("id", "account_id", "name", "data", "type", "creation_date", "last_update", "public") as $field) {
      $fields .= ", main.$field";
    }
    return $fields;
  }

  private function get_inner_join() {
    $table = $this->get_table("user");
    return "LEFT JOIN $table ON main.account_id = user.id";
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
    $qp = "";
    foreach ($this->params as $key => $value) {
      $qp .= ($key > 0 ? " AND " : "") . $value->get();
    }
    return (count($qp) > 0) ? $qp : "true";
  }

  private function add_public_param($user) {
    if (!($this->force || $user->data->admin)) {
      $public_param = new QueryParam('public', EComparator::EQUAL, 1);
      if ($user) {
        $user_param = new QueryParam('account_id', EComparator::EQUAL, $user->id);
        $public_param->or_query($user_param);
      }
      $this->add_query_param($public_param);
    }
  }

  private function add_private_param($user) {
    if (!($this->force || $user->data->admin)) {
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
        $table = $this->get_table();
        $fields = $this->get_inner_fields();
        $join = $this->get_inner_join();
        $query_params = $this->get_query_params();
        $where = $query_params ? "WHERE $query_params" : "";
        $query = rtrim("SELECT $fields FROM $table $join $where $this->order $this->limit");
      break;

      case EQueryCommand::INSERT:
        if ($user && $item) {
          $item->accout_id = $user->id;
          $now = date('Y-m-d G:i:s');
          $item->creation_date = $now;
          $item->last_update = $now;
          $table = $this->get_table();
          $insert_query = $item->insert_query();
          $query = "INSERT INTO $table $insert_query";
        }
      break;

      case EQueryCommand::UPDATE:
        if ($user && $item) {
          $this->add_param('id', EComparator::EQUAL, $item->id);
          $this->add_private_param($user);
          $table = $this->get_table();
          $query_params = $this->get_query_params();
          $update_query = $item->update_query();
          $query = "UPDATE $table SET $update_query WHERE $query_params";
        }
      break;

      case EQueryCommand::DELETE:
        if ($user && $item) {
          $this->add_param('id', EComparator::EQUAL, $item->id);
          $this->add_private_param($user);
          $table = $this->get_table();
          $query_params = $this->get_query_params();
          $query = "DELETE FROM $table WHERE $query_params";
        }
      break;
    }

    return $query ? SQL::query($query) : null;
  }

}

?>
