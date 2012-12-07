<?php
$cwd = dirname(__FILE__);
require_once($cwd . '/popup/_functions.php');

define('GUIDER_CSV',$cwd . '/dbdata/guider.csv');
define('KATEGORIER_CSV', $cwd . '/dbdata/kategorier.csv');
define('STATS_CSV',$cwd . '/dbdata/stats.csv');

function data_definitions(){
    $create_guider = ''.
        'CREATE TABLE mdl_elsa_guider (
            id SERIAL PRIMARY KEY,
            kategori INT NOT NULL,
            titel_dk TEXT NOT NULL,
            titel_en TEXT NOT NULL,
            indh_dk  TEXT NOT NULL,
            indh_en  TEXT NOT NULL,';
    
    // These are used as bools, but postgres stores true/false as t/f
    // while mysql uses 0/1, and the application is written as a 0/1 app
    $create_guider .= ''.
            'show_not_loggedin INT DEFAULT 0,
            show_frontpage_student INT DEFAULT 0,
            show_frontpage_teacher INT DEFAULT 0,
            show_my_student INT DEFAULT 0,
            show_my_teacher INT DEFAULT 0,
            show_course_student INT DEFAULT 0,
            show_course_teacher INT DEFAULT 0 )';

    $create_kategorier = ''.
        'CREATE TABLE  mdl_elsa_kategorier (
            id SERIAL PRIMARY KEY,
            titel_dk TEXT NOT NULL,
            titel_en TEXT NOT NULL )';

    $create_stats = ''.
        'CREATE TABLE mdl_elsa_stats (
            id SERIAL PRIMARY KEY,
            hits INT NOT NULL)';

    $cols_guider = array(
        'id','kategori','titel_dk','titel_en','indh_dk','indh_en',
        'show_not_loggedin','show_frontpage_student','show_frontpage_teacher',
        'show_my_student','show_my_teacher','show_course_student',
        'show_course_teacher');

    $tables = array(
        'mdl_elsa_guider' => array( 
            'cols' => $cols_guider, 
            'csv' => GUIDER_CSV, 
            'create' => $create_guider),
        'mdl_elsa_kategorier' => array( 
            'cols' => array('id','titel_dk','titel_en'), 
            'csv' => KATEGORIER_CSV,
            'create' => $create_kategorier),
        'mdl_elsa_stats' => array( 
            'cols' => array('id','hits'), 
            'csv' => STATS_CSV,
            'create' => $create_stats));

    foreach($tables as $table => $attr ){
        $cols = implode(',',$attr['cols']);
        $val = preg_replace('/,$/','',str_repeat('?,',sizeof($attr['cols'])));
        $query = sprintf('INSERT INTO %s (%s) VALUES (%s)',$table,$cols,$val);
        $tables[$table]['insert'] = $query;
//        $tables[$table]['drop'] = 'DROP TABLE IF EXISTS ' . $table;
    }
    return $tables;
}

function db2csv(){
    $db = dbOpenConnection();
    $tables = data_definitions();
    foreach($tables as $table => $attr){
        $cols = implode(",",$attr['cols']);
        $csv =  $attr['csv'];
        if(! $res = $db->execute("SELECT $cols FROM $table")) die("error");
        
        file_put_contents($csv,''); // delete file content
        foreach($res as $row){
            $result = array();
            foreach($row as $id => $data) if(is_numeric($id)) $result[] = $data;
            $line = implode("#",$result) . PHP_EOL;
            file_put_contents($csv,$line,FILE_APPEND);
        }
    }
}

function setupDB(){
    $tables = data_definitions();
    $db = dbOpenConnection();
    foreach($tables as $table => $attr){
//        if( ! $db->execute($attr['drop']))      die($db->ErrorMsg());
        if( ! $db->execute($attr['create']))    die($db->ErrorMsg());
        if( ! file_exists($attr['csv'])) die('no file: '.$attr['csv']);
        $data = file($attr['csv']);
        foreach($data as $line){
            $insert = explode("#",trim($line,"\n"));
            if( ! $db->execute($attr['insert'],$insert)) die($db->ErrorMsg());
        }
    }
}
