<?php

class Data {

  public $id;
  public $account_id;
  public $name;
  public $data;
  public $type;
  public $date_creation;
  public $public;

  function __construct($res) {
    $this->id = intval($res['id']);
    $this->account_id = intval($res['account_id']);
    $this->name = $res['name'];
    $this->data = json_decode($res['data']);
    $this->type = $res['type'];
    $this->date_creation = $res['creation_date'];
    $this->public = intval($res['public']) ? true : false;
  }
}

?>
