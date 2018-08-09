<?php

class USER {

  public static $data = null;

  public static function connect($username, $password) {
    $query = new Query(EQueryCommand::SELECT, 'account');
    $query->add_param('name', EComparator::EQUAL, $username);
    $query->force();
    $res = $query->exec();

    if (SQL::query_count($res) === 1) {
      $arr = SQL::fetch_assoc($res);
      $user = new Data($arr);
      $user->parse_data();

      if ($user->data->password === md5($password)) {
        $_SESSION['USER'] = $user;
        self::$data = $user;
        return true;
      }
    }
    return false;
  }

  public static function disconnect() {
    $wc = self::get();
    session_destroy();
    return $wc ? true : false;
  }

  public static function get() {
    if (!self::$data && isset($_SESSION['USER'])) {
      self::$data = $_SESSION['USER'];
    }
    return self::$data;
  }

}

?>
