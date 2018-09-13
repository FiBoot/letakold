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

  function __construct($key, $comparator = EComparator::EQUAL, $value, $table_name = "") {
    $this->key = $key;
    $table_name = $table_name ? "$table_name." : "";
    $this->param = "$table_name`$key` $comparator '". addslashes($value) ."'";
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

  private $force;

  private $type;
  private $params;
  private $keyvalues;
  private $order;
  private $limit;


	function __construct($command = EQueryCommand::SELECT, $type = null) {
    $this->force = false;

    $this->command = $command;
    $this->type = $type;
    $this->params = array();
    $this->order = "";
    $this->limit = "";
  }


  public function force() {
    $this->force = true;
  }

  private function get_table($table_name = "") {
    $table = self::TABLE;
    return "`$table` $table_name";
  }

  private function get_inner_fields($table_name = "") {
    $table_name = $table_name ? "$table_name." : "";
    $fields = "user.name as owner";
    foreach (array("id", "account_id", "name", "data", "type", "creation_date", "last_update", "public") as $field) {
      $fields .= ", $table_name$field";
    }
    return $fields;
  }

  private function get_inner_join($table_name = "") {
    $table_name = $table_name ? "$table_name." : "";
    $table = $this->get_table("user");
    return "LEFT JOIN $table ON $table_name`account_id` = user.id";
  }

  public function add_param($field, $comparator, $value, $table_name = "") {
    $param = new QueryParam($field, $comparator, $value, $table_name);
    array_push($this->params, $param);
  }

  public function add_query_param($query_param) {
    array_push($this->params, $query_param);
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

  private function add_public_param($user, $table_name = "") {
    if (!($this->force || $user->data->admin)) {
      $public_param = new QueryParam('public', EComparator::EQUAL, 1, $table_name);
      if ($user) {
        $user_param = new QueryParam('account_id', EComparator::EQUAL, $user->id, $table_name);
        $public_param->or_query($user_param);
      }
      $this->add_query_param($public_param);
    }
  }

  private function add_private_param($user, $table_name = "") {
    if (!($this->force || $user->data->admin)) {
      $this->add_param("account_id", EComparator::EQUAL, $user->id, $table_name);
    }
  }

  public function exec($item = null) {
    $table_name = "main";
    $user = USER::get();
    $query = null;

    if ($this->type) {
      $this->add_param('type', EComparator::EQUAL, $this->type, $table_name);
    }

    switch ($this->command) {

      case EQueryCommand::SELECT:
        $this->add_public_param($user, $table_name);
        $table = $this->get_table($table_name);
        $fields = $this->get_inner_fields($table_name);
        $join = $this->get_inner_join($table_name);
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
