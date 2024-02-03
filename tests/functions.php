<?php
//php7 hiányzó funkciói
    include('./php7/mysql_replacement.php');
    include('./php7/ereg-functions.php');


error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

mysql_connect ('mysql.luciendelmar.com','luciendelmar','9CUiNwYzV3');
mysql_select_db('luciendelmar');


mysql_query("SET NAMES `latin2`");

$defaultLang = "hun";
//$defaultLang = "eng";


if(!$_SESSION['language']){
    if($_COOKIE['preflanguage']){
        $_SESSION['language'] = $_COOKIE['preflanguage'];
    }
    else{
        $_SESSION['language'] = $defaultLang;
    }
}
if(!$_SESSION['varos']){
    $_SESSION['varos'] = 'budapest';
}

if($_SESSION['language'] == 'hun' and file_exists('translations_HUN.php')){
    include_once('translations_HUN.php');
}
else if($_SESSION['language'] == 'ger' and file_exists('translations_GER.php')){
    include_once('translations_GER.php');
}
else if($_SESSION['language'] == 'rus' and file_exists('translations_GER.php')){
    include_once('translations_RUS.php');
}
else if($_SESSION['language'] == 'gr' and file_exists('translations_GR.php')){
    ob_start();
    include_once('translations_GR.php');
    ob_end_clean();
}
else if($_SESSION['language'] == 'si' and file_exists('translations_SI.php')){
    ob_start();
    include_once('translations_SI.php');
    ob_end_clean();
}
else if($_SESSION['language'] == 'slo' and file_exists('translations_SLO.php')){
    ob_start();
    include_once('translations_SLO.php');
    ob_end_clean();
}
else if($_SESSION['language'] == 'sp' and file_exists('translations_SP.php')){
    ob_start();
    include_once('translations_SP.php');
    ob_end_clean();
}
else if($_SESSION['language'] == 'cro' and file_exists('translations_CRO.php')){
    ob_start();
    include_once('translations_CRO.php');
    ob_end_clean();
}
else if($_SESSION['language'] == 'it' and file_exists('translations_IT.php')){
    ob_start();
    include_once('translations_IT.php');
    ob_end_clean();
}
else if(file_exists('translations_ENG.php')){
    include_once('translations_ENG.php');
}

if($_SESSION['language'] == 'gr'){
    $CHARSET = "UTF-8";
}
else if($_SESSION['language'] == 'si'){
    $CHARSET = "windows-1250";
}
else{
    $CHARSET = "ISO-8859-2";
}

$MAIN_PAGE = "flowinfo.php";

function DEBUG($variable)
{
	print_r("<pre>");
	print_r($variable);
	print_r("</pre>");
}

function setAsRegisteredUser($userId)
{
	$query = "INSERT INTO `LiveUser` (`UserID`) VALUES (".$userId.")";
	$result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
}


function saveCardOrder($CardNum,$Name,$Address,$Email)
{
	$query = "INSERT INTO `luciendelmar`.`cardsOrder` (`ID` ,`CardNumber` ,`Name` ,`Address` ,`Email`,`Type`) ";
	$query .= "VALUES (NULL , '$CardNum', '$Name', '$Address', '$Email','card');";

	$result = mysql_query($query);
	if(!$result){
		print mysql_error();
		return false;
	}
	else
	{
		sendOrderToLucien($CardNum,$Name,$Address,$Email);
		sendLetterToBuyer($CardNum,$Name,$Address,$Email);
	}
}

 
function sendLetterToBuyer($CardNum,$Name,$Address,$Email)
{
	$price = 6500;
	$afa = 0;
	$sumprice = $price*$CardNum + ($price*$CardNum*$afa);
	$to = $Email;
    $subject = "Kártya megrendelés adatai:";
    $fromName = 'Lucien del Mar';
    $fromEmail = "hello@luciendelmar.com";
    $body .= "<html><body>";
	$body .= "<p>Megrendeles:</p>";
    $body .= "Kártya db: ".$CardNum."<br>";
	$body .= "Megrendelõ neve: ".$Name."<br>";
	$body .= "Cím : ".$Address."<br>";
	$body .= "e-mail: ".$Email."<br>";
	//$body .= "Honnan értesültél a könyvrol: ".$Ertesul."<br>";
	$body .= "<br>";
	$body .= "<p>Fizetendõ:</p>";
	$body .= "Összesen: ".$sumprice." Ft <br>";
	$body .= "</body></html>";
	
	
	endiMail($to, $subject, $body, $fromName, $fromEmail);
 }

function getUserResponse($UserID)
{
	$query = "SELECT * FROM user_response WHERE userID=".$UserID;
	$result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $UserRecomendations = array();
    while($row = mysql_fetch_assoc($result)) 
	{
            $UserRecomendations[] = $row;
    }
	
	return $UserRecomendations;
}


function getUserResponsefromBook($UserID,$BookID)
{
	$query = "SELECT * FROM user_response WHERE userID=".$UserID." and BookID =".$BookID;
	$result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $UserRecomendations = array();
    while($row = mysql_fetch_assoc($result)) 
	{
            $UserRecomendations[] = $row;
    }
	
	return $UserRecomendations;
}

function saveUserResponseByBookId($UserID,$BookID,$Response)
{
	$row = getUserResponsefromBook($UserID,$BookID);
	if($row)
	{
		$query = "UPDATE user_response set userResponse = '".$Response."' where userID=".$UserID." and BookID = ".$BookID;
		
		$result = mysql_query($query);
		if(!$result){
			print mysql_error();
			return false;
		}
		
	}
	else
	{
		$query = "INSERT INTO user_response (ID ,BookID ,userID ,userResponse) ";
		$query .= "VALUES (NULL , '$BookID', '$UserID', '$Response');";
		
		$result = mysql_query($query);
		if(!$result){
			print mysql_error();
			return false;
		}
		
	}
}

function getUserRecomendation($UserID)
{
	$query = "SELECT * FROM `jelentkezok_recomendation` WHERE UserID=".$UserID;
	$result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $UserRecomendations = array();
    while($row = mysql_fetch_assoc($result)) 
	{
            $UserRecomendations[] = $row;
    }
	
	return $UserRecomendations;
}

function getUserExercises($userID)
{

	$query = "SELECT b.* , r_ex.exercise_id FROM ruin_exercises r_ex, book b, jelentkezok_solution js WHERE r_ex.ruin_id = js.user_solution AND js.user_id =".$userID." AND b.ID = r_ex.exercise_id ORDER BY b.order asc";
	$result = mysql_query($query);
    if(!$result){
  
		print mysql_error();
		return false;
    }
	while($row = mysql_fetch_assoc($result)) {
        $Exercises[] = $row;
    }
    return $Exercises;
}

function findById($ID,$ary)
{
	for($i=0;$i<count($ary);$i++)
	{
		if($ary[$i]['id']== $ID)
		{
			
			$sub = $ary[$i];
			return $sub;
		}
	}
	
	return null;
} 

function updateUserParams($param,$userID,$answer,$isInteger)
{
	if($isInteger==false)
	{
		$query = "update jelentkezok set ".$param." = '$answer' where ID = " . (int)$userID;
		$result = mysql_query($query);
		if(!$result){
			print mysql_error();
			return false;
		}
	}
	else
	{
		$query = "update jelentkezok set ".$param." = ".$answer." where ID = " . (int)$userID;
		$result = mysql_query($query);
		if(!$result){
			print mysql_error();
			return false;
		}
	}
}

function getQuestionaryAnswer($QuestionID,$userID)
{
	$query = "SELECT * FROM Questionarie_user_answer where userID =".$userID." and QuestionID =".$QuestionID.";";
    $result = mysql_query($query);
    $row = mysql_fetch_assoc($result);
    return $row;
}

function getQuestionaryAnswerWhitoutUserID($QuestionID)
{
	$query = "SELECT * FROM Questionarie_user_answer where QuestionID =".$QuestionID.";";
    $result = mysql_query($query);
    $row = mysql_fetch_assoc($result);
    return $row;
}
/*
function saveQuestionaryAnswer($QuestionarieID,$QuestionID,$userID,$answer)
{
    $query = getQuestionaryAnswerQuery($QuestionarieID,$QuestionID,$userID,$answer);

	$result = mysql_query($query);
	if(!$result){
		print mysql_error();
		return false;
	}
}
*/
function getQuestionaryAnswerQuery($QuestionarieID,$QuestionID,$userID,$answer)
{
    $answer = str_replace("'", "''", $answer);
    $query = "INSERT INTO `luciendelmar`.`Questionarie_user_answer` (`ID`,`QuestionarieID`,`QuestionID`,`userID`,`answer`,`Questionarie_FillsID`) ";
    $query .= " VALUES (NULL , '$QuestionarieID', '$QuestionID', '$userID', '$answer', ßplaceholderß);";
	return $query;
}

function saveQuestFill($userName, $questionarieId, $consultantId){
    $userName = str_replace("'", "''", $userName);
    $sql = "insert into Questionarie_Fills (userName, QuestionarieID, ConsultantId) values ('$userName', $questionarieId, $consultantId)";
	$result = mysql_query($sql);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $sql);
    }
    return mysql_insert_id();
}

function countEmailLinkClick($campainID)
{
	$query = "Update email_click_stat set count = count + 1 where ID = ".$campainID;
	$result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
}

function getQuestionarieGroup($Questionarie)
{
	$query = "select qGroup.* From Question_Group qGroup, Questionarie_List qList where qGroup.ID = qList.GroupID and qList.QuestionarieID = ".$Questionarie." order by qList.order asc";
	
	$result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $QuestionarieGroup = array();
    while($row = mysql_fetch_assoc($result)) 
	{
            $QuestionarieGroup[] = $row;
    }
	
	return $QuestionarieGroup;
}

function GetQuestionsByGroup($GroupId)
{
	$query = "select q.* from Question q,Questions_by_group QbyG where QbyG.GroupID = ".$GroupId." and q.ID = QbyG.QuestionID order by QbyG.ID";
	
	$result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $resultArray = array();
    while($row = mysql_fetch_assoc($result)) 
	{
            $resultArray[] = $row;
    }
	
	return $resultArray;
}


function deleteUserByEmail($email)
{
    $query = "select id from jelentkezok where email = '$email'";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        return false;
    }
    $ids = array();
    while($row = mysql_fetch_row($result)) {
        $ids[] = $row[0];
    }
    if(count($ids) === 0){
        return 0;
    }
    $query = "delete from jelentkezok_kedvenc where jelentkezok_id in (" . implode(",", $ids) . ")";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        return false;
    }

    $query = "delete from user_ruins where user_id in (" . implode(",", $ids) . ")";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        return false;
    }

    $query = "delete from jelentkezok where id in (" . implode(",", $ids) . ")";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        return false;
    }
    return mysql_affected_rows();
}

function getAllRuins()
{
    $query = "SELECT ID as ruin_ID, ruin, category FROM ruin order by ruin";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $resultArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $resultArray[] = $row;
    }
    return $resultArray;
}

function getAllRuinsWithExercises()
{
    $query = "SELECT r.id AS ruin_ID, r.ruin, r.category, re.exercise_id
                FROM ruin r
                left outer join ruin_exercises re on r.id = re.ruin_id
                order by ruin";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $resultArray = array();
    $categoryArray = array();
    while($row = mysql_fetch_assoc($result)) {
        /* minden idézetet csak egyszer adunk hozzá az eredményhez */
        if(!is_array($categoryArray[$row['ruin_ID']])){
            $resultArray[] = $row;
        }
        if($row['exercise_id'] > 0){
            $categoryArray[$row['ruin_ID']][] = $row['exercise_id'];
        }
    }
    for($i = 0; $i < count($resultArray); $i++){
        unset($resultArray[$i]['exercise_id']);
        $resultArray[$i]['exercises'] = $categoryArray[$resultArray[$i]['ruin_ID']];
    }
    return $resultArray;
}

function getRuinUserNumbers()
{
    $query = "SELECT r.id, count(*) as num
                FROM ruin r
                inner join user_ruins ur on r.id = ur.ruin_id
                group by r.id";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $resultArray = array();
    while($row = mysql_fetch_row($result)) {
        $resultArray[$row[0]] = $row[1];
    }
    return $resultArray;
}

function getRuinCategories()
{
    $query = "SELECT id, name FROM ruin_categories order by ord";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $resultArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $resultArray[] = $row;
    }
    return $resultArray;
}

function getRuinById($id)
{
    $query = "SELECT category, ruin FROM ruin where ID = " . (int)$id;

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $resultArray = array();
    $ruin = array();
    while($row = mysql_fetch_assoc($result)) {
        $ruin = $row;
        //$ruin['categoryName'] = getCategoryName($row['category']);
    }
    return $ruin;
}

function getRuinsByCategory($id)
{
    $query = "SELECT r.category, r.ruin, c.name as categoryName, r.id
                FROM ruin r
                inner join ruin_categories c on r.category = c.id
                where category = " . (int)$id;

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $resultArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $resultArray[] = $row;
    }
    return $resultArray;
}

function getSubChaptersByRuins($selectedRuins)
{
    if(!is_array($selectedRuins)){
        return false;
    }
    $selectedRuins[] = 0;
    $query = "SELECT r.ruin, b.ID as subChapterId, b.name as subChapterName, b.done as subChapterDone, b.updti as subChapterUpdti
                FROM ruin r
                inner join ruins_subchapters rs on r.ID = rs.ruin_ID
                inner join book b on b.ID = rs.subchapter_ID
                where r.ID in (" . implode(',', $selectedRuins) . ")";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $resultArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $resultArray[] = $row;
    }
    return $resultArray;
}

function getAllSubChapters()
{
    $query = "SELECT b1.name as chapterName, b2.name as subChapterName, b1.ID as chapterID, b2.ID as subChapterID, b2.done, b2.updti, b2.concept
                FROM book b1
                left outer join book b2 on b2.ref_ID = b1.ID
                where b1.ref_ID is null
                order by b1.order, b1.name, b2.order, b2.name";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $resultArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $resultArray[] = $row;
    }
    return $resultArray;
}

function getAllSubChaptersSpecial1()
{
    $query = "SELECT name as subChapterName, ID as subChapterID, done, updti, concept
                FROM book
                where ref_ID is not null and done in (2, 3)
                order by done desc, ltrim(name)";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $resultArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $resultArray[] = $row;
    }
    return $resultArray;
}

function getRandomSubChapterSpecial()
{
    $query = "SELECT alchapters.id, alchapters.name, alchapters.concept
                FROM book alchapters
                inner join book chapters on alchapters.ref_id = chapters.id
                where chapters.done = 90
                order by rand()";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    return mysql_fetch_assoc($result);
}

function getAllSuccessStories()
{
    $query = "SELECT b.ID as subChapterId, s.ID as storyId, s.story, s.story_name, s.CRDTI
                FROM success_stories s
                left outer join book b on s.book_ID = b.ID
                order by b.ID, s.CRDTI desc";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $resultArray = array();
    while($row = mysql_fetch_assoc($result)) {
        list($row['CRDTI']) = explode(' ', $row['CRDTI']);
        $resultArray[$row['subChapterId']][] = $row;
    }
    return $resultArray;
}

function getAllSuccessStoriesByDate()
{
    $query = "SELECT b.ID as subChapterId, s.ID as storyId, s.story, s.story_name, s.CRDTI
                FROM success_stories s
                left outer join book b on s.book_ID = b.ID
                order by s.CRDTI desc";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $resultArray = array();
    while($row = mysql_fetch_assoc($result)) {
        list($row['CRDTI']) = explode(' ', $row['CRDTI']);
        $resultArray[] = $row;
    }
    return $resultArray;
}

function getAllIdezetByDate($onlyLucien = false)
{
    if($onlyLucien){
        $addition = " and lcase(i.szerzo) = 'lucien del mar' ";
    }
    $query = "SELECT i.ID as idezetId, i.idezet, i.idezet_eng, i.idezet_ger, i.idezet_gre, i.szerzo, i.kikuldve, ic.category_id, i.datum, count(f.id) as favouriteNumber
                FROM idezet i
                left outer join idezet_categories ic on i.id = ic.idezet_id
                left outer join jelentkezok_kedvenc f on f.table_name = 'idezet' and f.table_id = i.id
                where 1 = 1 $addition
                group by  i.ID, i.idezet, i.idezet_eng, i.idezet_ger, i.idezet_gre, i.szerzo, i.kikuldve, ic.category_id, i.datum
                order by kikuldve asc, datum desc";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $resultArray = array();
    $categoryArray = array();
    while($row = mysql_fetch_assoc($result)) {
        /* minden idézetet csak egyszer adunk hozzá az eredményhez */
        if(!is_array($categoryArray[$row['idezetId']])){
            list($row['datum']) = explode(' ', $row['datum']);
            $resultArray[] = $row;
        }
        if($row['category_id'] > 0){
            $categoryArray[$row['idezetId']][] = $row['category_id'];
        }
    }
    for($i = 0; $i < count($resultArray); $i++){
        unset($resultArray[$i]['category_id']);
        $resultArray[$i]['categories'] = $categoryArray[$resultArray[$i]['idezetId']];
    }

    return $resultArray;
}

function getRandomLucienQuote()
{
    global $_SESSION;

    if(!$GLOBALS['userObject']['ID']){
        $userId = 0;
    }
    else{
        $userId = $GLOBALS['userObject']['ID'];
    }

    if($_SESSION['language'] == 'eng' || $_SESSION['language'] == 'gr'){
        $idezet = 'i.idezet_eng';
        $title = '0';
        $query_add = " and i.idezet_eng is not null and i.idezet_eng != ''";
    }
    else{
        $title = 'i.quote_title';
        $idezet = 'i.idezet';
    }
    $query = "select i.ID as idezetId, i.idezet, $idezet as idezet, i.datum, i.szerzo, i.kikuldve, k.id as kedvencek_id, $title
                from idezet i
                left outer join jelentkezok_kedvenc k on k.jelentkezok_id = " . $userId . " and k.table_name = 'idezet' and k.table_id = i.id
                where lcase(i.szerzo) = 'lucien del mar'" . $query_add . "
                order by rand()";
    $result = mysql_query($query);
    $row = mysql_fetch_assoc($result);
    return $row;
}

