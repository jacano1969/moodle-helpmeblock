<?php
header('Content-type: text/html; charset="utf-8"');
require_once('_functions.php');

if (!isset($_GET["guideId"])) 
{
	die("Intet GuideId");
}
else if(is_numeric($_GET["guideId"]))
{
	$guideId = $_GET["guideId"];
}	
else
	die('GuideId kunne ikke parses');
	
if(isset($_GET["lang"])){
	$lang = $_GET["lang"];
}
else
	$lang = 'dk';

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN">
<HTML>
<HEAD>
	<TITLE>
	<?php
	if($lang == 'en')
	{
		echo "Moodle Help";
	}
	else
	{
		echo "Moodle Hjælp";
	}
	?>
	</TITLE>
<LINK REL="stylesheet" HREF="help_layout.css" TYPE="text/css">
<script language = "javascript" type="text/javascript">
function moveMe(leftPos)
{
  window.moveTo(leftPos,0);
}
function resizeMe()
{
  var leftPos = 450;
  var topPos = 0;
  var wt = 240; 
  var ht = 570;
  if(screen)
  {
   leftPos = (screen.availWidth-wt);
   ht = (screen.availHeight);
  }
  window.resizeTo(wt,ht);
  var point = "moveMe("+leftPos+")";
  window.setTimeout(point, 1);
  return false;
}
</script>
</HEAD>

<script src="googleanalytics.js"></script>
<BODY onload='resizeMe();'>
<div id='nav-bar'><span class='nav-title'>

<?php
	if($lang == 'en')
	{
		echo "<a href='help.php?lang=en'>To the overview</a>";
	}
	else
	{
		echo "<a href='help.php?lang=dk'>Til oversigten</a>";
	}
	?>
	</span>
</div>

<div id="box" style="width:200px;">
<?php
dbOpenConnection();

//Tæl hit stats en tak op..
dbQuery("UPDATE mdl_elsa_stats SET hits = hits+1 WHERE id=1");

$query = "SELECT kategori, titel_dk, titel_en, indh_dk, indh_en FROM mdl_elsa_guider WHERE id=".$guideId;
			$guides = dbQuery($query);
			$guide = dbFetchArray($guides);
			if(isset($guide['titel_'.$lang]))
				echo "<div class='box-headline'>".$guide['titel_'.$lang]."</div>";
			if(isset($guide['indh_'.$lang]))
				echo $guide['indh_'.$lang];
?>
</div>
<?php
$query = "SELECT id, titel_dk, titel_en, indh_dk, indh_en FROM mdl_elsa_guider WHERE kategori=".$guide['kategori']." AND id!=".$guideId;
			$extras = dbQuery($query);
			$extra = dbFetchArray($extras);
			if($extra){ 
				echo '<div id="subcatshead" class="more-box-headline">';
				if($lang == 'en'){
					echo "Read more";
				}else{
				echo "I samme kategori";
				}
				echo '</div><ul id="subcatlist">';
				while($extra = dbFetchArray($extras))
				{
					if(isset($extra["id"]))
					{
						if(!empty($extra["titel_".$lang]))
							echo "<li><a href='helpguides.php?guideId=".$extra["id"]."&lang=".$lang."'>".$extra["titel_".$lang]."</a></li>";
					}
				}
				echo "</ul>";
			}
?>
<div id="footer">
<?php
	if($lang == 'en'){
		echo "Made by ";
	}else{
		echo "Lavet af ";
	}
	?>
	<a href="http://www.elsa.aau.dk/" target="_blank">Moodle-Support</a>
</div>
</BODY>
</HTML>
