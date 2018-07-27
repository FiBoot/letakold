<?php

class Query {

  const TABLE = "fiboot_global";

  public $type;
  private $params;
  private $order;
  public $limit;

  private $keys;
  private $values;


	function __construct($command = EQueryCommand::SELECT, $type = null) {
    $this->command = $command;
    $this->type = $type;
    $this->params = array();
  }

  function addParam($param) {
    array_push($this->params, $param);
  }

  function setOrder($by, $order = EQueryOrder::DESC) {
    $this->order = "ORDER BY $field $order";
  }

  function insert($keys, $values) {
    $this->keys = $keys;
    $this->values = $values;
  }

  function exec() {

    if ($this->type) {
      $this->addParam(new QueryParam('type', EComparator::EQUAL, $this->type));
    }

    switch ($this->command) {

      case EQueryCommand::SELECT:
        $first = true;
        $queryParams = "";
        foreach ($this->params as $param) {
          $queryParams .= ($first ? "": " AND ") . $param->get();
          $first = false;
        }
        $query = "SELECT * FROM `". self::TABLE ."`";
        $query .= ($queryParams ? " WHERE " : "") . $queryParams;
      break;

      case EQueryCommand::INSERT_INTO:
        $queryKeys = "";
        $first = true;
        foreach ($this->keys as $key) {
          $queryKeys .= ($first ? "" : ", ") ."`$key`";
          $first = false;
        }
        $queryValues = "";
        $first = true;
        foreach ($this->values as $value) {
          $queryValues .= ($first ? "" : ", ") . "'". addslashes($value) ."'";
          $first = false;
        }
        $query = "INSERT INTO `". self::TABLE ."` ($queryKeys) VALUES ($queryValues)";
      break;

      case EQueryCommand::UPDATE:
      break;

      case EQueryCommand::DELETE:
      break;

      default:
        $query = null;
    }

    return Sql::query("$query;");
  }

}


class QueryParam {
  private $param;
  function __construct($field, $comparator, $value) {
    $this->param = "`$field` $comparator '". addslashes($value) ."'";
  }
  function get() {
    return $this->param;
  }
}

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

?>