function getAllFreshThings($filterValue)
{
    global $_SESSION;

    if(!$GLOBALS['userObject']['ID']){
        $userId = 0;
    }
    else{
        $userId = $GLOBALS['userObject']['ID'];
    }
    
    if(is_null($filterValue)){
        $filterValue = '';
    }

    $nameCol = 'story_name';
    $storyCol = 'story';
    if($_SESSION['language'] == 'eng'){
        $nameCol .= '_eng';
        $storyCol .= '_eng';
    }
    else if($_SESSION['language'] == 'gr'){
        $nameCol .= '_gre';
        $storyCol .= '_gre';
    }

    $filterValue = str_replace("'", "''", $filterValue);

    if(strlen($filterValue) > 0){
        $filterAdd = " and lcase(s.{$storyCol}) like lcase('%{$filterValue}%') ";
    }

    $query = "SELECT s.ID as ID, s.{$nameCol} as DISPLAY_TEXT, s.CRDTI as DATUM, s.{$storyCol} as EXT_TEXT
                FROM success_stories s
                left outer join book b on s.book_ID = b.ID
                where 1=1 $filterAdd
                order by s.CRDTI desc";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $resultArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $row['EXT_TEXT'] = str_replace(chr(13) . chr(10), "<br>", $row['EXT_TEXT']);
        list($row['DATUM']) = explode(' ', $row['DATUM']);
        $resultArray[$row['DATUM']]['successStory'][] = $row;
        $countArray['successStory']++;
    }

    if(strlen($filterValue) > 0){
        $filterAdd = " and lcase(concept) like lcase('%{$filterValue}%') ";
    }
    $query = "SELECT name as DISPLAY_TEXT, ID, coalesce(updti, crdti) as DATUM, concept as EXT_TEXT
                FROM book
                where ref_ID is not null and done = 3 $filterAdd";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    while($row = mysql_fetch_assoc($result)) {
        $row['EXT_TEXT'] = str_replace(chr(13) . chr(10), "<br>", $row['EXT_TEXT']);
        list($row['DATUM']) = explode(' ', $row['DATUM']);
        $resultArray[$row['DATUM']]['subChapter'][] = $row;
        $countArray['subChapter']++;
    }

    $nameCol = 'name';
    if($_SESSION['language'] == 'eng' || $_SESSION['language'] == 'si'){
        $nameCol .= '_eng';
    }
    else if($_SESSION['language'] == 'ger'){
        $nameCol .= '_ger';
    }

    if(strlen($filterValue) > 0){
        $filterAdd = " and 1 = 0 ";
    }
    $query = "SELECT $nameCol as DISPLAY_TEXT, ID, DATUM, LINK, type
                FROM linker where 1 = 1 $filterAdd";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    while($row = mysql_fetch_assoc($result)) {
        if(substr($row['LINK'], 0, 7) != 'http://'){
            $row['LINK'] = 'http://' . $row['LINK'];
        }
        list($row['DATUM']) = explode(' ', $row['DATUM']);
        if($row['type'] == '1'){
            $resultArray[$row['DATUM']]['letOrome'][] = $row;
            $countArray['letOrome']++;
        }
        else if($row['type'] == '2'){
            $resultArray[$row['DATUM']]['gondolatEbreszto'][] = $row;
            $countArray['gondolatEbreszto']++;
        }
    }

    $idezetCol = 'idezet';
    if($_SESSION['language'] == 'eng'){
        $idezetCol .= '_eng';
    }
    else if($_SESSION['language'] == 'ger'){
        $idezetCol .= '_ger';
    }
    else if($_SESSION['language'] == 'gr'){
        $idezetCol .= '_gre';
    }

    if(strlen($filterValue) > 0){
        $filterAdd = " and lcase($idezetCol) like lcase('%{$filterValue}%') ";
    }
    $query = "SELECT i.$idezetCol as DISPLAY_TEXT, i.ID, i.DATUM, k.id as kedvencek_id
                FROM idezet i
                left outer join jelentkezok_kedvenc k on k.jelentkezok_id = " . $userId . " and k.table_name = 'idezet' and k.table_id = i.id
                where i.kikuldve = 1 and lcase(i.szerzo) = 'lucien del mar' $filterAdd";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    while($row = mysql_fetch_assoc($result)) {
        list($row['DATUM']) = explode(' ', $row['DATUM']);
        $row['DISPLAY_TEXT'] = "\"{$row['DISPLAY_TEXT']}\"";
        $resultArray[$row['DATUM']]['lucienIdezet'][] = $row;
        $countArray['lucienIdezet']++;
    }

    if(strlen($filterValue) > 0){
        $filterAdd = " and lcase(i.$idezetCol) like lcase('%{$filterValue}%') ";
    }

    $query = "SELECT i.$idezetCol as DISPLAY_TEXT, i.ID, i.DATUM, i.szerzo as COMMENT, ic.category_id, k.id as kedvencek_id
                FROM idezet i
                left outer join idezet_categories ic on i.ID = ic.idezet_id
                left outer join jelentkezok_kedvenc k on k.jelentkezok_id = " . $userId . " and k.table_name = 'idezet' and k.table_id = i.id
                where i.kikuldve = 1 and i.$idezetCol is not null and i.$idezetCol != '' $filterAdd order by i.datum desc";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $categoryArray = array();
    while($row = mysql_fetch_assoc($result)) {
        /* minden idézetet csak egyszer adunk hozzá az eredményhez */
        if(!is_array($categoryArray[$row['ID']])){
            list($row['DATUM']) = explode(' ', $row['DATUM']);
            $resultArray[$row['DATUM']]['idezetek_time'][] = $row;
            $countArray['idezetek']++;
        }
        if($row['category_id'] > 0){
            $categoryArray[$row['ID']][] = $row['category_id'];
        }
    }
    foreach($resultArray as $datum => $value){
        for($i = 0; $i < count($value['idezetek_time']); $i++){
            unset($resultArray[$datum]['idezetek_time'][$i]['category_id']);
            $resultArray[$datum]['idezetek_time'][$i]['categories'] = $categoryArray[$value['idezetek_time'][$i]['ID']];
        }
    }
    krsort($resultArray);

    if(strlen($filterValue) > 0){
        $filterAdd = " and lcase($idezetCol) like lcase('%{$filterValue}%') ";
    }
    $query = "SELECT i.$idezetCol as DISPLAY_TEXT, i.ID, i.SZERZO as DATUM, ic.category_id, k.id as kedvencek_id
                FROM idezet i
                left outer join idezet_categories ic on i.ID = ic.idezet_id
                left outer join jelentkezok_kedvenc k on k.jelentkezok_id = " . $userId . " and k.table_name = 'idezet' and k.table_id = i.id
                where i.kikuldve = 1 and i.$idezetCol is not null and i.$idezetCol != '' $filterAdd order by i.szerzo";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $categoryArray = array();
    while($row = mysql_fetch_assoc($result)) {
        /* minden idézetet csak egyszer adunk hozzá az eredményhez */
        if(!is_array($categoryArray[$row['ID']])){
            $resultArray[$row['DATUM']]['idezetek_author'][] = $row;
        }
        if($row['category_id'] > 0){
            $categoryArray[$row['ID']][] = $row['category_id'];
        }
    }
    foreach($resultArray as $datum => $value){
        for($i = 0; $i < count($value['idezetek_author']); $i++){
            unset($resultArray[$datum]['idezetek_author'][$i]['category_id']);
            $resultArray[$datum]['idezetek_author'][$i]['categories'] = $categoryArray[$value['idezetek_author'][$i]['ID']];
        }
    }

    $query = "SELECT count(*) as num FROM jelentkezok_kedvenc k
                where k.jelentkezok_id = " . $userId . " and k.table_name = 'idezet'";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    while($row = mysql_fetch_row($result)) {
        $countArray['favourites'] = $row[0];
    }
    return array($resultArray, $countArray);
}

function getSongs()
{
    $nameCol = 'name';
    if($_SESSION['language'] == 'eng' || $_SESSION['language'] == 'si'){
        $nameCol .= '_eng';
    }
    else if($_SESSION['language'] == 'ger'){
        $nameCol .= '_ger';
    }

    $query = "SELECT $nameCol as DISPLAY_TEXT, ID, DATUM, LINK, type
                FROM linker where type = 1 order by DATUM desc";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    while($row = mysql_fetch_assoc($result)) {
        if(substr($row['LINK'], 0, 7) != 'http://'){
            $row['LINK'] = 'http://' . $row['LINK'];
        }
        list($row['DATUM']) = explode(' ', $row['DATUM']);
        $resultArray[$row['DATUM']]['letOrome'][] = $row;
    }
    return $resultArray;
}

function getLinker($id)
{
    $nameCol = 'name';
    if($_SESSION['language'] == 'eng' || $_SESSION['language'] == 'si'){
        $nameCol .= '_eng';
    }
    else if($_SESSION['language'] == 'ger'){
        $nameCol .= '_ger';
    }

    $query = "SELECT $nameCol as DISPLAY_TEXT, ID, DATUM, LINK, type
                FROM linker where ID = " . (int)$id;

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    if($row = mysql_fetch_assoc($result)) {
        if(substr($row['LINK'], 0, 7) != 'http://'){
            $row['LINK'] = 'http://' . $row['LINK'];
        }
        list($row['DATUM']) = explode(' ', $row['DATUM']);
    }
    return $row;
}


function getLastSong()
{
    $nameCol = 'name';
    if($_SESSION['language'] == 'eng'){
        $nameCol .= '_eng';
    }
    else if($_SESSION['language'] == 'ger'){
        $nameCol .= '_ger';
    }
    $query = "SELECT $nameCol as DISPLAY_TEXT, ID, DATUM, LINK, type
                FROM linker where type = 1 order by datum desc";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $resultArray = array();
    if($row = mysql_fetch_assoc($result)) {
        if(substr($row['LINK'], 0, 7) != 'http://'){
            $row['LINK'] = 'http://' . $row['LINK'];
        }
        list($row['DATUM']) = explode(' ', $row['DATUM']);
        $resultArray = $row;
    }
    return $resultArray;
}

function getCurrentLearningQuestion($question_id)
{
    $question_id = (int)$question_id;
    $query = "select b1.ID, b1.name, b1.name_eng, b1.name_si, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng
                    , b2.name_si as sub_name_si, b2.concept, b2.concept_eng, b2.concept_si
                    , b2.exercise, b2.exercise_eng, b2.exercise_si, b2.done as subDone, b2.imgName
                from book b1
                inner join book b2 on b1.ID = b2.ref_id
                where b2.ID = $question_id";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $return = array();
    while($row = mysql_fetch_assoc($result)) {
        $return = $row;
    }

    $query = "select from_id, to_id from question_relations where from_id = $question_id or to_id = $question_id";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $return['exercises'] = array();
    while($row = mysql_fetch_row($result)) {
        if($row[0] != $question_id and !in_array($row[0], $return['exercises'])){
            $return['exercises'][] = $row[0];
        }
        if($row[1] != $question_id and !in_array($row[1], $return['exercises'])){
            $return['exercises'][] = $row[1];
        }
    }
    return $return;
}

function getCurrentStory($story_ID)
{
    $query = "SELECT b.ID as subChapterId, b.name as subChapterName, s.ID as storyId, s.story, s.story_eng, s.story_gre, s.CRDTI, s.story_name,
                    s.story_name_eng, s.story_name_gre, s.stay_category, b.done, e.exercise_id
                FROM success_stories s
                left outer join book b on s.book_ID = b.ID
                left outer join success_story_exercises e on s.id = e.success_stories_id
                where s.ID = " . (int)$story_ID . "
                order by b.ID, s.CRDTI desc";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $resultArray = array();
    $exerciseArray = array();
    while($row = mysql_fetch_assoc($result)) {
        list($row['CRDTI']) = explode(' ', $row['CRDTI']);
        /* minden sikertörténetet csak egyszer adunk hozzá az eredményhez */
        if(!is_array($exerciseArray[$row['storyId']])){
            $resultArray = $row;
        }
        if($row['exercise_id'] > 0){
            $exerciseArray[$row['storyId']][] = $row['exercise_id'];
        }
    }
    unset($resultArray['exercise_id']);
    $resultArray['exercises'] = $exerciseArray[$resultArray['storyId']];

    return $resultArray;
}

function getCurrentIdezet($ID)
{
    $query = "SELECT i.ID as idezetId, i.idezet, i.idezet_eng, i.idezet_ger, i.idezet_gre, i.szerzo, i.szerzo_eng, i.szerzo_ger, i.szerzo_gre, i.kikuldve, i.health,
                    i.datum, ic.category_id, i.quote_title, i.book_chapter
                FROM idezet i
                left outer join idezet_categories ic on i.ID = ic.idezet_id
                where i.ID = " . (int)$ID . "
                order by i.ID, i.datum desc";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $resultArray = array();
    $categoryArray = array();
    while($row = mysql_fetch_assoc($result)) {
        /* minden idézetet csak egyszer adunk hozzá az eredményhez */
        if(!is_array($categoryArray[$row['idezetId']])){
            list($row['datum']) = explode(' ', $row['datum']);
            $resultArray = $row;
        }
        if($row['category_id'] > 0){
            $categoryArray[$row['idezetId']][] = $row['category_id'];
        }
    }
    unset($resultArray['category_id']);
    $resultArray['categories'] = $categoryArray[$resultArray['idezetId']];

    return $resultArray;
}

function getCurrentRuin($ID)
{
    $query = "SELECT r.ID as ruinId, r.ruin, r.category, re.exercise_id
                FROM ruin r
                left outer join ruin_exercises re on r.ID = re.ruin_id
                where r.ID = " . (int)$ID . "
                order by r.ID";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $resultArray = array();
    $categoryArray = array();
    while($row = mysql_fetch_assoc($result)) {
        /* minden idézetet csak egyszer adunk hozzá az eredményhez */
        if(!is_array($categoryArray[$row['ruinId']])){
            $resultArray = $row;
        }
        if($row['exercise_id'] > 0){
            $categoryArray[$row['ruinId']][] = $row['exercise_id'];
        }
    }
    unset($resultArray['exercise_id']);
    $resultArray['exercises'] = $categoryArray[$resultArray['ruinId']];

    return $resultArray;
}

function getChapters()
{
    $query = "SELECT b1.ID, b1.name as chapterName, count(b2.ID) as subChaptersNumber, b1.done
                FROM book b1
                left outer join book b2 on b2.ref_ID = b1.ID
                where b1.ref_ID is null
                group by b1.ID, b1.name, b1.done
                order by b1.order";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $resultArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $resultArray[] = $row;
    }
    return $resultArray;
}

function getSubChaptersByChapterId($chapterId)
{
    $query = "SELECT b1.ID, b1.ref_ID, b1.name as subChapterName, b1.done, b1.order, count(f.id) as forumEntryNumber
                FROM book b1
                left outer join forum f on f.book_id = b1.id
                where b1.ref_ID = " . (int)$chapterId . "
                group by b1.ID, b1.ref_ID, b1.name, b1.done, b1.order
                order by b1.order";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $resultArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $resultArray[] = $row;
    }
    return $resultArray;
}

function getCurrentChapter($sourceId)
{
    $query = "SELECT ID, name, name_eng, name_gre, name_si, ref_ID, concept, concept_eng, concept_si, exercise, exercise_eng, exercise_si, done, `order`, crdti, updti
                FROM book
                where ID = " . (int)$sourceId;

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    while($row = mysql_fetch_assoc($result)) {
        $record = $row;
    }
    return $record;
}

function getCurrentSubChapter($sourceId)
{
    $query = "SELECT sc.ID as subChapterId, sc.name as subChapterName, sc.ref_ID as chapterId,
                    sc.exercise,
                    c.name as chapterName, sc.concept, sc.done, sc.order, sc.crdti, sc.updti
                FROM book sc
                inner join book c on sc.ref_ID = c.ID
                where sc.ID = " . (int)$sourceId;

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    while($row = mysql_fetch_assoc($result)) {
        $record = $row;
    }
    return $record;
}

function deb($array)
{
    print "<pre>";
    print_r($array);
    print "</pre>";
}

function updateChapter($storeArray)
{
    $fields['name'] = "'" . $storeArray['name'] . "'";
    $fields['name_eng'] = "'" . $storeArray['name_eng'] . "'";
    $fields['name_si'] = "'" . $storeArray['name_si'] . "'";
    $fields['name_gre'] = "'" . $storeArray['name_gre'] . "'";
    $fields['concept'] = "'" . $storeArray['concept'] . "'";
    $fields['exercise'] = "'" . $storeArray['exercise'] . "'";
    $fields['concept_eng'] = "'" . $storeArray['concept_eng'] . "'";
    $fields['exercise_eng'] = "'" . $storeArray['exercise_eng'] . "'";
    $fields['concept_si'] = "'" . $storeArray['concept_si'] . "'";
    $fields['exercise_si'] = "'" . $storeArray['exercise_si'] . "'";
    $fields['done'] = (int)$storeArray['done'];
    if($storeArray['ref_ID']){
        $fields['ref_ID'] = $storeArray['ref_ID'];
    }
    else{
        $fields['ref_ID'] = 'null';
    }
    $fields['updti'] = $storeArray['updti'];

    $sqlString = array();
    foreach($fields as $key => $value){
        $sqlString[] = "$key = $value";
    }
    $sql = "update book set " . implode(', ', $sqlString) . "
                where ID = " . (int)$storeArray['ID'];

    $result = mysql_query($sql);
    if(!$result){
        print mysql_error();
        return false;
    }
    setOrder($storeArray['ID'], $storeArray['ref_ID'], $storeArray['order']);
    return true;
}

function updateChapterSimple($storeArray)
{
    $fields['name'] = "'" . $storeArray['name'] . "'";
    $fields['concept'] = "'" . $storeArray['concept'] . "'";
    $fields['updti'] = $storeArray['updti'];

    $sqlString = array();
    foreach($fields as $key => $value){
        $sqlString[] = "$key = $value";
    }
    $sql = "update book set " . implode(', ', $sqlString) . "
                where ID = " . (int)$storeArray['ID'];

    $result = mysql_query($sql);
    if(!$result){
        print mysql_error();
        return false;
    }
    return true;
}

function setOrder($currentId, $group, $afterWhat)
{
    if(!$afterWhat){
        return true;
    }
    $query = "select `order` from book where ID = " . (int)$afterWhat;

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        return false;
    }
    $order = 0;
    while($row = mysql_fetch_assoc($result)) {
        $order = (int)$row['order'];
    }

    if($group > 0){
        $queryGroup = " and ref_ID = " . (int)$group;
    }
    else{
        $queryGroup = " and ref_ID is null";
    }

    $query = "update book set `order` = `order` + 2
                    where `order` >= $order and ID != " . (int)$afterWhat . $queryGroup;

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        return false;
    }

    $query = "update book set `order` = $order + 1 where ID = '$currentId' " . $queryGroup;
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        return false;
    }
    return true;
}

function insertChapter($storeArray)
{
    $fields['name'] = "'" . $storeArray['name'] . "'";
    $fields['name_eng'] = "'" . $storeArray['name_eng'] . "'";
    $fields['name_si'] = "'" . $storeArray['name_si'] . "'";
    $fields['name_gre'] = "'" . $storeArray['name_gre'] . "'";
    $fields['concept'] = "'" . $storeArray['concept'] . "'";
    $fields['exercise'] = "'" . $storeArray['exercise'] . "'";
    $fields['concept_eng'] = "'" . $storeArray['concept_eng'] . "'";
    $fields['exercise_eng'] = "'" . $storeArray['exercise_eng'] . "'";
    $fields['concept_si'] = "'" . $storeArray['concept_si'] . "'";
    $fields['exercise_si'] = "'" . $storeArray['exercise_si'] . "'";
    $fields['done'] = (int)$storeArray['done'];
    if($storeArray['ref_ID']){
        $fields['ref_ID'] = $storeArray['ref_ID'];
    }
    else{
        $fields['ref_ID'] = 'null';
    }
    $fields['updti'] = $storeArray['updti'];
    $fields['crdti'] = $fields['updti'];

    if($storeArray['ref_ID'] > 0){
        $query = "select max(`order`) + 1 from book where ref_id = {$fields['ref_ID']}";
        $result = mysql_query($query);
        if(!$result){
            print mysql_error();
            return true;
        }
        while($row = mysql_fetch_row($result)) {
            $fields['`order`'] = $row[0];
        }
    }
    if(!$fields['`order`']){
        $fields['`order`'] = 1;
    }
    $sql = "insert into book (" . implode(', ', array_keys($fields)) . ") values (" . implode(', ', array_values($fields)) . ")";
//print $sql;
    $result = mysql_query($sql);
    if(!$result){
        print mysql_error();
        return false;
    }

    $query = "select b1.ID as MAIN_ID, b2.ID AS REF_ID
                from book b1
                left outer join book b2 on b1.ref_ID = b2.ID
                where b1.name = {$fields['name']} and coalesce(b1.ref_ID, 0) = " . (int)$storeArray['ref_ID'];
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        return true;
    }
    while($row = mysql_fetch_assoc($result)) {
        $id = $row['MAIN_ID'];
        $refId = $row['REF_ID'];
    }
    // ha ez létezik, akkor õ alfejezet és a ref_id a fejezet
    if($refId > 0){
        $chapterId = $refId;
        $subChapterId = $id;
    }
    else{
        $chapterId = $id;
        $subChapterId = null;
    }
    return array($id, $chapterId, $subChapterId);
}

function deleteChapter($id)
{
    $query = "select count(*) as nr from book where ref_ID = " . (int)$id;
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        return false;
    }
    while($row = mysql_fetch_assoc($result)) {
        $record = $row;
    }
    if($record['nr'] > 0){
        print "<script>alert('Erre a fejezetre hivatkoznak alfejezetek!');</script>";
        return false;
    }

    $sql = "delete from ruin_exercises where exercise_id = " . (int)$id;
    $result = mysql_query($sql);
    if(!$result){
        print mysql_error();
        return false;
    }

    $sql = "delete from book where ID = " . (int)$id;
//print $sql;

    $result = mysql_query($sql);
    if(!$result){
        print mysql_error();
        return false;
    }

    return true;
}

function storeRuinSubChapterLink($ruin_ID, $selectedSubChaptersIdArray)
{
    if(!$ruin_ID){
        return false;
    }
    $query = "BEGIN";
    $result = mysql_query($query);
    if(!$result){
        print __LINE__;
        print mysql_error();
        return false;
    }

    $query = "delete from ruins_subchapters where ruin_ID = " . (int)$ruin_ID;
    $result = mysql_query($query);
    if(!$result){
        print __LINE__;
        print mysql_error();
        $query = "ROLLBACK";
        $result = mysql_query($query);
        return false;
    }
    foreach($selectedSubChaptersIdArray as $actSubChapterId){
        $query = "insert into ruins_subchapters (ruin_ID, subchapter_ID) values(" . (int)$ruin_ID . ", " . (int)$actSubChapterId . ')';
        $result = mysql_query($query);
        if(!$result){
            print __LINE__ . ": $query<BR>";
            print mysql_error();
            $query = "ROLLBACK";
            $result = mysql_query($query);
            return false;
        }
    }
    $query = "COMMIT";
    $result = mysql_query($query);
}

function getUserObj($username)
{
    if(strlen($username) == 0){
        return false;
    }

    $query = "SELECT * from jelentkezok where username = '$username'";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $userObject = false;
    while($row = mysql_fetch_assoc($result)) {
        $userObject = $row;
    }
    if($userObject){
        $query = "select count(*) from kod where jelentkezok_id = " . (int)$userObject['ID'];
        $result = mysql_query($query);
        if(!$result){
            print mysql_error();
            exit("Nem sikerült: " . $query);
        }
        $row = mysql_fetch_row($result);
        $userObject["hasKod"] = ($row[0] > 0);
    }

    return $userObject;
}

