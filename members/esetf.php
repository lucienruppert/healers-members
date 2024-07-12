<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
session_start();
include_once('functions.php');
if (!$userObject) {
	include_once('index.php');
	exit;
}

$isOnlyOwned = ($userObject['status'] == 2);
$isTest = ($userObject['status'] == 3 || $userObject['status'] == 5);

if ($isTest) {
	$questUsers = selectTestQuestUsers();

	//PROGRAMRéSZ: HOGY TéBB TANéCSADé IS HASZNéLHASSA AZ éLELMISZERLISTA GENERéTORT
	$szamlalo = count($questUsers);
	for ($x = 0; $x < $szamlalo; $x++) {
		$subarrayofArray = $questUsers[$x];
		if ($subarrayofArray['consultantID'] != $userObject['ID']) {
			unset($questUsers[$x]);
		}
	}
} else
	$questUsers = selectQuestUsers(array(2, 666, 667, 668), $userObject['ID'], !$isOnlyOwned);
?>

<html>

<head>
	<title>Healers Digital</title>
	<meta http-equiv="content-type" content=<?php print "'text-html; charset=$CHARSET'"; ?>>
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.2/themes/smoothness/jquery-ui.css">
	<link rel=stylesheet type='text/css' href='baseStyle2.css'>
	<link rel="stylesheet" href="esetf.css">
	<link rel="stylesheet" href="customProtocols.css">
	<style>
		div.note1,
		div.note2,
		div.note22,
		div.note3,
		div.addConsultation,
		div.specCons {
			float: right;
			padding: 2px 4px 2px 4px;
			margin-right: 3px;
			background-color: <?php echo $color ?>;
			border-radius: 3px;
			font-size: 12px;
			font-weight: bold;
			color: white;
		}

		div.selSpecCons {
			background-color: black;
		}

		span.btnConsultations {
			margin-left: 10px;
		}

		div.hdn {
			display: none;
		}

		#btnGenRecom,
		#btnGenFoodRecom {
			border-width: 0px;
			font-size: 14px;
			color: white;
			cursor: pointer;
			padding: 10px;
			width: 200px;
			background-color: <?php echo $color ?>;
			border-radius: 5px;
			margin-left: 400px;
			font-weight: bold;
		}

		#btnGenFoodRecom2 {
			border-width: 0px;
			font-size: 14px;
			color: white;
			cursor: pointer;
			padding: 10px;
			width: 50px;
			background-color: <?php echo $color ?>;
			border-radius: 5px;
			font-weight: bold;
		}

		.ujButton {
			border-width: 0px;
			font-size: 14px;
			color: white;
			cursor: pointer;
			padding: 3px 10px 3px 10px;
			width: 50px;
			background-color: <?php echo $color ?>;
			border-radius: 2px;
		}

		#divQuestFill,
		#divNote1 {
			width: calc(50vw - 15px);
			height: calc(100vh - 70px);
			overflow: auto;
		}

		#divNote2,
		#divNote22,
		#divNote3 {
			width: 100%;
		}

		#divUserPlate {
			position: fixed;
			top: 50px;
			left: 0px;
		}

		body {
			padding-top: 50px;
		}

		#divMenuCont {
			position: fixed;
			top: 0;
			left: 0;
			width: calc(100vw - 20px);
		}

		.noanswers {
			text-align: center;
			font-size: 2rem;
		}

		#flexCont2 {
			display: flex;
			width: calc(100vw - 20px);
		}

		.flexChild1 {
			flex: 1;
		}

		.flexCont {
			display: flex;
			width: 100%;
		}

		.copyPaste {
			width: 15px;
			min-width: 15px;
			height: 15px;
			margin-top: 10px;
			cursor: pointer;
			margin-left: auto;
			margin-right: 20px;
			border: 2px grey solid;
			border-radius: 3px;
		}

		.color {
			border: 1px solid <?php echo $color ?>;
		}

		.generateButton,
		.saveButton {
			background-color: <?php echo $color ?>;
		}

		.generateButtonOld,
		.foodListGeneration {
			text-align: right;
		}

		#divUserPlate .fillDate {
			display: none;
		}

		.fillDate {
			font-weight: bold;
		}
	</style>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
	<script src="underscore-min.js"></script>
	<script type='text/javascript' src='customProtocols.js' defer></script>
	<script>
		var fillId = null;
		var fillType = null;
		var myTimeout = null;
		var isTest = <?php print($isTest ? "true" : "false"); ?>;

		$(document).ready(function() {
			$("body").on("click", "div.kitoltes", function(e) {
				selectFillRow(this);
				if (!isTest) {
					$("#tblMain > tbody > tr > td:first").hide();

					$("#divQuestFill").show();
					fillDivQuestFill($(this).data("myid"), 1);

					e.stopPropagation();
					$("#divNote2").hide();
					$("#divNote22").hide();
					$("#divNote3").hide();
					$("#txtNote1").appendTo($("#divNote1"));
					showNote($(this).data("myid"), 1);
				} else {
					$("#divNote3").show();
					fillFoodFilters($(this).data("myid"))
				}
			});
			$("#btnDone").click(function() {
				$.post("sendAjanlas.php", {
					id: fillId,
					type: 1,
					acstipus: getRadioValue("acstipus")
				});
				if (fillId !== null)
					window.open("ajanlas.php?id=" + fillId + "&acstipus=" + getRadioValue("acstipus"), "_blank");
			});
			$("body").on("click", "tr.answered", function() {
				var isHighlighted = $(this).closest("tr").hasClass("highlighted");
				var answerId = $(this).closest("tr").data("answerId");
				var obj = $(this);
				$.post("changeHighlight.php", {
					answerId: answerId,
					isHighlighted: !isHighlighted
				}, function(result) {
					if (result.isHighlighted == 1) {
						obj.closest("tr").addClass("highlighted");
					} else {
						obj.closest("tr").removeClass("highlighted");
					}
				});
			});
			$("body").on("click", ".fish", function() {
				var isChecked = $(this).prop("checked");
				$(".fish").prop("checked", isChecked);
			});
			$("body").on("click", "div.note1", function(e) {
				e.stopPropagation();
				$(this).closest("div.kitoltes").trigger("click");
			});
			$("body").on("click", "div.note2", function(e) {
				selectFillRow($(this).closest("div.kitoltes"));
				e.stopPropagation();
				$("#divQuestFill").hide();
				$("#divNote1").hide();
				$("#txtNote1").appendTo($("#txtNote1Cont2"));
				$("#divNote22").hide();
				$("#divNote3").hide();
				showNote($(this).closest("div.kitoltes").data("myid"), 2);
				showNote($(this).closest("div.kitoltes").data("myid"), 21);
				showNote($(this).closest("div.kitoltes").data("myid"), 144);
				showRootCauses($(this).closest("div.kitoltes").data("myid"));
				showSolutionSteps($(this).closest("div.kitoltes").data("myid"));
				showEatMore($(this).closest("div.kitoltes").data("myid"));
			});
			$("body").on("click", "div.note22", function(e) {
				selectFillRow($(this).closest("div.kitoltes"));
				e.stopPropagation();
				$("#divQuestFill").hide();
				$("#divNote1").hide();
				$("#txtNote1").appendTo($("#txtNote1Cont22"));
				$("#divNote2").hide();
				$("#divNote3").hide();
				$("#divNote22").show();
				loadCustomProtocols($(this).closest("div.kitoltes").data("myid"));
			});
			$("body").on("click", "div.note3", function(e) {
				selectFillRow($(this).closest("div.kitoltes"));
				e.stopPropagation();
				$("#divQuestFill").hide();
				$("#divNote1").hide();
				$("#divNote2").hide();
				$("#divNote22").hide();
				$("#divNote3").show();
				fillFoodFilters($(this).closest("div.kitoltes").data("myid"));
			});
			$("body").on("click", "div.addConsultation", function(e) {
				e.stopPropagation();
				if (confirm("Biztos fel szeretnél venni egy új konzultációt?") == true) {
					var contDiv = $(this).closest("div.kitoltes");
					$.post("addNewConsultation.php", {
						id: contDiv.data("myid")
					}, function(result) {
						contDiv.find(".btnConsultations").prepend($("<div class='specCons' data-consid='" + result.newId + "'>" + result.lastDate + "</div>"));
						showConsultation(contDiv, result.newId);
					});
				}
			});
			$("body").on("click", "div.specCons", function(e) {
				e.stopPropagation();
				var id = $(this).data("consid");
				showConsultation($(this).closest("div.kitoltes"), id);
			})
			$("#txtNote1").change(function() {
				storeNote(1);
			});
			$("#txtNote21").change(function() {
				storeNote(21);
			});
			$("#txtNote144").change(function() {
				storeNote(144);
			});
			$("#btnSearch").click(function() {
				var exp = $("#txtSearch").val();
				if (exp.length == 0) {
					location.reload();
				} else {
					$.get("getFilteredUsers.php", {
						txt: exp
					}, function(data) {
						$("#divUserList div.kitoltes").hide();
						if (data.length > 0) {
							for (var i = 0; i < data.length; i++) {
								$("#divUserList div.kitoltes[data-myid=" + data[i] + "]").show();
							}
						}
					});
				}
			});
			$("#btnAddOrphanUser").click(function() {
				var userName = prompt("Ügyfél neve");
				$.post("saveOrphanUser.php", {
					userName: userName
				}, function() {
					location.reload();
				});
			});
			$("#txtSearch").on("keydown", function(e) {
				if (e.keyCode == 13)
					$("#btnSearch").trigger("click");
				else if (e.keyCode == 8) {
					if (myTimeout != null)
						clearTimeout(myTimeout);
					myTimeout = setTimeout(function() {
						$("#btnSearch").trigger("click");
					}, 1000);
				}
			});
			$("#txtSearch").on("keypress", function(e) {
				if (myTimeout != null)
					clearTimeout(myTimeout);
				myTimeout = setTimeout(function() {
					$("#btnSearch").trigger("click");
				}, 1000);
			});

			$("#btnAddTestUser").click(function() {
				var uName = $("#txtAddTestUser").val();
				var uConsultantId = $("#txtConsultantId").val();
				if (uName.length == 0) {
					alert("Kérlek add meg az ügyfél nevét.");
				} else {
					$.get("refreshTestUsers.php", {
						userName: uName,
						consId: uConsultantId
					}, function(data) {
						location.reload();
					});
				}
			});

			$("#btnGenRecom").click(function() {
				window.open("esetf_ajanlas.php?id=" + fillId + "_blank");
			});

			$("#btnGenFoodRecom").click(function() {
				var isGlutenmentes = $("#cbIsGlutenmentes").prop("checked") ? "1" : "0";
				var isCsucsorfele = $("#cbIsCsucsorfele").prop("checked") ? "1" : "0";
				var isOlajosMag = $("#cbIsOlajosMag").prop("checked") ? "1" : "0";
				var isMagasHisztamin = $("#cbIsMagasHisztamin").prop("checked") ? "1" : "0";
				var isMagasGI = $("#cbIsMagasGI").prop("checked") ? "1" : "0";
				var isMagasFruktoz = $("#cbIsMagasFruktoz").prop("checked") ? "1" : "0";
				var isMagasFodmap = $("#cbIsMagasFodmap").prop("checked") ? "1" : "0";
				var isKozepesFodmap = $("#cbIsKozepesFodmap").prop("checked") ? "1" : "0";
				var isHuvelyes = $("#cbIsHuvelyesek").prop("checked") ? "1" : "0";
				var isTejtermek = $("#cbIsTejtermekek").prop("checked") ? "1" : "0";
				var isTojas = $("#cbIsTojas").prop("checked") ? "1" : "0";
				var isRefluxTr = $("#cbIsRefluxTr").prop("checked") ? "1" : "0";
				var isTejKereszt = $("#cbIsTejKereszt").prop("checked") ? "1" : "0";
				var isHus = $("#cbIsHusok").prop("checked") ? "1" : "0";
				var isLektin = $("#cbIsLektin").prop("checked") ? "1" : "0";
				// ITT HOZZUK LéTRE A VéLTOZéT A NYELVVéLTéSHOZ
				var lang = '';
				if ($("#magyarBtn").is(":checked")) {
					lang = 'magyar';
				} else if ($("#nemetBtn").is(":checked")) {
					lang = 'nemet';
				} else if ($("#angolBtn").is(":checked")) {
					lang = 'angol';
				}

				var overwrite = [];
				$("div.owTag").each(function() {
					overwrite.push($(this).data("value"));
				});

				var chkArray = [];
				$("#tdIntol input[type=checkbox]:checked").each(function() {
					chkArray.push($(this).val());
				});
				var intolStr = chkArray.join("%2C");

				var chkArray2 = [];
				$("#tdIzles input[type=checkbox]:checked").each(function() {
					chkArray2.push($(this).val());
				});
				var izlesStr = chkArray2.join("%2C");

				var chkArray3 = [];
				$("#tdKiemeles input[type=checkbox]:checked").each(function() {
					chkArray3.push($(this).val());
				});
				var kiemelesStr = chkArray3.join("%2C");

				window.open("elelmiszer_ajanlas.php?id=" + fillId +
					"&isTest=" + (isTest ? "1" : "0") +
					"&isglm=" + isGlutenmentes +
					"&iscsucs=" + isCsucsorfele +
					"&isolm=" + isOlajosMag +
					"&ismhiszt=" + isMagasHisztamin +
					"&ismaggi=" + isMagasGI +
					"&ismagfruk=" + isMagasFruktoz +
					"&ismagfod=" + isMagasFodmap +
					"&iskozfod=" + isKozepesFodmap +
					"&istejx=" + isTejKereszt +
					"&ishuvely=" + isHuvelyes +
					"&istojas=" + isTojas +
					"&isreflux=" + isRefluxTr +
					"&istej=" + isTejtermek +
					"&ishus=" + isHus +
					"&intol=" + intolStr +
					"&izles=" + izlesStr +
					"&kiemeles=" + kiemelesStr +
					"&lang=" + lang +
					"&ow=" + encodeURIComponent(JSON.stringify(overwrite)), "_blank");
			});

			$("#btnGenFoodRecom2").click(function() {
				var isGlutenmentes = $("#cbIsGlutenmentes").prop("checked") ? "1" : "0";
				var isCsucsorfele = $("#cbIsCsucsorfele").prop("checked") ? "1" : "0";
				var isOlajosMag = $("#cbIsOlajosMag").prop("checked") ? "1" : "0";
				var isMagasHisztamin = $("#cbIsMagasHisztamin").prop("checked") ? "1" : "0";
				var isMagasGI = $("#cbIsMagasGI").prop("checked") ? "1" : "0";
				var isMagasFruktoz = $("#cbIsMagasFruktoz").prop("checked") ? "1" : "0";
				var isMagasFodmap = $("#cbIsMagasFodmap").prop("checked") ? "1" : "0";
				var isKozepesFodmap = $("#cbIsKozepesFodmap").prop("checked") ? "1" : "0";
				var isHuvelyes = $("#cbIsHuvelyesek").prop("checked") ? "1" : "0";
				var isTejtermek = $("#cbIsTejtermekek").prop("checked") ? "1" : "0";
				var isTojas = $("#cbIsTojas").prop("checked") ? "1" : "0";
				var isRefluxTr = $("#cbIsRefluxTr").prop("checked") ? "1" : "0";
				var isTejKereszt = $("#cbIsTejKereszt").prop("checked") ? "1" : "0";
				var isHus = $("#cbIsHusok").prop("checked") ? "1" : "0";
				var isLektin = $("#cbIsLektin").prop("checked") ? "1" : "0";
				// ITT HOZZUK LéTRE A VéLTOZéT A NYELVVéLTéSHOZ
				var lang = '';
				if ($("#magyarBtn").is(":checked")) {
					lang = 'magyar';
				} else if ($("#nemetBtn").is(":checked")) {
					lang = 'nemet';
				} else if ($("#angolBtn").is(":checked")) {
					lang = 'angol';
				}

				var overwrite = [];
				$("div.owTag").each(function() {
					overwrite.push($(this).data("value"));
				});

				var chkArray = [];
				$("#tdIntol input[type=checkbox]:checked").each(function() {
					chkArray.push($(this).val());
				});
				var intolStr = chkArray.join("%2C");

				var chkArray2 = [];
				$("#tdIzles input[type=checkbox]:checked").each(function() {
					chkArray2.push($(this).val());
				});
				var izlesStr = chkArray2.join("%2C");

				var chkArray3 = [];
				$("#tdKiemeles input[type=checkbox]:checked").each(function() {
					chkArray3.push($(this).val());
				});
				var kiemelesStr = chkArray3.join("%2C");

				window.open("elelmiszer_ajanlas2.php?id=" + fillId +
					"&isTest=" + (isTest ? "1" : "0") +
					"&isglm=" + isGlutenmentes +
					"&iscsucs=" + isCsucsorfele +
					"&isolm=" + isOlajosMag +
					"&ismhiszt=" + isMagasHisztamin +
					"&ismaggi=" + isMagasGI +
					"&ismagfruk=" + isMagasFruktoz +
					"&ismagfod=" + isMagasFodmap +
					"&iskozfod=" + isKozepesFodmap +
					"&istejx=" + isTejKereszt +
					"&ishuvely=" + isHuvelyes +
					"&istojas=" + isTojas +
					"&isreflux=" + isRefluxTr +
					"&istej=" + isTejtermek +
					"&ishus=" + isHus +
					"&intol=" + intolStr +
					"&izles=" + izlesStr +
					"&kiemeles=" + kiemelesStr +
					"&lang=" + lang +
					"&ow=" + encodeURIComponent(JSON.stringify(overwrite)), "_blank");
			});

			$("#divRootCauses").on("change", ".cbRootCauses", function() {
				$.post("storeCheckbox.php", {
					type: 1,
					fillId: fillId,
					value: $(this).val(),
					isChecked: ($(this).prop("checked") ? 1 : 0)
				}, function(result) {
					notify("Mentve", 1000);
				});
			});
			$("#divRootCauses").on("change", "#rootCauseOther", function() {
				$.post("storeText.php", {
					type: 1,
					fillId: fillId,
					value: $(this).val()
				}, function(result) {
					notify("Mentve", 1000);
				});
			});

			$("#divSolutionSteps").on("change", ".cbSolutionSteps", function() {
				$.post("storeCheckbox.php", {
					type: 2,
					fillId: fillId,		
					value: $(this).val(),
					isChecked: ($(this).prop("checked") ? 1 : 0)
				}, function(result) {
					notify("Mentve", 1000);
				});
			});

			$("#divEatMore").on("change", ".cbEatMore", function() {
				$.post("storeCheckbox.php", {
					type: 3,
					fillId: fillId,
					value: $(this).val(),
					isChecked: ($(this).prop("checked") ? 1 : 0)
				}, function(result) {
					notify("Mentve", 1000);
				});
			});

			$("#cbIsGlutenmentes").click(function() {
				checkFoodByCat(1, this);
			});
			$("#cbIsCsucsorfele").click(function() {
				checkFoodByCat(2, this);
			});
			$("#cbIsOlajosMag").click(function() {
				checkFoodByCat(3, this);
			});
			$("#cbIsMagasHisztamin").click(function() {
				checkFoodByCat(4, this);
			});
			$("#cbIsMagasGI").click(function() {
				checkFoodByCat(5, this);
			});
			$("#cbIsMagasFruktoz").click(function() {
				checkFoodByCat(6, this);
			});
			$("#cbIsMagasFodmap").click(function() {
				checkFoodByCat(7, this);
			});
			$("#cbIsKozepesFodmap").click(function() {
				checkFoodByCat(8, this);
			});
			$("#cbIsTejKereszt").click(function() {
				checkFoodByCat(9, this);
			});
			$("#cbIsHuvelyesek").click(function() {
				checkFoodByCat(10, this);
			});
			$("#cbIsTejtermekek").click(function() {
				checkFoodByCat(11, this);
			});
			$("#cbIsHusok").click(function() {
				checkFoodByCat(12, this);
			});
			$("#cbIsTojas").click(function() {
				checkFoodByCat(13, this);
			});
			$("#cbIsRefluxTr").click(function() {
				checkFoodByCat(14, this);
			});
			$("#cbIsLektin").click(function() {
				checkFoodByCat(15, this);
			});

			$("#overwrite").autocomplete({
				source: "searchOverwrite.php",
				minLength: 1,
				select: function(event, ui) {
					$("#divOwTags").append("<div class='owTag' data-value='" + ui.item.id + "'>" + ui.item.value + "</div>");
					$(this).val('');
					return false;
				}
			});
			$("body").on("click", "#divOwTags div.owTag", function() {
				$(this).remove();
			});
			$("body").on("click", ".copyPaste", function(e) {
				e.stopPropagation();
				$("#txtNote1").val($("#txtNote1").val() + "\n\n" + $(this).prev(".answer").text() + "\n(" + $(this).closest("td").prev().find("div.question").text() + ")");
				$("#txtNote1").trigger("change");
			});
		});

		function showConsultation(obj, id) {
			obj.attr("data-myid", id);
			$("#divQuestFill").show();
			fillDivQuestFill(id, 1);
			$("#divNote2").hide();
			$("#divNote22").hide();
			$("#divNote3").hide();
			$("#txtNote1").appendTo($("#divNote1"));
			showNote(id, 1);
		}

		function checkFoodByCat(type, obj) {
			$.get("getElelmList.php", {
				type: type
			}, function(data) {
				if (data.length > 0) {
					for (var i = 0; i < data.length; i++) {
						$("#tdIzles input[type=checkbox][value=" + data[i] + "]").prop("checked", $(obj).prop("checked"));
						$("#tdIntol input[type=checkbox][value=" + data[i] + "]").prop("checked", $(obj).prop("checked"));
					}
				}
			});
		}

		function selectFillRow(obj) {
			$("div.kitoltes").css("background-color", "inherit");
			$(obj).css("background-color", "lightgrey");

			if (isTest) {
				$("#divUserPlate").hide();
			} else {
				$("#divUserPlate").html("");
				var newObj = $(obj).clone();
				newObj.find(".hdn").removeClass("hdn");
				newObj.css({
					"background-color": "white"
				});
				$("#divUserPlate").append(newObj);
			}
			$("#divNote3").hide();
			$("#divNote2").hide();
			$("#divNote22").hide();
			$("#divNote1").hide();
			$("#divQuestFill").hide();
			fillId = $(obj).data("myid");
			fillType = 1;
		}

		function storeNote(noteType) {
			var val = $("#txtNote" + noteType.toString()).val();

			$.post("storeNote.php", {
				noteType: noteType,
				note: val,
				fillId: fillId
			}, function(result) {
				notify("Mentve", 1000);
			});
		}

		function showNote(fillId, noteType) {
			var obj = $("#txtNote" + noteType.toString());
			if (obj.length == 0) {
				return;
			}

			$.get("getNote.php", {
				noteType: noteType,
				fillId: fillId
			}, function(result) {
				obj.val(result);
				obj.closest(".divNote").show();
			});
		}

		function showRootCauses(fillId) {
			$.get("getRootCauses.php", {
				fillId: fillId
			}, function(result) {
				$("#divRootCauses").empty();
				var otherCause = null;
				for (var i = 0; i < result.length; i++) {
					if (result[i].Value != null && result[i].Value > -1)
						$("#divRootCauses").append($("<input type='checkbox' name='rootCauses[]' id='cbRootCause_" + result[i].Value + "' class='cbRootCauses' value='" + result[i].Value + "'" + (result[i].IsChecked ? "checked" : "") + "><label for='cbRootCause_" + result[i].Value + "'>" + result[i].Name + "</label><br>"));
					else
						otherCause = result[i].Name;
				}
				$("#divRootCauses").append($("<br><textArea name='rootCauseOther' id='rootCauseOther'>" + (otherCause != null ? htmlDecode(otherCause) : "") + "</textarea><br>"));
			});
		}

		function showSolutionSteps(fillId) {
			$.get("getSolutionSteps.php", {
				fillId: fillId
			}, function(result) {
				$("#divSolutionSteps").empty();
				for (var i = 0; i < result.length; i++) {
					$("#divSolutionSteps").append($("<input type='checkbox' name='solutionSteps[]' id='cbRootCause_" + result[i].Value + "' class='cbSolutionSteps' value='" + result[i].Value + "'" + (result[i].IsChecked ? "checked" : "") + "><label for='cbRootCause_" + result[i].Value + "'>" + result[i].Name + "</label><br>"));
				}
			});
		}

		function showEatMore(fillId) {
			$.get("getEatMore.php", {
				fillId: fillId
			}, function(result) {
				$("#divEatMore").empty();
				for (var i = 0; i < result.length; i++) {
					$("#divEatMore").append($("<input type='checkbox' name='eatMore[]' id='cbEatMore_" + result[i].Value + "' class='cbEatMore' value='" + result[i].Value + "'" + (result[i].IsChecked ? "checked" : "") + "><label for='cbEatMore_" + result[i].Value + "'>" + result[i].Name + "</label><br>"));
				}
			});
		}

		function fillFoodFilters(fillId) {
			$.get("getFoodFilters.php", {
				fillId: fillId,
				isTest: (isTest ? 1 : 0)
			}, function(result) {
				var categoryFilters = result.CategoryFilters;

				$("#divNote3 #cbIsGlutenmentes").prop("checked", categoryFilters["IsGlutentartalmuFilter"] === "1");
				$("#divNote3 #cbIsCsucsorfele").prop("checked", categoryFilters["IsCsucsorfeleFilter"] === "1");
				$("#divNote3 #cbIsOlajosMag").prop("checked", categoryFilters["IsOlajosMagFilter"] === "1");
				$("#divNote3 #cbIsMagasHisztamin").prop("checked", categoryFilters["IsMagasHisztaminFilter"] === "1");
				$("#divNote3 #cbIsMagasGI").prop("checked", categoryFilters["IsMagasGIFilter"] === "1");
				$("#divNote3 #cbIsMagasFruktoz").prop("checked", categoryFilters["IsMagasFruktozFilter"] === "1");
				$("#divNote3 #cbIsMagasFodmap").prop("checked", categoryFilters["IsMagasFodmapFilter"] === "1");
				$("#divNote3 #cbIsKozepesFodmap").prop("checked", categoryFilters["IsKozepesFodmapFilter"] === "1");
				$("#divNote3 #cbIsHuvelyesek").prop("checked", categoryFilters["IsHuvelyesFilter"] === "1");
				$("#divNote3 #cbIsTejtermekek").prop("checked", categoryFilters["IsTejtermekFilter"] === "1");
				$("#divNote3 #cbIsHusok").prop("checked", categoryFilters["IsHusFilter"] === "1");
				$("#divNote3 #cbIsTojas").prop("checked", categoryFilters["IsTojasFilter"] === "1");
				$("#divNote3 #cbIsRefluxTr").prop("checked", categoryFilters["IsRefluxTrFilter"] === "1");
				$("#divNote3 #cbIsTejKereszt").prop("checked", categoryFilters["IsTejKeresztFilter"] === "1");
				$("#divNote3 #cbIsLektin").prop("checked", categoryFilters["IsLektinFilter"] === "1");

				var intolFilters = result.IntolFilters;
				$("#tdIntol").html("");
				for (var i = 0; i < intolFilters.length; i++) {
					var node = $("<div class='divInnerNode divIntolNode'></div>");
					var cb = $("<input type='checkbox' value='" + intolFilters[i].Id + "'" + (intolFilters[i].IsChecked ? " checked" : "") + "> " + intolFilters[i].Nev + "</input>");
					node.append(cb);
					$("#tdIntol").append(node);
				}

				var izlesFilters = result.IzlesFilters;
				$("#tdIzles").html("");
				for (var i = 0; i < izlesFilters.length; i++) {
					var node = $("<div class='divInnerNode divIzlesNode'></div>");
					var cb = $("<input type='checkbox' class='" + (izlesFilters[i].IsFish ? "fish" : "") + "' value='" + izlesFilters[i].Id + "'" + (izlesFilters[i].IsChecked ? " checked" : "") + "> " + izlesFilters[i].Nev + "</input>");
					node.append(cb);
					$("#tdIzles").append(node);
				}

				var kiemelesFilters = result.KiemelesFilters;
				$("#tdKiemeles").html("");
				for (var i = 0; i < kiemelesFilters.length; i++) {
					var node = $("<div class='divInnerNode divKiemelesNode'></div>");
					var cb = $("<input type='checkbox' value='" + kiemelesFilters[i].Id + "'" + (kiemelesFilters[i].IsChecked ? " checked" : "") + "> " + kiemelesFilters[i].Nev + "</input>");
					node.append(cb);
					$("#tdKiemeles").append(node);
				}

				var megisKell = result.MegisKell;
				$("#divOwTags").empty();
				for (var i = 0; i < megisKell.length; i++) {
					$("#divOwTags").append("<div class='owTag' data-value='" + megisKell[i].Id + "'>" + megisKell[i].Nev + "</div>");
				}
			});
		}

		function notify(msg, ms) {
			$("div#msg").text(msg);
			$("div#msg").show();
			setTimeout(function() {
				$("div#msg").hide();
			}, ms);
		}

		function htmlEncode(value) {
			return $("<div/>").text(value).html();
		}

		function getRadioValue(name) {
			return $('input[name=' + name + ']:checked').val();
		}

		function fillDivQuestFill(fid, type) {
			fillId = null;
			fillType = null;
			$("#divQuestFill").html("");
			var btnConsultations = $("div.kitoltes[data-myid=" + fid + "]").find(".btnConsultations");
			btnConsultations.html("");
			$.get("tprofilServer.php", {
				id: fid,
				type: type
			}, function(result) {
				if (result.success) {
					fillId = fid;
					fillType = type;
					var txt = "";
					if (type === 1)
						txt = "Anyagcsere típus";
					else if (type === 2)
						txt = "összes infó";

					if (type === 1) {
						$("#anyagcsereTipus").hide();
						for (var i = 0; i < result.consultations.length; i++) {
							btnConsultations.prepend($("<div class='specCons" + (result.consultations[i]["ID"] == fid ? " selSpecCons" : "") + "' data-consid='" + result.consultations[i]["ID"] + "'>" + result.consultations[i]["CrDate"] + "</div>"));
						}
						var answers = _.sortBy(result.answers, "groupId");
						var groupName = null;
						var myTable = $("<table style='width:100%'></table>");
						var rowCache = [];
						var hasRow = false;
						var myRow, myCell;
						for (var i = 0; i < answers.length; i++) {
							var group = answers[i];
							if (group.groupName != groupName) {
								if (hasRow) {
									for (var j = 0; j < rowCache.length; j++) {
										myTable.append(rowCache[j]);
									}
								}
								rowCache = [];
								hasRow = false;
								myRow = $("<tr></tr>");
								myCell = $("<td colspan='2'></td>");

								myCell.append($("<br>"));
								myRow.append(myCell);

								rowCache.push(myRow);

								myRow = $("<tr></tr>");
								myCell = $("<td colspan=2></td>");

								myCell.append($("<div class='groupName'>" + htmlEncode(group.groupName) + "</div>"));
								myRow.append(myCell);

								rowCache.push(myRow);

								groupName = group.groupName;
							}

							if (group.answer.trim() != "Nincs egyik sem") {
								hasRow = true;

								var _class = "answered";
								if (group.isHighlighted == 1) {
									_class += " highlighted";
								}

								myRow = $("<tr class='" + _class + "' data-answer-id='" + group.answerId + "'></tr>");
								myCell = $("<td style='vertical-align:top'></td>");

								myCell.append($("<div class='question'>" + htmlEncode(group.question) + "</div>"));
								myRow.append(myCell);

								myCell = $("<td style='vertical-align:top'></td>");

								var flexCont = $("<div class='flexCont'></div>");
								flexCont.append($("<div class='answer'>" + htmlEncode(group.answer).replace(/(?:\r\n|\r|\n)/g, '<br>').replace("é1", "<span class='otherAnswer'>").replace("é2", "</span>") + "</div>"));
								flexCont.append($("<div class='copyPaste'></div>"));
								myCell.append(flexCont);
								myRow.append(myCell);

								rowCache.push(myRow);
							}
						}
						if (hasRow) {
							for (var j = 0; j < rowCache.length; j++) {
								myTable.append(rowCache[j]);
							}
						}
						if (answers.length === 0) {
							myRow = $("<tr></tr>");
							myCell = $("<td></td>");

							myCell.append($("<div class='noanswers'>Kérdõív nélküli konzultáció.</div>"));
							myRow.append(myCell);
							myTable.append(myRow);
						}

						$("#divQuestFill").append(myTable);
					}
				} else {
					$("#divQuestFill").html("");
					$("#anyagcsereTipus").hide();
				}
			});
		}

		function htmlEncode(value) {
			return $('<div/>').text(value).html().replace(/&/g, '%26');
		}

		function htmlDecode(input) {
			var doc = new DOMParser().parseFromString(input, "text/html");
			return doc.documentElement.textContent;
		}
	</script>
