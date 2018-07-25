<?php

class Response {

  public $request;
  public $status;
  public $message;
  public $data;

  public $query_count;
  public $elapsed_time;

  function __construct() {
    $this->request = null;
    $this->status = false;
    $this->message = "Une erreur s'est produite";
    $this->data = null;

    $this->query_count = 0;
    $this->elapsed_time = 0;
  }

  function set_request($action = null, $type = null) {
    $this->request = "$action $type";
  }

  function ok($msg = null) {
    $this->status = true;
    $this->message = $msg;
  }

  function nok($msg = null) {
    $this->status = false;
    $this->message = $msg;
  }

}

?>
