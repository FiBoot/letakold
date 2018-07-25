<?php

class Sql {

	private $link;
	private $query_number;
	private $queries;

	private $start_time;

	function __construct() {
		$this->bdd_connect();
		$this->query_number	= 0;
		$this->queries = array();
	}


	function bdd_connect() {
		$this->start_time = microtime(true);
		$this->link	= mysql_connect("localhost", "letakol", "283669");
		mysql_select_db("letakol");
	}

	function protect($field) {
		return "\"". addslashes($field) ."\"";
	}

	function query($req) {
		$result	= mysql_query("$req;");
		if (!$result) {
			die("Sql error within: <span style='color:#555;'>$req</span><br /><span style='color:#C00;'>". mysql_error() ."</span>");
		}

		$this->query_number	+= 1;
		array_push($this->queries, $req);

		return $result;
	}

	function fetch_array($res) {
		return mysql_fetch_array($res);
	}

	function assoc_row($res) {
		return mysql_fetch_assoc($res);
	}

	function row_number($res) {
		return mysql_num_rows($res);
	}

	function affected_row($res) {
		return mysql_affected_rows($res);
	}


	function assoc_tab($res) {
		$tab = array();
		while ($row = mysql_fetch_assoc($res))
		{
			$tab[] = $row;
		}
		return $tab;
	}

	function query_count() {
		return $this->query_number;
	}

	function get_queries() {
		return $this->queries;
	}

	function get_elapsed_time() {
		$now = microtime(true);
		return $now - $this->start_time;
	}

	function close() {
		mysql_close($this->link);
	}

}

?>
