<?php
require_once (dirname(__FILE__) . '/../../config.php');
require_once ($CFG->dirroot . '/lib/accesslib.php');
require_once ($CFG->dirroot . '/lib/deprecatedlib.php');
require_once ($CFG->dirroot . '/lib/weblib.php');
require_once ($CFG->dirroot . '/blocks/elsa/popup/_functions.php');
//deprecatedlib.php bliver brugt til isadmin, isstudent, isteacher.

class block_elsa extends block_base {
		
	function init() {
		$this->title= '<img src="../blocks/elsa/elsa_qmark_small.gif" style="vertical-align:middle" /> '.get_string('title', 'block_elsa');
		$this->version= 2009121200;
	}
	function has_config() {
	  return true;
	}
	
	function config_save($data)
	{
		dbOpenConnection();
		//build array
		$subjects = dbQuery("SELECT id FROM mdl_elsa_guider ORDER BY id");
		$subject = dbFetchArray($subjects);
		$i = 0;
		while($subject != null)
		{
			$multi_array[$i][0] = $subject["id"];
			$multi_array[$i][1] = 0;
			$multi_array[$i][2] = 0;
			$multi_array[$i][3] = 0;
			$multi_array[$i][4] = 0;
			$multi_array[$i][5] = 0;
			$multi_array[$i][6] = 0;
			$multi_array[$i][7] = 0;
			$i++;
			$subject = dbFetchArray($subjects);
		}
		
		//udfyld de valgte checkboxes i arrayet
		foreach ($data as $name => $value) {
			
			
			if(strstr($name, "ikke_logget_ind"))
			{
				for($t = 0; $t<$i;$t++)
				{
					if($multi_array[$t][0] == $value)
					{
						$multi_array[$t][1] = 1;
					}
				}
			}


			if(strstr($name, "forsiden_studerende"))
			{
				for($t = 0; $t<$i;$t++)
				{
					if($multi_array[$t][0] == $value)
					{
						$multi_array[$t][2] = 1;
					}
				}
			}


			if(strstr($name, "forsiden_underviser"))
			{
				for($t = 0; $t<$i;$t++)
				{
					if($multi_array[$t][0] == $value)
					{
						$multi_array[$t][3] = 1;
					}
				}
			}


			if(strstr($name, "mymoodle_studerende"))
			{
				for($t = 0; $t<$i;$t++)
				{
					if($multi_array[$t][0] == $value)
					{
						$multi_array[$t][4] = 1;
					}
				}
			}


			if(strstr($name, "mymoodle_underviser"))
			{	
				for($t = 0; $t<$i;$t++)
				{
					if($multi_array[$t][0] == $value)
					{
						$multi_array[$t][5] = 1;
					}
				}
			}


			if(strstr($name, "kursus_studerende"))
			{
				for($t = 0; $t<$i;$t++)
				{
					if($multi_array[$t][0] == $value)
					{
						$multi_array[$t][6] = 1;
					}
				}
			}


			if(strstr($name, "kursus_underviser"))
			{
				for($t = 0; $t<$i;$t++)
				{
					if($multi_array[$t][0] == $value)
					{
						$multi_array[$t][7] = 1;
					}
				}
			}
		}
		//Opdater databasen i forhold til arrayet
		for($t = 0; $t<$i;$t++)
		{

			if($multi_array[$t][1] == 0)
				dbQuery('UPDATE mdl_elsa_guider SET show_not_loggedin = 0 WHERE id = '.$multi_array[$t][0]);
			else
				dbQuery('UPDATE mdl_elsa_guider SET show_not_loggedin = 1 WHERE id = '.$multi_array[$t][0]);
				
			if($multi_array[$t][2] == 0)
				dbQuery('UPDATE mdl_elsa_guider SET show_frontpage_student = 0 WHERE id = '.$multi_array[$t][0]);
			else
				dbQuery('UPDATE mdl_elsa_guider SET show_frontpage_student = 1 WHERE id = '.$multi_array[$t][0]);
		
			if($multi_array[$t][3] == 0)
				dbQuery('UPDATE mdl_elsa_guider SET show_frontpage_teacher = 0 WHERE id = '.$multi_array[$t][0]);
			else
				dbQuery('UPDATE mdl_elsa_guider SET show_frontpage_teacher = 1 WHERE id = '.$multi_array[$t][0]);
		
			if($multi_array[$t][4] == 0)
				dbQuery('UPDATE mdl_elsa_guider SET show_my_student = 0 WHERE id = '.$multi_array[$t][0]);
			else
				dbQuery('UPDATE mdl_elsa_guider SET show_my_student = 1 WHERE id = '.$multi_array[$t][0]);
		
			if($multi_array[$t][5] == 0)
				dbQuery('UPDATE mdl_elsa_guider SET show_my_teacher = 0 WHERE id = '.$multi_array[$t][0]);
			else
				dbQuery('UPDATE mdl_elsa_guider SET show_my_teacher = 1 WHERE id = '.$multi_array[$t][0]);
		
			if($multi_array[$t][6] == 0)
				dbQuery('UPDATE mdl_elsa_guider SET show_course_student = 0 WHERE id = '.$multi_array[$t][0]);
			else
				dbQuery('UPDATE mdl_elsa_guider SET show_course_student = 1 WHERE id = '.$multi_array[$t][0]);
		
			if($multi_array[$t][7] == 0)
				dbQuery('UPDATE mdl_elsa_guider SET show_course_teacher = 0 WHERE id = '.$multi_array[$t][0]);
			else
				dbQuery('UPDATE mdl_elsa_guider SET show_course_teacher = 1 WHERE id = '.$multi_array[$t][0]);
		
		}

		return true;
	}
	
