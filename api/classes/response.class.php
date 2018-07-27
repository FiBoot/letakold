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

  function update($status, $message) {
    $this->status = $status;
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
