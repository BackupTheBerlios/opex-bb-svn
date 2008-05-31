<?php
/*
	Database connection
	by Simon Fetzel
	7.12.2007

	Supports:
		-mysql
		-sqlite (parts)
		-sqlite3 (parts)
*/
class database
{
	private $host;
	private $sql;
	private $user;
	private $password;
	private $link;

	function __construct($sql, $host, $user, $password = null, $db = null, $connect = false)
	{
		$this->sql =		$sql;
		$this->host =		$host;
		$this->user =		$user;
		$this->password =	$password;
		
		
		if($connect == true)
		{
			$this->connect();
			if($this->link == false)
				return false;
			else
				return true;
		}
	}

	function error($msg, $function)
	{
		echo "\n<b>Error:</b> ".$msg." <b>by function</b> ".$function;
	}

	function connect($db = null)
	{
		switch($this->sql)
		{
			case "sqlite":
				$this->link = sqlite_open($this->host);
				break;
			case "sqlite3":
				$this->link = sqlite3_open($this->host);
				break;
			case "mysql":
				$this->link = mysql_connect($this->host, $this->user,
						 $this->password, true);
				
				if (isset($db))
					$this->query("USE ".$db.";");

				break;
			default:
				$this->error($this->sql." is not supported yet", "connect");
				return false;
				break;
		}
	}

	function field_name($query, $index)
	{
		switch($this->sql)
		{
			case "mysql":
				return mysql_field_name($query, $index);
				break;
			default:
				$this->error($this->sql." is not supported yet", "field_name");
				return false;
				break;
		}
	}

	function field_type($query, $index)
	{
		switch($this->sql)
		{
			case "mysql":
				return mysql_field_type($query, $index);
				break;
			default:
				$this->error($this->sql." is not supported yet", "field_type");
				return false;
				break;
		}
	}

	function field_len($query, $index)
	{
		switch($this->sql)
		{
			case "mysql":
				return mysql_field_len($query, $index);
				break;
			default:
				$this->error($this->sql." is not supported yet", "field_len");
				return false;
				break;
		}
	}


	function query($query)
	{
		switch($this->sql)
		{
			case "mysql":
				$result =  mysql_query($query, $this->link) or
				 die(mysql_error($this->link)." - ".$query);
				return $result;
				break;
			case "sqlite":
				$result =  sqlite_query($this->link, $query) or

					die("<b>Error on query:</b> - ".$query);
				return $result;
				break;
			case "sqlite3":
				$result =  sqlite3_query($this->link, $query) or
					die("<b>Error on query:</b> - ".$query);
				return $result;
				break;
			default:
				$this->error($this->sql." is not supported yet", "query");
				return false;
				break;
		}
	}

	function insert($table, $columns, $values)
	{
		switch($this->sql)
		{
			case "sqlite":
			case "mysql":
				$sql = "INSERT INTO ".$table.
					"(".implode(",", $columns).")".
					" VALUES('".implode("','", $values)."')";
				return $this->query($sql);
				break;
			default:
				$this->error($this->sql." is not supported yet", "query");
				return false;
				break;
		}
	}

	function getWhere($where)
	{
		$sql = " ";
		if(count($where) > 0)
		{
			$sql .= " WHERE ";
			$j = 0;
			foreach($where as $column => $value)
			{
				$sql .= $column.$value;
				if($j != (count($where) -1 ))
					$sql .= " AND ";
				$j++;
			}
		}
		return $sql;
	}

	function update($table, $values, $where)
	{
		switch($this->sql)
		{
			case "sqlite":
			case "sqlite3":
			case "mysql":
				$sql = "UPDATE ".$table." SET";
				$i = 0;
				foreach($values as $key => $value)
				{
					$sql .= " ".$key."='".$value."'";
					if($i != (count($values) - 1))
						$sql .= ",";
					$i++;
				}

				$sql .= $this->getWhere($where);
				return $this->query($sql);
				break;
			default:
				$this->error($this->sql." is not supported yet", "query");
				return false;
				break;
		}
	}

