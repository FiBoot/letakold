<?php

class Sql {

	var $link;
	var $query_number;
	var $queries;

	var $start_time;

	function __construct()
	{
		$this->start_time = microtime(true);

		$this->bdd_connect();
		$this->query_number	= 0;
		$this->queries = array();
	}


	function bdd_connect()
	{
		$this->link	= mysql_connect("localhost", "letakol", "283669");
		mysql_select_db("letakol");
	}

	function protect($field)
	{
		return "\"".addslashes($field)."\"";
	}

	function query($req)
	{
		$result	= mysql_query($req);
		if (!$result)
			die("Sql error within: <span style='color:#555;'>$req</span><br /><span style='color:#C00;'>". mysql_error() ."</span>");

		$this->query_number	+= 1;
		array_push($this->queries, $req);

		return $result;
	}

	function assoc_tab($res)
	{
		$tab = array();
		while ($row = $this->assoc_row($res))
		{
			$tab[] = $row;
		}
		return $tab;
	}

	function query_count()
	{
		return $this->query_number;
	}

	function get_queries()
	{
		return $this->queries;
	}

	function get_elapsed_time()
	{
		$now 		= microtime(true);
		return $now - $this->start_time;
	}

	function close()
	{
		mysql_close($this->link);
	}

}


?>
