<?php

$cwd = dirname(__FILE__);
require_once ($cwd . '/../../../config.php');
require_once ($cwd . '/_functions.php');
require_once ($CFG->dirroot . '/lib/deprecatedlib.php');
require_login();
if(! isadmin())	die("Not Admin");

dbOpenConnection();
if(isset($_GET["catId"]))
{
	$catId = $_GET["catId"];
}
else
	$catId = 0;

if($_GET["action"] == "delete")
{
	dbQuery('DELETE FROM mdl_elsa_kategorier WHERE id='.$catId);
	header('Location:'.$CFG->wwwroot.'/blocks/elsa/popup/editcat.php');
}

header("Content-type: text/html; charset=utf-8"); 
if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	if(isset($_GET["catId"]) && $_GET["action"] == "edit")
	{
		dbQuery('UPDATE mdl_elsa_kategorier SET titel_dk = '.check_input($_POST['newcatdk']).', titel_en = '.check_input($_POST['newcaten']).'  WHERE id = '.$catId);
		header('Location:'.$CFG->wwwroot.'/blocks/elsa/popup/editcat.php');
	}
	if($_GET["action"] == "new")
	{
		dbQuery('INSERT INTO mdl_elsa_kategorier (titel_dk, titel_en) VALUES('.check_input($_POST['newcatdk']).', '.check_input($_POST['newcaten']).')');
	}
	
	print_r( 'Indholdet blev gemt!<br/>' );
}

echo'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN"><HTML><HEAD>';

if($catId !== 0)
	echo '<TITLE>Rediger kategori</TITLE>';
else
	echo '<TITLE>Liste over kategorier</TITLE>';

echo'</HEAD>
<script src="googleanalytics.js"></script>
<body><br />';
if($catId == 0)
{
	echo 'Kategorier: <br /><br />';

	$query = "SELECT id, titel_dk, titel_en FROM mdl_elsa_kategorier ORDER BY id";
		$categories = dbQuery($query);

		while($category = dbFetchArray($categories)){
			echo $category["titel_dk"].'('.$category["titel_en"].') - <a href="editcat.php?catId='.$category["id"].'">Rediger</a> / <a href="editcat.php?action=delete&catId='.$category["id"].'">Slet</a> <br />';
		}
	echo '<br/><br/><hr>Opret ny kategori:<br/><form name="newcatform" action="'.$CFG->wwwroot.'/blocks/elsa/popup/editcat.php?action=new" method="POST"><br/>DK: <input name="newcatdk" type="text" size="25" value="Indtast kategori navn her!"><br/><br/>EN: <input name="newcaten" type="text" size="25" value="Indtast kategori navn her!"><br/><input type="submit" value="Save" /></form>';
}
else
{
	echo 'Rediger Kategori: <br /><br />';
	$query = "SELECT id, titel_dk, titel_en FROM mdl_elsa_kategorier WHERE id = ".$catId;
	$array = dbQuery($query);
	$category = dbFetchArray($array);
	echo '<form name="newcatform" action="'.$CFG->wwwroot.'/blocks/elsa/popup/editcat.php?action=edit&catId='.$category["id"].'" method="POST"><br/>DK: <input name="newcatdk" type="text" size="25" value="'.$category["titel_dk"].'"><br/><br/>EN: <input name="newcaten" type="text" size="25" value="'.$category["titel_en"].'"><br/><input type="submit" value="Save" /></form>';

}
echo '</body></html>';
