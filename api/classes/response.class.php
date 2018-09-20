<?php

class Response {

  public $action;
  public $status;
  public $message;
  public $data;

  public $query_count;
  public $elapsed_time;

  function __construct() {
    $this->action = null;
    $this->status = false;
    $this->message = "Une erreur s'est produite";
    $this->data = null;

    $this->query_count = 0;
    $this->elapsed_time = 0;
  }

  function update($status, $message) {
    $this->status = $status ? true : false;
    $this->message = $message;
  }

  function ok($message = null) {
    $this->update(true, $message);
  }

  function nok($message = null) {
    $this->update(false, $message);
  }


}

?>
