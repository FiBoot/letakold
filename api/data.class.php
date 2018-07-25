<?php

class Data {

  public $id;
  public $account_id;
  public $data;
  public $type;
  public $date_creation;
  public $public;

  function __construct($res) {
    $this->id = $res['id'];
    $this->account_id = $res['account_id'];
    $this->data = json_decode($res['data']);
    $this->type = $res['type'];
    $this->date_creation = $res['date_creation'];
    $this->public = $res['public'];
  }
}

?>
