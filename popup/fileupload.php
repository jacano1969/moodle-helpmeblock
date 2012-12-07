<?php

$cwd = dirname(__FILE__);
require($cwd . '/../../../config.php');
require_once ($cwd . '/_functions.php');
require_once ($CFG->dirroot . '/lib/deprecatedlib.php');

require_login();
if(! isadmin())	die("Not Admin");
dbOpenConnection();
echo '<html>
<head>
    <title>Fil / billede upload</title>
</head>
<script language=\'javascript\'>
function confirmDelete(name, id){
	if(confirm(\'Vil du slette \'+name+\'?\')){
		location.href=\''.$CFG->wwwroot.'/blocks/elsa/popup/fileupload.php?slet=\'+id;
	}
}
</script>

<script src="googleanalytics.js"></script>
<body> ';

if(isset($_GET["slet"]))
{
	if(unlink($CFG->dirroot .'/blocks/elsa/popup/guideimages/'.$_GET["slet"]))
		echo 'Filen '.$_GET["slet"].' blev slettet.<br />';
	else
		echo 'Filen '.$_GET["slet"].' kunne ikke slettes.<br /><br />';
}

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	if ((($_FILES["file"]["type"] == "image/gif") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/pjpeg") || ($_FILES["file"]["type"] == "image/png")) && ($_FILES["file"]["size"] < 1000000))
	  {
	  if ($_FILES["file"]["error"] > 0)
		{
		echo "Fejlkode: " . $_FILES["file"]["error"] . "<br />";
		}
	  else
		{
		echo "Upload: " . $_FILES["file"]["name"] . "<br />";
		echo "Type: " . $_FILES["file"]["type"] . "<br />";
		echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";

		if (file_exists($CFG->dirroot .'/blocks/elsa/popup/guideimages/' . $_FILES["file"]["name"]))
		  {
		  echo $_FILES["file"]["name"] . " eksisterer allerede. <br />";
		  }
		else
		  {
		  move_uploaded_file($_FILES["file"]["tmp_name"],
		  $CFG->dirroot .'/blocks/elsa/popup/guideimages/' . $_FILES["file"]["name"]);
		  echo "Gemt i : " . "../guideimages/" . $_FILES["file"]["name"]."<br />";
		  }
		}
	  }
	else
	  {
	  echo "Invalid file<br />";
	  }

}
echo '<b>Fil upload:</b><br /><form enctype="multipart/form-data" action="'.$CFG->wwwroot.'/blocks/elsa/popup/fileupload.php" method="post">
<input type="file" name="file" value=""><br>
<input type="submit" name="submit" value="Upload">
</form>';

if ($handle = opendir($CFG->dirroot .'/blocks/elsa/popup/guideimages')) 
{
	echo '<br /><hr>Filerne kan tilgåes via '.$CFG->wwwroot .'/blocks/elsa/popup/guideimages/billedenavn.*** eller blot guideimages/billedenavn.*** når de bruges i guiderne.<br /><b>Liste over eksisterende filer:</b><br />';
	while (false !== ($file = readdir($handle))) {
		if($file !== "." && $file !== "..")
		echo $file.' - <button type="button" onclick="javascript:confirmDelete(\''.$file.'\', \''.$file.'\')">Slet</button><br />';
    }


}
else
	echo '<br /><hr>Mappen til billeder kunne ikke findes.';

?>
