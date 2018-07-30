<?php

class USER {

  public static $data = null;

  public static function connect($username, $password) {
    $query = new Query(EQueryCommand::SELECT, 'account');
    $query->add_param(new QueryParam('name', EComparator::EQUAL, $username));
    $res = $query->exec(true);

    if (SQL::query_count($res) === 1) {
      $arr = SQL::fetch_assoc($res);
      $user = new Data($arr);
      if ($user->data->password === $password) {
        $_SESSION['USER'] = $user;
        self::$data = $user;
        return true;
      }
    }
    return false;
  }

  public static function disconnect() {
    session_destroy();
  }

  public static function get() {
    if (!self::$data && isset($_SESSION['USER'])) {
      self::$data = $_SESSION['USER'];
    }
    return self::$data;
  }

}

?>
