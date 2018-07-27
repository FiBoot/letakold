<?php

class User {

  public static function find() {
    if (isset($_SESSION['connected'])) {
      // query avec $_SESSION username et password
    }
  }

  public static function disconnect() {
    session_destroy();
  }

}

?>