</head>

<body>
	<?php include("adminmenu.php") ?>

	<table id="tblMain" cellspacing="0" cellpadding="0">
		<colgroup>
			<col style="width:400px">
			<col>
		</colgroup>
		<tr>
			<td style="vertical-align:top;height:100%;">
				<div id="divUserList" style="height:100%;overflow:auto;">
					<?php if (!$isTest) { ?>
						<div>
							<input type="text" id="txtSearch">
							<input type="button" id="btnSearch" value="Szûr" style="display:none">
							<input type="button" id="btnAddOrphanUser" class="ujButton" value="Új">
						</div>
					<?php } else { ?>
						<div class="Felvesz">
							<input type="text" id="txtAddTestUser">
							<input type="hidden" id="txtConsultantId" value="<?php echo $userObject['ID']; ?>">
							<input type="button" id="btnAddTestUser" value="Felvesz">
						</div>
					<?php } ?>
					<?php
					foreach ($questUsers as $user) {
						$datum = substr($user["crdti"], 0, 10);
						if (!$isTest) {
							print "<div class='kitoltes' data-myid='${user["ID"]}'><span class='fillDate'>$datum</span> ${user["userName"]}<div class='note3 hdn'>é-lista</div><div class='note22 hdn'>Ajánlás2</div><div class='note2 hdn'>Ajánlás</div><div class='note1 hdn'>Jegyzet</div><div class='addConsultation hdn'>+</div><span class='btnConsultations'></span></div>";
						} else {
							print "<div class='kitoltes' data-myid='${user["ID"]}'><b>$datum</b> ${user["userName"]}</div>";
						}
					}
					?>
				</div>
			</td>
			<td style="vertical-align:top;height:100%;display:flex">
				<div id="divQuestFill"></div>
				<div id="divNote1" class="divNote" style="display:none"><textarea id="txtNote1" class="txtNote" style="width:100%;height:100%;"></textarea></div>
				<div id="divNote2" class="divNote" style="display:none">
					<div class='generateButtonOld' id="divGenRecom"><input type="button" id="btnGenRecom" value="AJÁNLÁS GENERÁLÁSA"></div>
					<div id="flexCont2">
						<div id="txtNote1Cont2" class="flexChild1"></div>
						<div class="divInnerNode flexChild1"><span class="spnLabel">MEGOLDANDÓK</span><br><br>
							<div id="divRootCauses"></div>
						</div>
					</div>
					<!-- <div class="divInnerNode"><span class="spnLabel">A MEGOLDéS LéPéSEI</span><br><br><div id="divSolutionSteps"></div></div>
					<div class="divInnerNode"><span class="spnLabel">MIBéL FOGYASSZ TéBBET</span><br><br><div id="divEatMore"></div></div> -->
					<div class="divInnerNode"><span class="spnLabel">JAVASLATOK</span><br><br><textarea id="txtNote21" class="txtNote2" style="width:100%;height:70vh"></textarea></div>
				</div>
				<div id="divNote22" class="divNote" style="display:none">
					<div class='page1'>
						<div class='jegyzet' id="txtNote1Cont22">
						</div>
						<div>
							<div class='container target' id='nowProtocols'>
								<span>Kezelendõ½</span>
							</div>
							<div class='container target' id='laterProtocols'>
								<span>Kivizsgálandó½</span>
							</div>
							<div class='saveDiv'>
								<input type="button" class="saveButton" value='KéSZ'>
							</div>
						</div>
						<div id='list' class='container'></div>
					</div>
					<div class='page1_overlay'></div>
					<div>
						<hr>
					</div>
					<div class='page2'>
						<div>
							<div class='lowerBoxTitle'>Késõbbi lépések listája</div>
							<div class='bottomDivs container target' id='recommLaterProtocolSteps'></div>
						</div>
						<div>
							<div class='lowerBoxTitle'>További táplálkozási és életmód ajánlások</div>
							<div class='bottomDivs container target' id='recommNowProtocolSteps'></div>
						</div>
						<div>
							<div class='lowerBoxTitle'>Javasolt étrend-kiegészítõk</div>
							<div class='bottomDivs container target' id='suppProtocolSteps'></div>
						</div>
					</div>
					<div class='generateDiv'>
						<input type="button" id="btnGenRecom22" class="generateButton" value='VéGLEGESéTéS éS AJéNLéS GENERéLéS'>
					</div>
				</div>
				<div id="divNote3" class="divNote" style="display:none">
					<div class='foodListGeneration' id="divGenRecom">
						<input type="button" id="btnGenFoodRecom" value="MENTÉS ÉS GENERÁLÁS">
						<input type="button" id="btnGenFoodRecom2" value="é">
						<input type="radio" id="magyarBtn" name="nyelvValtas" checked><label for="magyar">magyar</label>
						<input type="radio" id="nemetBtn" name="nyelvValtas"><label for="nemet">német</label>
						<input type="radio" id="angolBtn" name="nyelvValtas"><label for="angol">angol</label>
					</div>
					<div id="divOverwrite" style="display:flex; margin-top: 3px; margin-bottom: 5px; margin-left:5px;">
						<div><label for="overwrite">Mégis kell: </label><input type="text" id="overwrite"></div>
						<div id="divOwTags" style="display:grid;margin-left:10px;max-width: calc(100% - 250px);"></div>
					</div>
					<table class='tblNote3'>
						<tr>
							<th colspan="3">KIZÁRÁSOK</th>
							<th rowspan="2">KIEMELÉSEK</th>
						</tr>
						<tr class="alcim">
							<th>Élelmiszer csoportok</th>
							<th>Egyéni intoleranciák</th>
							<th>Ízlés</th>
						</tr>
						<tr>
							<td>
								<div class="divInnerNode"><input type="checkbox" id="cbIsGlutenmentes" value="1"> Gluténtartalmú ételek</div>
								<div class="divInnerNode"><input type="checkbox" id="cbIsCsucsorfele" value="1"> Burgonyafélék</div>
								<div class="divInnerNode"><input type="checkbox" id="cbIsOlajosMag" value="1"> Olajos magvak</div>
								<div class="divInnerNode"><input type="checkbox" id="cbIsMagasHisztamin" value="1"> Hisztamin tartalmú és felszabadété ételek</div>
								<div class="divInnerNode"><input type="checkbox" id="cbIsMagasGI" value="1"> Magas GI</div>
								<div class="divInnerNode"><input type="checkbox" id="cbIsMagasFruktoz" value="1"> Magas fruktóz</div>
								<div class="divInnerNode"><input type="checkbox" id="cbIsMagasFodmap" value="1"> Magas fodmap</div>
								<div class="divInnerNode"><input type="checkbox" id="cbIsKozepesFodmap" value="1"> Közepes fodmap</div>
								<div class="divInnerNode"><input type="checkbox" id="cbIsTejKereszt" value="1"> Tej keresztallergének</div>
								<div class="divInnerNode"><input type="checkbox" id="cbIsHuvelyesek" value="1"> Hüvelyesek</div>
								<div class="divInnerNode"><input type="checkbox" id="cbIsTejtermekek" value="1"> Tejtermékek</div>
								<div class="divInnerNode"><input type="checkbox" id="cbIsHusok" value="1"> Húsok, húskészítmények, belséségek</div>
								<div class="divInnerNode"><input type="checkbox" id="cbIsTojas" value="1"> Tojás, tojáskészétmény</div>
								<div class="divInnerNode"><input type="checkbox" id="cbIsRefluxTr" value="1"> Reflux triggerek</div>
								<div class="divInnerNode"><input type="checkbox" id="cbIsLektin" value="1"> Lektin tartalmú</div>
							</td>
							<td id='tdIntol'></td>
							<td id='tdIzles'></td>
							<td id='tdKiemeles'></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
	<div id="divUserPlate"></div>
	<div id="msg" style="display:none"></div>
</body>

</html>