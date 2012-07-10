<?php
$cwd = dirname(__FILE__);
require($cwd . '/../../../config.php');
require_once ($cwd . '/_functions.php');
require_once ($CFG->dirroot . '/lib/deprecatedlib.php');
require_login();
if(! isadmin())	die("Not Admin");

dbOpenConnection();
if(isset($_GET["guideId"]))
{
	$guideId = $_GET["guideId"];
	$postString = '?guideId='.$guideId;
}
else if(isset($_GET["action"]))
{
	$guideId = 0;
	$postString = '?action=new';
}
else
	die();

header("Content-type: text/html; charset=utf-8"); 
if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	if(isset($_GET["guideId"]))
		dbQuery('UPDATE mdl_elsa_guider SET kategori = '.check_input($_POST['kategorier']).', titel_dk = '.check_input($_POST['titeldk']).', indh_dk = '.check_input($_POST['main_contentdk']).', titel_en = '.check_input($_POST['titelen']).', indh_en = '.check_input($_POST['main_contenten']).' WHERE id = '.$guideId);
		
	if(isset($_GET["action"]))
		dbQuery('INSERT INTO mdl_elsa_guider (kategori, titel_dk, indh_dk, titel_en, indh_en) VALUES('.check_input($_POST['kategorier']).', '.check_input($_POST['titeldk']).', '.check_input($_POST['main_contentdk']).', '.check_input($_POST['titelen']).', '.check_input($_POST['main_contenten']).')');
	
	print_r( 'Indholdet blev gemt!<br/>' );

}

echo'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN"><HTML><HEAD>';

if($guideId == 0)
	echo '<TITLE>Opret nyt hjælpeemne</TITLE>';
else
	echo '<TITLE>Rediger hjælpeemne indhold</TITLE>';

echo '<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="../../../lib/editor/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
tinyMCE.init({
	mode : "exact",
	theme : "advanced",
	elements : "main_contentdk,main_contenten"
});
</script>

</HEAD>
<script src="googleanalytics.js"></script>
<body>';

$query = "SELECT kategori, titel_dk, titel_en, indh_dk, indh_en FROM mdl_elsa_guider WHERE id=".$guideId;
			$guides = dbQuery($query);
			$guide = dbFetchArray($guides);


echo '<form accept-charset="utf-8" method="post" action="edit.php'.$postString.'">
	<p>
		Titel DK:<br />
		<textarea id="titeldk" name="titeldk" cols="50" rows="1">'.$guide["titel_dk"].'</textarea><br />
	</p>
	<p>
		Indhold DK:<br/>
		<textarea id="main_contentdk" name="main_contentdk" cols="50" rows="15">'.$guide["indh_dk"].'</textarea>
	</p>
	<p>
		Titel EN:<br />
		<textarea id="titelen" name="titelen" cols="50" rows="1">'.$guide["titel_en"].'</textarea><br />
	</p>
	<p>
		Indhold EN:<br/>
		<textarea id="main_contenten" name="main_contenten" cols="50" rows="15">'.$guide["indh_en"].'</textarea>
	</p>
';

echo '<br/>Vælg kategori:<br/>
    <select name="kategorier">';
	$query = "SELECT * FROM mdl_elsa_kategorier ORDER BY id";
	$categories = dbQuery($query);
	while($category = dbFetchArray($categories)){
	    echo '<option';
		if($category["id"] == $guide["kategori"]) echo ' selected ';
	    echo ' value="'.$category["id"].'">'.$category["titel_dk"].'('.$category["titel_en"].')</option>';
	}

echo '</select><br/><br/><input type="submit" value="Save" /></form>';
echo '</body></html>';
?>