function getUserObjForLogin($username, $email)
{
    if(strlen($username) == 0 || strlen($email) == 0){
        return false;
    }
    $username = mysql_real_escape_string($username);
    $email = mysql_real_escape_string($email);
    $query = "SELECT * from jelentkezok where LOWER(username) = LOWER('$username') and LOWER(email) = LOWER('$email')";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $userObject = false;
    while($row = mysql_fetch_assoc($result)) {
        $userObject = $row;
    }
    if($userObject){
        $query = "select count(*) from kod where jelentkezok_id = " . (int)$userObject['ID'];
        $result = mysql_query($query);
        if(!$result){
            print mysql_error();
            exit("Nem sikerült: " . $query);
        }
        $row = mysql_fetch_row($result);
        $userObject["hasKod"] = ($row[0] > 0);
    }
    return $userObject;
}

function getUserObjForLoginByHash($hash)
{
    if(strlen($hash) == 0){
        return false;
    }
    $hash = mysql_real_escape_string($hash);
    $query = "SELECT * from jelentkezok where hash = '$hash'";
	//DEBUG($query);
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $userObject = false;
    while($row = mysql_fetch_assoc($result)) {
        $userObject = $row;
    }
    if($userObject){
        $query = "select count(*) from kod where jelentkezok_id = " . (int)$userObject['ID'];
        $result = mysql_query($query);
        if(!$result){
            print mysql_error();
            exit("Nem sikerült: " . $query);
        }
        $row = mysql_fetch_row($result);
        $userObject["hasKod"] = ($row[0] > 0);
    }
    return $userObject;
}

function getUserObjByEmail($email)
{
    if(strlen($email) == 0){
        return false;
    }
    $email = mysql_real_escape_string($email);
    $query = "SELECT * from jelentkezok where email = '$email'";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $userObject = false;
    while($row = mysql_fetch_assoc($result)) {
        $userObject = $row;
    }
    if($userObject){
        $query = "select count(*) from kod where jelentkezok_id = " . (int)$userObject['ID'];
        $result = mysql_query($query);
        if(!$result){
            print mysql_error();
            exit("Nem sikerült: " . $query);
        }
        $row = mysql_fetch_row($result);
        $userObject["hasKod"] = ($row[0] > 0);
    }

    return $userObject;
}
function getUserObjById($id)
{
    if(strlen($id) == 0){
        return false;
    }
    $query = "SELECT j.* from jelentkezok j where j.ID = '$id'";
	
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $userObject = false;
    while($row = mysql_fetch_assoc($result)) {
        $userObject = $row;
    }

    return $userObject;
}

function getOrszagObjectById($id)
{
    if(strlen($id) == 0){
        return false;
    }

    $query = "SELECT * from orszag where ID = '$id'";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $orszagObject = false;
    while($row = mysql_fetch_assoc($result)) {
        $orszagObject = $row;
    }
    return $orszagObject;
}

function get_user_desire($user_id)
{
	
    $query = "SELECT user_desire from jelentkezok_info where user_id = '$user_id'";
	$result = mysql_query($query);
    if(!$result){
        
		return "";
    }
    $row = mysql_fetch_row($result);
	$user_desire = $row[0];
    return $user_desire;
}

function get_user_notes($user_id)
{
	
    $query = "SELECT * from jelentkezok_notes where `user_id` = '$user_id'";
	$result = mysql_query($query);
    if(!$result){
        
		return "";
    }
    $row = mysql_fetch_row($result);
	$user_notes = $row[1];
	
    return $user_notes;
}

function selectUserSolution($userID)
{
	
	$query = "SELECT r.ruin,js.ID FROM ruin r, jelentkezok_solution js  WHERE js.user_solution = r.ID and js.user_id =".$userID;
	$result = mysql_query($query);
    if(!$result){
  
		print mysql_error();
		return false;
    }
	while($row = mysql_fetch_assoc($result)) {
        $solutions[] = $row;
    }
    return $solutions;
	
}
function deleteSelectedSolution($ID)
{
	$query = "delete from jelentkezok_solution where ID=".$ID;
	$result = mysql_query($query);
	if(!$result){
		print mysql_error();
		return false;
	}
}

function addUserSolutionIntoDb($userID,$solutionID)
{
	
	$query = "insert into jelentkezok_solution (user_id,user_solution) SELECT * from ( select ".$userID.",".$solutionID.") AS tmp WHERE NOT EXISTS (SELECT ID FROM jelentkezok_solution WHERE user_solution=".$solutionID." and user_id=".$userID.") LIMIT 1";
	$result = mysql_query($query);
	if(!$result){
		print mysql_error();
		return false;
	}

}
function addUserProblemIntoDb($ID,$problem,$userObj)
{
	$query = "insert into user_problema (UserID,Problema) values(".$ID.",'".$problem."')";
	$result = mysql_query($query);
	if(!$result){
		print mysql_error();
		return false;
	}
	
	$subject = "RE: Válasz - ".$problem."";
        //$body = "Probléma :".$problem."<br>";
	$body .= "Feladó :".$userObj["keresztnev"]." -".$userObj["email"]."<br>";
	
	$to = "hello@luciendelmar.com";
	print "<script>";
	if(endiMail($to, $subject, $body, 'Lucien', 'hello@luciendelmar.com', $users)){
        print "alert(' Köszönöm a választ! Lucien')";
    }
    else{
        print "alert('Üzenet küldése nem sikerült!')";
    }
    print "</script>";
}

function addUserDesireIntoDb($ID,$desire,$new)
{
	if($new == true)
	{
		str_replace("'", "''", $desire);
		
		$query = "insert into jelentkezok_info (user_id,user_desire) values(".$ID.",'".$desire."')";
		$result = mysql_query($query);
		if(!$result){
			print mysql_error();
			return false;
		}
	}
	else
	{
		str_replace("'", "''", $desire);
		$query = "update jelentkezok_info set `user_desire` = '".$desire."' where user_id = $ID";
		$result = mysql_query($query);
		if(!$result){
			print mysql_error();
			return false;
		}
	}
}

function addUserNotesIntoDb($ID,$notes,$new)
{
	str_replace("'", "''", $notes);
	$query = "INSERT INTO jelentkezok_notes (user_id, user_notes) values(".$ID.",'".$notes."') ON DUPLICATE KEY UPDATE  user_notes='".$notes."'";
	
	//$query = "insert into jelentkezok_notes (user_id,user_notes) values(".$ID.",'".$notes."')";
	$result = mysql_query($query);
	if(!$result){
		print mysql_error();
		return false;
	}
	
}

function getVarosObjectById($id)
{
    if(strlen($id) == 0){
        return false;
    }

    $query = "SELECT * from varos where ID = '$id'";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $varosObject = false;
    while($row = mysql_fetch_assoc($result)) {
        $varosObject = $row;
    }
    return $varosObject;
}

function getVarosList($orszagId = 0)
{
    if((int)$orszagId > 0){
        $sqlpart = "where orszag_id = '$orszagId'";
    }

    $query = "SELECT * from varos $sqlpart order by title_hun";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $varosList = array();
    while($row = mysql_fetch_assoc($result)) {
        $varosList[] = $row;
    }
    return $varosList;
}

function endiMail($to, $subject, $body, $fromName, $fromEmail, $hiddenAddresses = array(), $userNames = array(), $userLogins = array(), $charcode = 'ISO-8859-2', $userEmails = array())
{
    // a levélküldés levágja a body utolsó két karakterét, tudja a hóhér hogy miért
    $body = str_replace(chr(13) . chr(10), "<br>", $body);
    $body = "<span style='font-family:Arial;'>" . $body . '</span>  ';
    $body = "<HTML><head><META HTTP-EQUIV='CHARSET' CONTENT='text/html; charset=$charcode'></head><body>" . $body . "</body></html>  ";
    $mime_boundary = "---- pHGeneration ----" . md5(time());
    $headers = "From: $fromName <$fromEmail>\n";
    $headers .= "Reply-To: $fromName <$fromEmail>\n";
    $headers .= "MIME-Version: 1.0\n";
//    $headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\r\n";
    $headers .= "Content-Type: text/html\n";
    if(count((array)$hiddenAddresses) > 0){
        for($i = 0; $i < count((array)$hiddenAddresses); $i++){
            if(count($userNames) == count($hiddenAddresses) and count($userNames) > 0){
                $body2 = str_replace('<nameVar />', $userNames[$i], $body);
                $body2 = str_replace('<loginVar />', $userLogins[$i], $body2);
                $body2 = str_replace('<emailVar />', $userEmails[$i], $body2);
            }
            if(!mail($hiddenAddresses[$i], $subject, $body2, $headers)){
                return false;
            }
            logEmail();
        }
        return true;

        // $headers .= "BCC: " . implode(',', $hiddenAddresses) . "\r\n";
    }
    set_time_limit(0);
    if(!mail($to, $subject, $body, $headers)){
        return false;
    }
    logEmail();
    return true;
}

function getCategoryName($category)
{
    switch($category){
        case 1:
            $categoryName = 'ÖNISMERET, ÖNMEGVALÓSÍTÁS';
            break;
        case 2:
            $categoryName = 'PÁRKAPCSOLAT';
            break;
        case 3:
            $categoryName = 'CSALÁD';
            break;
        case 4:
            $categoryName = 'MUNKA, PÉNZ';
            break;
        case 5:
            $categoryName = 'KAPCSOLATOK, KOMMUNIKÁCIÓ';
            break;
        case 6:
            $categoryName = 'ÜZLETI/VEZETÕI KÉPESSÉGEK';
            break;
        default:
            $categoryName = 'Ismeretlen';
            break;
    }
    return $categoryName;
}

function getAllUsers()
{
    $query = "SELECT * from jelentkezok";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $userObject = array();
    while($row = mysql_fetch_assoc($result)) {
        $userObject[] = $row;
    }
    return $userObject;
}

function getSpecificUsers($status = 0, $language = 0)
{
    $query = "SELECT * from jelentkezok where status = $status and send_mail = 1";
    if($language > 0){
        $query .= " and language = " . (int)$language;
    }

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $userObject = array();
    while($row = mysql_fetch_assoc($result)) {
        $userObject[] = $row;
    }
    return $userObject;
}

function getBpNeutralHunUsers()
{
    $query = "SELECT j.* from jelentkezok j
                inner join orszag o on j.orszag_id = o.id
                left outer join varos v on j.varos_id = v.id
                where o.title_eng = 'Hungary'
                and (v.id is null or v.title_eng = 'Budapest')";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $userObject = array();
    while($row = mysql_fetch_assoc($result)) {
        $userObject[] = $row;
    }
    return $userObject;
}

function getUsersByCountryId($countryId)
{
    $query = "SELECT * from jelentkezok where orszag_id = " . (int)$countryId;

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $userObject = array();
    while($row = mysql_fetch_assoc($result)) {
        $userObject[] = $row;
    }
    return $userObject;

}

function getSubscribedUsers($lastSentId = 0, $lang = 0)
{
    if($lang == 1){
        $part = " and language = 1 ";
    }
    else if($lang == 2){
        $part = " and language = 2 ";
    }
    $query = "SELECT * from jelentkezok where is_news = 1 {$part} and ID > " . (int)$lastSentId . " order by ID";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $userObject = array();
    while($row = mysql_fetch_assoc($result)) {
        $userObject[] = $row;
    }
    return $userObject;
}

function getTestUsers($lastSentId = 0)
{
    $query = "SELECT * from tesztcimek where ID > " . (int)$lastSentId . " order by ID";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $userObject = array();
    while($row = mysql_fetch_assoc($result)) {
        $userObject[] = $row;
    }
    return $userObject;
}

function sendFormMail($subChapterId)
{
    $to = 'hello@luciendelmar.com';
    $allUsers = getSubscribedUsers();
    $users = array();
    for($i = 0; $i < count($allUsers); $i++){
        $users[] = $allUsers[$i]['email'];
    }
    $subChapter = getCurrentChapter($subChapterId);
//    $subject = "Elkészült egy alfejezet";
    $subject = "ELKÉSZÜLT ALFEJEZET: {$subChapter['name']}";
    $body = "Szia! A(z) \"{$subChapter['name']}\" címû gyakorlat új változata elkészült. Ha szívesen megismerkednél vele, a <a href='http://www.lifelovers.net'>http://www.lifelovers.net</a> weboldalon 'A Gyakorlatsor' menüpont alatt olvashatod a jelszavaddal való belépés után. Kívánok felismeréseket, bizonyosságod megerõsödését! Lucien";

    print "<script>";
    if(endiMail($to, $subject, $body, 'active-silence', 'hello@luciendelmar.com', $users)){
        print "alert('Üzenet küldése sikerült!')";
    }
    else{
        print "alert('Üzenet küldése nem sikerült!')";
    }
    print "</script>";
}

function sendStoryMail($storyId)
{
    $to = 'hello@luciendelmar.com';
    $allUsers = getSubscribedUsers();
    //$allUsers = getTestUsers();
    $users_hun = array();
    $users_eng = array();
    $users_gre = array();
    $names_hun = array();
    $names_eng = array();
    $names_gre = array();
    $emails_hun = array();
    $emails_eng = array();
    $emails_gre = array();
    for($i = 0; $i < count($allUsers); $i++){
        if($allUsers[$i]['language'] == 1){
            $users_hun[] = $allUsers[$i]['email'];
            $names_hun[] = $allUsers[$i]['keresztnev'];
            $passwds_hun[] = $allUsers[$i]['username'];
            $emails_hun[] = $allUsers[$i]['email'];
        }
        else if($allUsers[$i]['language'] == 2){
            $users_eng[] = $allUsers[$i]['email'];
            $names_eng[] = $allUsers[$i]['keresztnev'];
            $passwds_eng[] = $allUsers[$i]['username'];
            $emails_eng[] = $allUsers[$i]['email'];
        }
        else if($allUsers[$i]['language'] == 4){
            $users_gre[] = $allUsers[$i]['email'];
            $names_gre[] = $allUsers[$i]['keresztnev'];
            $passwds_gre[] = $allUsers[$i]['username'];
            $emails_gre[] = $allUsers[$i]['email'];
        }
    }
    $story = getCurrentStory($storyId);
    $categories = getExercises();

    list($subject_hun, $body_hun) = getStoryMail($story, $categories, 1);
/*
    list($subject_eng, $body_eng) = getStoryMail($story, $categories, 2);
    list($subject_gre, $body_gre) = getStoryMail($story, $categories, 4);
*/
    print "<script>";
    if(endiMail($to, $subject_hun, $body_hun, 'active-silence', 'hello@luciendelmar.com', $users_hun, $names_hun, $passwds_hun, 'ISO-8859-2', $emails_hun)
/*
        and endiMail($to, $subject_eng, $body_eng, 'flow', 'info@lifelovers.net', $users_eng, $names_eng, $passwds_eng, 'ISO-8859-2', $emails_eng)
        and endiMail($to, $subject_gre, $body_gre, 'flow', 'info@lifelovers.net', $users_gre, $names_gre, $passwds_gre, 'UTF-8', $emails_gre)
*/
    ){
        print "alert('Üzenet küldése sikerült!')";
    }
    else{
        print "alert('Üzenet küldése nem sikerült!')";
    }
    print "</script>";

    global $trans;
    $_SESSION['language'] = 'hun';
    include('translations_HUN.php');
    return count($names_hun);
}

function getStoryMail($story, $categories, $language)
{
    global $trans;
    $imgSize = 8;
    $trans = array();
    $_language = 'hun';
	if($language == 2){
        $_SESSION['language'] = 'eng';
        include('translations_ENG.php');
        $story_name = $story['story_name_eng'];
        $story_content = $story['story_eng'];
        $_language = 'eng';
    }
	else if($language == 4){
        $_SESSION['language'] = 'gr';
        include('translations_GR.php');
        $story_name = $story['story_name_gre'];
        $story_content = $story['story_gre'];
        $_language = 'gre';
    }
    else{
        $_SESSION['language'] = 'hun';
        include('translations_HUN.php');
        $story_name = $story['story_name'];
        $story_content = $story['story'];
    }

    $subject = $story_name;
    $body = "<link rel=stylesheet type='text/css' href='http://www.luciendelmar.com/usermenu.css'>
    <table width=840><tr><td><table align=left width=100% border = '0'><tr>";
    $body .= "<td align=left valign=top width=160>";
    $body .= "<img src='http://www.luciendelmar.com/images/celebrate.jpg' border=0 width=120>

            <span style='font-weight:bold'>" . translate("LetOromeNehanyEleme") . "</span>
            <table border=0 align='left' >";

    $prevId = 0;
    for($i = 0; $i < count($categories); $i++){
        $sub_name = $categories[$i]['sub_name'];
        if($_language == 'eng'){
            $sub_name = $categories[$i]['sub_name_eng'];
        }
        else if($_language == 'gre'){
            $sub_name = $categories[$i]['sub_name_gre'];
        }

        // ha megváltozott a main category
/*
        if($categories[$i]['ID'] != $prevId){
            $num = 1;
            $body .= "<tr><td><p>&nbsp;</p></td></tr>";
            $body .= "<tr><th align=left style='color:#696969;'>{$categories[$i]['name']}</th></tr>";
        }
*/
        if(in_array($categories[$i]['sub_ID'], $story['exercises'])){
            $color = 'red';
            $golyo = 'golyo-orange.jpg';
        }
        else
        {
            continue;
            $color = '#696969';
            $golyo = 'golyo.jpg';
        }
        $body .= "<tr><td style='font-family:Arial;font-size:9;color:$color;'><img src='http://www.luciendelmar.com/images/$golyo' width='$imgSize' height='$imgSize'> {$sub_name}</td></tr>";
        $num++;
        $prevId = $categories[$i]['ID'];
    }
    $body .= "</table>";

    $body .= "</td><td align=left valign=top width=600><span style='font-size:10pt'><br>" . translate("story_mail_body_1") . "
<i>\"" . rtrim($story_content) . "\"</i>
" . translate("story_mail_body_2")
/*
. "<img id='img_filter_1' src='http://www.lifelovers.net/images/golyo-orange.jpg' width='$imgSize' height='$imgSize'>&nbsp;&nbsp;<a href='http://www.lifelovers.net/index.php?emailAd=consulting&username=<loginVar />&email=<emailVar />'>" . translate("Coaching") . "</a>
<img id='img_filter_2' src='http://www.lifelovers.net/images/golyo.jpg' width='$imgSize' height='$imgSize'>&nbsp;&nbsp;<a href='http://www.lifelovers.net/index.php?emailAd=locations&username=<loginVar />&email=<emailVar />'>" . translate("LetOromeTanfolyam") . "</a>"
*/
. "<br><br><a href='http://www.luciendelmar.com/index.php?emailAd=main&username=<loginVar />&email=<emailVar />'>www.luciendelmar.com</a>"
. "
</span>
</td>
</tr></table></td>
</tr></table>
";
    $body = str_replace(chr(13) . chr(10), "<br>", $body);
    return array($subject, $body);
}

function sendSuccessFormMail($subChapterId, $emailGroup = 'all', $pictureName)
{
    $to = 'hello@luciendelmar.com';
    if($emailGroup == 'all'){
        $allUsers = getAllUsers();
    }
    else if($emailGroup == 'students'){
        $allUsers = getSpecificUsers(2);
    }
    else if($emailGroup == 'students_hun'){
        $allUsers = getSpecificUsers(2, 1);
    }
    else if($emailGroup == 'students_eng'){
        $allUsers = getSpecificUsers(2, 2);
    }
    else if($emailGroup == 'hunBpNeutral'){
        $allUsers = getBpNeutralHunUsers();
    }
    else if((int)$emailGroup > 0){
        $allUsers = getUsersByCountryId((int)$emailGroup);
    }
    else if ($emailGroup == 'subscribed_hun'){
        $allUsers = getSubscribedUsers(0, 1);
    }
    else if ($emailGroup == 'subscribed_eng'){
        $allUsers = getSubscribedUsers(0, 2);
    }
    else{
        $allUsers = getSubscribedUsers();
    }
    //$allUsers = getTestUsers();
    $subChapter = getCurrentChapter($subChapterId);
//    $subject = "Elkészült egy alfejezet";
    $subject = $subChapter['name'];
    $body = $subChapter['concept'];

    $success = true;
    $sentMails = 0;
    for($i = 0; $i < count($allUsers); $i++){
        if($allUsers[$i]['language'] == "1"){
            $hitext = "Szia";
            $text = "Azonnali belépéshez klikkelj ide!";
        }
        else{
            $hitext = "Dear";
            $text = "Click here for direct access!";
        }
        $body2 = "<link rel=stylesheet type='text/css' href='http://www.luciendelmar.com/usermenu.css'>\n";
        $body2 .= "<table width=620 border='0'><tr><td bgcolor='#0033ff' align='center'>\n";
        $body2 .= "<table width=600 border='0' cellpadding='20'><tr>\n";
        $body2 .= "<td align='center' bgcolor=#FAFAFA><img src='http://www.luciendelmar.com/images/{$pictureName}.jpg' border=0></td></tr>\n";
        $body2 .= "<td align='left' bgcolor=#FAFAFA><span style='font-size:12pt'>{$hitext} {$allUsers[$i]['keresztnev']}!</span>\n";
        $body2 .= "<span style='font-size:10pt'>" . chr(13) . chr(10) . chr(13) . chr(10) . $body ."</span></td>";
        $body2 .= "</tr>\n";
        $body2 .= "<tr>\n";
        $body2 .= "<td><a href='http://www.luciendelmar.com/index.php?actionType=login&username={$allUsers[$i]['username']}&email={$allUsers[$i]['email']}'>";
        $body2 .= "{$text}</a></td></tr></table>\n";
        $body2 .= "</td></tr></table>\n";
        if(!endiMail($allUsers[$i]['email'], $subject, $body2, 'lucien del mar', 'hello@luciendelmar.com')){
            $success = false;
        }
        else{
            $sentMails++;
        }
    }
    print "<script>";
    if($success){
        print "alert('" . $sentMails . " db üzenet küldése sikerült!')";
    }
    else{
        print "alert('Üzenet küldése nem sikerült!')";
    }
    print "</script>";
}

