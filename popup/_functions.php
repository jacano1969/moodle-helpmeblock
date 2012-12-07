<?php
require_once(dirname(__FILE__) .'/../adodb5/adodb.inc.php');
require_once(dirname(__FILE__) .'/../../../config.php');

function get_support_link($locale = 'dk'){
    // list of different support form recievers
    $receivers = array(
        'cgs.moodle.aau.dk' => 'cgs.moodle',
        'art.moodle.aau.dk' => 'art.moodle',
        'huminfaau.moodle.aau.dk' => 'huminfaau.moodle',
        'huminf.moodle.aau.dk' => 'huminf.moodle',
        'learninglab.moodle.aau.dk' => 'learninglab.moodle',
        'master-it.moodle.aau.dk' => 'master-it.moodle',
        'musik.moodle.aau.dk' => 'musik.moodle',
        'sadp.moodle.aau.dk' => 'sadp.moodle',
        'sef.moodle.aau.dk' => 'sef.moodle',
        'ses.moodle.aau.dk' => 'ses.moodle',
        'sict.moodle.aau.dk' => 'sict.moodle',
        'smh.moodle.aau.dk' => 'smh.moodle' );
    
    $support_uri = "http://www.elsa.aau.dk/Kontakt-Support.moodle-3.0.html";
    $args = array();

    if ( $r = @$receivers[$_SERVER['SERVER_NAME']])  
        $args['receiver'] = $r;

    if($locale == 'en') 
        $args['L'] = 2;

    // append arguments if any to supporturi 
    if(! empty($args)){
        foreach($args as $k => $v) 
            @$list[] = "$k=$v";

        $support_uri .= "?".implode($list,"&");
    }

    return $support_uri;
}

function dbOpenConnection(){
    global $CFG;
    if( is_object($CFG) ){
        $type = $CFG->dbtype;
        $type = strstr($type,'postgres') ? 'pgsql' : $type;
        $user = $CFG->dbuser;
        $pass = urlencode($CFG->dbpass);
        $host = $CFG->dbhost;
        $name = $CFG->dbname;
        $dsn  = sprintf('%s://%s:%s@%s/%s',$type,$user,$pass,$host,$name);
    }

    if( ! $db = & ADONewConnection($dsn))
        die("Could not open database connection\n");
    $db->Charset = 'utf8';
    $db->Execute("SET NAMES 'utf8'");  
    return $db;
}

function dbQuery($query){
    $db = dbOpenConnection();
    $result = $db->Execute($query) or die( $db->ErrorMsg() );
    return $result;
    $db->Close();
}

function check_input($value){
    // Stripslashes
    $value = stripslashes($value);
    $db = dbOpenConnection();
    $pg = (bool) strstr($db->databaseType,"postgres");
    $my = (bool) strstr($db->databaseType,"mysql");

    // Quote if not a number
    if (!is_numeric($value)){
      if ($pg) return sprintf("'%s'",pg_escape_string($value));
      if ($my) return sprintf("'%s'",mysql_real_escape_string($value));
    }
    return $value;
}

// emulate mysql_fetch_array() behavior
function dbFetchArray(&$result){
    if(empty($result->fields)) return null;
    $row = $result->fields;
    $result->MoveNext();
    return $row;
}

function isDBsetup(){
	$db = dbOpenConnection();
    $table_list = "('mdl_elsa_stats', 'mdl_elsa_guider', 'mdl_elsa_kategorier')";

    $pg = (bool) strstr($db->databaseType,"postgres");
    $my = (bool) strstr($db->databaseType,"mysql");
    if($my) $q = "SELECT * FROM information_schema.tables WHERE table_name IN $table_list'";
    if($pg) $q = "SELECT * FROM pg_tables WHERE tablename IN $table_list";

	$result = dbQuery($q);
    if($result->RecordCount() == 3) return true;
    return false;
}
?>
