<?php

	function getLang($lang){

		if ($lang == 'magyar') { $langVal = ""; }
		if ($lang == 'nemet') { $langVal = "_nemet"; }
		if ($lang == 'angol') { $langVal = "_angol"; }

		return $langVal;
	}

	// function getElelmiszerList($nevFilter = null, $lang){

	// 	$nevFilter = str_replace("'", "''", iconv("UTF-8", "Windows-1250", $nevFilter));
	// 	$query = "select e.Id, e.Nev";
	// 	$query .= getLang($lang);
	// 	$query .= ", e.MakroForras, e.ElelmiszerKategoriaId, k.Nev";
	// 	$query .= getLang($lang);
	// 	$query .= " as ElelmiszerKategoriaNev, e.IsGlutentartalmu, e.IsProblemas, e.IsCsucsorfele, e.IsOlajosMag, e.IsMagasHisztamin, e.IsFoodTeszt, e.IsPreferencia, e.IsMagasGI, e.IsMagasFruktoz, e.IsMagasFodmap, e.IsKozepesFodmap, e.IsTejKereszt, e.IsKiemeles from Elelmiszer e inner join ElelmiszerKategoria k on e.ElelmiszerKategoriaId = k.Id";
	// 	if($nevFilter != null && strlen($nevFilter) > 0){			
	// 		$query .= " where e.Nev";
	// 		$query .= getLang($lang);
	// 		$query .= " like '%$nevFilter%' order by e.Nev";
	// 		$query .= getLang($lang);
	// 	}
	// 	else{
	// 		$query .= " order by k.Sorrend, e.IsProblemas, e.Nev";
	// 		$query .= getLang($lang);
	// 	}
	// 	$result = mysql_query($query);
	// 	if(!$result){
	// 		print mysql_error();
	// 		exit("Nem siker�lt: " . $query);
	// 	}
	// 	$retArray = array();
	// 	while($row = mysql_fetch_assoc($result)) {
	// 		$retArray[] = $row;
	// 	}		
	// 	return $retArray;
	// }
	
	function getElelmiszerList($nevFilter = null){

		$nevFilter = str_replace("'", "''", iconv("UTF-8", "Windows-1250", $nevFilter));
		$query = "select e.Id, e.Nev, e.MakroForras, e.ElelmiszerKategoriaId, k.Nev as ElelmiszerKategoriaNev, e.IsGlutentartalmu, e.IsProblemas, e.IsCsucsorfele, e.IsOlajosMag, e.IsMagasHisztamin, e.IsFoodTeszt, e.IsPreferencia,
				e.IsMagasGI, e.IsMagasFruktoz, e.IsMagasFodmap, e.IsKozepesFodmap, e.IsTejKereszt, e.IsKiemeles, e.IsRefluxTr, e.IsLektin
			from Elelmiszer e inner join ElelmiszerKategoria k on e.ElelmiszerKategoriaId = k.Id";

		if($nevFilter != null && strlen($nevFilter) > 0){			
			$query .= " 
				where e.Nev like '%$nevFilter%'
				order by e.Nev
			";
		}
		else{
			$query .= "
				order by k.Sorrend, e.IsProblemas, e.Nev		
			";
		}
		$result = mysql_query($query);
		if(!$result){
			print mysql_error();
			exit("Nem siker�lt: " . $query);
		}
		$retArray = array();
		while($row = mysql_fetch_assoc($result)) {
			$retArray[] = $row;
		}		
		return $retArray;
	}
	
	function getElelmiszerListById($idArray){
		$idArray = (array)$idArray;
		if(count($idArray) == 0){
			return array();
		}
		$query = "
			select e.Id, e.Nev, e.MakroForras, e.ElelmiszerKategoriaId, k.Nev as ElelmiszerKategoriaNev, e.IsGlutentartalmu, e.IsProblemas, e.IsCsucsorfele, e.IsOlajosMag, e.IsMagasHisztamin, e.IsFoodTeszt, e.IsPreferencia,
				e.IsMagasGI, e.IsMagasFruktoz, e.IsMagasFodmap, e.IsKozepesFodmap, e.IsTejKereszt, e.IsKiemeles, e.IsRefluxTr, e.IsLektin
			from Elelmiszer e
			inner join ElelmiszerKategoria k on e.ElelmiszerKategoriaId = k.Id			
			where e.Id in (" . implode(",", $idArray) . ")
		";
		$result = mysql_query($query);
		if(!$result){
			print mysql_error();
			exit("Nem siker�lt: " . $query);
		}
		$retArray = array();
		while($row = mysql_fetch_assoc($result)) {
			$retArray[] = $row;
		}		
		return $retArray;				
	}
	
	function storeChange($id, $col, $value, $isChecked){
		if($col != "MakroForras" && $col != "IsGlutentartalmu" && $col != "IsProblemas" && $col != "IsCsucsorfele" && $col != "IsOlajosMag" && $col != "IsMagasHisztamin" && $col != "IsFoodTeszt" && $col != "IsPreferencia" && $col != "IsMagasGI" && $col != "IsMagasFruktoz" && $col != "IsMagasFodmap" && $col != "IsKozepesFodmap" && $col != "IsTejKereszt" && $col != "IsKiemeles" && $col != "IsRefluxTr" && $col != "IsLektin"){
			return;
		}
		// ha az adatb�zismez� bit t�pus�
		if($col != "MakroForras" && $col != "IsProblemas"){
			if(!$isChecked)
				$value = 0;
		}

		$sql = "update Elelmiszer set {$col} = {$value} where Id = {$id}";			

		$result = mysql_query($sql);
		if(!$result){
			print mysql_error();
			exit("Nem siker�lt: " . $sql);
		}			
	}
	
	function saveFilter($id, $isTest, $isGlutenmentes, $isCsucsorfele, $isOlajosMag, $isMagasHisztamin, $isMagasGI, $isMagasFruktoz, $isMagasFodmap, $isKozepesFodmap, $isTejKereszt, $isHuvelyes, $isTejtermek, $isHus, $isTojas, $isReflux, $isLektin, $intol, $izles, $kiemeles, $megisKell){
		
		$tableName = ($isTest == 1 ? "ElelmiszerTesztFelh" : "Questionarie_Fills");
		$sql = "
			update {$tableName} 
			set IsGlutentartalmuFilter = {$isGlutenmentes}, IsCsucsorfeleFilter = {$isCsucsorfele}, IsOlajosMagFilter = {$isOlajosMag}, IsMagasHisztaminFilter = {$isMagasHisztamin},
				IsMagasGIFilter = {$isMagasGI}, IsMagasFruktozFilter = {$isMagasFruktoz}, IsMagasFodmapFilter = {$isMagasFodmap}, IsKozepesFodmapFilter = {$isKozepesFodmap}, IsHuvelyesFilter = {$isHuvelyes}, 
				IsTejtermekFilter = {$isTejtermek}, IsHusFilter = {$isHus}, IsTojasFilter = {$isTojas}, IsRefluxTrFilter = {$isReflux}, IsTejKeresztFilter = {$isTejKereszt}, IsLektinFilter = {$isLektin}, IntolFilter = '{$intol}', IzlesFilter = '{$izles}', KiemelesFilter = '{$kiemeles}', MegisKell = '$megisKell'
			where ID = {$id}
		";
		
		$result = mysql_query($sql);
		if(!$result){
			print mysql_error();
			exit("Nem siker�lt: " . $sql);
		}		
	}
	
	function getFilter($id, $isTest){		
		$tableName = ($isTest == 1 ? "ElelmiszerTesztFelh" : "Questionarie_Fills");
		$sql = "
			select IsGlutentartalmuFilter, IsCsucsorfeleFilter, IsOlajosMagFilter, IsMagasHisztaminFilter, IsMagasGIFilter, IsMagasFruktozFilter, IsMagasFodmapFilter, IsKozepesFodmapFilter, IsHuvelyesFilter, IsTojasFilter, 
				IsRefluxTrFilter, IsTejtermekFilter, IsHusFilter, IsTejKeresztFilter, IsLektinFilter, IntolFilter, IzlesFilter, KiemelesFilter, MegisKell
			from {$tableName} where ID = {$id}
		";
		
		$result = mysql_query($sql);
		if(!$result){
			print mysql_error();
			exit("Nem siker�lt: " . $sql);
		}
		return mysql_fetch_assoc($result);
	}
	
	function getIntoleranciaElelm(){		
		$query = "
			select e.Id, e.Nev
			from Elelmiszer e
			where e.IsFoodTeszt = 1
			order by Nev
		";

		$result = mysql_query($query);
		if(!$result){
			print mysql_error();
			exit("Nem siker�lt: " . $query);
		}
		$retArray = array();
		while($row = mysql_fetch_assoc($result)) {
			$retArray[] = $row;
		}		
		return $retArray;
	}

	function getIzlesElelm(){		
		$query = "
			select e.Id, e.Nev, e.ElelmiszerKategoriaId
			from Elelmiszer e
			where e.IsPreferencia = 1
			order by Nev
		";

		$result = mysql_query($query);
		if(!$result){
			print mysql_error();
			exit("Nem siker�lt: " . $query);
		}
		$retArray = array();
		while($row = mysql_fetch_assoc($result)) {
			$retArray[] = $row;
		}		
		return $retArray;
	}

	function getKiemelesElelm(){		
		$query = "
			select e.Id, e.Nev
			from Elelmiszer e
			where e.IsKiemeles = 1
			order by Nev
		";

		$result = mysql_query($query);
		if(!$result){
			print mysql_error();
			exit("Nem siker�lt: " . $query);
		}
		$retArray = array();
		while($row = mysql_fetch_assoc($result)) {
			$retArray[] = $row;
		}		
		return $retArray;
	}
	
	function getTypedElelm($type){
		$filter = "1 = 1";
		if($type === 1){
			$filter = "e.IsGlutentartalmu = 1";
		} else if ($type === 2){
			$filter = "e.IsCsucsorfele = 1";
		} else if ($type === 3){
			$filter = "e.IsOlajosMag = 1";
		} else if ($type === 4){
			$filter = "e.IsMagasHisztamin = 1";
		} else if ($type === 5){
			$filter = "e.IsMagasGI = 1";
		} else if ($type === 6){
			$filter = "e.IsMagasFruktoz = 1";
		} else if ($type === 7){
			$filter = "e.IsMagasFodmap = 1";
		} else if ($type === 8){
			$filter = "e.IsKozepesFodmap = 1";
		} else if ($type === 9){
			$filter = "e.IsTejKereszt = 1";
		} else if ($type === 10){
			$filter = "e.ElelmiszerKategoriaId = 5";
		} else if ($type === 11){
			$filter = "e.ElelmiszerKategoriaId = 8";
		} else if ($type === 12){
			$filter = "e.ElelmiszerKategoriaId = 9";
		} else if ($type === 13){
			$filter = "e.ElelmiszerKategoriaId = 10";
		} else if ($type === 14){
			$filter = "e.IsRefluxTr = 1";
		} else if ($type === 15){
			$filter = "e.IsLektin = 1";
		}				
		
		$query = "select e.Id from Elelmiszer e where {$filter}";

		$result = mysql_query($query);
		if(!$result){
			print mysql_error();
			exit("Nem siker�lt: " . $query);
		}
		$retArray = array();
		while($row = mysql_fetch_row($result)) {
			$retArray[] = $row[0];
		}		
		return $retArray;
	}	
	
	function printAjanlasBlokk($list, $kiemelesArray, $lastGroupId = null){
		$i = 0;
		foreach($list as $kaja){
			if($lastGroupId != $kaja["ElelmiszerKategoriaId"]){
				if($lastGroupId != null)
					print "</ul>";
				print "<div class='subCategoryHeader'>" . $kaja["ElelmiszerKategoriaNev"] . "</div>";				
			}
			if($lastGroupId != $kaja["ElelmiszerKategoriaId"] || $i == 0)
				print "<ul class='elelmUl'>";
			
			$class = $kaja["IsProblemas"] == "1" ? "nehez" : "konnyu";
			
			print "<li" . (in_array($kaja["Id"], $kiemelesArray) ? " class='kiem $class'" : " class='$class'") . ">{$kaja["Nev"]}</li>"; 
			$lastGroupId = $kaja["ElelmiszerKategoriaId"];
			$i++;
		}
		if($lastGroupId != null)
			print "</ul>";
		
		return $lastGroupId;
	}

	function printAjanlasBlokk2($list, $kiemelesArray, $lastGroupId = null){
		$i = 0;
		foreach($list as $kaja){

			if ($kaja["ElelmiszerKategoriaNev"] != '') {
			
					if($lastGroupId != $kaja["ElelmiszerKategoriaId"]){
						if($lastGroupId != null)
							print "</span>";
						print "<br><br><span class='subCategoryHeader'>" . $kaja["ElelmiszerKategoriaNev"] . "</span><br><br>";				
					}
					if($lastGroupId != $kaja["ElelmiszerKategoriaId"] || $i == 0)
						print "<div class='elelmUl'>";
					
					$class = $kaja["IsProblemas"] == "1" ? "nehez" : "konnyu";
					
					print "<span" . (in_array($kaja["Id"], $kiemelesArray) ? " class='kiem $class'" : " class='$class'") . ">{$kaja["Nev"]}, </span>"; 
					$lastGroupId = $kaja["ElelmiszerKategoriaId"];
					$i++;
			}

		}
		if($lastGroupId != null)
		print "</span><br><br>";

		return $lastGroupId;
	}

	function CategoryLangChange($elelmiszerList,$lang) {
		$lang = getLang($lang);
		for ($x = 0; $x < sizeof($elelmiszerList); $x++) {
			$elelmiszerList[$x]['ElelmiszerKategoriaNev'];
			$query = "SELECT Nev";
			$query .= $lang;
			$query .= " FROM ElelmiszerKategoria WHERE Nev = '";
			$query .= $elelmiszerList[$x]['ElelmiszerKategoriaNev']. "'";			
			$result = mysql_query($query);
			if(!$result){
				print mysql_error();
				exit("Nem siker�lt: " . $query);
			}
			$row = mysql_fetch_row($result);
			$elelmiszerList[$x]['ElelmiszerKategoriaNev'] = $row[0];
		}
		return $elelmiszerList;
	}

	function NameLangChange($elelmiszerList,$lang) {
		$lang = getLang($lang);
		for ($x = 0; $x < sizeof($elelmiszerList); $x++) {
			//ITT BEH�VJUK A MAGYAR NEVEKET SORBAN
			$elelmiszerList[$x]['Nev'];
			//A DB-B�L EZEN NEVEKHEZ TARTOZ� M�S NYELV� NEVET LE KELL H�VNUNK
			$query = "SELECT Nev";
			$query .= $lang;
			$query .= " FROM Elelmiszer WHERE Nev = '";
			$query .= $elelmiszerList[$x]['Nev']. "'";			
			$result = mysql_query($query);
			if(!$result){
				print mysql_error();
				exit("Nem siker�lt: " . $query);
			}
			$row = mysql_fetch_row($result);
			//OUTPUTNAK KICSER�LJ�K A MAGYAR NEVET AZ IDEGEN NYELV�RE
			$elelmiszerList[$x]['Nev'] = $row[0];
		}
		return $elelmiszerList;
	}

?>