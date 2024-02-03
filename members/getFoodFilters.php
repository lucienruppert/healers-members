<?php
    session_start();
	header('Content-Type: application/json; charset=utf-8');
    include_once('functions.php');
	include_once('elelmiszer_functions.php');

    $fillId = (int)$_REQUEST["fillId"];
	$isTest = (int)$_REQUEST["isTest"];
	
	$filters = getFilter($fillId, $isTest);
	$intolFilters = explode(",", $filters["IntolFilter"]);		
	$izlesFilters = explode(",", $filters["IzlesFilter"]);
	$kiemelesFilters = explode(",", $filters["KiemelesFilter"]);
	$megisKell = strlen($filters["MegisKell"]) > 0 ? explode(",", $filters["MegisKell"]) : array();
	
	$obj = new stdClass;
	$obj->CategoryFilters = $filters;
	$obj->CategoryFilters["IntolFilter"] = null;
	
	$obj->IntolFilters = array();
	
	$intolList = getIntoleranciaElelm();
	
	foreach($intolList as $filter){
		$filterObj = new stdClass;
		$filterObj->Id = $filter["Id"];
		$filterObj->Nev = iconv("Windows-1250", "UTF-8", ltrim(rtrim($filter["Nev"])));
		$filterObj->IsChecked = in_array($filter["Id"], $intolFilters);
		$obj->IntolFilters[] = $filterObj;
	}
	
	$obj->IzlesFilters = array();
	
	$izlesList = getIzlesElelm();
	
	foreach($izlesList as $filter){
		$filterObj = new stdClass;
		$filterObj->Id = $filter["Id"];
		$filterObj->Nev = iconv("Windows-1250", "UTF-8", ltrim(rtrim($filter["Nev"])));
		$filterObj->IsChecked = in_array($filter["Id"], $izlesFilters);
		$filterObj->IsFish = ($filter["ElelmiszerKategoriaId"] == 6);
		$obj->IzlesFilters[] = $filterObj;
	}	

	$obj->KiemelesFilters = array();
	
	$kiemelesList = getKiemelesElelm();
	
	foreach($kiemelesList as $filter){
		$filterObj = new stdClass;
		$filterObj->Id = $filter["Id"];
		$filterObj->Nev = iconv("Windows-1250", "UTF-8", ltrim(rtrim($filter["Nev"])));
		$filterObj->IsChecked = in_array($filter["Id"], $kiemelesFilters);
		$obj->KiemelesFilters[] = $filterObj;
	}	
	
	$obj->MegisKell = array();
	$megisKellList = getElelmiszerListById($megisKell);

	$elelmObj = null;
	foreach($megisKell as $elelmId){
		$elelmObj = null;
		foreach ($megisKellList as $element) {
			if ($elelmId == $element["Id"]) {
				$elelmObj = $element;
				break;
			}
		}
		if($elelmObj != null){
			$filterObj = new stdClass;
			$filterObj->Id = $elelmObj["Id"];
			$filterObj->Nev = iconv("Windows-1250", "UTF-8", ltrim(rtrim($elelmObj["Nev"])));
			$obj->MegisKell[] = $filterObj;
		}
	}

    print json_encode($obj);
?>