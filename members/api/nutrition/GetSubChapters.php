<?php
require_once('../../functions.php');
header('Content-Type: application/json');


GetNutritionSubChaptersById($_GET["id"]);

function utf8_encode_all($dat) // -- It returns $dat encoded to UTF8
{
  if (is_string($dat)) 
  {
	  return utf8_encode($dat);
	  //return $dat;
  }
  if (!is_array($dat)) return $dat;
  $ret = array();
  foreach($dat as $i=>$d) 
  {
	  
	$ret[$i] = utf8_encode_all($d);
	
  }
  return $ret;
} 

function GetNutritionSubChaptersById($Id)
{
	$subChapterArray = getSubChaptersByChapterId($Id);
	echo json_encode(utf8_encode_all($subChapterArray));
}
?>