	function initContent() {
		global $CFG;
		require_js($CFG->httpswwwroot . '/blocks/elsa/resizeJava.js');
		dbOpenConnection();
		$text = '';
		
		$language = get_string('elsalang', 'block_elsa');
		
		if(isDBsetup())
		{
			if($_SERVER["REQUEST_URI"] == "/" || strstr($_SERVER["REQUEST_URI"], "/index.php")){
				if(isloggedin()){
					if(isadmin())
					{
						//Forsiden og Admin
						$text .= get_string('studentsee', 'block_elsa');
						$query = "SELECT id, titel_".$language." FROM mdl_elsa_guider WHERE show_frontpage_student = 1 ORDER BY id;";
						$subjects = dbQuery($query);
						$subject = dbFetchArray($subjects);
						if($subject == null)
							$text .= "<br />";
						
						while($subject != null)
						{
							$text .= $this->build_support_item($subject["titel_".$language], $subject["id"], $language);
							$subject = dbFetchArray($subjects);
						}
						
						$text .= get_string('teachersee', 'block_elsa');
						$query = "SELECT id, titel_".$language." FROM mdl_elsa_guider WHERE show_frontpage_teacher = 1 ORDER BY id;";
						$subjects = dbQuery($query);
						$subject = dbFetchArray($subjects);
						
						while($subject != null)
						{
							$text .= $this->build_support_item($subject["titel_".$language], $subject["id"], $language);
							$subject = dbFetchArray($subjects);
						}
					
					}
					else if(isteacherinanycourse())
					{
						//Dette er forsiden og du ER logget ind som UNDERVISER.
						$query = "SELECT id, titel_".$language." FROM mdl_elsa_guider WHERE show_frontpage_teacher = 1 ORDER BY id;";
						$subjects = dbQuery($query);
						$subject = dbFetchArray($subjects);
						
						while($subject != null)
						{
							$text .= $this->build_support_item($subject["titel_".$language], $subject["id"], $language);
							$subject = dbFetchArray($subjects);
						}
					}
					else
					{
						//Dette er forsiden og du ER logget ind som STUDERENDE.
						$query = "SELECT id, titel_".$language." FROM mdl_elsa_guider WHERE show_frontpage_student = 1 ORDER BY id;";
						$subjects = dbQuery($query);
						$subject = dbFetchArray($subjects);
						
						while($subject != null)
						{
							$text .= $this->build_support_item($subject["titel_".$language], $subject["id"], $language);
							$subject = dbFetchArray($subjects);
						}
					}
				}
				else
					{
					//Dette er forsiden og du er IKKE logget ind.
						$query = "SELECT id, titel_".$language." FROM mdl_elsa_guider WHERE show_not_loggedin = 1 ORDER BY id;";
						$subjects = dbQuery($query);
						$subject = dbFetchArray($subjects);
						
						while($subject != null)
						{
							$text .= $this->build_support_item($subject["titel_".$language], $subject["id"], $language);
							$subject = dbFetchArray($subjects);
						}
					}
			}
			else if(strstr($_SERVER["REQUEST_URI"], "my")){
				if(isadmin())
				{
					//MyMoodle siden og Admin
					$text .= get_string('studentsee', 'block_elsa');
					$query = "SELECT id, titel_".$language." FROM mdl_elsa_guider WHERE show_my_student = 1 ORDER BY id;";
					$subjects = dbQuery($query);
					$subject = dbFetchArray($subjects);
					if($subject == null)
						$text .= "<br />";
					
					while($subject != null)
					{
						$text .= $this->build_support_item($subject["titel_".$language], $subject["id"], $language);
						$subject = dbFetchArray($subjects);
					}
					$text .= get_string('teachersee', 'block_elsa');
					$query = "SELECT id, titel_".$language." FROM mdl_elsa_guider WHERE show_my_teacher = 1 ORDER BY id;";
					$subjects = dbQuery($query);
					$subject = dbFetchArray($subjects);
					
					while($subject != null)
					{
						$text .= $this->build_support_item($subject["titel_".$language], $subject["id"], $language);
						$subject = dbFetchArray($subjects);
					}
					
				}
				else if(isteacherinanycourse())
				{
					//Dette er My Moodle siden og du ER logget ind som UNDERVISER.
						$query = "SELECT id, titel_".$language." FROM mdl_elsa_guider WHERE show_my_teacher = 1 ORDER BY id;";
						$subjects = dbQuery($query);
						$subject = dbFetchArray($subjects);
						
						while($subject != null)
						{
							$text .= $this->build_support_item($subject["titel_".$language], $subject["id"], $language);
							$subject = dbFetchArray($subjects);
						}
				}
				else
				{
					//Dette er My Moodle siden og du ER logget ind som STUDERENDE.
						$query = "SELECT id, titel_".$language." FROM mdl_elsa_guider WHERE show_my_student = 1 ORDER BY id;";
						$subjects = dbQuery($query);
						$subject = dbFetchArray($subjects);
						
						while($subject != null)
						{
							$text .= $this->build_support_item($subject["titel_".$language], $subject["id"], $language);
							$subject = dbFetchArray($subjects);
						}
				}
			}
			else if(strstr($_SERVER["REQUEST_URI"], "course")){
				/* Denne kan ikke bruges, da der ikke kan vises en block på denne side.
				if(strstr($_SERVER["REQUEST_URI"], "category"))
				{
					if(isadmin())
						$text .= "Dette er en liste over kursuskategorier og du ER logget ind som ADMIN.";
					else if(isteacher())
						$text .= "Dette er en liste over kursuskategorier og du ER logget ind som UNDERVISER.";
					else if(isstudent())
						$text .= "Dette er en liste over kursuskategorier og du ER logget ind som STUDERENDE.";
				}
				else if(strstr($_SERVER["REQUEST_URI"], "view"))
				*/
				{
					if(isadmin())
					{
						//Kursus og Admin
						$text .= get_string('studentsee', 'block_elsa');
						$query = "SELECT id, titel_".$language." FROM mdl_elsa_guider WHERE show_course_student = 1 ORDER BY id;";
						$subjects = dbQuery($query);
						$subject = dbFetchArray($subjects);
						if($subject == null)
							$text .= "<br />";
						
						
						while($subject != null)
						{
							$text .= $this->build_support_item($subject["titel_".$language], $subject["id"], $language);
							$subject = dbFetchArray($subjects);
						}
						$text .= get_string('teachersee', 'block_elsa');
						$query = "SELECT id, titel_".$language." FROM mdl_elsa_guider WHERE show_course_teacher = 1 ORDER BY id;";
						$subjects = dbQuery($query);
						$subject = dbFetchArray($subjects);
						
						while($subject != null)
						{
							$text .= $this->build_support_item($subject["titel_".$language], $subject["id"], $language);
							$subject = dbFetchArray($subjects);
						}
						
					}
					else if(isteacherinanycourse())
					{
						//Dette er siden til det enkelte kursus og du ER logget ind som UNDERVISER.
						$query = "SELECT id, titel_".$language." FROM mdl_elsa_guider WHERE show_course_teacher = 1 ORDER BY id;";
						$subjects = dbQuery($query);
						$subject = dbFetchArray($subjects);
						
						while($subject != null)
						{
							$text .= $this->build_support_item($subject["titel_".$language], $subject["id"], $language);
							$subject = dbFetchArray($subjects);
						}
					}
					else
					{
						//Dette er siden til det enkelte kursus og du ER logget ind som STUDERENDE.
						$query = "SELECT id, titel_".$language." FROM mdl_elsa_guider WHERE show_course_student = 1 ORDER BY id;";
						$subjects = dbQuery($query);
						$subject = dbFetchArray($subjects);
						
						while($subject != null)
						{
							$text .= $this->build_support_item($subject["titel_".$language], $subject["id"], $language);
							$subject = dbFetchArray($subjects);
						}
						
					}
				}
			}
			else
				$text .= get_string('notregistered', 'block_elsa');
		}
		else
			$text = 'Database ikke opsat korrekt. Gå til indstillinger for blocken for at opsætte den korrekt.';
		
		$this->content= new stdClass;
		$this->content->footer= '<hr /><div style="text-align:center;"><a href="javascript:openAndResize(\''.$CFG->httpswwwroot.'/blocks/elsa/popup/help.php?lang='.$language.'\')">'.get_string('footertext', 'block_elsa').'</a></div>';
		$this->content->text .= $text;
	}
	
	function build_support_item($text, $linkid, $language)
	{
		global $CFG;
		if(!empty($text))
			$html = '<p style="margin:2px"><img src="../blocks/elsa/elsa_qmark_small.gif" style="vertical-align:middle" /><a href="javascript:openAndResize(\''.$CFG->httpswwwroot.'/blocks/elsa/popup/helpguides.php?guideId='.$linkid.'&lang='.$language.'\')"> '.$text.'</a></p>';
		else
			$html = "";
		return $html;
	}

	function get_content() 
	{
		if ($this->content !== NULL) {
			return $this->content;
		}

		if (!$this->content) {
			$this->initContent();
		}
	}
}
?>
