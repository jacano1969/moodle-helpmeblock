<?php
require_once(dirname(__FILE__) .'/../adodb5/adodb.inc.php');
require_once(dirname(__FILE__) .'/../../../config.php');

function get_support_link($locale = 'dk'){
    // list of different support form recievers
    $recievers = array(
        'cgs.moodle.aau.dk' => 'cgs.moodle',
        'art.moodle.aau.dk' => 'art.moodle',
        'huminfaau.moodle.aau.dk' => 'huminfaau.moodle',
        'huminf.moodle.aau.dk' => 'huminf.moodle',
        'learninglab.moodle.aau.dk' => 'learninglab.moodle',
        'master-it.moodle.aau.dk' => 'master-it.moodle',
        'musik.moodle.aau.dk' => 'musik.moodle' );
    
    $support_uri = "http://www.elsa.aau.dk/Kontakt-Support.moodle-3.0.html";
    $args = array();

    if ( $r = @$links[$_SERVER['SERVER_NAME']])  
        $args['reciever'] = $r;

    if($locale == 'en') 
        $args['L'] = 2;

    // append arguments if any to supporturi 
    if(! empty($args)){
        foreach($args as $k => $v) 
            @$list[] = "$k=$v";

        $supporturi .= "?".implode($list,"&");
    }

    return $supporturi;
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
    $pg = (bool) strstr($db->databaseType,"postgres");
    $my = (bool) strstr($db->databaseType,"mysql");
    if($my) $q = "SHOW TABLES LIKE 'mdl_elsa%'";
    if($pg) $q = "SELECT * FROM pg_tables WHERE tablename LIKE 'mdl_elsa%'";

	$result = dbQuery($q);
    if($result->RecordCount() == 3) return true;
    return false;
}
?>
