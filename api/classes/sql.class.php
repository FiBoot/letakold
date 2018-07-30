<?php

class SQL {

	private static $link;
	private static $queries;

	private static $start_time;

	public static function start() {
		if (!self::$link) {
			self::$queries = array();
			self::$start_time = microtime(true);
			self::$link	= mysql_connect("localhost", "letakol", "283669");
			mysql_select_db("letakol");
		}
	}

	public static function protect($field) {
		return "\"". addslashes($field) ."\"";
	}

	public static function query($req) {
		$result	= mysql_query("$req;");
		if (!$result) {
			$error = mysql_error();
			die("Sql error within: <span style='color:#555;'>$req</span><br /><span style='color:#C00;'>$error</span>");
		}

		array_push(self::$queries, $req);

		return $result;
	}

	public static function fetch_assoc($res) {
		return mysql_fetch_assoc($res);
		// return new Data(mysql_fetch_assoc($res));
	}

	public static function row_number($res) {
		return mysql_num_rows($res);
	}

	public static function affected_row($res) {
		return mysql_affected_rows($res);
	}

	public static function assoc_tab($res) {
		$tab = array();
		while ($row = self::fetch_assoc($res)) {
			$tab[] = $row;
		}
		return $tab;
	}

	public static function query_count() {
		return count(self::$queries);
	}

	public static function get_queries() {
		return self::$queries;
	}

	public static function get_elapsed_time() {
		$now = microtime(true);
		return $now - self::$start_time;
	}

	public static function close() {
		mysql_close(self::$link);
	}

}

?>
