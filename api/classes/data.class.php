<?php

class Data {

  public $id;
  public $account_id;
  public $owner;
  public $name;
  public $data;
  public $type;
  public $creation_date;
  public $public;

  function __construct($res) {
    $this->id = intval($res['id']);
    $this->account_id = intval($res['account_id']);
    $this->owner = $res['owner'];
    $this->name = $res['name'];
    $this->data = $res['data'];
    $this->type = $res['type'];
    $this->creation_date = $res['creation_date'];
    $this->public = intval($res['public']);
  }

  private function quote_slashes($string) {
    return str_replace("'", "\'", $string);
  }

  public function parse_data() {
    return $this->data = json_decode($this->data);
  }

  public function encode_data() {
    return $this->data = $this->quote_slashes($this->data);
  }

  public function insert_query() {
    $fields = "main.`account_id`, main.`name`, main.`data`, main.`type`, main.`creation_date`, main.`last_update`, main.`public`";
    $values = "$this->account_id, '".$this->quote_slashes($this->name)."', '".$this->encode_data()."', '$this->type', '$this->creation_date', '$this->last_update', $this->public";
    return "(main.$fields) VALUES ($values)";
  }

  public function update_query() {
    $name = addslashes($this->name);
    $data = $this->encode_data();
    $now = date('Y-m-d G:i:s');
    return "main.`name` = '$name', main.`data` = '$data', main.`last_update` = '$now', main.`public` = '$this->public'";
  }

}

?>