function searchChapters($targetField, $text)
{
    if($targetField == 'subChapter'){
        $field = 'name';
    }
    else if($targetField == 'concept'){
        $field = 'concept';
    }
    else{
        $field = "''";
    }
    $text = str_replace("'", "''", $text);
    $query = "select c.name as chapterName, sc.name as subChapterName, sc.concept
                from book sc
                inner join book c on sc.ref_ID = c.ID
                where sc.ref_ID is not null
                and sc.{$field} like '%$text%'";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $results = array();
    while($row = mysql_fetch_assoc($result)) {
        $results[] = $row;
    }
    return $results;
}

function getClearedNumber()
{
    $query = "select count(*) as nr from book where done = 1";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}
function getDoneNumber()
{
    $query = "select count(*) as nr from book where done = 2";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function storeSettings($userId, $settingArray, &$errorMessage)
{
    if(!$userId){
        return false;
    }

    $query = "update jelentkezok set
        is_napidezete = " . (int)$settingArray['is_napidezete'] . ",
        is_news = " . (int)$settingArray['is_news'] . ",
        language = " . (int)$settingArray['language'] . ",
        varos_id = " . (int)$settingArray['hunCity'] . ",
        keresztnev = '" . str_replace("'", "''", $settingArray['keresztnev']) . "'
        where ID = " . (int)$userId;

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        return false;
    }

    if($settingArray['aktivalo_kod']){
        $settingArray['aktivalo_kod'] = str_replace("\\", "", str_replace('"', '', str_replace("'", "", $settingArray['aktivalo_kod'])));

        $query = "select count(*) from kod where kod = '{$settingArray['aktivalo_kod']}' and jelentkezok_id is null";
        $result = mysql_query($query);
        while(list($cnt) = mysql_fetch_row($result)) {
            if($cnt == 0) {
                $errorMessage = translate('aktivalo_kod_nem_jo');
                return;
            }
        }

        $query = "update kod set jelentkezok_id = " . (int)$userId . " where kod = '{$settingArray['aktivalo_kod']}' and jelentkezok_id is null";
        $result = mysql_query($query);
        if(!$result){
            print mysql_error();
            return false;
        }
    }
    return true;
}

function deleteUser($userId)
{
    if(!$userId){
        return false;
    }
    $query = "delete from jelentkezok_kedvenc where jelentkezok_id = " . (int)$userId;

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        return false;
    }

    $query = "delete from jelentkezok where ID = " . (int)$userId . ";";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        return false;
    }
    return true;
}

function storeNewUser($userArray, &$message)
{
    if(!$userArray['login'] or !$userArray['firstName'] or !$userArray['secondName'] or !$userArray['email'] or /*!$userArray['orszag'] or*/ !$userArray['reference'] or !$userArray['inviter']){
        return false;
    }
    foreach($userArray as $key => $value){
        $userArray[$key] = str_replace("'", "''", $value);
    }

    $query = "select count(*) as nr from jelentkezok where username = '{$userArray['login']}'";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    if($number > 0){
        $message = "Ez a jelszó már foglalt, válassz másikat!";
        return false;
    }

    $query = "select count(*) as nr from jelentkezok where email = '{$userArray['email']}'";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    if($number > 0){
        $message = "Ezzel az e-mail címmel már van regisztrált felhasználó!";
        return false;
    }
    $hash = bin2hex(mcrypt_create_iv(22, MCRYPT_DEV_URANDOM));
    $query = "insert into jelentkezok (username, vezeteknev, keresztnev, email, orszag_id, varos_id, reference, crdti, language, inviter, reg_source, is_napidezete, is_news, course, hash)
                values('{$userArray['login']}', '{$userArray['firstName']}', '{$userArray['secondName']}', '{$userArray['email']}', " . (int)$userArray['orszag'] . ", " . (int)$userArray['varos'] . ", "
                . (int)$userArray['reference'] . ", NOW(),  " . (int)$userArray['language'] . ", " . (int)$userArray['inviter'] . ", 1, " . (int)$userArray['is_napidezete'] . ", " . (int)$userArray['is_news'] . "," . ($userArray['course'] ? "0" : "null") . ", '$hash')";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        return false;
    }

    if($userArray['aktivalo_kod']){
        $userArray['aktivalo_kod'] = str_replace("\\", "", str_replace('"', '', str_replace("'", "", $userArray['aktivalo_kod'])));
        
        $query = "select count(*) from kod where kod = '{$userArray['aktivalo_kod']}' and jelentkezok_id is null";
        $result = mysql_query($query);
        $isFree = false;
        while(list($cnt) = mysql_fetch_row($result)) {
            if($cnt == 0)
                $message = translate('aktivalo_kod_nem_jo');
            else
                $isFree = true;
        }
        if($isFree){
            $query = "select ID from jelentkezok where username = '{$userArray['login']}' and vezeteknev = '{$userArray['firstName']}' and keresztnev = '{$userArray['secondName']}' and email = '{$userArray['email']}'";
            $result = mysql_query($query);
            if(!$result){
                print mysql_error();
                return false;
            }
            list($id) = mysql_fetch_row($result);
            $query = "update kod set jelentkezok_id = " . (int)$id . " where kod = '{$userArray['aktivalo_kod']}' and jelentkezok_id is null";
            $result = mysql_query($query);
            if(!$result){
                print mysql_error();
                return false;
            }
        }
    }
    return addExpirePointsToUser((int)$userArray['inviter'], 50);
}

function createUser($userArray, &$message)
{
    if(!$userArray['username'] or !$userArray['vezeteknev'] or !$userArray['keresztnev'] or !$userArray['email']){
        $message = "A vezetéknév, keresztnév, username és email kitöltése kötelezõ!";
        return false;
    }
    foreach($userArray as $key => $value){
        $userArray[$key] = str_replace("'", "''", $value);
    }
    $userArray['viz'] = (int)$userArray['viz'];
    $userArray['ph_so'] = (int)$userArray['ph_so'];
    $userArray['zold_por'] = (int)$userArray['zold_por'];
    $userArray['varos_id'] = (int)$userArray['varos_id'];
    $userArray['send_mail'] = (int)$userArray['send_mail'];
    $userArray['language'] = (int)$userArray['language'];

    $query = "select count(*) as nr from jelentkezok where email = '{$userArray['email']}'";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    if($number > 0){
        $message = "Ezzel az e-mail címmel már van regisztrált felhasználó!";
        return false;
    }
    $hash = bin2hex(mcrypt_create_iv(22, MCRYPT_DEV_URANDOM));
    $query = "insert into jelentkezok (username, vezeteknev, keresztnev, email, phone_number, program_start_date, viz, ph_so, ph_so_date, zold_por, zold_por_date, status,
                birth_date, is_napidezete, varos_id, client_data, send_mail, language, crdti, hash)
                values('{$userArray['username']}', '{$userArray['vezeteknev']}', '{$userArray['keresztnev']}', '{$userArray['email']}'
                    , '{$userArray['phone_number']}', '{$userArray['program_start_date']}', {$userArray['viz']}, {$userArray['ph_so']}, '{$userArray['ph_so_date']}', {$userArray['zold_por']}
                    , '{$userArray['zold_por_date']}', 2, '{$userArray['birth_date']}', 0, {$userArray['varos_id']}, '{$userArray['client_data']}', {$userArray['send_mail']}, {$userArray['language']}, NOW(), '$hash')";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        return false;
    }
    $query = "select id from jelentkezok where email = '{$userArray['email']}'";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $id = 0;
    while($row = mysql_fetch_row($result)) {
        $id = $row[0];
    }

    return $id;
}

function modifyUser($storeArray, &$message)
{
    $fields['email'] = "'" . $storeArray['email'] . "'";
    $fields['phone_number'] = "'" . $storeArray['phone_number'] . "'";
    $fields['birth_date'] = "'" . $storeArray['birth_date'] . "'";
    $fields['username'] = "'" . $storeArray['username'] . "'";
    $fields['vezeteknev'] = "'" . $storeArray['vezeteknev'] . "'";
    $fields['keresztnev'] = "'" . $storeArray['keresztnev'] . "'";
    $fields['program_start_date'] = "'" . $storeArray['program_start_date'] . "'";
    $fields['viz'] = (int)$storeArray['viz'];
    $fields['ph_so'] = (int)$storeArray['ph_so'];
    $fields['ph_so_date'] = "'" . $storeArray['ph_so_date'] . "'";
    $fields['zold_por'] = (int)$storeArray['zold_por'];
    $fields['zold_por_date'] = "'" . $storeArray['zold_por_date'] . "'";
    $fields['varos_id'] = (int)$storeArray['varos_id'];
    $fields['client_data'] = "'" . $storeArray['client_data'] . "'";
    $fields['send_mail'] = (int)$storeArray['send_mail'];
    $fields['language'] = (int)$storeArray['language'];

    $query = "select count(*) as nr from jelentkezok where email = '{$storeArray['email']}' and id != " . (int)$storeArray['id'];

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    if($number > 0){
        $message = "Ezzel az e-mail címmel már van regisztrált felhasználó!";
        return false;
    }

    $sqlString = array();
    foreach($fields as $key => $value){
        $sqlString[] = "$key = $value";
    }
    $sql = "update jelentkezok set " . implode(', ', $sqlString) . "
                where id = " . (int)$storeArray['id'];

    $result = mysql_query($sql);
    if(!$result){
        print mysql_error();
        return false;
    }
    return true;
}

function addExpirePointsToUser($userId, $point)
{
    $query = "update jelentkezok set expire_number = expire_number + " . (int)$point . " where ID = " . (int)$userId;
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        return false;
    }
    return true;
}

function logLogin()
{
    $query = "select count(*) as nr from login_log where DATUM = CURDATE()";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    if($number > 0){
        $query = "update login_log set NR = NR + 1 where DATUM = CURDATE()";
    }
    else{
        $query = "insert into login_log (DATUM) values(CURDATE())";
    }

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        return false;
    }
    return true;
}

function logPersonalLogin($id)
{
    $query = "update jelentkezok set login_nr = login_nr + 1 where id = " . (int)$id;
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
}

function getLoggedNumber()
{
    $query = "select NR from login_log where DATUM = CURDATE()";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['NR'];
    }
    return $number;
}

function getRegisteredNumber()
{
    $query = "select count(*) as NR from jelentkezok where CRDTI > CURDATE()";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['NR'];
    }
    return $number;
}

function changePassword($userId, $settingArray)
{
    if(!$userId){
        return false;
    }
    if($settingArray['newPassword'] != $settingArray['confirmNewPassword']){
        print "<script>alert('" . translate('passwordMismatch') . "');</script>";
        return false;
    }
    $username = str_replace("'", "''", $settingArray['oldPassword']);
    $newUsername = str_replace("'", "''", $settingArray['newPassword']);
    $query = "select count(*) as NR from jelentkezok where username = '$username' and ID = " . (int)$userId;

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['NR'];
    }
    if($number < 1){
        print "<script>alert('" . translate('origPwBad') . "');</script>";
        return false;
    }

    $query = "update jelentkezok set username = '$newUsername' where ID = " . (int)$userId;

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        return false;
    }
    return true;
}

function getSubChaptersForStories()
{
    $query = "SELECT b2.name as subChapterName, b2.ID as subChapterID, b2.done, b2.updti, b2.concept
                FROM book b2
                where b2.done in (99)
                order by b2.name";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $resultArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $resultArray[] = $row;
    }
    return $resultArray;
}

function updateLearningQuestion($storeArray)
{
    $fields['name_eng'] = "'" . $storeArray['name'] . "'";
    $fields['concept_eng'] = "'" . $storeArray['concept'] . "'";
    $fields['imgName'] = "'" . $storeArray['imgName'] . "'";
    $fields['imgPath'] = "'" . $storeArray['imgPath'] . "'";
    $fields['updti'] = $storeArray['updti'];

    $sqlString = array();
    foreach($fields as $key => $value){
        $sqlString[] = "$key = $value";
    }
    $sql = "update book set " . implode(', ', $sqlString) . "
                where ID = " . (int)$storeArray['ID'];
    $result = mysql_query($sql);
    if(!$result){
        print mysql_error();
        return false;
    }
    
    if(array_key_exists('exercises', $storeArray)){
        $sql = "delete from question_relations where from_id = " . (int)$storeArray['ID'] . " or to_id = " . (int)$storeArray['ID'];
        $result = mysql_query($sql);
        if(!$result){
            print mysql_error();
            return false;
        }
        foreach((array)$storeArray['exercises'] as $currentId){
            $sql = "insert into question_relations (from_id, to_id) values(" . (int)$storeArray['ID'] . ", $currentId)";
            $result = mysql_query($sql);
            if(!$result){
                print mysql_error();
                return false;
            }
        }
    }
    return true;
}

function updateStory($storeArray)
{
    $fields['story_name'] = "'" . $storeArray['story_name'] . "'";
    $fields['story'] = "'" . $storeArray['story'] . "'";
    $fields['story_name_eng'] = "'" . $storeArray['story_name_eng'] . "'";
    $fields['story_eng'] = "'" . $storeArray['story_eng'] . "'";
    $fields['story_name_gre'] = "'" . $storeArray['story_name_gre'] . "'";
    $fields['story_gre'] = "'" . $storeArray['story_gre'] . "'";
    $fields['stay_category'] = $storeArray['stay_category'];

    $sqlString = array();
    foreach($fields as $key => $value){
        $sqlString[] = "$key = $value";
    }
    $sql = "update success_stories set " . implode(', ', $sqlString) . "
                where ID = " . (int)$storeArray['ID'];

    $result = mysql_query($sql);
    if(!$result){
        print mysql_error();
        return false;
    }

    if(array_key_exists('exercises', $storeArray)){
        $sql = "select ID from book where done = 90";
        $result = mysql_query($sql);
        if(!$result){
            print mysql_error();
            return true;
        }
        $ids = array(0);
        while($row = mysql_fetch_row($result)) {
            $ids[] = $row[0];
        }

        $sql = "delete from success_story_exercises where exercise_id in (" . implode(',', $ids) . ") and success_stories_id = " . (int)$storeArray['ID'];

        $result = mysql_query($sql);
        if(!$result){
            print mysql_error();
            return false;
        }
        foreach((array)$storeArray['exercises'] as $currentId){
            $sql = "insert into success_story_exercises (success_stories_id, exercise_id) values(" . (int)$storeArray['ID'] . ", " . (int)$currentId . ")";

            $result = mysql_query($sql);
            if(!$result){
                print mysql_error();
                return false;
            }
        }
    }

    return true;
}

function insertStory($storeArray)
{
    $fields['story_name'] = "'" . $storeArray['story_name'] . "'";
    $fields['story'] = "'" . $storeArray['story'] . "'";
    $fields['story_name_eng'] = "'" . $storeArray['story_name_eng'] . "'";
    $fields['story_eng'] = "'" . $storeArray['story_eng'] . "'";
    $fields['story_name_gre'] = "'" . $storeArray['story_name_gre'] . "'";
    $fields['story_gre'] = "'" . $storeArray['story_gre'] . "'";
    $fields['crdti'] = $storeArray['updti'];
    $fields['stay_category'] = $storeArray['stay_category'];

    $sql = "insert into success_stories (" . implode(', ', array_keys($fields)) . ") values (" . implode(', ', array_values($fields)) . ")";
//print $sql;
    $result = mysql_query($sql);
    if(!$result){
        print mysql_error();
        return false;
    }
    $query = "select ID
                from success_stories
                where story_name = {$fields['story_name']} ";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        return true;
    }
    while($row = mysql_fetch_assoc($result)) {
        $id = $row['ID'];
    }

    foreach((array)$storeArray['exercises'] as $currentId){
        $sql = "insert into success_story_exercises (success_stories_id, exercise_id) values(" . (int)$id . ", " . (int)$currentId . ")";

        $result = mysql_query($sql);
        if(!$result){
            print mysql_error();
            return false;
        }
    }

    return array($id);
}

function deleteStory($id)
{
    $sql = "delete from success_stories where ID = " . (int)$id;
//print $sql;

    $result = mysql_query($sql);
    if(!$result){
        print mysql_error();
        return false;
    }

    $sql = "delete from success_story_exercises where success_stories_id = " . (int)$id;
    $result = mysql_query($sql);
    if(!$result){
        print mysql_error();
        return false;
    }

    return true;
}

function updateIdezet($storeArray, $categoryPointer = 99)
{
    if(array_key_exists('idezet', $storeArray)){
        $storeArray['idezet'] = str_replace("\\'", "'", $storeArray['idezet']);
        $storeArray['idezet'] = str_replace("\\\"", "\"", $storeArray['idezet']);
        $fields['idezet'] = "'" . str_replace("'", "''", $storeArray['idezet']) . "'";
    }
    if(array_key_exists('idezet_eng', $storeArray)){
        $storeArray['idezet_eng'] = str_replace("\\'", "'", $storeArray['idezet_eng']);
        $storeArray['idezet_eng'] = str_replace("\\\"", "\"", $storeArray['idezet_eng']);
        $fields['idezet_eng'] = "'" . str_replace("'", "''", $storeArray['idezet_eng']) . "'";
    }
    if(array_key_exists('idezet_ger', $storeArray)){
        $storeArray['idezet_ger'] = str_replace("\\'", "'", $storeArray['idezet_ger']);
        $storeArray['idezet_ger'] = str_replace("\\\"", "\"", $storeArray['idezet_ger']);
        $fields['idezet_ger'] = "'" . str_replace("'", "''", $storeArray['idezet_ger']) . "'";
    }
    if(array_key_exists('idezet_gre', $storeArray)){
        $storeArray['idezet_gre'] = str_replace("\\'", "'", $storeArray['idezet_gre']);
        $storeArray['idezet_gre'] = str_replace("\\\"", "\"", $storeArray['idezet_gre']);
        $fields['idezet_gre'] = "'" . str_replace("'", "''", $storeArray['idezet_gre']) . "'";
    }
    if(array_key_exists('szerzo', $storeArray)){
        $storeArray['szerzo'] = str_replace("\\'", "'", $storeArray['szerzo']);
        $storeArray['szerzo'] = str_replace("\\\"", "\"", $storeArray['szerzo']);
        $fields['szerzo'] = "'" . str_replace("'", "''", $storeArray['szerzo']) . "'";
    }

    if(array_key_exists('szerzo_eng', $storeArray)){
        $storeArray['szerzo_eng'] = str_replace("\\'", "'", $storeArray['szerzo_eng']);
        $storeArray['szerzo_eng'] = str_replace("\\\"", "\"", $storeArray['szerzo_eng']);
        $fields['szerzo_eng'] = "'" . str_replace("'", "''", $storeArray['szerzo_eng']) . "'";
    }
    else if(array_key_exists('szerzo', $storeArray)){
        $fields['szerzo_eng'] = $fields['szerzo'];
    }
    if(array_key_exists('szerzo_ger', $storeArray)){
        $storeArray['szerzo_ger'] = str_replace("\\'", "'", $storeArray['szerzo_ger']);
        $storeArray['szerzo_ger'] = str_replace("\\\"", "\"", $storeArray['szerzo_ger']);
        $fields['szerzo_ger'] = "'" . str_replace("'", "''", $storeArray['szerzo_ger']) . "'";
    }
    else if(array_key_exists('szerzo_gre', $storeArray)){
        $storeArray['szerzo_gre'] = str_replace("\\'", "'", $storeArray['szerzo_gre']);
        $storeArray['szerzo_gre'] = str_replace("\\\"", "\"", $storeArray['szerzo_gre']);
        $fields['szerzo_gre'] = "'" . str_replace("'", "''", $storeArray['szerzo_gre']) . "'";
    }
    else if(array_key_exists('szerzo_eng', $storeArray)){
        $fields['szerzo_ger'] = $fields['szerzo_eng'];
    }
    else if(array_key_exists('szerzo', $storeArray)){
        $fields['szerzo_ger'] = $fields['szerzo'];
    }

    if(array_key_exists('quote_title', $storeArray)){
        $storeArray['quote_title'] = str_replace("\\'", "'", $storeArray['quote_title']);
        $storeArray['quote_title'] = str_replace("\\\"", "\"", $storeArray['quote_title']);
        $fields['quote_title'] = "'" . str_replace("'", "''", $storeArray['quote_title']) . "'";
    }
    if(array_key_exists('chapter', $storeArray)){
        if((int)$storeArray['chapter'] > 0){
            $fields['book_chapter'] = (int)$storeArray['chapter'];
        }
        else{
            $fields['book_chapter'] = 'null';
        }
    }

    if(array_key_exists('kikuldve', $storeArray)){
        $fields['kikuldve'] = (int)$storeArray['kikuldve'];
        if($fields['kikuldve'] == 0){
            $fields['datum'] = '0000-00-00';
        }
    }
    if(array_key_exists('health', $storeArray)){
        $fields['health'] = (int)$storeArray['health'];
    }

    $sqlString = array();
    if(is_array($fields)){
        foreach($fields as $key => $value){
            $sqlString[] = "$key = $value";
        }
        $sql = "update idezet set " . implode(', ', $sqlString) . "
                    where ID = " . (int)$storeArray['ID'];
        $result = mysql_query($sql);
        if(!$result){
            print mysql_error();
            return false;
        }
    }

    $sql = "select ID from book where done = {$categoryPointer}";
    $result = mysql_query($sql);
    if(!$result){
        print mysql_error();
        return true;
    }
    $ids = array(0);
    while($row = mysql_fetch_row($result)) {
        $ids[] = $row[0];
    }


    if(array_key_exists('categories', $storeArray)){
        $sql = "delete from idezet_categories where category_id in (" . implode(',', $ids) . ") and idezet_id = " . (int)$storeArray['ID'];

        $result = mysql_query($sql);
        if(!$result){
            print mysql_error();
            return false;
        }
        foreach((array)$storeArray['categories'] as $currentId){
            $sql = "insert into idezet_categories (idezet_id, category_id) values(" . (int)$storeArray['ID'] . ", " . (int)$currentId . ")";

            $result = mysql_query($sql);
            if(!$result){
                print mysql_error();
                return false;
            }
        }
    }
    return true;
}

