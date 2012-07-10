<?php 
header('Content-type: text/html; charset="utf-8"');
require_once('_functions.php');

$language = $_GET['lang'];

if(empty($language)){
	$language ='dk';
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//en">
<HTML>
<HEAD>
<LINK REL='stylesheet' HREF='help_layout.css' TYPE='text/css'>"
<script language = "javascript" type="text/javascript">
	function Toggle(item){
		obj=document.getElementById(item);
    visible=(obj.style.display!="none");
    key=document.getElementById("x" + item);
    if (visible){
			obj.style.display="none";
      key.innerHTML ="<img src=\"icons/aabn.gif\" class=\"firstlevel-img\">";
    } else {
			obj.style.display="block";
      key.innerHTML ="<img src=\"icons/luk.gif\" width=\"11\" height=\"11\" class=\"firstlevel-img\">";
    }
	}
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
	if($language == 'en')
	{
		echo "Moodle Guides";
	}
	else
	{
		echo "Moodle Guider";
	}
	?>
	</span>
</div>

<div id="reg-text">
<?php
    $support_link=get_support_link($language);
	if($language == 'en')
	{
        echo "Here you will find guides for the use of Moodle. Use these to be guided "
            ."through some of the general functions and tasks in Moodle. Further help "
            ."can be found in <A HREF=\"$support_link\" TARGET=\"_blank\">Moodle-support</A>."; 
	}
	else
	{
        echo "Her finder du guider i brugen af Moodle. Benyt disse til at blive guidet "
            ."igennem nogle af de generelle funktioner og opgaver i Moodle. Yderligere "
            ."hjælp kan fåes i <A HREF=\"$support_link\" TARGET=\"_blank\">Moodle-support</A>.";
	}
?>
</div>
<div id="border-box">
	<div class="box-headline">
		Guider
	</div>
	<ul class="guide-list">
<?php
makeDirectory($language);

function makeDirectory($lang){
	dbOpenConnection();
	$query = "SELECT * FROM mdl_elsa_kategorier ORDER BY id";
	$categories = dbQuery($query);
	while($category = dbFetchArray($categories))
	{
		echo "<a ID='xpunkt".$category['id']."' href=\"javascript:Toggle('punkt".$category['id']."');\"><img src='icons/aabn.gif' width='11' height='11' class='firstlevel-img'></a><div class='firstlevel-text'><a ID='xpunkt".$category['id']."' href=\"javascript:Toggle('punkt".$category['id']."');\">".$category['titel_'.$lang]."</a></div>\n";
		$query = "SELECT * FROM mdl_elsa_guider WHERE kategori=".$category['id']." ORDER BY id;";
		$guides = dbQuery($query);
		echo "<div ID='punkt".$category['id']."' style='display:none; margin-left:2em;'>\n";
		while($guide = dbFetchArray($guides))
		{
			if(!empty($guide['titel_'.$lang]))
				echo "<li class='guide-list'><a href='helpguides.php?guideId=".$guide['id']."&lang=".$lang."'>".$guide['titel_'.$lang]."</a></li>\n";
		}
		echo "</div>\n";
	}
}
