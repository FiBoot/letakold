<?php

class Data {

  public $id;
  public $account_id;
  public $name;
  public $data;
  public $type;
  public $creation_date;
  public $public;

  function __construct($res) {
    $this->id = intval($res['id']);
    $this->account_id = intval($res['account_id']);
    $this->name = $res['name'];
    $this->data = $res['data'];
    $this->type = $res['type'];
    $this->creation_date = $res['creation_date'];
    $this->public = intval($res['public']);
  }

  public function parse_data() {
    return $this->data = json_decode($this->data);
  }

  public function encode_data() {
    return $this->data = json_encode($this->data);
  }

  public function insert_query() {
    $fields = "`account_id`, `name`, `data`, `type`, `creation_date`, `last_update`, `public`";
    $values = "$this->account_id, '".addslashes($this->name)."', '".addslashes($this->encode_data())."', '$this->type', '$this->creation_date', '$this->last_update', $this->public";
    return "($fields) VALUES ($values)";
  }

  public function update_query() {
    $now = date('Y-m-d G:i:s');
    $query = "`name` = '".addslashes($this->name)."', ";
    $query .= "`data` = '".addslashes($this->encode_data())."', ";
    $query .= "`last_update` = '$now', ";
    $query .= "`public` = '$this->public'";
    return $query;
  }

}

?>
