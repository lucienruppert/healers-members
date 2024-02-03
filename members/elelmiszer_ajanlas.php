<?php
    session_start();
    include_once('functions.php');
	include_once('elelmiszer_functions.php');
	
    $id = (int)$_REQUEST["id"];
	$isTest = (int)$_REQUEST["isTest"];
	$isGlutenmentes = (int)$_REQUEST["isglm"];
	$isCsucsorfele = (int)$_REQUEST["iscsucs"];
	$isOlajosMag = (int)$_REQUEST["isolm"];
	$isMagasHisztamin = (int)$_REQUEST["ismhiszt"];
	$isMagasGI = (int)$_REQUEST["ismaggi"];
	$isMagasFruktoz = (int)$_REQUEST["ismagfruk"];
	$isMagasFodmap = (int)$_REQUEST["ismagfod"];
	$isKozepesFodmap = (int)$_REQUEST["iskozfod"];
	$intolStr = $_REQUEST["intol"];
	$izlesStr = $_REQUEST["izles"];
	$kiemelesStr = $_REQUEST["kiemeles"];
	$isHuvelyes = (int)$_REQUEST["ishuvely"];
	$isTojas = (int)$_REQUEST["istojas"];
	$isReflux = (int)$_REQUEST["isreflux"];
	$isTejtermek = (int)$_REQUEST["istej"];
	$isHus = (int)$_REQUEST["ishus"];
	$isTejKereszt = (int)$_REQUEST["istejx"];
	$isLektin = (int)$_REQUEST["islektin"];
	$megisKell = (array)json_decode($_REQUEST["ow"]);
	
	$intol = explode(",", $intolStr);
	$izles = explode(",", $izlesStr);
	$kiemeles = explode(",", $kiemelesStr);

	$lang = $_REQUEST["lang"];
	include_once('translate.php');
	
	$filteredStr = array();
	if($isGlutenmentes) $filteredStr[] = $Glutenmentes;
	if($isCsucsorfele) $filteredStr[] = $Csucsorfele;
	if($isOlajosMag) $filteredStr[] = $OlajosMag;
	if($isMagasHisztamin) $filteredStr[] = $MagasHisztamin;
	if($isMagasGI) $filteredStr[] = $MagasGI;
	if($isMagasFruktoz) $filteredStr[] = $MagasFruktoz;
	if($isMagasFodmap) $filteredStr[] = $MagasFodmap;
	if($isKozepesFodmap) $filteredStr[] = $KozepesFodmap;
	if($isHuvelyes) $filteredStr[] = $Huvelyes;
	if($isTojas) $filteredStr[] = $Tojas;
	if($isReflux) $filteredStr[] = $Reflux;
	if($isLektin) $filteredStr[] = $Lektin;
	if($isTejKereszt) $filteredStr[] = $TejKereszt;
	if($isTejtermek) $filteredStr[] = $Tejtermek;
	if($isHus) $filteredStr[] = $Hus;
	
	if(count($intol) > 1 || count($intol) == 1 && $intol[0] != ""){
		$filteredStr[] = "$intoler";
	}
	if(count($izles) > 1 || count($izles) == 1 && $izles[0] != ""){
		$filteredStr[] = "$izlesed";
	}

	saveFilter($id, $isTest, $isGlutenmentes, $isCsucsorfele, $isOlajosMag, $isMagasHisztamin, $isMagasGI, $isMagasFruktoz, $isMagasFodmap, $isKozepesFodmap, $isTejKereszt, $isHuvelyes, $isTejtermek, $isHus, $isTojas, $isReflux, $isLektin, implode(",", $intol), implode(",", $izles), implode(",", $kiemeles), implode(",", (array)$megisKell));
	
	if($isTest != 1)
		$fill = selectQuestFill($id);
	else
		$fill = selectQuestFillForTestUsers($id);
		
	$elelmiszerList = getElelmiszerList('',$lang);					
	//$elelmiszerList = array_filter($elelmiszerList, "konnyuFilter");
	
	if($isGlutenmentes === 1)
		$elelmiszerList = array_filter($elelmiszerList, "glutenmentesFilter");
	if($isCsucsorfele === 1)
		$elelmiszerList = array_filter($elelmiszerList, "csucsorfeleFilter");
	if($isOlajosMag === 1)
		$elelmiszerList = array_filter($elelmiszerList, "olajosmagFilter");
	if($isMagasHisztamin === 1)
		$elelmiszerList = array_filter($elelmiszerList, "magashisztaminFilter");
	if($isMagasGI === 1)
		$elelmiszerList = array_filter($elelmiszerList, "magasGIFilter");
	if($isMagasFruktoz === 1)
		$elelmiszerList = array_filter($elelmiszerList, "magasFruktozFilter");
	if($isMagasFodmap === 1)
		$elelmiszerList = array_filter($elelmiszerList, "magasFodmapFilter");
	if($isKozepesFodmap === 1)
		$elelmiszerList = array_filter($elelmiszerList, "kozepesFodmapFilter");
	if($isHuvelyes === 1)
		$elelmiszerList = array_filter($elelmiszerList, "huvelyesFilter");
	if($isTojas === 1)
		$elelmiszerList = array_filter($elelmiszerList, "tojasFilter");
	if($isReflux === 1)
		$elelmiszerList = array_filter($elelmiszerList, "refluxFilter");
	if($isLektin === 1)
		$elelmiszerList = array_filter($elelmiszerList, "lektinFilter");
	if($isTejKereszt === 1)
		$elelmiszerList = array_filter($elelmiszerList, "tejKeresztFilter");
	if($isTejtermek === 1)
		$elelmiszerList = array_filter($elelmiszerList, "tejtermekFilter");
	if($isHus === 1)
		$elelmiszerList = array_filter($elelmiszerList, "husFilter");
	if(count($intol) > 0)
		$elelmiszerList = array_filter($elelmiszerList, function ($elelmiszer) use ($intol) { return intolFilter($elelmiszer, $intol); });
	if(count($izles) > 0)
		$elelmiszerList = array_filter($elelmiszerList, function ($elelmiszer) use ($izles) { return izlesFilter($elelmiszer, $izles); });
	
	$szenhidratList = array_filter($elelmiszerList, "szenhidratFilter");
	$szenhidratList = NameLangChange($szenhidratList,$lang);
	$szenhidratList = CategoryLangChange($szenhidratList,$lang);

	$feherjeList = array_filter($elelmiszerList, "feherjeFilter");
	$feherjeList = NameLangChange($feherjeList,$lang);	
	$feherjeList = CategoryLangChange($feherjeList,$lang);

	$zsirList = array_filter($elelmiszerList, "zsirFilter");
	$zsirList = NameLangChange($zsirList,$lang);	
	$zsirList = CategoryLangChange($zsirList,$lang);	
	
	$rostList = array_filter($elelmiszerList, "rostFilter");
	$rostList = NameLangChange($rostList,$lang);	
	$rostList = CategoryLangChange($rostList,$lang);	

	$egyebList = array_filter($elelmiszerList, "egyebFilter");
	$egyebHossz = count($egyebList) / 4;
	$egyebList = NameLangChange($egyebList,$lang);	
	$egyebList = CategoryLangChange($egyebList,$lang);	
	
	$egyebArr = array();
	$egyebArr[0] = array_slice($egyebList, 0, $egyebHossz);
	$egyebArr[1] = array_slice($egyebList, $egyebHossz, $egyebHossz);
	$egyebArr[2] = array_slice($egyebList, 2 * $egyebHossz, $egyebHossz);
	$egyebArr[3] = array_slice($egyebList, 3 * $egyebHossz, count($egyebList) - 3 * $egyebHossz);
	
	//$egyebArr = array( $egyebList );
	
	function konnyuFilter($elelmiszer){ return $elelmiszer["IsProblemas"] === "0" || in_array($elelmiszer["Id"], (array)$GLOBALS["megisKell"]); }
	
	function glutenmentesFilter($elelmiszer){ return $elelmiszer["IsGlutentartalmu"] === "0" || in_array($elelmiszer["Id"], (array)$GLOBALS["megisKell"]); }
	function csucsorfeleFilter($elelmiszer){ return $elelmiszer["IsCsucsorfele"] === "0" || in_array($elelmiszer["Id"], (array)$GLOBALS["megisKell"]); }
	function olajosmagFilter($elelmiszer){ return $elelmiszer["IsOlajosMag"] === "0" || in_array($elelmiszer["Id"], (array)$GLOBALS["megisKell"]); }
	function magashisztaminFilter($elelmiszer){ return $elelmiszer["IsMagasHisztamin"] === "0" || in_array($elelmiszer["Id"], (array)$GLOBALS["megisKell"]); }
	function magasGIFilter($elelmiszer){ return $elelmiszer["IsMagasGI"] === "0" || in_array($elelmiszer["Id"], (array)$GLOBALS["megisKell"]); }
	function magasFruktozFilter($elelmiszer){ return $elelmiszer["IsMagasFruktoz"] === "0" || in_array($elelmiszer["Id"], (array)$GLOBALS["megisKell"]); }
	function magasFodmapFilter($elelmiszer){ return $elelmiszer["IsMagasFodmap"] === "0" || in_array($elelmiszer["Id"], (array)$GLOBALS["megisKell"]); }
	function kozepesFodmapFilter($elelmiszer){ return $elelmiszer["IsKozepesFodmap"] === "0" || in_array($elelmiszer["Id"], (array)$GLOBALS["megisKell"]); }
	function huvelyesFilter($elelmiszer){ return $elelmiszer["ElelmiszerKategoriaId"] !== "5" || in_array($elelmiszer["Id"], (array)$GLOBALS["megisKell"]); }
	function tojasFilter($elelmiszer){ return $elelmiszer["ElelmiszerKategoriaId"] !== "10" || in_array($elelmiszer["Id"], (array)$GLOBALS["megisKell"]); }
	function refluxFilter($elelmiszer){ return $elelmiszer["IsRefluxTr"] === "0" || in_array($elelmiszer["Id"], (array)$GLOBALS["megisKell"]); }
	function lektinFilter($elelmiszer){ return $elelmiszer["IsLektin"] === "0" || in_array($elelmiszer["Id"], (array)$GLOBALS["megisKell"]); }
	function tejKeresztFilter($elelmiszer){ return $elelmiszer["IsTejKereszt"] === "0" || in_array($elelmiszer["Id"], (array)$GLOBALS["megisKell"]); }
	function tejtermekFilter($elelmiszer){ return $elelmiszer["ElelmiszerKategoriaId"] !== "8" || in_array($elelmiszer["Id"], (array)$GLOBALS["megisKell"]); }
	function husFilter($elelmiszer){ return $elelmiszer["ElelmiszerKategoriaId"] !== "9" || in_array($elelmiszer["Id"], (array)$GLOBALS["megisKell"]); }

	function szenhidratFilter($elelmiszer){ return $elelmiszer["MakroForras"] === "1"; }
	function feherjeFilter($elelmiszer){ return $elelmiszer["MakroForras"] === "2"; }
	function zsirFilter($elelmiszer){ return $elelmiszer["MakroForras"] === "3"; }
	function rostFilter($elelmiszer){ return $elelmiszer["MakroForras"] === "4"; }
	function egyebFilter($elelmiszer){ return $elelmiszer["MakroForras"] === "0"; }
	
	function intolFilter($elelmiszer, $intol){ return !in_array($elelmiszer["Id"], $intol) || in_array($elelmiszer["Id"], (array)$GLOBALS["megisKell"]); }
	function izlesFilter($elelmiszer, $izles){ return !in_array($elelmiszer["Id"], $izles) || in_array($elelmiszer["Id"], (array)$GLOBALS["megisKell"]); }
	function kiemelesFilter($elelmiszer, $kiemeles){ return !in_array($elelmiszer["Id"], $kiemeles) || in_array($elelmiszer["Id"], (array)$GLOBALS["megisKell"]); }
