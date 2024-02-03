<?php
    session_start();
    include_once('functions.php');	
	include_once('elelmiszer_functions.php');
	
	if(!$userObject){
        include_once('index.php');
        exit;
    }	
	
	$elelmiszerList = getElelmiszerList();
	// print_r($elelmiszerList)
?>

<html>
<head>
    <title>Lista</title>
    <meta http-equiv="content-type" content=<?php print "'text-html; charset=$CHARSET'"; ?>>
    <link rel=stylesheet type='text/css' href='baseStyle2.css'>
	<link rel="shortcut icon" href="#">
    <style>
        html, body, table {
            height:100%;
        }
		body{
			margin: 0px;
		}
		#mainTable{			
			
			border: 1px black solid;
			border-collapse: collapse;
			/* margin-left: 5px !important;
			margin-top: 5px !important; */
		}
		#mainTable th, #mainTable td{
			border: 1px black solid;
			padding: 3px 10px;
		}
		#mainTable td.kategoria{
			font-weight: bold;
			background-color: lightgray;
			font-size:12pt;
		}
		#mainTable td.cb{
			text-align: center;
		}
		#mainTable th {
			background: lightgray;
			position: sticky;
			top: 0; /* Don't forget this, required for the stickiness */
			box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
			font-size: 9pt;
		}
		.trElelm:hover{
			background-color: pink;
		}
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="underscore-min.js"></script>
	<script>
		$(document).ready(function () {
			$("#mainTable td.cb").click(function(e){
				$(this).find('input[type=checkbox]').prop("checked", !$(this).find('input[type=checkbox]').prop("checked"));
				$(this).find('input[type=checkbox]').change();
			});
			$("#mainTable td.cb input[type='checkbox']").click(function(e){
				e.stopPropagation();
			});			
			$("#mainTable td.cb input[type='checkbox'][data-col='MakroForras']").change(function(e){
				e.stopPropagation();
				if($(this).prop("checked")){
					$(this).closest("tr").find("input[type='checkbox'][data-col='MakroForras']").prop("checked", false);					
				}
				$(this).prop("checked", true);
			});
			$("#mainTable td.cb input[type='checkbox'][data-col='IsProblemas']").change(function(e){
				e.stopPropagation();
				if($(this).prop("checked")){
					$(this).closest("tr").find("input[type='checkbox'][data-col='IsProblemas']").prop("checked", false);					
				}
				$(this).prop("checked", true);
			});
			$("#mainTable td.cb input[type='checkbox']").change(function(e){
				e.stopPropagation();

				$.post("elelmiszer_store.php", { id: $(this).attr("data-id"), col: $(this).data("col"), value: $(this).val(), isChecked: ($(this).prop("checked") ? 1 : 0) }, function (result) {
					console.log(result);
				});				
			});
		});
	</script>
