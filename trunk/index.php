<?php
/*
 *      index.php
 *      Last Change: 2008-06-07
 *
 *      (C) Copyright 2008 by the OpeX BB Development Team
 *
 *      This program is free software: you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation, either version 3 of the License, or
 *      (at your option) any later version.
 *
 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *
 *      You should have received a copy of the GNU General Public License
 *      along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


include("inc/db.class.php");

/* hier sind die Templates gespeichert */
define("TPL_DIR", "template/");

/* hier sind die Module gespeichert */
define("MOD_DIR", "modules/");


class Template
{
	/* die Werte für die Platzhalter */
	private $tpl_vars;

	/* eine Instanz der Database-Klasse von db.class.php */
	private $database;

	function __construct($file = null, $tpl_vars = null)
	{
		if(isset($tpl_vars))
			$this->tpl_vars = $tpl_vars;
		if(isset($file))
			$this->Load($file);
	}

	/* Diese Funktion ladet das Template und gibt es direkt aus */
	function Load($file)
	{
		/* wenn das Template nicht existiert, abbrechen */
		if( !file_exists(TPL_DIR . $file) )
			return false;

		/* nun wird das Template dargestellt */
		include TPL_DIR . $file;

		return true;
	}

	/*	Diese Funktion ladet das Template und unterdrückt die Ausgabe.
		Die Ausgabe wird zurückgegeben.
	*/
	function LoadSilent($file)
	{
		/* wenn das Template nicht existiert, abbrechen */
		if(!file_exists(TPL_DIR.$file))
			return false;

		/* Ausgabe unterdrücken */
		ob_start();

		/* Template laden */
		include TPL_DIR . $file;

		/* Ausgabepuffer zwischenspeichern */
		$text = ob_get_contents();
		/*	Damit neue Ausgaben mit echo oder print wieder ausgegeben werden,
			muss die Ausgabenunterdrückung wieder abgeschaltet werden
		*/
		ob_end_clean();

		return $text;
	}

	function __set($var, $value)
	{
		switch($var)
		{
			case "tpl_vars":
				$this->tpl_vars = $value;
			case "database":
				$this->database = $value;
				break;
			default:
				/* Definition eines Platzhalters */
				$this->tpl_vars[$var] = $value;
				break;
		}
	}

	function __get($var)
	{
		switch($var)
		{
			case "database":
				return $this->database;
				break;
			default:
				if(isset($this->tpl_vars[$var]))
					return $this->tpl_vars[$var]; // Abrufen eines Platzhalters <- oneliner
				else
					return "";
				break;
		}
	}

	/* laden der Werte für die Platzhalter aus der Datenbank */
	function setContentFromDatabase($id)
	{
		$values = $this->database->select(
			Array("name", "content"),
			"content",
			Array(	"id" => "='".
				$this->database->escape($id).
				"'")
			);

		while($value = $this->database->fetch_array($values, "assoc"))
			$this->$value["name"] =
				(isset($this->$value["name"]) ? $this->$value["name"] : "").
				utf8_decode($value["content"]); // <<-- das mit dem Decode muss anders gelöst werden; Christian macht das!

		/* Laden eines "Moduls" */
		$modules = $this->database->select(
			Array("name", "file", "class"),
			"modules",
			Array(	"id" => "='".
				$this->database->escape($id).
				"'")
			);

		while($module = $this->database->fetch_array($modules, "assoc"))
		{
			/* Überprüfen, ob das Modul existiert */
			if(!file_exists(MOD_DIR . $module["file"])) continue;

			/*	Es kann passieren, dass ein Modul 2x auf einer Seite geladen wird,
				deshalb wird es nur inkludiert, wenn die Klasse für das Modul
				noch nicht existiert
			*/
			if(!class_exists($module["class"])) include MOD_DIR . $module["file"];

			/* Ausgabe unterdrücken */
			ob_start();

			$content = new $module["class"](); /* Instanz von der Modulklasse laden */
			$content->database = $this->database; /* Datenbankobjekt übergeben ans Modul ---- TODO ----- */
			$content->Run(); /* Modul ausführen */

			$this->{$module["name"]} .= ob_get_contents(); /* Ausgaben an den Platzhalten hinzufügen */
			ob_end_clean(); /* Ausgabenpuffer löschen */
		}
	}
}


$id = isset($_GET["id"]) ? (ctype_digit($_GET["id"]) ? $_GET["id"] : 1) : "1";


$template = new Template();
$template->database = new database("mysql", "localhost", "root", "passwort", "rockboard", true); # bestens
$template->setContentFromDatabase($id);
$template->Load("testTemplate.php"); /* Danke */
?>