?>
<html>
<head>
<?php include_once('headLinks.php'); ?>
    <style>
		div.divHeader{
			width: 25%;
			text-align: left;
			margin-top: 30px;
		}
        div.userName{
            margin-top: 10px;
            margin-bottom: 20px;
            margin-left: 10px;
            font-size: 1rem;
            text-align: left;
        }
        .title1{
            margin-top: 60px;
            font-size: 2.7rem;
            text-align: center;
			color: <?php echo $color ?>;
        }
        div.filterTitle{
            margin-bottom: 60px;
			margin-left: auto;
			margin-right: auto;
            width: 45%;
            font-size: 16pt;
            text-align: center;
			line-height: 1.5;
        }	
 		div.filterItem{
            margin-bottom: 5px;
        }	
        span.upperCase{
            font-weight: bold !important;
        }
        @media print {
            div.acstipus, div.hortipus, div.testatipus, div.eatingHabits, div.tovabbi, div.pagebreak {page-break-before: always;}
        }
        h1, h2, h3, h4, h5, h6{
            width: 100%;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
        }
		div.divCommon{
			font-size: 16pt;
			padding-bottom: 10px;
		}
		div.divTitle{
			font-weight: bold;
			padding-top: 30px;
		}
		#mainTable{
			width: 100%;			
			border-collapse: collapse;
		}
		#mainTable td{
			vertical-align: top;
			border: 1px lightgrey solid;
			padding: 5px 5px 5px 20px;
		}
		#mainTable .mainCategory{
			width: 25%;
		}
		#mainTable .mainCategoryHeader{
			font-size: 16pt;
            width: 100%;
			padding-top: 20px;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
			font-weight: bold;
			min-height: 100px;
			height: 100px;
		}
		#mainTable .subCategoryHeader{
			font-weight: bold;
		}
		.elelmUl{
			line-height: 25px;
			list-style: none;
			padding-left: 0px;
		}
		li.kiem{
			font-weight: bold;
			text-decoration: underline;
		}
		li.nehez{
			background-color: white;			
			padding-left: 10px;
		}
		li.konnyu{
			background-color: white;
			padding-left: 10px;
		}
		div.footer{
			text-align: center;
			padding: 10px;	
		}
	</style>