</head>
<body>
	<?php include("adminmenu.php") ?>

	<table id='mainTable' cellspacing="0" cellpadding="0">
		<tr>
			<th></th>
			<th>Könnyû</th>
			<th>Macera</th>
			<th>Ch</th>
			<th>Fehérje</th>
			<th>Zsír</th>
			<th>Rost</th>
			<th>Egyéb</th>
			<th>Glutént</th>
			<th>Csucsor</th>
			<th>Olajos m</th>
			<th>Magas hiszt</th>
			<th>Intole- ranciák</th>
			<th>Reflux triggerek</th>
			<th>Lektin</th>
			<th>Ízlés</th>
			<th>Magas GI</th>
			<th>Magas fruktóz</th>
			<th>Magas fodmap</th>
			<th>Közepes fodmap</th>
			<th>Tej kereszt</th>
			<th>Kiem</th>
		</tr>
		<?php
		$lastGroupId = null;
		foreach($elelmiszerList as $elelmiszer){			
			if($lastGroupId != $elelmiszer["ElelmiszerKategoriaId"]){
				print "<tr><td class='kategoria' colspan='100%'>" . $elelmiszer["ElelmiszerKategoriaNev"] . "</td></tr>";
			}
			print "<tr class='trElelm'>";
			print "<td>" . $elelmiszer["Nev"] . "</td>";
			print "<td class='cb'><input type='checkbox' value='0' data-id='{$elelmiszer["Id"]}' data-col='IsProblemas' " . ($elelmiszer["IsProblemas"] === "0" ? "checked" : "") . "></td>";
			print "<td class='cb'><input type='checkbox' value='1' data-id='{$elelmiszer["Id"]}' data-col='IsProblemas' " . ($elelmiszer["IsProblemas"] === "1" ? "checked" : "") . "></td>";
			print "<td class='cb'><input type='checkbox' value='1' data-id='{$elelmiszer["Id"]}' data-col='MakroForras' " . ($elelmiszer["MakroForras"] === "1" ? "checked" : "") . "></td>";
			print "<td class='cb'><input type='checkbox' value='2' data-id='{$elelmiszer["Id"]}' data-col='MakroForras' " . ($elelmiszer["MakroForras"] === "2" ? "checked" : "") . "></td>";
			print "<td class='cb'><input type='checkbox' value='3' data-id='{$elelmiszer["Id"]}' data-col='MakroForras' " . ($elelmiszer["MakroForras"] === "3" ? "checked" : "") . "></td>";
			print "<td class='cb'><input type='checkbox' value='4' data-id='{$elelmiszer["Id"]}' data-col='MakroForras' " . ($elelmiszer["MakroForras"] === "4" ? "checked" : "") . "></td>";
			print "<td class='cb'><input type='checkbox' value='0' data-id='{$elelmiszer["Id"]}' data-col='MakroForras' " . ($elelmiszer["MakroForras"] === "0" ? "checked" : "") . "></td>";
			print "<td class='cb'><input type='checkbox' value='1' data-id='{$elelmiszer["Id"]}' data-col='IsGlutentartalmu' " . ($elelmiszer["IsGlutentartalmu"] === "1" ? "checked" : "") . "></td>";
			print "<td class='cb'><input type='checkbox' value='1' data-id='{$elelmiszer["Id"]}' data-col='IsCsucsorfele' " . ($elelmiszer["IsCsucsorfele"] === "1" ? "checked" : "") . "></td>";
			print "<td class='cb'><input type='checkbox' value='1' data-id='{$elelmiszer["Id"]}' data-col='IsOlajosMag' " . ($elelmiszer["IsOlajosMag"] === "1" ? "checked" : "") . "></td>";
			print "<td class='cb'><input type='checkbox' value='1' data-id='{$elelmiszer["Id"]}' data-col='IsMagasHisztamin' " . ($elelmiszer["IsMagasHisztamin"] === "1" ? "checked" : "") . "></td>";
			print "<td class='cb'><input type='checkbox' value='1' data-id='{$elelmiszer["Id"]}' data-col='IsFoodTeszt' " . ($elelmiszer["IsFoodTeszt"] === "1" ? "checked" : "") . "></td>";
			print "<td class='cb'><input type='checkbox' value='1' data-id='{$elelmiszer["Id"]}' data-col='IsRefluxTr' " . ($elelmiszer["IsRefluxTr"] === "1" ? "checked" : "") . "></td>";
			print "<td class='cb'><input type='checkbox' value='1' data-id='{$elelmiszer["Id"]}' data-col='IsLektin' " . ($elelmiszer["IsLektin"] === "1" ? "checked" : "") . "></td>";
			print "<td class='cb'><input type='checkbox' value='1' data-id='{$elelmiszer["Id"]}' data-col='IsPreferencia' " . ($elelmiszer["IsPreferencia"] === "1" ? "checked" : "") . "></td>";			
			print "<td class='cb'><input type='checkbox' value='1' data-id='{$elelmiszer["Id"]}' data-col='IsMagasGI' " . ($elelmiszer["IsMagasGI"] === "1" ? "checked" : "") . "></td>";
			print "<td class='cb'><input type='checkbox' value='1' data-id='{$elelmiszer["Id"]}' data-col='IsMagasFruktoz' " . ($elelmiszer["IsMagasFruktoz"] === "1" ? "checked" : "") . "></td>";
			print "<td class='cb'><input type='checkbox' value='1' data-id='{$elelmiszer["Id"]}' data-col='IsMagasFodmap' " . ($elelmiszer["IsMagasFodmap"] === "1" ? "checked" : "") . "></td>";
			print "<td class='cb'><input type='checkbox' value='1' data-id='{$elelmiszer["Id"]}' data-col='IsKozepesFodmap' " . ($elelmiszer["IsKozepesFodmap"] === "1" ? "checked" : "") . "></td>";
			print "<td class='cb'><input type='checkbox' value='1' data-id='{$elelmiszer["Id"]}' data-col='IsTejKereszt' " . ($elelmiszer["IsTejKereszt"] === "1" ? "checked" : "") . "></td>";
			print "<td class='cb'><input type='checkbox' value='1' data-id='{$elelmiszer["Id"]}' data-col='IsKiemeles' " . ($elelmiszer["IsKiemeles"] === "1" ? "checked" : "") . "></td>";
			$lastGroupId = $elelmiszer["ElelmiszerKategoriaId"];
			print "</tr>";
		}
		?>
	</table>
</body>
</html>