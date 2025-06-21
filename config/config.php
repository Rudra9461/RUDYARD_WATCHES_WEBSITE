<?php
session_start();

$siteUrl = 'http://'.$_SERVER['HTTP_HOST'].'/SKIT1/index1';
$SiteTitle ="Welcome to RUDYARD WATCHS FOR MANS";

// DATA BASE//
$dbhost ='localhost';
$dbuser ='root';
$dbpass ='';
$dbname ='skit1_db';

$conn = mysqli_connect($dbhost, $dbuser ,$dbpass , $dbname);

if(!$conn) {
	die('Database connection error!');
}