</head>
<body>

<?php

include_once('proLogos.php');

print "<div class='userName'>" . htmlentities($fill["userName"], ENT_QUOTES | ENT_SUBSTITUTE, "ISO-8859-1") . "&nbsp;-&nbsp;" . date("Y.m.d.") . "</div>";
print "<div class='title1'>$title</div>";
print "<div class='filterTitle'>$title2";
print implode(", ", $filteredStr) . "</div>";
?>

<table id='mainTable' cellpadding="0" cellspacing="0">
	<tr>
	<td class="mainCategory">
		<div class="mainCategoryHeader"><?php echo $ch; ?></div>
		<?php printAjanlasBlokk($szenhidratList, $kiemeles); ?>
	</td>
	<td class="mainCategory">
		<div class="mainCategoryHeader"><?php echo $pro; ?></div>
		<?php printAjanlasBlokk($feherjeList, $kiemeles); ?>
	</td>
	<td class="mainCategory">
		<div class="mainCategoryHeader"><?php echo $fat; ?></div>
		<?php printAjanlasBlokk($zsirList, $kiemeles); ?>
	</td>
	<td class="mainCategory">
		<div class="mainCategoryHeader"><?php echo $fiber; ?></div>
		<?php printAjanlasBlokk($rostList, $kiemeles); ?>
	</td>
	</tr>
	<tr>
	<?php 
	$lastGroupId = null;
	for($i = 0; $i < count($egyebArr); $i++){ 
	?>
	<td class="mainCategory">
		<div class="mainCategoryHeader"><?php print $i == 0 ? $other : " " ?></div>
		<?php $lastGroupId = printAjanlasBlokk($egyebArr[$i], $kiemeles, $lastGroupId); ?>
	</td>
	<?php } ?>
	</tr>
</table>
<div class="footer">powered by Healers Digital</div>
</body>
</html>