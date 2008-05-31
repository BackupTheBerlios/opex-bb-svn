<?php
/* Forenmodul

Ente, ente :)

*/

class Forum
{
	private $database;

	function __construct()
	{
	}
	
	function Run()
	{
		/* damn, how to know what to do? */
	}
	
	function DisplayForums()
	{
		$boards = $this->database->select(	"*",
							"subboards",
							Array("refid" => "='".(isset($_GET["refboard"]) ? $_GET["refboard"] : "")."'") /* Würgstelle, die Where Klausel muss verbessert werden */
						);
		$template = new Template("board.tpl.html", Array());

		while($board = $this->database->fetch_array($boards, "assoc")) /* eine Konstante zu definieren wäre nicht dumm */
		{
			$template->tpl_vars = Array(	"name"		=> $board["name"],
							"description"	=> $board["description"],
							"link"		=> ($board["refboard"] != "" ? $board["refbaord"] : "TODO - Display Threads")
						);
			$template->Load();
		}
	}
}