function insertIdezet($storeArray)
{
    $storeArray['idezet'] = str_replace("\\'", "'", $storeArray['idezet']);
    $storeArray['idezet'] = str_replace("\\\"", "\"", $storeArray['idezet']);
    $storeArray['idezet_eng'] = str_replace("\\'", "'", $storeArray['idezet_eng']);
    $storeArray['idezet_eng'] = str_replace("\\\"", "\"", $storeArray['idezet_eng']);
    $storeArray['idezet_ger'] = str_replace("\\'", "'", $storeArray['idezet_ger']);
    $storeArray['idezet_ger'] = str_replace("\\\"", "\"", $storeArray['idezet_ger']);
    $storeArray['idezet_gre'] = str_replace("\\'", "'", $storeArray['idezet_gre']);
    $storeArray['idezet_gre'] = str_replace("\\\"", "\"", $storeArray['idezet_gre']);
    $storeArray['szerzo'] = str_replace("\\'", "'", $storeArray['szerzo']);
    $storeArray['szerzo'] = str_replace("\\\"", "\"", $storeArray['szerzo']);
    $storeArray['szerzo_eng'] = str_replace("\\'", "'", $storeArray['szerzo_eng']);
    $storeArray['szerzo_eng'] = str_replace("\\\"", "\"", $storeArray['szerzo_eng']);
    $storeArray['szerzo_ger'] = str_replace("\\'", "'", $storeArray['szerzo_ger']);
    $storeArray['szerzo_ger'] = str_replace("\\\"", "\"", $storeArray['szerzo_ger']);
    $storeArray['szerzo_gre'] = str_replace("\\'", "'", $storeArray['szerzo_gre']);
    $storeArray['szerzo_gre'] = str_replace("\\\"", "\"", $storeArray['szerzo_gre']);

    $fields['idezet'] = "'" . str_replace("'", "''", $storeArray['idezet']) . "'";
    $fields['idezet_eng'] = "'" . str_replace("'", "''", $storeArray['idezet_eng']) . "'";
    $fields['idezet_ger'] = "'" . str_replace("'", "''", $storeArray['idezet_ger']) . "'";
    $fields['idezet_gre'] = "'" . str_replace("'", "''", $storeArray['idezet_gre']) . "'";
    $fields['szerzo'] = "'" . str_replace("'", "''", $storeArray['szerzo']) . "'";
    $fields['szerzo_eng'] = "'" . str_replace("'", "''", $storeArray['szerzo_eng']) . "'";
    $fields['szerzo_ger'] = "'" . str_replace("'", "''", $storeArray['szerzo_ger']) . "'";
    $fields['szerzo_gre'] = "'" . str_replace("'", "''", $storeArray['szerzo_gre']) . "'";
    if($fields['szerzo_eng'] == "''"){
        $fields['szerzo_eng'] = $fields['szerzo'];
    }
    if($fields['szerzo_ger'] == "''"){
        $fields['szerzo_ger'] = $fields['szerzo_eng'];
    }
    if($fields['szerzo_gre'] == "''"){
        $fields['szerzo_gre'] = $fields['szerzo_eng'];
    }
    if(array_key_exists('kikuldve', $storeArray)){
        $fields['kikuldve'] = (int)$storeArray['kikuldve'];
    }
    if(array_key_exists('health', $storeArray)){
        $fields['health'] = (int)$storeArray['health'];
    }
    if(array_key_exists('quote_title', $storeArray)){
        $storeArray['quote_title'] = str_replace("\\'", "'", $storeArray['quote_title']);
        $storeArray['quote_title'] = str_replace("\\\"", "\"", $storeArray['quote_title']);
        $fields['quote_title'] = "'" . str_replace("'", "''", $storeArray['quote_title']) . "'";
    }
    if(array_key_exists('chapter', $storeArray)){
        if((int)$storeArray['chapter'] > 0){
            $fields['book_chapter'] = (int)$storeArray['chapter'];
        }
        else{
            $fields['book_chapter'] = 'null';
        }
    }

    $sql = "insert into idezet (" . implode(', ', array_keys($fields)) . ") values (" . implode(', ', array_values($fields)) . ")";
    $result = mysql_query($sql);
    if(!$result){
        print mysql_error();
        return false;
    }
    $query = "select ID from idezet where idezet = {$fields['idezet']}";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        return true;
    }
    while($row = mysql_fetch_assoc($result)) {
        $id = $row['ID'];
    }

    foreach((array)$storeArray['categories'] as $currentId){
        $sql = "insert into idezet_categories (idezet_id, category_id) values(" . (int)$id . ", " . (int)$currentId . ")";

        $result = mysql_query($sql);
        if(!$result){
            print mysql_error();
            return false;
        }
    }

    return array($id);
}

function deleteIdezet($id)
{
    $sql = "delete from idezet where ID = " . (int)$id;

    $result = mysql_query($sql);
    if(!$result){
        print mysql_error();
        return false;
    }

    return true;
}

function updateRuin($storeArray)
{
    if(array_key_exists('ruin', $storeArray)){
        $storeArray['ruin'] = str_replace("\\'", "'", $storeArray['ruin']);
        $storeArray['ruin'] = str_replace("\\\"", "\"", $storeArray['ruin']);
        $fields['ruin'] = "'" . str_replace("'", "''", $storeArray['ruin']) . "'";
    }
    if(array_key_exists('category', $storeArray)){
        $fields['category'] = $storeArray['category'];
    }

    $sqlString = array();
    if(is_array($fields)){
        foreach($fields as $key => $value){
            $sqlString[] = "$key = $value";
        }
        $sql = "update ruin set " . implode(', ', $sqlString) . "
                    where ID = " . (int)$storeArray['ID'];

        $result = mysql_query($sql);
        if(!$result){
            print mysql_error();
            return false;
        }
    }
    /*
    $sql = "select ID from book where done = 90";
    $result = mysql_query($sql);
    if(!$result){
        print mysql_error();
        return true;
    }
    $ids = array(0);
    while($row = mysql_fetch_row($result)) {
        $ids[] = $row[0];
    }
    */
    if(array_key_exists('exercises', $storeArray)){
        $sql = "delete from ruin_exercises where ruin_id = " . (int)$storeArray['ID'];

        $result = mysql_query($sql);
        if(!$result){
            print mysql_error();
            return false;
        }
        foreach((array)$storeArray['exercises'] as $currentId){
            $sql = "insert into ruin_exercises (ruin_id, exercise_id) values(" . (int)$storeArray['ID'] . ", " . (int)$currentId . ")";

            $result = mysql_query($sql);
            if(!$result){
                print mysql_error();
                return false;
            }
        }
    }
}

function insertRuin($storeArray)
{
    $storeArray['ruin'] = str_replace("\\'", "'", $storeArray['ruin']);
    $storeArray['ruin'] = str_replace("\\\"", "\"", $storeArray['ruin']);
    $fields['ruin'] = "'" . str_replace("'", "''", $storeArray['ruin']) . "'";
    $fields['category'] = $storeArray['category'];

    $sql = "insert into ruin (" . implode(', ', array_keys($fields)) . ") values (" . implode(', ', array_values($fields)) . ")";
    $result = mysql_query($sql);
    if(!$result){
        print mysql_error();
        return false;
    }
    $query = "select ID from ruin where ruin = {$fields['ruin']}";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        return true;
    }
    while($row = mysql_fetch_assoc($result)) {
        $id = $row['ID'];
    }

    foreach((array)$storeArray['exercises'] as $currentId){
        $sql = "insert into ruin_exercises (ruin_id, exercise_id) values(" . (int)$id . ", " . (int)$currentId . ")";

        $result = mysql_query($sql);
        if(!$result){
            print mysql_error();
            return false;
        }
    }
    return $id;
}

function deleteRuin($id)
{
    $sql = "delete from ruin_exercises where ruin_id = " . (int)$id;
    $result = mysql_query($sql);
    if(!$result){
        print mysql_error();
        return false;
    }

    $sql = "delete from ruin where id = " . (int)$id;
    $result = mysql_query($sql);
    if(!$result){
        print mysql_error();
        return false;
    }
}


function getNapidezeteNumber()
{
    $query = "select count(*) as nr from idezet where kikuldve = 0";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}