	function num_rows($result)
	{
		switch($this->sql)
		{
			case "mysql":
				return mysql_num_rows($result);
				break;
			case "sqlite":
			case "sqlite3":
				return sqlite_num_rows($result);
				break;
			default:
				$this->error($this->sql." is not supported yet", "num_rows");
				return false;
				break;
		}
	}

	function escape($string)
	{
		switch($this->sql)
		{
			case "mysql":
				return mysql_real_escape_string($string);
				break;
			case "sqlite":
			case "sqlite3":
				return sqlite_escape_string($string);
				break;
			default:
				$this->error($this->sql." is not supported yet", "escape");
				return false;
				break;
		}
	}

	function num_fields($result)
	{
		switch($this->sql)
		{
			case "mysql":
				return mysql_num_fields($result);
				break;
			default:
				$this->error($this->sql." is not supported yet", "num_fields");
				return false;
				break;
		}
	}

	function deleteRow($table, $where = Array(), $limit = null)
	{
		switch($this->sql)
		{
			case "mysql":
			case "sqlite":
			case "sqlite3":
				$sql = "DELETE FROM ".$table.$this->getWhere($where);

				if($limit != null)
					$sql .= " LIMIT ".$limit;

				return $this->query($sql);
				break;
			default:
				$this->error($this->sql." is not supported yet", "query");
				return false;
				break;
		}
	}

	function select($columns, $table, $where = Array(), $limit = null, $order = null)
	{
		switch($this->sql)
		{
			case "sqlite":
			case "sqlite3":
			case "mysql":
				$sql = "SELECT ";
				if(is_array($columns))
					for($i = 0; $i < count($columns); $i++)
					{
						$sql .= $columns[$i];
						if($i != (count($columns) - 1))
							$sql .= ",";
					}
				else
					$sql .= $columns;

				$sql .= " FROM ".$table.$this->getWhere($where);
				if($limit != null && trim($limit) != "")
					$sql .= " LIMIT ".$limit;
				if($order != null && trim($order) != "")
					$sql .= " ORDER BY ".$order;

				return $this->query($sql);
				break;
			default:
				$this->error($this->sql." is not supported yet", "query");
				return false;
				break;
		}
	}

	function fetch_array($result, $mode = "both")
	{
		switch($this->sql)
		{
			case "sqlite":
				switch($mode)
				{
					case "assoc":
						$mode = SQLITE_ASSOC;
						break;
					default:
						$mode = SQLITE_BOTH;
						break;
				}
				if($result != false)
					return sqlite_fetch_array($result, $mode);
				else
					return false;
				break;
			case "sqlite3":
				if($result != false)
					return sqlite3_fetch_array($result);
				else
					return false;
				break;
			case "mysql":
				switch($mode)
				{
					case "assoc":
						$mode = MYSQL_ASSOC;
						break;
					default:
						$mode = MYSQL_BOTH;
						break;
				}
				if($result != false)
					return mysql_fetch_array($result, $mode);
				else
					return false;
				break;
			default:
				$this->error($this->sql." is not supported yet",
				 "fetch_array");
				return false;
				break;
		}
	}

	function __set($name, $value)
	{
		switch($name)
		{
			case "host":
				$this->host = $value;
				break;
			case "sql":
				$this->sql = $value;
				break;
			case "user":
				$this->user = $value;
				break;
			case "db":
				$this->db = $value;
				break;
			case "password":
				$this->password = $value;
				break;
		}
	}

	function __get($name)
	{
		switch($name)
		{
			case "host":
				return $this->host;
				break;
			case "sql":
				return $this->sql;
				break;
			case "user":
				return $this->user;
				break;
			case "db":
				return $this->db;
				break;
			case "connected":
				if($this->link != null)
					return true;
				else
					return false;
				break;
		}
	}
}
?>