function getNapidezeteNumber1()
{
    $query = "SELECT count(*) as nr
                FROM book b1
                inner join book b2 on b2.ID = b1.ref_ID and b2.ref_ID is null
                where b1.ref_ID is not null and b1.concept_eng like '%<img%' and b1.done = 96";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getNapidezeteNumber2()
{
    $query = "SELECT count(*) as nr
                FROM book b1
                inner join book b2 on b2.ID = b1.ref_ID and b2.ref_ID is null
                where b1.ref_ID is not null and b1.concept_eng like '%<img%' and b1.done = 96 and (b1.is_silence_message_sent is null or b1.is_silence_message_sent != 1)";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getNapidezeteNumber3()
{
    $query = "SELECT count(*) as nr
                FROM book b1
                inner join book b2 on b2.ID = b1.ref_ID and b2.ref_ID is null
                where b1.ref_ID is not null and b1.done = 94";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getNapidezeteTotalNumber()
{
    $query = "select count(*) as nr from idezet where kikuldve = 1";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getJelentkezokNumber()
{
    $query = "select count(*) as nr from jelentkezok";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getJelentkezokNumberAngol()
{
    $query = "select count(*) as nr from jelentkezok where language = 2";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}


function getJelentkezokNumberAkiknekVanPontjuk()
{
    $query = "select count(*) as nr from jelentkezok where expire_number != 0";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}


function getAlfejezetNumber()
{
    $query = "select count(*) as nr from book where done = 90 or done = 91 or done = 92 or done = 93 or done = 94 or done = 95 or done = 96";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}


function getAlfejezetNumberGoodforme()
{
    $query = "select count(*) as nr from book where done = 91";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getAlfejezetNumberKorrektura()
{
    $query = "select count(*) as nr from book where done = 92";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getAlfejezetNumberAngolraLeforditva()
{
    $query = "select count(*) as nr from book where done = 93";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getAlfejezetNumberAngolcheckkolva()
{
    $query = "select count(*) as nr from book where done = 94";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getAlfejezetNumberGorogLeforditva()
{
    $query = "select count(*) as nr from book where done = 95";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}


function getAlfejezetNumberGorogcheckkolva()
{
    $query = "select count(*) as nr from book where done = 96";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getAlfejezetNumberConceptZero()
{
    $query = "select count(*) as nr from book where done = 90 and concept= ' '";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}


function getAlfejezetNumberConceptSomething()
{
    $query = "select count(*) as nr from book where done = 90 and concept!= ' '";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getAlfejezetNumberExerciseZero()
{
    $query = "select count(*) as nr from book where done = 90 and concept!= ' ' and exercise is null";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getJelentkezokNumberNemet()
{
    $query = "select count(*) as nr from jelentkezok where language = 3";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getJelentkezokNumberGraduates()
{
    $query = "select count(*) as nr from jelentkezok where status = 4";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getReferenceNumberReference()
{
    $query = "select count(*) as nr from jelentkezok where reference = 1";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getReferenceNumberSearchengine()
{
    $query = "select count(*) as nr from jelentkezok where reference = 2";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getReferenceNumberBanner()
{
    $query = "select count(*) as nr from jelentkezok where reference = 3";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getReferenceNumberByChance()
{
    $query = "select count(*) as nr from jelentkezok where reference = 4";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getReferenceNumberByKezdjelelni()
{
    $query = "select count(*) as nr from jelentkezok where reference = 5";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getReferenceNumberByIwiw()
{
    $query = "select count(*) as nr from jelentkezok where reference = 9";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getRuinNumber()
{
    $query = "select count(*) as nr from ruin";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getSuccessStoryNumber()
{
    $query = "select count(*) as nr from success_stories where book_ID != 0 ";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getDalNumber()
{
    $query = "select count(*) as nr from linker where type = 1 ";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}


function getUnsentNapidezeteSzerzok()
{
    $query = "select szerzo from idezet where kikuldve = 0 order by ID desc";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $szerzok = array();
    while($row = mysql_fetch_assoc($result)) {
        $szerzok[] = $row['szerzo'];
    }
    return $szerzok;
}

/**
 * Sends mail to the e-mail address given.
 * Supports attaching files and multiple encodings.
 * @param string The email address of the recipient
 * @param string The name to include in the from header
 * @param string The e-mail address to include in the from header
 * @param string the e-mail subject
 * @param string the e-mail body.
 * @param boolean true if the body is html or false if plain text.
 * @param string a path to a file on the server to attach to the e-mail. Null or an empty string indicates that there is no attachment.
 * @param string the encoding with which to encode the subject, from, and body.
 */
function SendMail($emailaddress, $from, $fromaddress, $emailsubject="", $body="", $html = true, $attachment="", $fileArray = null, $encoding="iso-8859-2")
{//{{{
  # Is the OS Windows or Mac or Linux
  if (strtoupper(substr(PHP_OS,0,3)=='WIN')) {
    $eol="
";
  } elseif (strtoupper(substr(PHP_OS,0,3)=='MAC')) {
    $eol="\r";
  } else {
    $eol="\n";
  }

  //set subject encoding
  if (!empty($emailsubject)) {
    //$emailsubject = encode_mail_string($emailsubject, $encoding);
  }
  //$from = encode_mail_string($from, $encoding);
  if ($encoding != "utf-8" && !empty($body)) {
    //$body = mb_convert_encoding($body, $encoding, "utf-8");
  }

  $msg = "";

  # Common Headers
  $headers .= "From: ".$from." <".$fromaddress.">".$eol;
  $headers .= "Reply-To: ".$from." <".$fromaddress.">".$eol;
  $headers .= "Return-Path: ".$from." <".$fromaddress.">".$eol;    // these two to set reply address
  $headers .= "Message-ID: <".time()." TheSystem@".$_SERVER['SERVER_NAME'].">".$eol;
  $headers .= "X-Mailer: PHP v".phpversion().$eol;          // These two to help avoid spam-filters
  $headers .= 'MIME-Version: 1.0'.$eol;

  if (!empty($attachment)) {
    //send multipart message
    # Boundry for marking the split & Multitype Headers
    $mime_boundary=md5(time());
    $headers .= "Content-Type: multipart/related; boundary=\"".$mime_boundary."\"".$eol;

    # File for Attachment

    if(!$fileArray){
        $f_name = $attachment;
        $handle=fopen($f_name, 'rb');
        $f_contents=fread($handle, filesize($f_name));
        $f_contents=chunk_split(base64_encode($f_contents));//Encode The Data For Transition using base64_encode();
        $f_type=filetype($f_name);
        fclose($handle);
    }
    else{
        $f_name = $fileArray['f_name'];
        $f_contents = $fileArray['f_contents'];
        $f_type = $fileArray['f_type'];
    }

    # Attachment
    $msg .= "--".$mime_boundary.$eol;
    $msg .= "Content-Type: application/jpeg; name=\"".$file."\"".$eol;
    $msg .= "Content-Transfer-Encoding: base64".$eol;
    $msg .= "Content-Disposition: attachment; filename=\"".basename($attachment)."\"".$eol.$eol; // !! This line needs TWO end of lines !! IMPORTANT !!
    $msg .= $f_contents.$eol.$eol;
    # Setup for text OR html
    $msg .= "Content-Type: multipart/alternative".$eol;


    $contentType = "text/plain";
    if ($html) {
      $contentType = "text/html";
    }

    # Body
    $msg .= "--".$mime_boundary.$eol;
    $msg .= "Content-Type: ".$contentType."; charset=\"".$encoding."\"".$eol;
    $msg .= "Content-Transfer-Encoding: 8bit".$eol.$eol; // !! This line needs TWO end of lines !! IMPORTANT !!
    $msg .= $body.$eol.$eol;

    # Finished
    $msg .= "--".$mime_boundary."--".$eol.$eol;  // finish with two eol's for better security. see Injection.
  } else {
    $headers .= "Content-Type: text/plain; charset=\"".$encoding."\"".$eol;
    $headers .= "Content-Transfer-Encoding: 8bit".$eol.$eol; // !! This line needs TWO end of lines !! IMPORTANT !!
    $msg .= $body.$eol.$eol;
  }

  // SEND THE EMAIL
  //LogMessage("Sending mail to: ".$emailaddress." => ".$emailsubject);

  //ini_set(sendmail_from, 'from@me.com');  // the INI lines are to force the From Address to be used !
  ini_set(sendmail_from, $fromaddress); //needed to hopefully get by spam filters.
  $success = mail($emailaddress, $emailsubject, $msg, $headers);
  ini_restore(sendmail_from);

  return $success;
}//}}}

function sendAttachmentMail($subject="", $body="", $attachment="", $lastSentId, $maxLetters)
{
    $to = 'hello@luciendelmar.com';
    $allUsers = /*getTestUsers($lastSentId)*/ getSubscribedUsers($lastSentId);

    if($attachment){
        $fileArray['f_name']= $f_name = $attachment;
        $handle=fopen($f_name, 'rb');
        $f_contents=fread($handle, filesize($f_name));
        $fileArray['f_contents']=chunk_split(base64_encode($f_contents));//Encode The Data For Transition using base64_encode();
        $fileArray['f_type']=filetype($f_name);
        fclose($handle);
    }

    $success = true;
    if($maxLetters > 0){
        $maxLetters = min(count($allUsers), $maxLetters);
    }
    else{
        $maxLetters = count($allUsers);
    }
    for($i = 0; $i < $maxLetters; $i++){
        $body2 = "Szia {$allUsers[$i]['keresztnev']}!" . chr(13) . chr(10) . chr(13) . chr(10) . $body;
        $body2 = str_replace(chr(13) . chr(10), "<br>", $body2);
        if(!SendMail($allUsers[$i]['email'], 'active-silence', 'hello@luciendelmar.com', $subject, $body2, true, $attachment, $fileArray)){
            $success = false;
        }
    }
    print "<script>";
    /*
    if($success){
        print "alert('Üzenet küldése sikerült!')";
    }
    else{
        print "alert('Üzenet küldése nem sikerült!')";
    }
    */
    print "</script>";
    return array($allUsers[$i - 1]['ID'], $i);
}

function getCategories($orderType = 0)
{
    if($orderType == 0){
        $orderString = "b.name";
    }
    else{
        $orderString = "quotNum";
    }
    $query = "select b.ID, b.name, b.ref_ID, count(i.ID) as quotNum
                from book b
                left outer join idezet_categories i on i.category_id = b.ID
                where b.done = 99
                group by b.ID, b.name, b.ref_ID
                order by $orderString asc";
//    $query = "select ID, name, ref_ID from book where done = 99 order by name";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_row($result)) {
        $returnArray[$row[0]] = array($row[1], $row[2], $row[3]);
    }
    return $returnArray;
}

function getExercises()
{
	$query = "select b1.ID, b1.name, b1.name_eng, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng, b2.concept, b2.concept_eng, b2.exercise, b2.exercise_eng, b2.done as subDone, b1.done
                from book b1
                left outer join book b2 on b1.ID = b2.ref_id
                where b1.done = 90 AND (b2.done = 98 or b2.done = 99 or b2.done = 17)
                order by b1.order, b2.order";
	/*			
    $query = "select b1.ID, b1.name, b1.name_eng, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng, b2.concept, b2.concept_eng, b2.exercise, b2.exercise_eng, b2.done as subDone
                from book b1
                left outer join book b2 on b1.ID = b2.ref_id
                where b1.done = 90 and (b2.done = 90 or b2.done = 91 or b2.done = 92 or b2.done = 93 or b2.done = 94 or b2.done = 95 or b2.done = 96)
                order by b1.order, b2.order";
				*/
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getExercises91()
{
    $query = "select b1.ID, b1.name, b1.name_eng, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng, b2.concept, b2.concept_eng, b2.exercise, b2.exercise_eng, b2.done as subDone
                from book b1
                left outer join book b2 on b1.ID = b2.ref_id
                where b1.done = 90 and b2.done = 91
                order by b1.order, b2.order";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getExercises95()
{
    $query = "select b1.ID, b1.name, b1.name_eng, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng, b2.concept, b2.concept_eng, b2.exercise, b2.exercise_eng, b2.done as subDone,
                    b2.crdti
                from book b1
                left outer join book b2 on b1.ID = b2.ref_id
                where b1.done = 90 and b2.done = 95
                order by b1.order, b2.order";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_assoc($result)) {
        list($row['crdti']) = explode(' ', $row['crdti']);
        $row['crdti'] = str_replace('-', '.', $row['crdti']);
        $returnArray[] = $row;
    }
    return $returnArray;
}


function getLearningQuestions()
{
    $query = "select b1.ID, b1.name, b1.name_eng, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng, b2.concept, b2.concept_eng,
                    b2.exercise, b2.exercise_eng, b2.done as subDone, b2.imgName, b2.imgPath
                from book b1
                left outer join book b2 on b1.ID = b2.ref_id
                where b1.done = 90 and (b2.done = 92 or b2.done = 94)
                order by b1.order, b2.order";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getLearningQuestions2()
{
    $query = "select b1.ID, b1.name, b1.name_eng, b1.name_si, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng, b2.name_si as sub_name_si
                    , b2.concept, b2.concept_eng, b2.concept_si
                    , b2.exercise, b2.exercise_eng, b2.exercise_si, b2.done as subDone, b2.imgName
                from book b1
                left outer join book b2 on b1.ID = b2.ref_id
                where b1.done = 90 and b2.done = 94
                order by b1.order, b2.order";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getExercisesOrderBySubchapter()
{
    $query = "select b1.ID, b1.name, b1.name_eng, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng, b2.concept, b2.concept_eng, b2.exercise, b2.exercise_eng, b2.done as subDone
                from book b1
                left outer join book b2 on b1.ID = b2.ref_id
                where b1.done = 90 and (b2.done = 90 or b2.done = 91 or b2.done = 92 or b2.done = 93 or b2.done = 94 or b2.done = 95 or b2.done = 96)
                order by b2.name";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getForumEntryNumbersByBooks($lang)
{
    $query = "select b.id, count(*) as nr from book b inner join forum f on b.id = f.book_id and f.language = " . (int)$lang . " group by b.id";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_row($result)) {
        $returnArray[$row[0]] = $row[1];
    }
    return $returnArray;
}

function getExercisesWithLucienQuotes()
{
    $query = "select b1.ID, b1.name, b1.name_eng, b1.name_gre, i.quote_title, i.datum, i.idezet
                from book b1
                left outer join idezet i on i.book_chapter = b1.id
                where b1.done = 90 and lcase(i.szerzo) = 'lucien del mar'
                order by b1.order";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getBlissEvents()
{
    $query = "select b1.ID, b1.name, b1.name_eng, b1.name_gre, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng, b2.name_gre as sub_name_gre
                from book b1
                left outer join book b2 on b1.ID = b2.ref_id
                where b1.done = 10 and (b2.done = 10 or b2.done = 12)
                order by b1.ID, b1.name, b2.name";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getBlissEventsPublic()
{
    $query = "select b1.ID, b1.name, b1.name_eng, b1.name_gre, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng, b2.name_gre as sub_name_gre
                from book b1
                left outer join book b2 on b1.ID = b2.ref_id
                where b1.done = 10 and b2.done = 10
                order by b1.order, b2.order";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}


function getExercisesToBeConceptized()
{
    $query = "select b1.ID, b1.name, b2.ID as sub_ID, b2.name as sub_name
                from book b1
                left outer join book b2 on b1.ID = b2.ref_id
                where b1.done = 90 and b2.done = 90
                order by b1.order, b2.order";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getRuins()
{
    $query = "select b1.ID, b1.name, b2.ID as sub_ID, b2.ruin as sub_name, b1.ord
                from ruin_categories b1
                left outer join ruin b2 on b1.ID = b2.category
                order by b1.ord, b2.ID";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getRuinsByInheritance($parent)
{
    $query = "SELECT distinct * FROM `ruin_categories` WHERE `parentID` = ".$parent." order by `ord` asc";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}
function GetParentRuin($childID)
{
	$query = "SELECT parentID FROM `ruin_categories` WHERE `id` = ".$childID;
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getRuinListByCategoryId($categoryId)
{
	$query = "SELECT ruin as name,ID,category FROM ruin WHERE category = ".$categoryId." order by ID asc";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getExerciseById($id)
{
    $query = "select b1.ID, b1.name, b1.name_eng, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng, b2.concept, b2.concept_eng, b2.exercise, b2.exercise_eng
                from book b1
                left outer join book b2 on b1.ID = b2.ref_id
                where b1.done = 90 and (b2.done = 90 or b2.done = 91 or b2.done = 92 or b2.done = 93 or b2.done = 94 or b2.done = 95 or b2.done = 96)
                and b1.ID = " . (int)$id . "
                order by b1.order, b2.order";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getExercisesByRuinId($id)
{
    $query = "select r.ID, r.ruin as name, b2.ID as sub_ID, b2.name as sub_name, b2.concept, b2.exercise
                from ruin_exercises b1
                left outer join ruin r on b1.ruin_id = r.id
                left outer join book b2 on b1.exercise_id = b2.id
                left outer join book b3 on b2.ref_id = b3.id
                where b1.ruin_id = " . (int)$id . "
                order by b3.order, b2.order";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getCategoriesForShowing()
{
    $query = "select b.ID, b.name, b.ref_ID, count(i.ID) as quotNum
                from book b
                left outer join idezet_categories i on i.category_id = b.ID
                where b.done = 99
                group by b.ID, b.name, b.ref_ID
                order by b.ref_ID, b.order";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $row["subCategories"] = array();
        if($row["ref_ID"] > 0){
            $returnArray[$row["ref_ID"]]["subCategories"][$row["ID"]] = $row;
        }
        else{
            $returnArray[$row["ID"]]["ID"] = $row["ID"];
            $returnArray[$row["ID"]]["name"] = $row["name"];
            $returnArray[$row["ID"]]["ref_ID"] = $row["ref_ID"];
            $returnArray[$row["ID"]]["quotNum"] = $row["quotNum"];
        }
    }
    return $returnArray;
}

function getCategoriesForShowing2()
{
    $nameCol = 'b.name';
    $idezetCol = 'id.idezet';
    if($_SESSION['language'] == 'eng'){
        $nameCol .= '_eng';
        $idezetCol .= '_eng';
    }

    $query = "select b.ID, $nameCol as name, b.ref_ID, count(i.ID) as quotNum
                from book b
                left outer join idezet_categories i on i.category_id = b.ID
                left outer join idezet id on i.idezet_id = id.id
                where b.done = 99 and ref_ID > 0 and $idezetCol != ''
                group by b.ID, $nameCol, b.ref_ID
                order by $nameCol";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getAllCategoryNamesByIds()
{
    $nameCol = 'b.name';
    if($_SESSION['language'] == 'eng'){
        $nameCol .= '_eng';
    }

    $query = "select ID, $nameCol from book b where done = 99";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_row($result)) {
        $returnArray[$row[0]] = $row[1];
    }
    return $returnArray;
}

function getNotCategorizedNumber()
{
    $query = "select count(*) as nr
                from idezet i
                left outer join idezet_categories ic on i.ID = ic.idezet_id
                group by i.ID
                having count(*) < 2";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = mysql_num_rows($result);
    return $number;
}

function getNotGermanTranslatedNumber()
{
    $query = "select count(*) as nr
                from idezet i
                where kikuldve != 1 and (idezet_ger is null or idezet_ger = '')";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    while($row = mysql_fetch_row($result)) {
        $number = $row[0];
    }
    return $number;
}

function getOrszagok()
{
    $query = "select id, title_hun, title_eng, title_ger from orszag";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getRegisteredCountryNumber()
{
    $query = "SELECT count(distinct orszag_id) FROM jelentkezok";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_row($result)) {
        $number = $row[0];
    }
    return $number;
}

function getRegisteredCountries()
{
    $query = "SELECT o.id, o.title_hun, count(*) num
                FROM jelentkezok j
                inner join orszag o on j.orszag_id = o.id
                group by o.title_hun
                order by num desc
                ";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $resultArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $resultArray[] = $row;
    }
    return $resultArray;
}

function getRegisteredHungarianCities()
{
    $query = "SELECT v.id, v.title_hun, count(*) num
                FROM jelentkezok j
                inner join orszag o on j.orszag_id = o.id
                inner join varos v on j.varos_id = v.id
                where o.title_eng = 'Hungary'
                group by v.title_hun
                order by title_hun
                ";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $resultArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $resultArray[] = $row;
    }
    return $resultArray;
}

function getUsersFromCountry($countryId)
{
    $query = "select *
                from jelentkezok
                where orszag_id = " . (int)$countryId . " order by crdti asc";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $resultArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $resultArray[] = $row;
    }
    return $resultArray;
}

function getUsersFromCity($cityId)
{
    $query = "select *
                from jelentkezok
                where varos_id = " . (int)$cityId . " order by crdti asc";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $resultArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $resultArray[] = $row;
    }
    return $resultArray;
}

function getUsersByStatus($status)
{
    $query = "select *
                from jelentkezok
                where status = " . (int)$status . " order by vezeteknev asc, keresztnev asc";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $resultArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $resultArray[] = $row;
    }
    return $resultArray;
}

function getUsersByStatus2($status)
{
    $query = "select j.*, v.title_hun as varos, datediff(now(), program_start_date) as eltelt_napok, datediff(now(), ph_so_date) as ph_so_eltelt_napok,
                    datediff(now(), zold_por_date) as zold_por_eltelt_napok, round(datediff(now(), birth_date) / 365) as eletkor
                from jelentkezok j
                left outer join varos v on j.varos_id = v.id
                where j.status = " . (int)$status . " order by j.program_start_date desc";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $resultArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $resultArray[] = $row;
    }
    return $resultArray;
}

function getInvitorUsers()
{
    $query = "select id, title_hun from varos";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $varosArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $varosArray[$row['id']] = $row['title_hun'];
    }

    $query = "select t1.id, t1.vezeteknev, t1.keresztnev, t1.email, t1.username, t1.is_napidezete, t1.is_news, t1.status,
                            t1.crdti, t1.updti, t1.orszag_id, t1.varos_id, t1.reference, t1.course, t1.language, t1.inviter, count(*) as num
                from jelentkezok t1
                inner join jelentkezok t2 on t1.id = t2.inviter
                group by t1.id, t1.vezeteknev, t1.keresztnev, t1.email, t1.username, t1.is_napidezete, t1.is_news, t1.status,
                            t1.crdti, t1.updti, t1.orszag_id, t1.varos_id, t1.reference, t1.course, t1.language, t1.inviter
                order by num desc";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $resultArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $row['varos'] = $varosArray[$row['varos_id']];
        $resultArray[] = $row;
    }
    return $resultArray;
}

function saveTestUserSubchapterId($userId, $subChapterId)
{
    $query = "update jelentkezok set test_subchapter_id = " . (int)$subChapterId . " where ID = " . (int)$userId;
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
}

function getOrszagById($id)
{
    $query = "select title_hun from orszag where id = " . (int)$id;
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $return = '';
    while($row = mysql_fetch_row($result)) {
        $return = $row[0];
    }
    return $return;
}

function getVarosById($id)
{
    $query = "select title_hun from varos where id = " . (int)$id;
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $return = '';
    while($row = mysql_fetch_row($result)) {
        $return = $row[0];
    }
    return $return;
}

function getReferences()
{
    $query = "select id, title_hun, title_eng, title_ger from reference";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function translate($word)
{
    global $trans;
    if(array_key_exists($word, (array)$trans)){
        return $trans[$word];
    }
    else{
        return $word;
    }
}

function translateDb($hunText, $engText, $gerText, $siText)
{
    global $_SESSION;
    if($_SESSION['language'] == 'eng'){
        return $engText;
    }
    else if($_SESSION['language'] == 'ger'){
        return $gerText;
    }
    else if($_SESSION['language'] == 'gr'){
        return $greekText;
    }
    else if($_SESSION['language'] == 'si'){
        return $siText;
    }
    else{
        return $hunText;
    }
}

function setAsFavouriteQuote($userId, $quoteId)
{
    if($userId == 0 or $quoteId == 0){
        return false;
    }
    $query = "SELECT count(*) as num FROM jelentkezok_kedvenc k
                where k.jelentkezok_id = $userId and k.table_name = 'idezet' and k.table_id = $quoteId";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    while($row = mysql_fetch_row($result)) {
        $num = $row[0];
    }
    if($num > 0){
        return -1;
    }

    $sql = "insert into jelentkezok_kedvenc (jelentkezok_id, table_name, table_id) values ($userId, 'idezet', $quoteId)";
    $result = mysql_query($sql);
    if(!$result){
        print mysql_error();
        return false;
    }
}

function getMyFavourites($userId)
{
    $idezetCol = 'idezet';
    $szerzoCol = 'szerzo';
    if($_SESSION['language'] == 'eng'){
        $suffix = '_eng';
    }
    else if($_SESSION['language'] == 'ger'){
        $suffix = '_ger';
    }
    $idezetCol .= $suffix;
    $szerzoCol .= $suffix;

    $query = "SELECT i.$idezetCol as idezet, i.$szerzoCol as szerzo, i.ID, i.DATUM, k.ID as kedvencek_id
                FROM idezet i
                inner join jelentkezok_kedvenc k on k.table_id = i.id and table_name = 'idezet' and k.jelentkezok_id = " . (int)$userId . "
                and i.$idezetCol is not null and i.$idezetCol != ''
                order by k.id desc";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $resultArray = array();
    while($row = mysql_fetch_assoc($result)) {
        list($row['DATUM']) = explode(' ', $row['DATUM']);
        $resultArray[] = $row;
    }
    return $resultArray;
}

function deleteFavourite($id)
{
    $query = "delete from jelentkezok_kedvenc where id = " . (int)$id;
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        return false;
    }
    return true;
}

function getFavouritesNumber()
{
    $query = "SELECT count(*) as num FROM jelentkezok_kedvenc k
                where k.table_name = 'idezet'";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    while($row = mysql_fetch_row($result)) {
        $num = $row[0];
    }
    return $num;
}

function getFavouritePeopleNumber()
{
    $query = "SELECT count(distinct jelentkezok_id) as num FROM jelentkezok_kedvenc k
                where k.table_name = 'idezet'";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    while($row = mysql_fetch_row($result)) {
        $num = $row[0];
    }
    return $num;
}

function getForumEntries($id, $lang)
{
    if($id > 0){
        $query_part = " and b.id = $id";
    }
    $query = "select t1.id, t1.comment, t1.datum, b.name as bookName, j.id as jelentkezok_id, j.vezeteknev, j.keresztnev
                from book b
                left outer join forum t1 on t1.book_id = b.id and t1.language = " . (int)$lang . "
                left outer join jelentkezok j on t1.jelentkezok_id = j.id
                where elfogadva = 1 $query_part
                order by t1.datum desc";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;

}

function getNewsLetterEntries($id)
{
    if($id > 0){
        $query_part = " and b2.id = $id";
    }
    $query = "select b1.ID, b1.name, b1.name_eng, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng, b2.concept, b2.concept_eng, b2.exercise, b2.exercise_eng, b2.done as subDone, b2.crdti
                from book b1
                left outer join book b2 on b1.ID = b2.ref_id
                where b1.done = 90 and b2.done in (95, 96, 91) $query_part
                order by b1.order, b2.order";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_assoc($result)) {
        list($row['crdti']) = explode(' ', $row['crdti']);
        $row['crdti'] = str_replace('-', '.', $row['crdti']);
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getEntriesByDone($id, $done,$page,$step)
{
	$min = $page*$step;
	if($id > 0){
        $query_part = " and b2.id = $id";
    }
    if(is_array($done) && count($done) > 0){
        $done = implode(", ", $done);
    }
    $query = "select b1.ID, b1.name, b1.name_eng, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng, b2.concept, b2.concept_eng, b2.exercise, b2.exercise_eng, b2.done as subDone, b2.crdti
                from book b1
                left outer join book b2 on b1.ID = b2.ref_id
                where b1.done = 90 and b2.done in ({$done}) $query_part
                order by b1.order, b2.order limit ".$min.",".$step;
      
	$result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_assoc($result)) {
        list($row['crdti']) = explode(' ', $row['crdti']);
        $row['crdti'] = str_replace('-', '.', $row['crdti']);
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getProgramokWhenSilentMessageSent()
{
    $query = "select b1.ID, b1.name, b1.name_eng, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng, b2.concept, b2.concept_eng, b2.exercise, b2.exercise_eng, b2.done as subDone, b2.crdti
                from book b1
                left outer join book b2 on b1.ID = b2.ref_id
                where b1.done = 90 and b2.done = 93 and b2.is_silence_message_sent = 1
                order by b1.order, b2.order";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_assoc($result)) {
        list($row['crdti']) = explode(' ', $row['crdti']);
        $row['crdti'] = str_replace('-', '.', $row['crdti']);
        $returnArray[] = $row;
    }
    return $returnArray;
}


function getEntriesByDone2($id, $done)
{
    if($id > 0){
        $query_part = " and b2.id = $id";
    }
    $query = "select b1.ID, b1.name, b1.name_eng, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng, b2.concept, b2.concept_eng, b2.exercise, b2.exercise_eng, b2.done as subDone, b2.crdti
                from book b1
                left outer join book b2 on b1.ID = b2.ref_id
                where b1.done = 90 and b2.done in (" . implode(",", (array)$done) . ") $query_part
                order by b1.order, b2.order";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_assoc($result)) {
        list($row['crdti']) = explode(' ', $row['crdti']);
        $row['crdti'] = str_replace('-', '.', $row['crdti']);
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getLastForumEntryDate()
{
    $query = "select max(datum) from forum where elfogadva = 1";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    while($row = mysql_fetch_row($result)) {
        $returnDate = $row[0];
    }
    return $returnDate;

}

function storeForumComment($storeArray, $isWithoutName)
{
    $storeArray['comment'] = str_replace("'", "''", $storeArray['comment']);
    if($isWithoutName == '1'){
        $jId = 0;
    }
    else{
        $jId = (int)$storeArray['jelentkezok_id'];
    }
    $query = "insert into forum (comment, datum, book_id, jelentkezok_id, language) values('{$storeArray['comment']}', now(), {$storeArray['book_id']}, {$jId}, {$storeArray['language']})";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        return -1;
    }

    $query = "select id from forum where comment = '{$storeArray['comment']}' and book_id = {$storeArray['book_id']} and jelentkezok_id = {$jId} and language = {$storeArray['language']}";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $row = mysql_fetch_row($result);
    return $row[0];
}

function sendMailToMeAboutForumEntry($categoryId, $bookId, $entry, $userObject)
{
    $query = "select b.name as bookName from book b where b.id = $bookId";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    while($row = mysql_fetch_row($result)) {
        $bookName = $row[0];
    }

    $to = "hello@luciendelmar.com";
    $subject = "Új fórumbejegyzés: $bookName";
    $fromName = $userObject['vezeteknev'] . ' ' . $userObject['keresztnev'];
    $fromEmail = $userObject['email'];
    $body = $entry;
    $body .= "
    \n
    <a href='http://www.luciendelmar.com/index.php?actionType=acceptForumEntry&hashcode=awoejkfldjk234asdf&entryId=$categoryId'>Elfogad</a>";
    endiMail($to, $subject, $body, $fromName, $fromEmail);
}

function acceptForumEntry($entryId)
{
    $query = "update forum set elfogadva = 1 where id = " . (int)$entryId;
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
}

/*
SELECT i.ID as idezetId, i.idezet, i.idezet_eng, i.idezet_ger, i.szerzo, i.kikuldve, i.datum, count(f.id) as favouriteNumber
                FROM idezet i
                left outer join jelentkezok_kedvenc f on f.table_name = 'idezet' and f.table_id = i.id
                where 1 = 1
                group by  i.ID, i.idezet, i.idezet_eng, i.idezet_ger, i.szerzo, i.kikuldve, i.datum
                order by favouriteNumber desc, kikuldve asc, datum desc
*/

function logSearch($word)
{
    $word = str_replace("'", "''", $word);
    $query = "select id from search_log where word = '$word'";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $id = 0;
    while($row = mysql_fetch_row($result)) {
        $id = $row[0];
    }

    if($id > 0){
        $query = "update search_log set num = num + 1 where id = " . (int)$id;
    }
    else{
        $query = "insert into search_log (word, num, crdti) values('$word', 1, now())";
    }
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }

}

function getSearchLog()
{
    $query = "select id, word, num, crdti from search_log order by num desc";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $return = array();
    while($row = mysql_fetch_assoc($result)) {
        $return[] = $row;
    }
    return $return;
}

function sendInvitation($userObject, $invitedAddress, $invitedMessage, $invitationLanguage)
{
    $body = $invitedMessage;
    $body .= "<br><br>";
    $body .= "<a href='http://www.luciendelmar.com/subscribe.php?invUserId=" . $userObject['ID'] . "&lang=" . (int)$invitationLanguage . "'>" . translate('invitationLetterBody') . "</a>";
    $body .= "<br><br>";
    if((int)$invitationLanguage == 1){
        $body .= "<img src='http://www.luciendelmar.com/images/ph.jpg' border=0>";
    }
    else{
        $body .= "<img src='http://www.luciendelmar.com/images/ph.jpg' border=0>";
    }
    return endiMail($invitedAddress, translate('invitationLetterSubject'), $body, $userObject['vezeteknev'] . ' ' . $userObject['keresztnev'], $userObject['email']);
}

function getInvitedNumber($userId)
{
    $query = "select count(*) from jelentkezok where inviter = " . (int)$userId;
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $row = mysql_fetch_row($result);
    return $row[0];
}

function changeUserStatus($userIdentity, $newStatus)
{
    if(strpos($userIdentity, "@") === false){
        $query_add = "id = " . (int)$userIdentity;
    }
    else{
        $query_add = "email = '{$userIdentity}'";
    }
    $query = "update jelentkezok set status = " . (int)$newStatus . " where " . $query_add;
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        return -1;
    }
    return mysql_affected_rows();
}

function getUserByIdentity($userIdentity)
{
    if(strpos($userIdentity, "@") === false){
        $query_add = "id = " . (int)$userIdentity;
    }
    else{
        $query_add = "email = '{$userIdentity}'";
    }
    $query = "select * from jelentkezok where " . $query_add;
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        return -1;
    }
    $row = mysql_fetch_assoc($result);
    return $row;
}

function getInviterNumber()
{
    $query = "select count(distinct inviter) from jelentkezok";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $row = mysql_fetch_row($result);
    return $row[0];
}

function getUsersByInviter($inviterId)
{
    $query = "SELECT * from jelentkezok where inviter = " . (int)$inviterId;

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $userObject = array();
    while($row = mysql_fetch_assoc($result)) {
        $userObject[] = $row;
    }
    return $userObject;
}

function getSendQuoteBody($imgName, $userObject, $quoteObject, $quoteAddressFirstName, $quoteAddress, $userMessage)
{
    if($_SESSION['language'] == 'eng'){
        $language = 2;
    }
    else{
        $language = 1;
    }
    $szinek = array( 'FF3366',
                 '663366',
                 '339933',
                 '993333',
                 '4B0082',
                 'DC143C',
                 '00008B',
                 '228B22',
                 '800000',
                 '4169E1',
                 '6A5ACD',
                 '006400');

    $szin = $szinek[rand(0, 9)];
    $szin = '000000';
    $idezetArray['hun'] = $quoteObject['idezet'];
    $idezetArray['eng'] = $quoteObject['idezet_eng'];
    $idezetArray['ger'] = $quoteObject['idezet_ger'];
    
    $szerzoArray['hun'] = $quoteObject['szerzo'];
    $szerzoArray['eng'] = $quoteObject['szerzo_eng'];
    $szerzoArray['ger'] = $quoteObject['szerzo_ger'];
    

    $message = "<HTML><head><META HTTP-EQUIV='CHARSET' CONTENT='text/html; charset=$CHARSET'>
                <link rel=stylesheet type='text/css' href='http://www.luciendelmar.com/usermenu.css'>
                </head>
    			<body>
    			<table width=750 border=0>
                <tr><td>
                      <table align=left width=100% border = '1' cellpadding='10'>
                       <tr>
          			<td align=left valign=top width=200>
      
          			<FONT face=Arial color=#000000>
          			<FONT size=4>
      
          			<p>";
          $message .= translate('levelkuldes_4');
          $message .= " &nbsp;" . $quoteAddressFirstName . "!</p>
          			<p><FONT face=Arial color=#000000><FONT size=2>";
          $message .= $userMessage;
          $message .= "</p>
                      <p><span style='font-family:Arial;font-size:13'>" . $userObject['vezeteknev'] . " " . $userObject['keresztnev'] . "</span></p>
                      <p><a style='font-size:10' href='http://www.luciendelmar.com/subscribe.php?invUserId=" . $userObject['ID'] . "&lang=" . (int)$language . "'>www.luciendelmar.com</a></p>
                      </td><td align=middle valign=bottom>
                    <img src='http://www.luciendelmar.com/quotepics/{$imgName}' height=300 border=0 style='cursor:default'><br><br><br>
          			<FONT size=2>
          			<FONT face=Arial color=#$szin>
          			<p>\"";
          $message .= $idezetArray[$_SESSION['language']];
          $message .= "\"</p>
          			<FONT size=2>
          			<p><i>(";
          $message .= $szerzoArray[$_SESSION['language']];
          $message .= ")</i></p>
          </td></tr></table>
     	</td></tr></table>
          </body></html>";
    return $message;
}

function getSendQuoteBodyImage($imgName, $userObject, $quoteObject)
{
    if($_SESSION['language'] == 'eng'){
        $language = 2;
    }
    else{
        $language = 1;
    }

    $idezetArray['hun'] = $quoteObject['idezet'];
    $idezetArray['eng'] = $quoteObject['idezet_eng'];
    $idezetArray['ger'] = $quoteObject['idezet_ger'];
    
    $szerzoArray['hun'] = $quoteObject['szerzo'];
    $szerzoArray['eng'] = $quoteObject['szerzo_eng'];
    $szerzoArray['ger'] = $quoteObject['szerzo_ger'];

    $imgArray = getFileNamesFromDirectory('quotepics');
    $message = "<HTML><head><META HTTP-EQUIV='CHARSET' CONTENT='text/html; charset=$CHARSET'>
                </head>
    			<body>
    			<table border=1>
                <tr><td>
                      <table align=left border = '1' cellpadding='10'>
                       <tr>
          			<td align=left valign=top width=200>

          			<FONT face=Arial>
          			<FONT size=4>

          			<p>";
          $message .= "<span class='#FAFAFA'>" . translate('levelkuldes_4') . "</span>";
          $message .= " &nbsp;<input type='text' name='quoteAddressFirstName' value='" . $_POST['quoteAddressFirstName'] . "' size=15>!</p>
          			<p><FONT face=Arial><FONT size=2>";
          $message .= "<textarea name='message' cols=23 rows=6>" . $_POST['message'] . "</textarea></font>";
          $message .= "</p>
                      <p><span class='#FAFAFA'>" . $userObject['vezeteknev'] . " " . $userObject['keresztnev'] . "</span></p>
                      <p><a href='#" . (int)$language . "'>www.luciendelmar.com</a></p>
                      </td><td align=middle valign=bottom width='578'>
                    <img id='mainImg' src='http://www.luciendelmar.com/quotepics/{$imgName}' height=300 border=0 style='cursor:default'><br><br><br>
          			<span class='#FAFAFA'>
          			<FONT size=2>
          			<FONT face=Arial>
          			<p>\"";
          $message .= $idezetArray[$_SESSION['language']];
          $message .= "\"</p>
          			<FONT size=2>
          			<p><i>(";
          $message .= $szerzoArray[$_SESSION['language']];
          $message .= ")</i></p></span>
          </td><td>";
         $message .= "<div style='height:415;width:120;overflow:auto'>";
         foreach($imgArray as $currentImage){
            $message .= "<img src='http://www.luciendelmar.com/quotepics/{$currentImage}' width=100 border=0 onclick=\"document.getElementById('imgName').value = '$currentImage';document.forms[0].submit();\"><br>";
         }
         $message .= "</div>";
          $message .= "</td>
         </tr></table>";
    return $message;
}

function getFileNamesFromDirectory($dir)
{
    // open this directory
    $myDirectory = opendir("./{$dir}");

    // get each entry
    while($entryName = readdir($myDirectory)) {
        if(is_file("./{$dir}/{$entryName}")){
        	$dirArray[] = $entryName;
        }
    }

    // close directory
    closedir($myDirectory);
    return $dirArray;
}

function getDirectoryNamesFromDirectory($dir)
{
    // open this directory
    $myDirectory = opendir("./{$dir}");

    // get each entry
    while($entryName = readdir($myDirectory)) {
        if(is_dir("./{$dir}/{$entryName}")){
        	$dirArray[] = $entryName;
        }
    }

    // close directory
    closedir($myDirectory);
    return $dirArray;
}

function sendQuote($userObject, $quoteAddressFirstName, $quoteAddress, $message)
{
    global $CHARSET;

    $emailAddress = "hello@luciendelmar.com";
    $headers = "MIME-Version: 1.0\n".
     "Content-type: text/html; charset=$CHARSET\n";
    $headers .= "From: {$userObject['vezeteknev']} {$userObject['keresztnev']} <{$userObject['email']}>\n";
    $headers .= "Reply-To: {$userObject['vezeteknev']} {$userObject['keresztnev']} <{$userObject['email']}>\n";

	mail($quoteAddressFirstName.' <'.$quoteAddress.'>',translate("levelkuldes_2"), $message, $headers);

	$query = "update jelentkezok set sent_quotes = sent_quotes + 1 where ID = " . (int)$userObject['ID'];
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
}

function getQuoteById($id)
{
    $query = "SELECT * from idezet where ID = " . (int)$id;

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $quoteObject = mysql_fetch_assoc($result);
    return $quoteObject;
}

function getSentQuotesNumber($userId)
{
    $query = "select sent_quotes from jelentkezok where ID = " . (int)$userId;
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $row = mysql_fetch_row($result);
    return $row[0];
}

function getAllSentQuotesNumber()
{
    $query = "select sum(sent_quotes) from jelentkezok";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $row = mysql_fetch_row($result);
    return $row[0];
}

function getSurveyUserNumber()
{
    $query = "select count(distinct user_id) from user_ruins";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $row = mysql_fetch_row($result);
    return $row[0];
}

function getUserNumberByStatus($status)
{
    $query = "select count(*)
                from jelentkezok
                where status = " . (int)$status;
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $row = mysql_fetch_row($result);
    return $row[0];
}

function setUserRuins($userObject, $selectedRuins)
{
    $query = "delete from user_ruins where user_id = " . (int)$userObject['ID'];
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    if(is_array($selectedRuins)){
        foreach($selectedRuins as $ruin_id){
            $query = "insert into user_ruins (user_id, ruin_id, datum) values(" . (int)$userObject['ID'] . ", {$ruin_id}, NOW())";
            $result = mysql_query($query);
            if(!$result){
                print mysql_error();
                exit("Nem sikerült: " . $query);
            }
        }
    }
}

function getUserRuins($user_id)
{
    $query = "select ur.*, r.ruin
                from user_ruins ur
                inner join ruin r on ur.ruin_id = r.ID
                where ur.user_id = " . (int)$user_id;
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $ret = array();
    while($row = mysql_fetch_assoc($result)){
        $ret[] = $row;
    }
    return $ret;
}

function updateUsers()
{
    $query = "SELECT inviter, count(*) FROM jelentkezok group by inviter having count(*) > 0";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $ret = array();
    while($row = mysql_fetch_row($result)){
        $ret[] = $row;
    }
    foreach($ret as $item){
        $plus = 50 * (int)$item[1];
        $query = "update jelentkezok set expire_number = expire_number + " . $plus . " where ID = " . (int)$item[0];
        print '<br>' . $query;
        $result = mysql_query($query);
        if(!$result){
            print mysql_error();
            exit("Nem sikerült: " . $query);
        }
    }
}

function getProblemBooks($ruinCategoryId)
{
    if($ruinCategoryId){
        $whereCond = "where rc.id = " . (int)$ruinCategoryId;
        $categoryNum = getRuinCategorySeq($ruinCategoryId);
    }
    $taskList = getExercises91();

    $query = "select rc.id as categoryId, rc.name, r.ruin as subName, r.ID as ruinId, b.name as subSubName, b.ID as bookId
                from ruin_categories rc
                left outer join ruin r on rc.id = r.category
                left outer join ruin_exercises re on r.ID = re.ruin_id
                left outer join book b on re.exercise_id = b.ID
                left outer join book b2 on b.ref_ID = b2.ID
                $whereCond
                order by rc.ord, r.ID, b2.order, b.order";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $ret = array();
    $prevRuinId = -1;
    $prevCatId = -1;
    $ruinSeq = 0;
    $catSeq = 0;
    while($row = mysql_fetch_assoc($result)){
        if($ruinCategoryId){
            $row['categorySeq'] = $categoryNum;
        }
        else{
            if($row['categoryId'] != $prevCatId){
                $ruinSeq = 0;
                $catSeq++;
                $prevCatId = $row['categoryId'];
            }
            $row['categorySeq'] = $catSeq;
        }
        if($row['ruinId'] != $prevRuinId){
            $ruinSeq++;
            $prevRuinId = $row['ruinId'];
        }
        $row['ruinSeq'] = $ruinSeq;

        $prevId = 0;
        $chapNum = 0;
        $chapterNum = -1;
        $subChapterNum = -1;
        for($i = 0; $i < count($taskList); $i++){
            // ha megváltozott a main category
            if($taskList[$i]['ID'] != $prevId){
                $num = 1;
                $chapNum++;
            }
            if($taskList[$i]['sub_ID'] == $row['bookId']){
                $chapterNum = $chapNum;
                $subChapterNum = $num;
                break;
            }
            $num++;
            $prevId = $taskList[$i]['ID'];
        }
        if($chapterNum > -1){
            $row['taskSeq'] = $chapterNum . '/' . $subChapterNum;
        }

        $ret[] = $row;
    }
    return $ret;
}

function getRuinCategorySeq($ruinCategoryId)
{
    $query = "select id from ruin_categories order by ord";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $num = 1;
    while($row = mysql_fetch_row($result)){
        if($row[0] == $ruinCategoryId){
            return $num;
        }
        $num++;
    }
    return -1;
}

function logEmail()
{
    $query = "select count(*) as nr from email_log where DATUM = CURDATE()";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    if($number > 0){
        $query = "update email_log set NR = NR + 1 where DATUM = CURDATE()";
    }
    else{
        $query = "insert into email_log (DATUM) values(CURDATE())";
    }

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        return false;
    }
    return true;
}

function languageChanged()
{
    global $trans;
    $trans = array();
    if($_SESSION['language'] == 'hun' and file_exists('translations_HUN.php')){
        include('translations_HUN.php');
    }
    else if($_SESSION['language'] == 'ger' and file_exists('translations_GER.php')){
        include('translations_GER.php');
    }
    else if($_SESSION['language'] == 'gr' and file_exists('translations_GR.php')){
        ob_start();
        include('translations_GR.php');
        ob_end_clean();
    }
    else if($_SESSION['language'] == 'si' and file_exists('translations_SI.php')){
        ob_start();
        include_once('translations_SI.php');
        ob_end_clean();
    }
    else if($_SESSION['language'] == 'slo' and file_exists('translations_SLO.php')){
        ob_start();
        include_once('translations_SLO.php');
        ob_end_clean();
    }
    else if($_SESSION['language'] == 'sp' and file_exists('translations_SP.php')){
        ob_start();
        include_once('translations_SP.php');
        ob_end_clean();
    }
else if($_SESSION['language'] == 'cro' and file_exists('translations_CRO.php')){
    ob_start();
    include_once('translations_CRO.php');
    ob_end_clean();
}
else if($_SESSION['language'] == 'it' and file_exists('translations_IT.php')){
    ob_start();
    include('translations_IT.php');
    ob_end_clean();
}
else if(file_exists('translations_ENG.php')){
    include('translations_ENG.php');
    }
    if($_SESSION['language'] == 'gr'){
        $CHARSET = "UTF-8";
    }
    else if($_SESSION['language'] == 'si'){
        $CHARSET = "windows-1250";
    }
    else{
        $CHARSET = "ISO-8859-2";
    }
}

function getProfilePicturePath($userObject)
{
    $userId = $userObject['ID'];
    $imgPathArray = getDirectoryNamesFromDirectory('../phpics');
    foreach($imgPathArray as $currentPath){
        if($currentPath == '..'){
            continue;
        }
        if(strrpos($currentPath, '_' . $userId) === strlen($currentPath) - strlen($userId) - 1){
            return $currentPath;
        }
    }
    return false;
}

function printMenuBallPart()
{
    ?>
    <script>
        function menuWork(currentMenu)
        {
            if(<?php print "'" . $_POST['selectedMenu'] . "'"; ?> == currentMenu){
                return;
            }
            document.getElementById('selectedMenu').value = currentMenu;
            document.forms[0].submit();
        }
    </script>
      <table border='0'>
          <tr><td valign='top' align='center' width='86' height='16'><img border="0" src=<?php print "'" . getImgSrcNameForMenuBalls('communityLife') . "'"; ?> style='cursor:pointer' onclick="menuWork('communityLife');"></td><td valign='center' align='left' width='138' style="font-size:14px;"><font color='#574b21'><span style='cursor:pointer' onclick="menuWork('communityLife');"><?php print translate('Community_life'); ?></span></td></tr>
          <tr><td valign='top' align='center' width='86' height='16'><img border="0" src=<?php print "'" . getImgSrcNameForMenuBalls('money') . "'"; ?> style='cursor:pointer' onclick="menuWork('money');"></td><td valign='center' align='left' width='138' style="font-size:14px;"><font color='#87268b'><span style='cursor:pointer' onclick="menuWork('money');"><?php print translate('Money'); ?></span></td></tr>
          <tr><td valign='top' align='center' width='86' height='16'><img border="0" src=<?php print "'" . getImgSrcNameForMenuBalls('soulTools') . "'"; ?> style='cursor:pointer' onclick="menuWork('soulTools');"></td><td valign='center' align='left' width='138' style="font-size:14px;"><font color='#190d7f'><span style='cursor:pointer' onclick="menuWork('soulTools');"><?php print translate('Tools_for_the_soul'); ?></span></td></tr>
          <tr><td valign='top' align='center' width='86' height='16'><img border="0" src=<?php print "'" . getImgSrcNameForMenuBalls('vitalProducts') . "'"; ?> style='cursor:pointer' onclick="menuWork('vitalProducts');"></td><td valign='center' align='left' width='138' style="font-size:14px;"><font color='#008a98'><span style='cursor:pointer' onclick="menuWork('vitalProducts');"><?php print translate('Vital_Products'); ?></span></td></tr>
          <tr><td valign='top' align='center' width='86' height='16'><img border="0" src=<?php print "'" . getImgSrcNameForMenuBalls('livingDiet') . "'"; ?> style='cursor:pointer' onclick="menuWork('livingDiet');"></td><td valign='center' align='left' width='138' style="font-size:14px;"><font color='#289a51'><span style='cursor:pointer' onclick="menuWork('livingDiet');"><?php print translate('Living_the_diet_and_lifestyle'); ?></span></td></tr>
          <tr><td valign='top' align='center' width='86' height='16'><img border="0" src=<?php print "'" . getImgSrcNameForMenuBalls('personalizedPh') . "'"; ?> style='cursor:pointer' onclick="menuWork('personalizedPh');"></td><td valign='center' align='left' width='138' style="font-size:14px;"><font color='#999920'><span style='cursor:pointer' onclick="menuWork('personalizedPh');"><?php print translate('Your_personalised_pH_program'); ?></span></td></tr>
          <tr><td valign='top' align='center' width='86' height='16'><img border="0" src=<?php print "'" . getImgSrcNameForMenuBalls('bloodAnalysis') . "'"; ?> style='cursor:pointer' onclick="menuWork('bloodAnalysis');"></td><td valign='center' align='left' width='138' style="font-size:14px;"><font color='#8e5500'><span style='cursor:pointer' onclick="menuWork('bloodAnalysis');"><?php print translate('Blood_analysis'); ?></span></td></tr>
          <tr><td valign='top' align='center' width='78' height='16'><img border="0" src=<?php print "'" . getImgSrcNameForMenuBalls('basicUnderstandings') . "'"; ?> style='cursor:pointer' onclick="menuWork('basicUnderstandings');"></td><td valign='center' align='left' width='138' style="font-size:14px;"><font color='#7f1f1f'><span style='cursor:pointer' onclick="menuWork('basicUnderstandings');"><?php print translate('Understanding_the_basics'); ?></span></td></tr>

      </table border='0'>
    <?php
}

function getImgSrcNameForMenuBalls($currentMenu)
{
    $imgNameArray = array(
            'communityLife' => array('btn_gold_normal.png', 'btn_gold_over.png'),
            'money' => array('btn_purple_normal.png', 'btn_purple_over.png'),
            'soulTools' => array('btn_blue_normal.png', 'btn_blue_over.png'),
            'vitalProducts' => array('btn_lightblue_normal.png', 'btn_lightblue_over.png'),
            'livingDiet' => array('btn_green_normal.png', 'btn_green_over.png'),
            'personalizedPh' => array('btn_yellow_normal.png', 'btn_yellow_over.png'),
            'bloodAnalysis' => array('btn_orange_normal.png', 'btn_orange_over.png'),
            'basicUnderstandings' => array('btn_red_normal.png', 'btn_red_over.png')
        );
    if($currentMenu == $_POST['selectedMenu']){
        return '/images/' . $imgNameArray[$currentMenu][1];
    }
    else{
        return '/images/' . $imgNameArray[$currentMenu][0];
    }
}

function printMainPageChangingPart($selectedMenu)
{
    switch($selectedMenu){
        case 'communityLife':
            ?>
                <tr valign='top'>
                  <td width='20' rowspan='3'>&nbsp;
                  </td>
                  <td valign='top' align='left' colspan='2'>
                       <font size='5' color='#5cacc0'><b><?php print translate('Community_life'); ?></b></font>
                  </td>
                  <td width='30' rowspan='3'>&nbsp;</td>
                  <td rowspan='3' background='images/program_panel.png' style="background-repeat:no-repeat;" width='216' height='371'>
                          <?php printMenuBallPart(); ?>
                  </td>
                  </tr>
                  <tr>
                   <td width='25' rowspan='2'>&nbsp;</td>
                   <td style="font-size:14px;" valign='top'><br>
                          <?php print translate('communityLife_text1'); ?>
                          <a href='http://www.luciendelmar.com/communitylife.php'><b><font color='#5cacc0'><?php echo translate('PTO') ?> ...</a></td></tr>
            <?php
            break;
        case 'money':
            ?>
                <tr valign='top'>
                  <td width='20' rowspan='3'>&nbsp;
                  </td>
                  <td valign='top' align='left' colspan='2'>
                       <font size='5' color='#5cacc0'><b><?php print translate('Money'); ?></b></font>
                  </td>
                  <td width='30' rowspan='3'>&nbsp;</td>
                  <td rowspan='3' background='images/program_panel.png' style="background-repeat:no-repeat;" width='216' height='371'>
                          <?php printMenuBallPart(); ?>
                  </td>
                  </tr>
                  <tr>
                   <td width='25' rowspan='2'>&nbsp;</td>
                   <td style="font-size:14px;" valign='top'><br>
                          <?php print translate('money_text'); ?>
                   </td></tr>
            <?php
            break;
        case 'soulTools':
            ?>
                <tr valign='top'>
                  <td width='20' rowspan='3'>&nbsp;
                  </td>
                  <td valign='top' align='left' colspan='2'>
                       <font size='5' color='#5cacc0'><b><?php print translate('phbalanceprogram_thesoulside1'); ?></b></font><br>
                  </td>
                  <td width='30' rowspan='3'>&nbsp;</td>
                  <td rowspan='3' background='images/program_panel.png' style="background-repeat:no-repeat;" width='216' height='371'>
                          <?php printMenuBallPart(); ?>
                  </td>
                  </tr>
                  <tr>
                   <td width='25' rowspan='2'>&nbsp;</td>
                   <td style="font-size:14px;" valign='top'><br>
                          <?php print translate('phbalanceprogram_thesoulside2'); ?>
                  </td>
                  </tr>
            <?php
            break;
        case 'vitalProducts':
            ?>
                <tr valign='top'>
                  <td width='20' rowspan='3'>&nbsp;
                  </td>
                  <td valign='top' align='left' colspan='2'>
                       <font size='5' color='#5cacc0'><b><?php print translate('phbalanceprogram_supplements1'); ?></b></font><br>
                  </td>
                  <td width='30' rowspan='3'>&nbsp;</td>
                  <td rowspan='3' background='images/program_panel.png' style="background-repeat:no-repeat;" width='216' height='371'>
                          <?php printMenuBallPart(); ?>
                  </td>
                  </tr>
                  <tr>
                   <td width='25' rowspan='2'>&nbsp;</td>
                   <td style="font-size:14px;" valign='top'><br>
                          <?php print translate('phbalanceprogram_supplements2'); ?>
                  </td>
                  </tr>
            <?php
            break;
        case 'livingDiet':
            ?>
                <tr valign='top'>
                  <td width='20' rowspan='3'>&nbsp;
                  </td>
                  <td valign='top' align='left' colspan='2'>
                       <font size='5' color='#5cacc0'><b><?php print translate('phbalanceprogram_experiences1'); ?></b></font><br> <br>
                  </td>
                  <td width='30' rowspan='3'>&nbsp;</td>
                  <td rowspan='3' background='images/program_panel.png' style="background-repeat:no-repeat;" width='216' height='371'>
                          <?php printMenuBallPart(); ?>
                  </td>
                  </tr>
                  <tr>
                   <td width='25' rowspan='2'>&nbsp;</td>
                   <td style="font-size:14px;" valign='top'>
                          <?php print translate('phbalanceprogram_experiences2'); ?>
                  </td>
                  </tr>
                  <tr><td align='right' valign='top'><a href='http://www.luciendelmar.com/livingthelifestyle.php'><b><font color='#5cacc0'><?php echo translate('PTO') ?> ...</a></td></tr>
            <?php
            break;
        case 'personalizedPh':
            ?>
                <tr valign='top'>
                  <td width='20' rowspan='3'>&nbsp;
                  </td>
                  <td valign='top' align='left' colspan='2'>
                       <font size='5' color='#5cacc0'><b><?php print translate('Your_personalised_pH_program_2'); ?></b></font><br>
                  </td>
                  <td width='30' rowspan='3'>&nbsp;</td>
                  <td rowspan='3' background='images/program_panel.png' style="background-repeat:no-repeat;" width='216' height='371'>
                          <?php printMenuBallPart(); ?>
                  </td>
                  </tr>
                  <tr>
                   <td width='25' rowspan='2'>&nbsp;</td>
                   <td style="font-size:14px;" valign='top'><br>
                          <?php print translate('phbalanceprogram_theindividualprogram2'); ?>
                  </td>
                  </tr>
            <?php
            break;
        case 'bloodAnalysis':
            ?>
                <tr valign='top'>
                  <td width='20' rowspan='3'>&nbsp;
                  </td>
                  <td valign='top' align='left' colspan='2'>
                       <font size='5' color='#5cacc0'><b><?php print translate('bloodanalysis_1'); ?></b></font><br> <br>
                  </td>
                  <td width='30' rowspan='3'>&nbsp;</td>
                  <td rowspan='3' background='images/program_panel.png' style="background-repeat:no-repeat;" width='216' height='371'>
                          <?php printMenuBallPart(); ?>
                  </td>
                  </tr>
                  <tr>
                   <td width='25' rowspan='2'>&nbsp;</td>
                   <td style="font-size:14px;" valign='top'>
                          <?php print translate('phbalanceprogram_purpose2'); ?>
                  </td>
                  </tr>
                  <tr><td align='right' valign='top'><a href='http://www.luciendelmar.com/verelemzes.php'><b><font color='#5cacc0'><?php echo translate('PTO') ?> ...</a></td></tr>
            <?php
            break;
        case 'basicUnderstandings':
            ?>
                <tr valign='top'>
                  <td width='20' rowspan='2'>&nbsp;
                  </td>
                  <td valign='top' align='left' colspan='2'>
                       <font size='5' color='#5cacc0'><b><?php print translate('introlecture_1'); ?></b></font><br>
                       <font size='4' color='#195b6a'><?php print translate('introlecture_2'); ?><br>
                  </td>
                  <td width='30' rowspan='2'>&nbsp;</td>
                  <td rowspan='2' background='images/program_panel.png' style="background-repeat:no-repeat;" width='216' height='371'>
                          <?php printMenuBallPart(); ?>
                  </td>
                  </tr>
                  <tr>
                   <td width='25'>&nbsp;</td>
                   <td style="font-size:14px;" valign='top'><br>
                          <?php print translate('introlecture_3'); ?>
                  </td>
                  </tr>
            <?php
            break;
        default:
            ?>
                <tr valign='top'>
                  <td width='20' rowspan='2'>&nbsp;
                  </td>
                  <td valign='top' align='left' colspan='2'>
                      <?php
                          if($_SESSION['language'] == 'hun'){
                      ?>
                              <img border="0" src='/images/program_logo_hun.png'>
                      <?php
                          }
                          else{
                      ?>
                              <img border="0" src='/images/program_logo_eng.png'>
                      <?php
                          }
                      ?>
                  </td>
                  <td width='30' rowspan='2'>&nbsp;
                  </td>
                  <td rowspan='2' background='images/program_panel.png' style="background-repeat:no-repeat;" width='216' height='371'>
                          <?php printMenuBallPart(); ?>
                  </td>
                  </tr>
                  <tr>
                  <td width='25'>&nbsp;</td>
                   <td style="font-size:14px;" valign='top'><br>
                          <?php print translate('asilence_missionstatement2'); ?>
                  </td>
                  </tr>
            <?php
    }
}

function storeOrder($fields)
{
    if(!$fields['varos'] or !$fields['vezeteknev'] or !$fields['keresztnev'] or !$fields['email'] or !$fields['cim']){
        $message = "A vezetéknév, keresztnév, város, cím és email kitöltése kötelezõ!";
        return false;
    }
    foreach($fields as $key => $value){
        $fields[$key] = str_replace("'", "''", $value);
    }

    $query = "insert into orders (order_time, vezeteknev, keresztnev, email, telefon, cegnev, varos, cim, irszam, megjegyzes)
                values(NOW(), '{$fields['vezeteknev']}', '{$fields['keresztnev']}', '{$fields['email']}'
                    , '{$fields['telefon']}', '{$fields['cegnev']}', '{$fields['varos']}', '{$fields['cim']}', '{$fields['irszam']}'
                    , '{$fields['megjegyzes']}')";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        return false;
    }
    $query = "select max(id) as id from orders where email = '{$fields['email']}' and vezeteknev = '{$fields['vezeteknev']}' and keresztnev = '{$fields['keresztnev']}' and cim = '{$fields['cim']}'";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $id = 0;
    while($row = mysql_fetch_row($result)) {
        $id = $row[0];
    }
    if($id > 0){
        foreach((array)$fields['product'] as $key => $value){
            $sql = "insert into order_products (orders_id, products_id, quantity) values({$id}, {$key}, " . (int)$value . ")";
            $result = mysql_query($sql);
            if(!$result){
                print mysql_error();
                exit("Nem sikerült: " . $sql);
            }
        }
    }
    return $id;
}

function getProducts()
{
    $query = "select * from products";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $return = array();
    while($row = mysql_fetch_assoc($result)) {
        $return[] = $row;
    }
    return $return;
}

function getProduct($id)
{
    $query = "select * from products where id = " . (int)$id;

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $row = mysql_fetch_assoc($result);
    return $row;
}

function getOrder($id)
{
    $query = "select o.*, op.quantity, p.title as product_title, p.price_huf, p.price_eur
                from orders o
                inner join order_products op on op.orders_id = o.id
                inner join products p on p.id = op.products_id
                where o.id = " . (int)$id;

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $i = 0;
    $order = array();
    while($row = mysql_fetch_assoc($result)){
        if($i == 0){
            $order['id'] = $id;
            $order['vezeteknev'] = $row['vezeteknev'];
            $order['keresztnev'] = $row['keresztnev'];
            $order['cegnev'] = $row['cegnev'];
            $order['varos'] = $row['varos'];
            $order['cim'] = $row['cim'];
            $order['irszam'] = $row['irszam'];
            $order['email'] = $row['email'];
            $order['telefon'] = $row['telefon'];
            $order['megjegyzes'] = $row['megjegyzes'];
            $order['order_time'] = $row['order_time'];
            $order['products'] = array();
        }
        $product = array();
        $product['title'] = $row['product_title'];
        $product['price_huf'] = $row['price_huf'];
        $product['price_eur'] = $row['price_eur'];
        $product['quantity'] = $row['quantity'];

        $order['products'][] = $product;

        $i++;
    }
    return $order;
}

function getTrainingMaterial()
{
    $query = "select b1.ID, b1.name as book_name, b2.ID as sub_ID, b2.name as sub_book_name, b2.concept 
                from book b1
                inner join book b2 on b2.ref_ID = b1.ID and b2.done = 11
                order by b1.order";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getExercisesByDone($done)
{
    if(is_array($done) && count($done) > 0){
        $done = implode(", ", $done);
    }
    $query = "select b1.ID, b1.name, b1.name_eng, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng, b2.concept, b2.concept_eng, b2.exercise, b2.exercise_eng, b2.done as subDone,
                    b2.crdti
                from book b1
                left outer join book b2 on b1.ID = b2.ref_id
                where b1.done = 90 and b2.done in (" . $done . ")
                order by b1.order, b2.order";
                
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_assoc($result)) {
        list($row['crdti']) = explode(' ', $row['crdti']);
        $row['crdti'] = str_replace('-', '.', $row['crdti']);
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getRandomCard($id = 0)
{
    if($id > 0){
        $add = " and b2.ID = " . (int)$id;
    }
    else if(is_array($_SESSION['selectedRandomQuoteIds']) && count((array)$_SESSION['selectedRandomQuoteIds']) > 0){
        $add = " and b2.ID not in (" . implode(",", (array)$_SESSION['selectedRandomQuoteIds']) . ")";
    }
    $query = "select b1.ID, b1.name, b1.name_eng, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng, b2.concept, b2.concept_eng, b2.exercise, b2.exercise_eng, b2.done as subDone,
                    b2.crdti, b2.card_number
                from book b1
                inner join book b2 on b1.ID = b2.ref_id
                where b1.done = 90 and b2.done = 96 and b2.concept_eng like '%<img%'
                $add
                order by rand() limit 1";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $row = mysql_fetch_assoc($result);
    if(!$row && is_array($_SESSION['selectedRandomQuoteIds']) && count((array)$_SESSION['selectedRandomQuoteIds']) > 0){
        unset($_SESSION['selectedRandomQuoteIds']);
        $_SESSION['selectedRandomQuoteIds'] = array();
        return getRandomCard();
    }
    list($row['crdti']) = explode(' ', $row['crdti']);
    $row['crdti'] = str_replace('-', '.', $row['crdti']);
    $_SESSION['selectedRandomQuoteIds'][] = $row['sub_ID'];
    return $row;
}

function getBlogEntry($id)
{
    $query = "select b1.ID, b1.name, b1.name_eng, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng, b2.concept, b2.concept_eng, b2.exercise, b2.exercise_eng, b2.done as subDone,
                    b2.crdti, b2.card_number
                from book b1
                inner join book b2 on b1.ID = b2.ref_id
                where b2.ID = " . (int)$id;

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $row = mysql_fetch_assoc($result);
    list($row['crdti']) = explode(' ', $row['crdti']);
    $row['crdti'] = str_replace('-', '.', $row['crdti']);
    return $row;
}

function getLastSentCard()
{
    $query = "select b1.ID, b1.name, b1.name_eng, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng, b2.concept, b2.concept_eng, b2.exercise, b2.exercise_eng, b2.done as subDone,
                    b2.crdti, b2.card_number
                from book b1
                inner join book b2 on b1.ID = b2.ref_id
                inner join levelkuldes_log l on b2.ID = l.bookId
                where b1.done = 90 and b2.done = 96 and b2.concept_eng like '%<img%'";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $row = mysql_fetch_assoc($result);
    list($row['crdti']) = explode(' ', $row['crdti']);
    $row['crdti'] = str_replace('-', '.', $row['crdti']);
    return $row;
}

function getCardByNumber($cardNumber)
{
    $query = "select b1.ID, b1.name, b1.name_eng, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng, b2.concept, b2.concept_eng, b2.exercise, b2.exercise_eng, b2.done as subDone,
                    b2.crdti, b2.card_number
                from book b1
                inner join book b2 on b1.ID = b2.ref_id
                where b1.done = 90 and b2.done = 96 and b2.concept_eng like '%<img%' and b2.card_number = " . (int)$cardNumber;

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $row = mysql_fetch_assoc($result);
    list($row['crdti']) = explode(' ', $row['crdti']);
    $row['crdti'] = str_replace('-', '.', $row['crdti']);
    return $row;

}

function getRandomOneliner($id = 0)
{
    if($id > 0){
        $add = " and b2.ID = " . (int)$id;
    }
    else if(is_array($_SESSION['selectedRandomQuoteIds']) && count((array)$_SESSION['selectedRandomQuoteIds']) > 0){
        $add = " and b2.ID not in (" . implode(",", (array)$_SESSION['selectedRandomQuoteIds']) . ")";
    }
    $query = "select b1.ID, b1.name, b1.name_eng, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng, b2.concept, b2.concept_eng, b2.exercise, b2.exercise_eng, b2.done as subDone,
                    b2.crdti
                from book b1
                inner join book b2 on b1.ID = b2.ref_id
                where b1.done = 90 and b2.done = 3
                $add
                order by rand() limit 1";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $row = mysql_fetch_assoc($result);
    if(!$row && is_array($_SESSION['selectedRandomQuoteIds']) && count((array)$_SESSION['selectedRandomQuoteIds']) > 0){
        unset($_SESSION['selectedRandomQuoteIds']);
        $_SESSION['selectedRandomQuoteIds'] = array();
        return getRandomOneliner();
    }
    list($row['crdti']) = explode(' ', $row['crdti']);
    $row['crdti'] = str_replace('-', '.', $row['crdti']);
    $_SESSION['selectedRandomQuoteIds'][] = $row['sub_ID'];
    return $row;
}

function getPracticeCard($id)
{
    $query = "select b1.ID, b1.name, b1.name_eng, b3.ID as sub_ID, b3.name as sub_name, b3.name_eng as sub_name_eng, b3.concept, b3.concept_eng, b3.exercise, b3.exercise_eng, b3.done as subDone,
                    b3.crdti
                from book b1
                inner join book b2 on b1.ID = b2.ref_id
                inner join subchapter_link l on b2.id = l.id_from
                inner join book b3 on b3.id = l.id_to
                where b1.done = 90 and b2.done = 96 and b2.concept_eng like '%<img%' and b3.done = 99
                and b2.ID = " . (int)$id;

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $row = mysql_fetch_assoc($result);
    list($row['crdti']) = explode(' ', $row['crdti']);
    $row['crdti'] = str_replace('-', '.', $row['crdti']);
    return $row;
}

function getLinkedCards($id)
{
    $query = "select b.ID, b.name, b.name_eng, b.done 
                from book b
                inner join subchapter_link l on l.id_to = b.ID
                where l.id_from = " . (int)$id . " and  done !=99
                order by b.name";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getMenuMainStructure()
{
    $query = "select b1.ID, b1.name, b1.name_eng, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng, b2.concept, b2.concept_eng, b2.exercise, b2.exercise_eng, b2.done as subDone, b1.done
                from book b1
                left outer join book b2 on b1.ID = b2.ref_id
                where b1.done = 90 and b2.done = 96 or b1.done = 10
                order by b1.order, b2.order";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getMenuCards($menuId)
{
    $query = "select ID, name, name_eng
                from book
                where ref_id = {$menuId} and done = 96
                order by book.order";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $row['name'] = utf8_encode($row['name']);
        $row['name_eng'] = utf8_encode($row['name_eng']);
        $returnArray[] = $row;
    }
    return $returnArray;
}

function logCardView()
{
    $query = "select count(*) as nr from click_log where DATUM = CURDATE()";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $number = 0;
    while($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    if($number > 0){
        $query = "update click_log set nr_card = nr_card + 1 where DATUM = CURDATE()";
    }
    else{
        $query = "insert into click_log (DATUM, nr_card) values(CURDATE(), 1)";
    }

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        return false;
    }
    return true;
}

function getForumDetails()
{
    $query = "select b1.ID, b1.name, b1.name_eng, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng, b2.concept, b2.concept_eng, b2.exercise, b2.exercise_eng, b2.done as subDone, b1.done
                from book b1
                inner join book b2 on b1.ID = b2.ref_id
                where b1.done = 90 and (b2.done = 95 or b2.done = 96 or b2.done = 99)
                order by b1.order, b2.order";
    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $returnArray = array();
    while($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getDesireSolutionFromBook() {
    $query = "select name
				from book 
                where done = 16
                order by rand() limit 1";

    $result = mysql_query($query);
    if(!$result){
        print mysql_error();
        exit("Nem sikerült: " . $query);
    }
    $row = mysql_fetch_assoc($result);
    return $row;
}

function simpleMail($to, $subject, $body, $headers = null) {
	if(is_null($headers)){
		$headers = "From: Healers Digital <hello@healers.digital>" . "\r\n";
		$headers .= "MIME-Version: 1.0\n";
		$headers .= "Content-Type: text/html; charset=utf-8\n";
	}
	mail($to,$subject,$body,$headers);	
}

?>