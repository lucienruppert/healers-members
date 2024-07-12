<?php
//php7 HI?NYZ? FUNKCI?I
include('./php7/mysql_replacement.php');
include('./php7/ereg-functions.php');

// GLOBAL COLOR BE?LL?T?SA
//$color = '#008080';
$color = '#0047AB';

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);

mysql_connect('mysql.luciendelmar.com', 'luciendelmar', '9CUiNwYzV3');
mysql_select_db('luciendelmar');
mysql_query("SET NAMES `latin2`");

if (!$GLOBALS["userObject"])
    $userObject = $GLOBALS["userObject"] = $_SESSION["userObject"];

if (!$_SESSION['language']) {
    if ($_COOKIE['preflanguage']) {
        $_SESSION['language'] = $_COOKIE['preflanguage'];
    } else {
        $_SESSION['language'] = 'hun';
    }
}

if ($_SESSION['language'] == 'hun' and file_exists('translations_HUN.php')) {
    include_once('translations_HUN.php');
} else if ($_SESSION['language'] == 'ger' and file_exists('translations_GER.php')) {
    include_once('translations_GER.php');
} else if (file_exists('translations_ENG.php')) {
    include_once('translations_ENG.php');
}

if ($_SESSION['language'] == 'gr') {
    //$CHARSET = "UTF-8";
    $CHARSET = "latin2";
} else {
    $CHARSET = "iso-8859-2";
}

function DEBUG($value)
{
    print_r("<pre>");
    print_r($value);
    print_r("</pre>");
}
function getUserResponse($UserID)
{
    $query = "SELECT * FROM user_response WHERE userID=" . $UserID;
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $UserRecomendations = array();
    while ($row = mysql_fetch_assoc($result)) {
        $UserRecomendations[] = $row;
    }

    return $UserRecomendations;
}

function GetUserShowRecommendationFlag($UserID)
{
    $query = "SELECT `show` FROM jelentkezok_recomendation WHERE UserID=" . $UserID . " limit 1";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $row = mysql_fetch_row($result);
    return $row[0];
}

function findById($ID, $ary)
{
    for ($i = 0; $i < count($ary); $i++) {
        if ($ary[$i]['id'] == $ID) {

            $sub = $ary[$i];
            return $sub;
        }
    }

    return null;
}
function getQuestionaryAnswer($QuestionID, $userID)
{
    $query = "SELECT * FROM Questionarie_user_answer where userID =" . $userID . " and QuestionID =" . $QuestionID . ";";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $row = mysql_fetch_assoc($result);
    return $row;
}

function selectQuestUsers($QuestionarieIDs, $consId, $see0)
{
    if (!is_array($QuestionarieIDs) || count($QuestionarieIDs) == 0) {
        return array();
    }
    $consId = (int)$consId;

    $query = "
		SELECT ID, userName, crdti, IsEvaluated 
		FROM Questionarie_Fills 
		where QuestionarieID in (" . implode(",", $QuestionarieIDs) . ") and ParentFillId is null
	";
    if ($see0) {
        $query .= "
			and ConsultantId in (0, $consId)
		";
    } else {
        $query .= "
			and ConsultantId = $consId
		";
    }
    $query .= "
		order by crdti desc
	";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $ret = array();
    while ($row = mysql_fetch_assoc($result)) {
        $ret[] = $row;
    }
    return $ret;
}

// A TP-HEZ MODIFIK?LT F?GGV?NY - ITT NEM K?R?NK HOZZ? CONSULTANT ID-T!
function selectQuestUsersTP($QuestionarieID)
{
    $query = "
		SELECT ID, userName, crdti, IsEvaluated 
		FROM Questionarie_Fills 
		where QuestionarieID = $QuestionarieID 		
	";
    $query .= "
		order by crdti desc
	";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $ret = array();
    while ($row = mysql_fetch_assoc($result)) {
        $ret[] = $row;
    }
    return $ret;
}

function selectTestQuestUsers()
{
    $query = "SELECT Id as ID, Nev as userName, ConsultantId as consultantID, CRDTI as crdti FROM ElelmiszerTesztFelh order by CRDTI desc";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $ret = array();
    while ($row = mysql_fetch_assoc($result)) {
        $ret[] = $row;
    }
    return $ret;
}

function addTestUser($userName, $newConsId)
{
    if (!(strlen($userName) > 0)) {
        return;
    }
    $newConsId = (int)$newConsId;
    $userName = str_replace("'", "''", $userName);
    $query = "insert into ElelmiszerTesztFelh (Nev, CRDTI, ConsultantId) values('{$userName}', NOW(), {$newConsId})";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        return false;
    }
}

function addOrphanUser($userName, $newConsId)
{
    if (!(strlen($userName) > 0)) {
        return;
    }
    $newConsId = (int)$newConsId;
    $userName = str_replace("'", "''", $userName);
    $query = "insert into Questionarie_Fills (userName, CRDTI, ConsultantId, QuestionarieID, isEvaluated) values('{$userName}', NOW(), {$newConsId}, 2, 0)";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        return false;
    }
}

function addNewConsultation($id)
{
    $id = (int)$id;

    $query = "insert into Questionarie_Fills (userName, CRDTI, ConsultantId, QuestionarieID, isEvaluated, ParentFillId) select userName, now(), ConsultantId, 0, QuestionarieID, $id from Questionarie_Fills where ID = $id";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        return false;
    }
    return mysql_insert_id();
}

function selectQuestFill($id)
{
    $query = "SELECT * FROM Questionarie_Fills where ID = $id";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $row = mysql_fetch_assoc($result);
    return $row;
}

function selectQuestFillForTestUsers($id)
{
    $query = "SELECT *, Nev as userName FROM ElelmiszerTesztFelh where ID = $id";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $row = mysql_fetch_assoc($result);
    return $row;
}


function selectQuestAnswersForAdmin($id)
{
    $query = "
        SELECT g.ID as groupId, g.Name as groupName, q.ID as questionId, q.short_name as question, a.answer, q.Values as questionValues, a.isHighlighted, a.ID as answerId
        FROM Questionarie_user_answer a
        inner join Question q on a.QuestionID = q.ID
        inner join Questions_by_group glink on glink.QuestionID = q.ID
        inner join Question_Group g on glink.GroupID = g.ID
        where a.Questionarie_FillsID = $id
        order by g.ID, glink.ID
    ";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $ret = array();
    while ($row = mysql_fetch_assoc($result)) {
        if (strlen($row["questionValues"]) > 0) {
            $options = explode(";", $row["questionValues"]);
            $answers = explode(";", $row["answer"]);
            $modAnswers = array();
            $assModAnswers = array();
            for ($i = 0; $i < count($answers); $i++) {
                $isFound = false;
                foreach ($options as $option) {
                    list($key, $val) = explode("ß", $option);
                    if ($key == $answers[$i]) {
                        $modAnswers[] = $val;
                        $assModAnswers[$key] = $val;
                        $isFound = true;
                        break;
                    }
                }
                if (!$isFound) {
                    $modAnswers[] = "ß1" . $answers[$i] . "ß2";
                }
            }
            $row["raw_answer"] = $row["answer"];
            $row["answer"] = implode("\n", $modAnswers);
            $row["answer_array"] = $assModAnswers;
        }
        $ret[] = $row;
    }
    return $ret;
}

function selectFilteredQuestAnswers($txt)
{
    $query = "
        SELECT a.Questionarie_FillsID, a.answer, q.Values as questionValues
        FROM Questionarie_user_answer a
        inner join Question q on a.QuestionID = q.ID
        order by a.Questionarie_FillsID
    ";
    mysql_query("SET NAMES `utf8`");
    $result = mysql_query($query);
    mysql_query("SET NAMES `latin2`");
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $ret = array();
    while ($row = mysql_fetch_assoc($result)) {
        if (strlen($row["questionValues"]) > 0) {
            $options = explode(";", $row["questionValues"]);
            $answers = explode(";", $row["answer"]);
            $modAnswers = array();
            for ($i = 0; $i < count($answers); $i++) {
                $isFound = false;
                foreach ($options as $option) {
                    list($key, $val) = explode("?", $option);
                    if ($key == $answers[$i]) {
                        $modAnswers[] = $val;
                        $isFound = true;
                        break;
                    }
                }
                if (!$isFound) {
                    $modAnswers[] = "?" . $answers[$i] . "?";
                }
            }
            $row["answer"] = implode("\n", $modAnswers);
        }
        $ret[] = $row;
    }
    $ids = array();
    foreach ($ret as $row) {
        if (mb_stripos($row["answer"], $txt) !== false) {
            $ids[] = $row["Questionarie_FillsID"];
        }
    }
    return $ids;
}

function getQuestionarieGroup($Questionarie)
{
    $query = "select qGroup.* From Question_Group qGroup, Questionarie qEst, Questionarie_List qList where qGroup.ID = qList.GroupID and qList.QuestionarieID = " . $Questionarie . " order by qList.order asc";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $QuestionarieGroup = array();
    while ($row = mysql_fetch_assoc($result)) {
        $QuestionarieGroup[] = $row;
    }

    return $QuestionarieGroup;
}

function GetQuestionsByGroup($GroupId)
{
    $query = " select q.* from Question q,Questions_by_group QbyG where QbyG.GroupID = " . $GroupId . " and q.ID = QbyG.QuestionID order by QbyG.ID ";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $resultArray = array();
    while ($row = mysql_fetch_assoc($result)) {
        $resultArray[] = $row;
    }

    return $resultArray;
}

function ChangeHighlighted($answerId, $isHighlighted)
{
    $answerId = (int)$answerId;
    if ($isHighlighted == 1)
        $isHighlighted = "1";
    else
        $isHighlighted = "0";

    $sql = "update Questionarie_user_answer set isHighlighted = $isHighlighted where ID = $answerId";

    $result = mysql_query($sql);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $query = "select isHighlighted from Questionarie_user_answer where ID = $answerId";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        return false;
    }
    $row = mysql_fetch_row($result);
    return $row[0];
}

function deleteUserByEmail($email)
{
    $query = "select id, status from jelentkezok where email = '$email'";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        return false;
    }
    $ids = array();
    $status = array();
    while ($row = mysql_fetch_row($result)) {
        $ids[] = $row[0];
        $status[] = $row[1];
    }
    if (count($ids) === 0) {
        return 0;
    }
    /*
    if(array_search(2, $status) !== false){
        return -2;
    }
    */
    $query = "delete from jelentkezok_kedvenc where jelentkezok_id in (" . implode(",", $ids) . ")";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        return false;
    }

    $query = "delete from user_ruins where user_id in (" . implode(",", $ids) . ")";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        return false;
    }

    $query = "delete from jelentkezok where id in (" . implode(",", $ids) . ")";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        return false;
    }
    return mysql_affected_rows();
}

function getAllSubChapters()
{
    $query = "SELECT b1.name as chapterName, b2.name as subChapterName, b1.ID as chapterID, b2.ID as subChapterID, b2.done, b2.updti, b2.concept
                FROM book b1
                left outer join book b2 on b2.ref_ID = b1.ID
                where b1.ref_ID is null
                order by b1.order, b1.name, b2.order, b2.name";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $resultArray = array();
    while ($row = mysql_fetch_assoc($result)) {
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $resultArray = array();
    while ($row = mysql_fetch_assoc($result)) {
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $resultArray = array();
    while ($row = mysql_fetch_assoc($result)) {
        list($row['CRDTI']) = explode(' ', $row['CRDTI']);
        $resultArray[$row['subChapterId']][] = $row;
    }
    return $resultArray;
}

function getCurrentLearningQuestion($question_id)
{
    $question_id = (int)$question_id;
    $query = "select b1.ID, b1.name, b1.name_eng, b1.name_si, b1.name_it, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng
                    , b2.name_si as sub_name_si, b2.name_it as sub_name_it, b2.concept, b2.concept_eng, b2.concept_si, b2.concept_it
                    , b2.exercise, b2.exercise_eng, b2.exercise_si, b2.exercise_it, b2.done as subDone, b2.imgName
                from book b1
                inner join book b2 on b1.ID = b2.ref_id
                where b2.ID = $question_id";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $return = array();
    while ($row = mysql_fetch_assoc($result)) {
        $return = $row;
    }

    $query = "select from_id, to_id, sub_category from question_relations where (from_id = $question_id or to_id = $question_id)";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $return['exercises'] = array();
    while ($row = mysql_fetch_row($result)) {
        if (!is_array($return['exercises'][(string)(int)$row[2]]))
            $return['exercises'][(string)(int)$row[2]] = array();

        if ($row[0] != $question_id and !in_array($row[0], $return['exercises'][(string)(int)$row[2]])) {
            $return['exercises'][(string)(int)$row[2]][] = $row[0];
        }
        if ($row[1] != $question_id and !in_array($row[1], $return['exercises'][(string)(int)$row[2]])) {
            $return['exercises'][(string)(int)$row[2]][] = $row[1];
        }
    }
    return $return;
}


function getChapters()
{
    $query = "SELECT b1.ID, b1.name as chapterName, count(b2.ID) as subChaptersNumber, b1.done, b1.crdti
                FROM book b1
                left outer join book b2 on b2.ref_ID = b1.ID
                where b1.ref_ID is null
                group by b1.ID, b1.name, b1.done
                order by b1.order";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $resultArray = array();
    while ($row = mysql_fetch_assoc($result)) {
        list($row['crdti']) = explode(' ', $row['crdti']);
        $row['crdti'] = str_replace('-', '.', $row['crdti']);
        $resultArray[] = $row;
    }
    return $resultArray;
}

function getSubChaptersByChapterId($chapterId)
{
    $query = "SELECT b1.ID, b1.ref_ID, b1.name as subChapterName, b1.done, b1.order, b1.is_silence_message_sent, b1.crdti, b1.updti, b1.concept_eng, count(f.id) as forumEntryNumber,(select count from Email_open_stat cc where cc.subchapterID=b1.ID) as mailCount
                FROM book b1 
				left outer join forum f on f.book_id = b1.id
				
                where b1.ref_ID = " . (int)$chapterId . "
                group by b1.ID, b1.ref_ID, b1.name, b1.done, b1.order
                order by b1.order";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $resultArray = array();
    while ($row = mysql_fetch_assoc($result)) {
        list($row['crdti']) = explode(' ', $row['crdti']);
        $row['crdti'] = str_replace('-', '.', $row['crdti']);
        $resultArray[] = $row;
    }
    return $resultArray;
}

function getSubChaptersByChapterIdOrderByTop($chapterId)
{
    $query = "SELECT b1.ID, b1.ref_ID, b1.name as subChapterName, b1.done, b1.order, b1.is_silence_message_sent, b1.crdti, b1.updti, b1.concept_eng, count(f.id) as forumEntryNumber,(select count from Email_open_stat cc where cc.subchapterID=b1.ID) as mailCount
                FROM book b1
				left outer join forum f on f.book_id = b1.id
				
                where b1.ref_ID = " . (int)$chapterId . "
                group by b1.ID, b1.ref_ID, b1.name, b1.done, b1.order
                order by mailCount desc";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $resultArray = array();
    while ($row = mysql_fetch_assoc($result)) {
        list($row['crdti']) = explode(' ', $row['crdti']);
        $row['crdti'] = str_replace('-', '.', $row['crdti']);
        $resultArray[] = $row;
    }
    return $resultArray;
}

function getCurrentChapter($sourceId)
{
    $query = "SELECT ID, name, name_eng, name_gre, name_si, name_it, ref_ID, concept, concept_eng, concept_si, concept_it, exercise, exercise_eng, exercise_si, exercise_it, done, `order`, crdti, updti, card_number
                FROM book
                where ID = " . (int)$sourceId;

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    while ($row = mysql_fetch_assoc($result)) {
        $record = $row;
    }
    return $record;
}

function getSubChaptersByChapter($chapterId)
{
    $query = "SELECT ID, name, name_eng, name_gre, name_si, name_it, ref_ID, concept, concept_eng, concept_si, concept_it, exercise, exercise_eng, exercise_si, exercise_it, done, `order`, crdti, updti, is_silence_message_sent
                FROM book
                where ref_ID = " . (int)$chapterId . "
                order by crdti";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $records = array();
    while ($row = mysql_fetch_assoc($result)) {
        $records[] = $row;
    }
    return $records;
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    while ($row = mysql_fetch_assoc($result)) {
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
    $fields['name_it'] = "'" . $storeArray['name_it'] . "'";
    $fields['name_gre'] = "'" . $storeArray['name_gre'] . "'";
    $fields['concept'] = "'" . $storeArray['concept'] . "'";
    $fields['exercise'] = "'" . $storeArray['exercise'] . "'";
    $fields['concept_eng'] = "'" . $storeArray['concept_eng'] . "'";
    $fields['exercise_eng'] = "'" . $storeArray['exercise_eng'] . "'";
    $fields['concept_si'] = "'" . $storeArray['concept_si'] . "'";
    $fields['concept_it'] = "'" . $storeArray['concept_it'] . "'";
    $fields['exercise_si'] = "'" . $storeArray['exercise_si'] . "'";
    $fields['exercise_it'] = "'" . $storeArray['exercise_it'] . "'";
    $fields['done'] = (int)$storeArray['done'];
    if ($storeArray['ref_ID']) {
        $fields['ref_ID'] = $storeArray['ref_ID'];
    } else {
        $fields['ref_ID'] = 'null';
    }
    $fields['updti'] = $storeArray['updti'];

    if ($fields['done'] == 96) {
        $sql = "select card_number from book where ID = " . (int)$storeArray['ID'];

        $result = mysql_query($sql);
        list($currentCardNumber) = mysql_fetch_row($result);
        if (!$currentCardNumber) {
            $sql = "select max(card_number) + 1 from book";

            $result = mysql_query($sql);
            list($newCardNumber) = mysql_fetch_row($result);

            $fields['card_number'] = $newCardNumber;
        }
    }

    $sqlString = array();
    foreach ($fields as $key => $value) {
        $sqlString[] = "$key = $value";
    }
    $sql = "update book set " . implode(', ', $sqlString) . "
                where ID = " . (int)$storeArray['ID'];

    $result = mysql_query($sql);
    if (!$result) {
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
    foreach ($fields as $key => $value) {
        $sqlString[] = "$key = $value";
    }
    $sql = "update book set " . implode(', ', $sqlString) . "
                where ID = " . (int)$storeArray['ID'];

    $result = mysql_query($sql);
    if (!$result) {
        print mysql_error();
        return false;
    }
    return true;
}

function setOrder($currentId, $group, $afterWhat)
{
    if (!$afterWhat) {
        return true;
    }
    $query = "select `order` from book where ID = " . (int)$afterWhat;

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        return false;
    }
    $order = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $order = (int)$row['order'];
    }

    if ($group > 0) {
        $queryGroup = " and ref_ID = " . (int)$group;
    } else {
        $queryGroup = " and ref_ID is null";
    }

    $query = "update book set `order` = `order` + 2
                    where `order` >= $order and ID != " . (int)$afterWhat . $queryGroup;

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        return false;
    }

    $query = "update book set `order` = $order + 1 where ID = '$currentId' " . $queryGroup;
    $result = mysql_query($query);
    if (!$result) {
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
    $fields['name_it'] = "'" . $storeArray['name_it'] . "'";
    $fields['name_gre'] = "'" . $storeArray['name_gre'] . "'";
    $fields['concept'] = "'" . $storeArray['concept'] . "'";
    $fields['exercise'] = "'" . $storeArray['exercise'] . "'";
    $fields['concept_eng'] = "'" . $storeArray['concept_eng'] . "'";
    $fields['exercise_eng'] = "'" . $storeArray['exercise_eng'] . "'";
    $fields['concept_si'] = "'" . $storeArray['concept_si'] . "'";
    $fields['exercise_si'] = "'" . $storeArray['exercise_si'] . "'";
    $fields['concept_it'] = "'" . $storeArray['concept_it'] . "'";
    $fields['exercise_it'] = "'" . $storeArray['exercise_it'] . "'";
    $fields['done'] = (int)$storeArray['done'];
    if ($storeArray['ref_ID']) {
        $fields['ref_ID'] = $storeArray['ref_ID'];
    } else {
        $fields['ref_ID'] = 'null';
    }
    $fields['updti'] = $storeArray['updti'];
    $fields['crdti'] = $fields['updti'];

    if ($storeArray['ref_ID'] > 0) {
        $query = "select max(`order`) + 1 from book where ref_id = {$fields['ref_ID']}";
        $result = mysql_query($query);
        if (!$result) {
            print mysql_error();
            return true;
        }
        while ($row = mysql_fetch_row($result)) {
            $fields['`order`'] = $row[0];
        }
    }
    if (!$fields['`order`']) {
        $fields['`order`'] = 1;
    }
    $sql = "insert into book (" . implode(', ', array_keys($fields)) . ") values (" . implode(', ', array_values($fields)) . ")";
    //print $sql;
    $result = mysql_query($sql);
    if (!$result) {
        print mysql_error();
        return false;
    }

    $query = "select b1.ID as MAIN_ID, b2.ID AS REF_ID
                from book b1
                left outer join book b2 on b1.ref_ID = b2.ID
                where b1.name = {$fields['name']} and coalesce(b1.ref_ID, 0) = " . (int)$storeArray['ref_ID'];
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        return true;
    }
    while ($row = mysql_fetch_assoc($result)) {
        $id = $row['MAIN_ID'];
        $refId = $row['REF_ID'];
    }
    // ha ez l?tezik, akkor ? alfejezet ?s a ref_id a fejezet
    if ($refId > 0) {
        $chapterId = $refId;
        $subChapterId = $id;
    } else {
        $chapterId = $id;
        $subChapterId = null;
    }
    return array($id, $chapterId, $subChapterId);
}

function deleteChapter($id)
{
    $query = "select count(*) as nr from book where ref_ID = " . (int)$id;
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        return false;
    }
    while ($row = mysql_fetch_assoc($result)) {
        $record = $row;
    }
    if ($record['nr'] > 0) {
        print "<script>alert('Erre a fejezetre hivatkoznak alfejezetek!');</script>";
        return false;
    }

    $sql = "delete from ruin_exercises where exercise_id = " . (int)$id;
    $result = mysql_query($sql);
    if (!$result) {
        print mysql_error();
        return false;
    }

    $sql = "delete from book where ID = " . (int)$id;
    //print $sql;

    $result = mysql_query($sql);
    if (!$result) {
        print mysql_error();
        return false;
    }

    return true;
}

function storeRuinSubChapterLink($ruin_ID, $selectedSubChaptersIdArray)
{
    if (!$ruin_ID) {
        return false;
    }
    $query = "BEGIN";
    $result = mysql_query($query);
    if (!$result) {
        print __LINE__;
        print mysql_error();
        return false;
    }

    $query = "delete from ruins_subchapters where ruin_ID = " . (int)$ruin_ID;
    $result = mysql_query($query);
    if (!$result) {
        print __LINE__;
        print mysql_error();
        $query = "ROLLBACK";
        $result = mysql_query($query);
        return false;
    }
    foreach ($selectedSubChaptersIdArray as $actSubChapterId) {
        $query = "insert into ruins_subchapters (ruin_ID, subchapter_ID) values(" . (int)$ruin_ID . ", " . (int)$actSubChapterId . ')';
        $result = mysql_query($query);
        if (!$result) {
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

function getUserObj($username, $email)
{
    if (strlen($username) == 0 && strlen($email)) {
        return false;
    }

    //?gy kisz?rj?k a securitiy k?d probl?m?t, ha aposztr?fot ?r be!
    $email = str_replace("'", "", $email);

    $username_encod = base64_encode($username);
    $query = "SELECT * from jelentkezok where username = '$username_encod' AND email = '$email'";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $userObject = false;
    while ($row = mysql_fetch_assoc($result)) {
        $userObject = $row;
    }
    return $userObject;
}

function getUserObjForLogin($username, $email)
{
    if (strlen($username) == 0 || strlen($email) == 0) {
        return false;
    }

    $query = "SELECT * from jelentkezok where username = '$username' and email = '$email'";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $userObject = false;
    while ($row = mysql_fetch_assoc($result)) {
        $userObject = $row;
    }
    return $userObject;
}

function getUserObjByEmail($email)
{
    if (strlen($email) == 0) {
        return false;
    }

    $query = "SELECT * from jelentkezok where email = '$email'";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $userObject = false;
    while ($row = mysql_fetch_assoc($result)) {
        $userObject = $row;
    }
    return $userObject;
}

function endiMail($to, $subject, $body, $fromName, $fromEmail, $hiddenAddresses = array(), $userNames = array(), $userLogins = array(), $charcode = 'ISO-8859-2', $userEmails = array(), $head = '')
{
    // a lev?lk?ld?s lev?gja a body utols? k?t karakter?t, tudja a h?h?r hogy mi?rt
    $body = str_replace(chr(13) . chr(10), "<br>", $body);
    $body = "<span style='font-family:Arial;'>" . $body . '</span>  ';
    $body = "<HTML><head><META HTTP-EQUIV='CHARSET' CONTENT='text/html; charset=$charcode'>" . $head . "</head><body>" . $body . "</body></html>  ";
    //$body = "<HTML><head><META HTTP-EQUIV='CHARSET' CONTENT='text/html; charset=utf-8'></head><body>" . $body . "</body></html>  ";
    $mime_boundary = "---- self-coaching ----" . md5(time());
    $headers = "From: $fromName <$fromEmail>\n";
    $headers .= "Reply-To: $fromName <$fromEmail>\n";
    $headers .= "MIME-Version: 1.0\n";
    //    $headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\r\n";
    $headers .= "Content-Type: text/html\n";
    //$headers .= "Content-Transfer-Encoding: base64" . "\n";

    if (count((array)$hiddenAddresses) > 0) {
        for ($i = 0; $i < count((array)$hiddenAddresses); $i++) {
            if (count($userNames) == count($hiddenAddresses) and count($userNames) > 0) {
                $body2 = str_replace('<nameVar />', $userNames[$i], $body);
                $body2 = str_replace('<loginVar />', $userLogins[$i], $body2);
                $body2 = str_replace('<emailVar />', $userEmails[$i], $body2);
            }
            if (!mail($hiddenAddresses[$i], $subject, $body2, $headers)) {
                return false;
            }
            logEmail();
        }
        return true;

        // $headers .= "BCC: " . implode(',', $hiddenAddresses) . "\r\n";
    }
    set_time_limit(0);

    $subject = "=?UTF-8?B?" . base64_encode($subject) . "?=";
    if (!mail($to, $subject, $body, $headers)) {
        return false;
    }
    logEmail();
    return true;
}

function getAllUsers()
{
    $query = "SELECT * from jelentkezok";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $userObject = array();
    while ($row = mysql_fetch_assoc($result)) {
        $userObject[] = $row;
    }
    return $userObject;
}

function getSpecificUsers($status = 0, $language = 0)
{
    $query = "SELECT * from jelentkezok where status = $status and send_mail = 1";
    if ($language > 0) {
        $query .= " and language = " . (int)$language;
    }

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $userObject = array();
    while ($row = mysql_fetch_assoc($result)) {
        $userObject[] = $row;
    }
    return $userObject;
}

function getUsersByStatusArray($statusArray)
{
    $statusArray[] = -1;
    $query = "SELECT * from jelentkezok where status in (" . implode(', ', $statusArray) . ")";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $userObject = array();
    while ($row = mysql_fetch_assoc($result)) {
        $userObject[$row['ID']] = $row;
    }
    return $userObject;
}

function getLingoCasaSubscribedUsers()
{
    $query = "SELECT * from lmjelentkezok order by ID";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $userObject = array();
    while ($row = mysql_fetch_assoc($result)) {
        $userObject[] = $row;
    }
    return $userObject;
}

function getTestUsers($lastSentId = 0)
{
    $query = "SELECT * from tesztcimek where ID > " . (int)$lastSentId . " order by ID";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $userObject = array();
    while ($row = mysql_fetch_assoc($result)) {
        $userObject[] = $row;
    }
    return $userObject;
}

function getTrainingMaterial()
{
    $query = "select b1.ID, b1.name as book_name, b2.ID as sub_ID, b2.name as sub_book_name, b2.concept 
                from book b1
                inner join book b2 on b2.ref_ID = b1.ID and b2.done = 91
                order by b1.order, b1.ID, b2.order";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function sendFormMail($subChapterId)
{
    $to = 'hello@selfcoaching.life';
    $allUsers = getSubscribedUsers();
    $users = array();
    for ($i = 0; $i < count($allUsers); $i++) {
        $users[] = $allUsers[$i]['email'];
    }
    $subChapter = getCurrentChapter($subChapterId);
    //    $subject = "Elk?sz?lt egy alfejezet";
    $subject = "ELK?SZ?LT ALFEJEZET: {$subChapter['name']}";
    $body = "Szia! A(z) \"{$subChapter['name']}\" c?m? gyakorlat ?j v?ltozata elk?sz?lt. Ha sz?vesen megismerkedn?l vele, a <a href='http://www.healers.digital'>http://www.healers.digital</a> weboldalon 'A Gyakorlatsor' men?pont alatt olvashatod a jelszavaddal val? bel?p?s ut?n. K?v?nok felismer?seket, bizonyoss?god meger?s?d?s?t! Lucien";

    print "<script>";
    if (endiMail($to, $subject, $body, 'pHGeneration', 'hello@selfcoaching.life', $users)) {
        print "alert('?zenet k?ld?se siker?lt!')";
    } else {
        print "alert('?zenet k?ld?se nem siker?lt!')";
    }
    print "</script>";
}

function searchChapters($text)
{
    $text = str_replace("'", "''", $text);
    $where_add = "(lower(sc.name) like lower('%$text%') or lower(c.name) like lower('%$text%') or lower(sc.name_eng) like lower('%$text%') or lower(c.name_eng) like lower('%$text%') or lower(sc.name_it) like lower('%$text%') or lower(c.name_it) like lower('%$text%') ";
    $where_add .= " or lower(sc.concept) like lower('%$text%') or lower(sc.concept_eng) like lower('%$text%') or lower(sc.concept_it) like lower('%$text%')";
    $where_add .= " or lower(sc.exercise) like lower('%$text%') or lower(sc.exercise_eng) like lower('%$text%') or lower(sc.exercise_it) like lower('%$text%') ) ";
    $query = "select c.ID as chapterId, sc.ID as subChapterId, c.name as chapterName, c.name_eng as chapterNameEng, c.name_it as chapterNameIt, sc.name as subChapterName, sc.name_eng as subChapterNameEng, 
                    sc.name_it as subChapterNameIt, sc.concept, sc.concept_eng, sc.concept_it, sc.exercise, sc.exercise_eng, sc.exercise_it
                from book sc
                inner join book c on sc.ref_ID = c.ID
                where sc.ref_ID is not null and sc.done not in (18, 13, 97, 95, 10, 90, 93, 15, 11)
                and {$where_add}";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $results = array();
    while ($row = mysql_fetch_assoc($result)) {
        $results[] = $row;
    }
    if (count($results) > 1) {
        foreach ($results as $row) {
            if (!contains($row['chapterName'], $text) && contains($row['chapterNameEng'], $text))
                $row['chapterName'] = $row['chapterNameEng'];
            if (!contains($row['subChapterName'], $text) && contains($row['subChapterNameEng'], $text))
                $row['subChapterName'] = $row['subChapterNameEng'];
            if (!contains($row['concept'], $text) && contains($row['concept_eng'], $text))
                $row['concept'] = $row['concept_eng'];
            if (!contains($row['exercise'], $text) && contains($row['exercise_eng'], $text))
                $row['exercise'] = $row['exercise_eng'];
        }
    }
    return $results;
}

function contains($haystack, $needle)
{
    if (strpos(mb_strtolower($haystack), mb_strtolower($needle)) !== false) {
        return true;
    }
    return false;
}

function getClearedNumber()
{
    $query = "select count(*) as nr from book where done = 1";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $number = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}
function getDoneNumber()
{
    $query = "select count(*) as nr from book where done = 2";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $number = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function storeSettings($userId, $settingArray)
{
    if (!$userId) {
        return false;
    }
    $query = "update jelentkezok set
        is_napidezete = " . (int)$settingArray['is_napidezete'] . ",
        is_news = " . (int)$settingArray['is_news'] . ",
        language = " . (int)$settingArray['language'] . ",
        varos_id = " . (int)$settingArray['hunCity'] . ",
        vezeteknev = '" . str_replace("'", "''", $settingArray['vezeteknev']) . "',
        keresztnev = '" . str_replace("'", "''", $settingArray['keresztnev']) . "'
        where ID = " . (int)$userId;

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        return false;
    }
    return true;
}


function logLogin()
{
    $query = "select count(*) as nr from login_log where DATUM = CURDATE()";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $number = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    if ($number > 0) {
        $query = "update login_log set NR = NR + 1 where DATUM = CURDATE()";
    } else {
        $query = "insert into login_log (DATUM) values(CURDATE())";
    }

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        return false;
    }
    return true;
}

function logPersonalLogin($id)
{
    $query = "update jelentkezok set last_login = NOW() where ID = " . (int)$id;

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
}

function getLoggedNumber()
{
    $query = "select NR from login_log where DATUM = CURDATE()";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $number = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $number = $row['NR'];
    }
    return $number;
}

function changePassword($userId, $settingArray)
{
    if (!$userId) {
        return false;
    }
    if ($settingArray['newPassword'] != $settingArray['confirmNewPassword']) {
        print "<script>alert('" . translate('passwordMismatch') . "');</script>";
        return false;
    }
    $username = str_replace("'", "''", $settingArray['oldPassword']);
    $newUsername = str_replace("'", "''", $settingArray['newPassword']);
    $query = "select count(*) as NR from jelentkezok where username = '$username' and ID = " . (int)$userId;

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $number = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $number = $row['NR'];
    }
    if ($number < 1) {
        print "<script>alert('" . translate('origPwBad') . "');</script>";
        return false;
    }

    $query = "update jelentkezok set username = '$newUsername' where ID = " . (int)$userId;

    $result = mysql_query($query);
    if (!$result) {
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $resultArray = array();
    while ($row = mysql_fetch_assoc($result)) {
        $resultArray[] = $row;
    }
    return $resultArray;
}

function updateLearningQuestion($storeArray)
{
    $fields['name'] = "'" . str_replace("'", "''", $storeArray['name']) . "'";
    $fields['concept'] = "'" . str_replace("'", "''", $storeArray['concept']) . "'";
    $fields['exercise'] = "'" . str_replace("'", "''", $storeArray['exercise']) . "'";
    $fields['imgName'] = "'" . $storeArray['imgName'] . "'";
    $fields['imgPath'] = "'" . $storeArray['imgPath'] . "'";
    $fields['updti'] = $storeArray['updti'];

    $sqlString = array();
    foreach ($fields as $key => $value) {
        $sqlString[] = "$key = $value";
    }
    $sql = "update book set " . implode(', ', $sqlString) . "
                where ID = " . (int)$storeArray['ID'];

    $result = mysql_query($sql);
    if (!$result) {
        print mysql_error();
        return false;
    }

    if (array_key_exists('exercises', $storeArray)) {
        $subCategory = $storeArray["subCategory"];
        $sql = "delete from question_relations where (from_id = " . (int)$storeArray['ID'] . " or to_id = " . (int)$storeArray['ID'] . ")";
        if ($subCategory)
            $sql .= " and sub_category = " . (int)$subCategory;
        else
            $sql .= " and sub_category is null";

        $result = mysql_query($sql);
        if (!$result) {
            print mysql_error();
            return false;
        }
        if (!$subCategory)
            $subCategorySql = "null";
        else
            $subCategorySql = (int)$subCategory;
        foreach ((array)$storeArray['exercises'] as $currentId) {
            $sql = "insert into question_relations (from_id, to_id, sub_category) values(" . (int)$storeArray['ID'] . ", $currentId, $subCategorySql)";
            $result = mysql_query($sql);
            if (!$result) {
                print mysql_error();
                return false;
            }
        }
    }
    return true;
}

function deleteLearningQuestion($id)
{
    $sql = "delete from question_relations where (from_id = " . (int)$id . " or to_id = " . (int)$id . ")";
    $result = mysql_query($sql);
    if (!$result) {
        print mysql_error();
        return false;
    }

    $sql = "delete from book where ID = " . (int)$id;
    $result = mysql_query($sql);
    if (!$result) {
        print mysql_error();
        return false;
    }
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
    foreach ($fields as $key => $value) {
        $sqlString[] = "$key = $value";
    }
    $sql = "update success_stories set " . implode(', ', $sqlString) . "
                where ID = " . (int)$storeArray['ID'];

    $result = mysql_query($sql);
    if (!$result) {
        print mysql_error();
        return false;
    }

    if (array_key_exists('exercises', $storeArray)) {
        $sql = "select ID from book where done = 90";
        $result = mysql_query($sql);
        if (!$result) {
            print mysql_error();
            return true;
        }
        $ids = array(0);
        while ($row = mysql_fetch_row($result)) {
            $ids[] = $row[0];
        }

        $sql = "delete from success_story_exercises where exercise_id in (" . implode(',', $ids) . ") and success_stories_id = " . (int)$storeArray['ID'];

        $result = mysql_query($sql);
        if (!$result) {
            print mysql_error();
            return false;
        }
        foreach ((array)$storeArray['exercises'] as $currentId) {
            $sql = "insert into success_story_exercises (success_stories_id, exercise_id) values(" . (int)$storeArray['ID'] . ", " . (int)$currentId . ")";

            $result = mysql_query($sql);
            if (!$result) {
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
    if (!$result) {
        print mysql_error();
        return false;
    }
    $query = "select ID
                from success_stories
                where story_name = {$fields['story_name']} ";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        return true;
    }
    while ($row = mysql_fetch_assoc($result)) {
        $id = $row['ID'];
    }

    foreach ((array)$storeArray['exercises'] as $currentId) {
        $sql = "insert into success_story_exercises (success_stories_id, exercise_id) values(" . (int)$id . ", " . (int)$currentId . ")";

        $result = mysql_query($sql);
        if (!$result) {
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
    if (!$result) {
        print mysql_error();
        return false;
    }

    $sql = "delete from success_story_exercises where success_stories_id = " . (int)$id;
    $result = mysql_query($sql);
    if (!$result) {
        print mysql_error();
        return false;
    }

    return true;
}

function getBookNumber($done)
{
    $query = "SELECT count(*) as nr
                FROM book
                where done = " . (int)$done;

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $number = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getAlfejezetNumberConceptZero()
{
    $query = "select count(*) as nr from book where done = 90 and concept= ' '";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $number = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getAlfejezetNumberConceptSomething()
{
    $query = "select count(*) as nr from book where done = 90 and concept!= ' '";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $number = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getAlfejezetNumberExerciseZero()
{
    $query = "select count(*) as nr from book where done = 90 and concept!= ' ' and exercise is null";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $number = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getJelentkezokNumberGraduates()
{
    $query = "select count(*) as nr from jelentkezok where status = 4";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $number = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getReferenceNumberReference()
{
    $query = "select count(*) as nr from jelentkezok where reference = 1";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $number = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getReferenceNumberSearchengine()
{
    $query = "select count(*) as nr from jelentkezok where reference = 2";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $number = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getReferenceNumberBanner()
{
    $query = "select count(*) as nr from jelentkezok where reference = 3";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $number = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getReferenceNumberByChance()
{
    $query = "select count(*) as nr from jelentkezok where reference = 4";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $number = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getReferenceNumberByKezdjelelni()
{
    $query = "select count(*) as nr from jelentkezok where reference = 5";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $number = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getReferenceNumberByIwiw()
{
    $query = "select count(*) as nr from jelentkezok where reference = 9";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $number = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getRuinNumber()
{
    $query = "select count(*) as nr from ruin";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $number = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getSuccessStoryNumber()
{
    $query = "select count(*) as nr from success_stories where book_ID != 0 ";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $number = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}

function getDalNumber()
{
    $query = "select count(*) as nr from linker where type = 1 ";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $number = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    return $number;
}


function getUnsentNapidezeteSzerzok()
{
    $query = "select szerzo from idezet where kikuldve = 0 order by ID desc";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $szerzok = array();
    while ($row = mysql_fetch_assoc($result)) {
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
function SendMail($emailaddress, $from, $fromaddress, $emailsubject = "", $body = "", $html = true, $attachment = "", $fileArray = null, $encoding = "iso-8859-2")
{ //{{{
    # Is the OS Windows or Mac or Linux
    if (strtoupper(substr(PHP_OS, 0, 3) == 'WIN')) {
        $eol = "
";
    } elseif (strtoupper(substr(PHP_OS, 0, 3) == 'MAC')) {
        $eol = "\r";
    } else {
        $eol = "\n";
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
    $headers .= "From: " . $from . " <" . $fromaddress . ">" . $eol;
    $headers .= "Reply-To: " . $from . " <" . $fromaddress . ">" . $eol;
    $headers .= "Return-Path: " . $from . " <" . $fromaddress . ">" . $eol;    // these two to set reply address
    $headers .= "Message-ID: <" . time() . " TheSystem@" . $_SERVER['SERVER_NAME'] . ">" . $eol;
    $headers .= "X-Mailer: PHP v" . phpversion() . $eol;          // These two to help avoid spam-filters
    $headers .= 'MIME-Version: 1.0' . $eol;

    if (!empty($attachment)) {
        //send multipart message
        # Boundry for marking the split & Multitype Headers
        $mime_boundary = md5(time());
        $headers .= "Content-Type: multipart/related; boundary=\"" . $mime_boundary . "\"" . $eol;

        # File for Attachment

        if (!$fileArray) {
            $f_name = $attachment;
            $handle = fopen($f_name, 'rb');
            $f_contents = fread($handle, filesize($f_name));
            $f_contents = chunk_split(base64_encode($f_contents)); //Encode The Data For Transition using base64_encode();
            $f_type = filetype($f_name);
            fclose($handle);
        } else {
            $f_name = $fileArray['f_name'];
            $f_contents = $fileArray['f_contents'];
            $f_type = $fileArray['f_type'];
        }

        # Attachment
        $msg .= "--" . $mime_boundary . $eol;
        $msg .= "Content-Type: application/jpeg; name=\"" . $file . "\"" . $eol;
        $msg .= "Content-Transfer-Encoding: base64" . $eol;
        $msg .= "Content-Disposition: attachment; filename=\"" . basename($attachment) . "\"" . $eol . $eol; // !! This line needs TWO end of lines !! IMPORTANT !!
        $msg .= $f_contents . $eol . $eol;
        # Setup for text OR html
        $msg .= "Content-Type: multipart/alternative" . $eol;


        $contentType = "text/plain";
        if ($html) {
            $contentType = "text/html";
        }

        # Body
        $msg .= "--" . $mime_boundary . $eol;
        $msg .= "Content-Type: " . $contentType . "; charset=\"" . $encoding . "\"" . $eol;
        $msg .= "Content-Transfer-Encoding: 8bit" . $eol . $eol; // !! This line needs TWO end of lines !! IMPORTANT !!
        $msg .= $body . $eol . $eol;

        # Finished
        $msg .= "--" . $mime_boundary . "--" . $eol . $eol;  // finish with two eol's for better security. see Injection.
    } else {
        $headers .= "Content-Type: text/plain; charset=\"" . $encoding . "\"" . $eol;
        $headers .= "Content-Transfer-Encoding: 8bit" . $eol . $eol; // !! This line needs TWO end of lines !! IMPORTANT !!
        $msg .= $body . $eol . $eol;
    }

    // SEND THE EMAIL
    //LogMessage("Sending mail to: ".$emailaddress." => ".$emailsubject);

    //ini_set(sendmail_from, 'from@me.com');  // the INI lines are to force the From Address to be used !
    ini_set(sendmail_from, $fromaddress); //needed to hopefully get by spam filters.
    $success = mail($emailaddress, $emailsubject, $msg, $headers);
    ini_restore(sendmail_from);

    return $success;
} //}}}

function sendAttachmentMail($subject = "", $body = "", $attachment = "", $lastSentId, $maxLetters)
{
    $to = 'hello@selfcoaching.life';
    $allUsers = /*getTestUsers($lastSentId)*/ getSubscribedUsers($lastSentId);

    if ($attachment) {
        $fileArray['f_name'] = $f_name = $attachment;
        $handle = fopen($f_name, 'rb');
        $f_contents = fread($handle, filesize($f_name));
        $fileArray['f_contents'] = chunk_split(base64_encode($f_contents)); //Encode The Data For Transition using base64_encode();
        $fileArray['f_type'] = filetype($f_name);
        fclose($handle);
    }

    $success = true;
    if ($maxLetters > 0) {
        $maxLetters = min(count($allUsers), $maxLetters);
    } else {
        $maxLetters = count($allUsers);
    }
    for ($i = 0; $i < $maxLetters; $i++) {
        $body2 = "Szia {$allUsers[$i]['keresztnev']}!" . chr(13) . chr(10) . chr(13) . chr(10) . $body;
        $body2 = str_replace(chr(13) . chr(10), "<br>", $body2);
        if (!SendMail($allUsers[$i]['email'], 'pHGeneration', 'hello@selfcoaching.life', $subject, $body2, true, $attachment, $fileArray)) {
            $success = false;
        }
    }
    print "<script>";
    /*
    if($success){
        print "alert('?zenet k?ld?se siker?lt!')";
    }
    else{
        print "alert('?zenet k?ld?se nem siker?lt!')";
    }
    */
    print "</script>";
    return array($allUsers[$i - 1]['ID'], $i);
}

function getCategories($orderType = 0)
{
    if ($orderType == 0) {
        $orderString = "b.name";
    } else {
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_row($result)) {
        $returnArray[$row[0]] = array($row[1], $row[2], $row[3]);
    }
    return $returnArray;
}

function getSubchaptersForLinking()
{
    $query = "select b.ID, b.name
                from book b
                where b.done = 96 and b.concept like '%<img%'
                order by b.name";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getBooks($done)
{
    $query = "select b.ID, b.name
                from book b
                where b.done = " . (int)$done . "
                order by b.name";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getConnectingSubchapterIds($id)
{
    $query = "select id_to from subchapter_link where id_from = " . (int)$id;
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_row($result)) {
        $returnArray[] = $row[0];
    }
    return $returnArray;
}

function getConnectingSubchapterNumbers()
{
    $query = "select l.id_from, count(l.id_to) as nr
                from subchapter_link l
                inner join book b on l.id_to = b.id
                where b.done = 96
                group by l.id_from";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_row($result)) {
        $returnArray[$row[0]] = $row[1];
    }
    return $returnArray;
}

function addSubchapterLink($idFrom, $idTo)
{
    $sql = "insert into subchapter_link (id_from, id_to) values ({$idFrom}, {$idTo})";
    $result = mysql_query($sql);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
}

function removeSubchapterLink($idFrom, $idTo)
{
    $sql = "delete from subchapter_link where id_from = {$idFrom} and id_to = {$idTo}";
    $result = mysql_query($sql);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
}

function getExercises()
{
    $query = "select b1.ID, b1.name, b1.name_eng, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng, b2.concept, b2.concept_eng, b2.exercise, b2.exercise_eng, b2.done as subDone, b1.done
                from book b1
                left outer join book b2 on b1.ID = b2.ref_id
                where  b1.done = 90 AND (b2.done = 98 or b2.done = 99 or b2.done = 17)
                order by b1.order, b2.order";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getForumDetails()
{
    $query = "select b1.ID, b1.name, b1.name_eng, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng, b2.concept, b2.concept_eng, b2.exercise, b2.exercise_eng, b2.done as subDone, b1.done
                from book b1
                inner join book b2 on b1.ID = b2.ref_id
                where b1.done = 90 and (b2.done = 96 or b2.done = 99)
                order by b1.order, b2.order";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getExercisesForPrintView1()
{
    $query = "select b1.ID, b1.name, b1.name_eng, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng, b2.concept, b2.concept_eng, b2.exercise, b2.exercise_eng, b2.done as subDone, b1.done
                from book b1
                left outer join book b2 on b1.ID = b2.ref_id
                where b1.done = 90 and b2.done = 96 or b1.done = 10
                order by b1.order, b2.order";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getExercisesForPrintView1a()
{
    $query = "select b1.ID, b1.name, b1.name_eng, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng, b2.concept, b2.concept_eng, b2.exercise, b2.exercise_eng, b2.done as subDone, b1.done
                from book b1
                left outer join book b2 on b1.ID = b2.ref_id
                where b1.done = 90 and b2.done = 3
                order by b1.order, b2.order";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_assoc($result)) {
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getExercises93()
{
    $query = "select b1.ID, b1.name, b1.name_eng, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng, b2.concept, b2.concept_eng, b2.exercise, b2.exercise_eng, b2.done as subDone,
                    b2.crdti
                from book b1
                left outer join book b2 on b1.ID = b2.ref_id
                where b1.done = 90 and b2.done = 93
                order by b1.order, b2.order";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_assoc($result)) {
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
                where b1.done = 90 and b2.done = 92
                order by b2.name COLLATE utf8_hungarian_ci";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getLearningQuestions2()
{
    $query = "select b1.ID, b1.name, b1.name_eng, b1.name_si, b1.name_it, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng, b2.name_si as sub_name_si, b2.name_it as sub_name_it
                    , b2.concept, b2.concept_eng, b2.concept_si, b2.concept_it
                    , b2.exercise, b2.exercise_eng, b2.exercise_si, b2.exercise_it, b2.done as subDone, b2.imgName
                from book b1
                left outer join book b2 on b1.ID = b2.ref_id
                where b1.done = 90 and b2.done = 94
                order by b1.order, b2.order";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_assoc($result)) {
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getForumEntryNumbersByBooks()
{
    $query = "select b.id, count(*) as nr from book b inner join forum f on b.id = f.book_id group by b.id";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_row($result)) {
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_assoc($result)) {
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_assoc($result)) {
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_assoc($result)) {
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_assoc($result)) {
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getExerciseById($id)
{
    $query = "select b1.ID, b1.name, b1.name_eng, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng, b2.concept, b2.concept_eng, b2.exercise, b2.exercise_eng
                from book b1
                left outer join book b2 on b1.ID = b2.ref_id
                where b1.done = 90 and (b2.done = 99 or b2.done = 94 or b2.done = 96 or b2.done = 3)
                and b1.ID = " . (int)$id . "
                order by b1.order, b2.order";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getBooksByDone($done, $from = 0, $to = 0)
{
    $part = "";
    if ($from > 0) {
        $part .= " and card_number >= " . (int)$from;
    }
    if ($to > 0) {
        $part .= " and card_number <= " . (int)$to;
    }
    $query = "select ID, name, name_eng, name_it, concept, concept_eng, concept_it, exercise, exercise_eng, exercise_it, card_number
                from book
                where done = " . (int)$done . " {$part} order by card_number";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getBooksByDone2($done)
{
    $query = "select ID, name, name_eng, name_it, concept, concept_eng, concept_it, exercise, exercise_eng, exercise_it, card_number
                from book
                where done = " . (int)$done . " order by name";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_assoc($result)) {
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_assoc($result)) {
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_assoc($result)) {
        $row["subCategories"] = array();
        if ($row["ref_ID"] > 0) {
            $returnArray[$row["ref_ID"]]["subCategories"][$row["ID"]] = $row;
        } else {
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
    if ($_SESSION['language'] == 'eng') {
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getAllCategoryNamesByIds()
{
    $nameCol = 'b.name';
    if ($_SESSION['language'] == 'eng') {
        $nameCol .= '_eng';
    }

    $query = "select ID, $nameCol from book b where done = 99";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_row($result)) {
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    while ($row = mysql_fetch_row($result)) {
        $number = $row[0];
    }
    return $number;
}

function getOrszagok()
{
    $query = "select id, title_hun, title_eng, title_ger from orszag";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function getRegisteredCountryNumber()
{
    $query = "SELECT count(distinct orszag_id) FROM jelentkezok";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $number = 0;
    while ($row = mysql_fetch_row($result)) {
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $resultArray = array();
    while ($row = mysql_fetch_assoc($result)) {
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $resultArray = array();
    while ($row = mysql_fetch_assoc($result)) {
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $resultArray = array();
    while ($row = mysql_fetch_assoc($result)) {
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $resultArray = array();
    while ($row = mysql_fetch_assoc($result)) {
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $resultArray = array();
    while ($row = mysql_fetch_assoc($result)) {
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $resultArray = array();
    while ($row = mysql_fetch_assoc($result)) {
        $resultArray[] = $row;
    }
    return $resultArray;
}

function getInvitorUsers()
{
    $query = "select id, title_hun from varos";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $varosArray = array();
    while ($row = mysql_fetch_assoc($result)) {
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $resultArray = array();
    while ($row = mysql_fetch_assoc($result)) {
        $row['varos'] = $varosArray[$row['varos_id']];
        $resultArray[] = $row;
    }
    return $resultArray;
}

function saveTestUserSubchapterId($userId, $subChapterId)
{
    $query = "update jelentkezok set test_subchapter_id = " . (int)$subChapterId . " where ID = " . (int)$userId;
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
}

function getOrszagById($id)
{
    $query = "select title_hun from orszag where id = " . (int)$id;
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $return = '';
    while ($row = mysql_fetch_row($result)) {
        $return = $row[0];
    }
    return $return;
}

function getVarosById($id)
{
    $query = "select title_hun from varos where id = " . (int)$id;
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $return = '';
    while ($row = mysql_fetch_row($result)) {
        $return = $row[0];
    }
    return $return;
}

function getReferences()
{
    $query = "select id, title_hun, title_eng, title_ger from reference";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function translate($word)
{
    global $trans;
    if (array_key_exists($word, (array)$trans)) {
        return $trans[$word];
    } else {
        return $word;
    }
}

function translateDb($hunText, $engText, $gerText, $siText)
{
    global $_SESSION;
    if ($_SESSION['language'] == 'eng') {
        return $engText;
    } else if ($_SESSION['language'] == 'ger') {
        return $gerText;
    } else if ($_SESSION['language'] == 'gr') {
        return $greekText;
    } else if ($_SESSION['language'] == 'si') {
        return $siText;
    } else {
        return $hunText;
    }
}

function setAsFavouriteQuote($userId, $quoteId)
{
    if ($userId == 0 or $quoteId == 0) {
        return false;
    }
    $query = "SELECT count(*) as num FROM jelentkezok_kedvenc k
                where k.jelentkezok_id = $userId and k.table_name = 'idezet' and k.table_id = $quoteId";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    while ($row = mysql_fetch_row($result)) {
        $num = $row[0];
    }
    if ($num > 0) {
        return -1;
    }

    $sql = "insert into jelentkezok_kedvenc (jelentkezok_id, table_name, table_id) values ($userId, 'idezet', $quoteId)";
    $result = mysql_query($sql);
    if (!$result) {
        print mysql_error();
        return false;
    }
}

function getMyFavourites($userId)
{
    $idezetCol = 'idezet';
    $szerzoCol = 'szerzo';
    if ($_SESSION['language'] == 'eng') {
        $suffix = '_eng';
    } else if ($_SESSION['language'] == 'ger') {
        $suffix = '_ger';
    }
    $idezetCol .= $suffix;
    $szerzoCol .= $suffix;

    $query = "SELECT i.$idezetCol as idezet, i.$szerzoCol as szerzo, i.ID, i.DATUM, k.ID as kedvencek_id
                FROM idezet i
                inner join jelentkezok_kedvenc k on k.table_id = i.id and table_name = 'idezet' and k.jelentkezok_id = " . (int)$userId . "
                where i.kikuldve = 1 and i.$idezetCol is not null and i.$idezetCol != ''
                order by k.id desc";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $resultArray = array();
    while ($row = mysql_fetch_assoc($result)) {
        list($row['DATUM']) = explode(' ', $row['DATUM']);
        $resultArray[] = $row;
    }
    return $resultArray;
}

function deleteFavourite($id)
{
    $query = "delete from jelentkezok_kedvenc where id = " . (int)$id;
    $result = mysql_query($query);
    if (!$result) {
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    while ($row = mysql_fetch_row($result)) {
        $num = $row[0];
    }
    return $num;
}

function getFavouritePeopleNumber()
{
    $query = "SELECT count(distinct jelentkezok_id) as num FROM jelentkezok_kedvenc k
                where k.table_name = 'idezet'";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    while ($row = mysql_fetch_row($result)) {
        $num = $row[0];
    }
    return $num;
}

function getForumEntries($id, $isMailSentFilter = false)
{
    $query_part = '';
    if ($id > 0) {
        $query_part .= " and b.id = $id";
    }
    if ($isMailSentFilter) {
        $query_part .= " and t1.mailsent = 0";
    }

    $query = "select t1.id, t1.comment, t1.datum, t1.mailsent, b.name as bookName, j.id as jelentkezok_id, j.vezeteknev, j.keresztnev, b.ID as bookId
                from book b
                left outer join forum t1 on t1.book_id = b.id
                left outer join jelentkezok j on t1.jelentkezok_id = j.id
                where t1.elfogadva = 1 $query_part
                order by t1.datum desc";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_assoc($result)) {
        $returnArray[] = $row;
    }
    return $returnArray;
}

function setForumEntryEmailSent($id, $isSent)
{
    $sql = "update forum set mailsent = " . (int)$isSent . " where id = " . (int)$id;
    $result = mysql_query($sql);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }

    $sql = "select mailsent from forum where id = " . (int)$id;
    $result = mysql_query($sql);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    list($isChecked) = mysql_fetch_row($result);

    return $isChecked;
}

function setAllForumEntryEmailSent()
{
    $sql = "update forum set mailsent = 1 where mailsent = 0 and elfogadva = 1";
    $result = mysql_query($sql);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
}

function getNewsLetterEntries($id)
{
    if ($id > 0) {
        $query_part = " and b2.id = $id";
    }
    $query = "select b1.ID, b1.name, b1.name_eng, b2.ID as sub_ID, b2.name as sub_name, b2.name_eng as sub_name_eng, b2.concept, b2.concept_eng, b2.exercise, b2.exercise_eng, b2.done as subDone, b2.crdti
                from book b1
                left outer join book b2 on b1.ID = b2.ref_id
                where b1.done = 90 and b2.done = 93 $query_part
                order by b1.order, b2.order";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $returnArray = array();
    while ($row = mysql_fetch_assoc($result)) {
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    while ($row = mysql_fetch_row($result)) {
        $returnDate = $row[0];
    }
    return $returnDate;
}

function storeForumComment($storeArray)
{
    $storeArray['comment'] = str_replace("'", "''", $storeArray['comment']);

    $query = "insert into forum (comment, datum, book_id, jelentkezok_id) values('{$storeArray['comment']}', now(), {$storeArray['book_id']}, {$storeArray['jelentkezok_id']})";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        return -1;
    }

    $query = "select id from forum where comment = '{$storeArray['comment']}' and book_id = {$storeArray['book_id']} and jelentkezok_id = {$storeArray['jelentkezok_id']}";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $row = mysql_fetch_row($result);
    return $row[0];
}

function sendMailToMeAboutForumEntry($categoryId, $bookId, $entry, $userObject)
{
    $query = "select b.name as bookName from book b where b.id = $bookId";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    while ($row = mysql_fetch_row($result)) {
        $bookName = $row[0];
    }

    $to = "hello@selfcoaching.life";
    $subject = "?j f?rumbejegyz?s: $bookName";
    $fromName = $userObject['vezeteknev'] . ' ' . $userObject['keresztnev'];
    $fromEmail = $userObject['email'];
    $body = $entry;
    $body .= "

    <a href='http://www.healers.digital/index.php?emailAd=acceptForumEntry&hashcode=awoejkfldjk234asdf&entryId=$categoryId'>Elfogad</a>";
    endiMail($to, $subject, $body, $fromName, $fromEmail);
}

function acceptForumEntry($entryId)
{
    $query = "update forum set elfogadva = 1 where id = " . (int)$entryId;
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $id = 0;
    while ($row = mysql_fetch_row($result)) {
        $id = $row[0];
    }

    if ($id > 0) {
        $query = "update search_log set num = num + 1 where id = " . (int)$id;
    } else {
        $query = "insert into search_log (word, num, crdti) values('$word', 1, now())";
    }
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
}

function getSearchLog()
{
    $query = "select id, word, num, crdti from search_log order by num desc";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $return = array();
    while ($row = mysql_fetch_assoc($result)) {
        $return[] = $row;
    }
    return $return;
}

function sendInvitation($userObject, $invitedAddress, $invitedMessage, $invitationLanguage)
{
    $body = $invitedMessage;
    $body .= "<br><br>";
    $body .= "<a href='http://www.healers.digital/subscribe.php?invUserId=" . $userObject['ID'] . "&lang=" . (int)$invitationLanguage . "'>" . translate('invitationLetterBody') . "</a>";
    $body .= "<br><br>";
    if ((int)$invitationLanguage == 1) {
        $body .= "<img src='http://www.healers.digital/images/ph.jpg' border=0>";
    } else {
        $body .= "<img src='http://www.healers.digital/images/ph.jpg' border=0>";
    }
    return endiMail($invitedAddress, translate('invitationLetterSubject'), $body, $userObject['vezeteknev'] . ' ' . $userObject['keresztnev'], $userObject['email']);
}

function getInvitedNumber($userId)
{
    $query = "select count(*) from jelentkezok where inviter = " . (int)$userId;
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $row = mysql_fetch_row($result);
    return $row[0];
}

function changeUserStatus($userIdentity, $newStatus)
{
    if (strpos($userIdentity, "@") === false) {
        $query_add = "id = " . (int)$userIdentity;
    } else {
        $query_add = "email = '{$userIdentity}'";
    }
    $query = "update jelentkezok set status = " . (int)$newStatus . " where " . $query_add;
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        return -1;
    }
    return mysql_affected_rows();
}

function getUserByIdentity($userIdentity)
{
    if (strpos($userIdentity, "@") === false) {
        $query_add = "id = " . (int)$userIdentity;
    } else {
        $query_add = "email = '{$userIdentity}'";
    }
    $query = "select * from jelentkezok where " . $query_add;
    $result = mysql_query($query);
    if (!$result) {
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $row = mysql_fetch_row($result);
    return $row[0];
}

function getUsersByInviter($inviterId)
{
    $query = "SELECT * from jelentkezok where inviter = " . (int)$inviterId;

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $userObject = array();
    while ($row = mysql_fetch_assoc($result)) {
        $userObject[] = $row;
    }
    return $userObject;
}

function getSendQuoteBody($imgName, $userObject, $quoteObject, $quoteAddressFirstName, $quoteAddress, $userMessage)
{
    if ($_SESSION['language'] == 'eng') {
        $language = 2;
    } else {
        $language = 1;
    }
    $szinek = array(
        'FF3366',
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
        '006400'
    );

    $szin = $szinek[rand(0, 9)];
    $szin = '000000';
    $idezetArray['hun'] = $quoteObject['idezet'];
    $idezetArray['eng'] = $quoteObject['idezet_eng'];
    $idezetArray['ger'] = $quoteObject['idezet_ger'];

    $szerzoArray['hun'] = $quoteObject['szerzo'];
    $szerzoArray['eng'] = $quoteObject['szerzo_eng'];
    $szerzoArray['ger'] = $quoteObject['szerzo_ger'];


    $message = "<HTML><head><META HTTP-EQUIV='CHARSET' CONTENT='text/html; charset=$CHARSET'>
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
                      <p><a style='font-size:10' href='http://www.healers.digital/subscribe.php?invUserId=" . $userObject['ID'] . "&lang=" . (int)$language . "'>www.lifelovers.net</a></p>
                      </td><td align=middle valign=bottom>
                    <img src='http://www.lifelovers.net/quotepics/{$imgName}' height=300 border=0 style='cursor:default'><br><br><br>
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
    if ($_SESSION['language'] == 'eng') {
        $language = 2;
    } else {
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
    $message .= "<span class='white'>" . translate('levelkuldes_4') . "</span>";
    $message .= " &nbsp;<input type='text' name='quoteAddressFirstName' value='" . $_POST['quoteAddressFirstName'] . "' size=15>!</p>
          			<p><FONT face=Arial><FONT size=2>";
    $message .= "<textarea name='message' cols=23 rows=6>" . $_POST['message'] . "</textarea></font>";
    $message .= "</p>
                      <p><span class='white'>" . $userObject['vezeteknev'] . " " . $userObject['keresztnev'] . "</span></p>
                      <p><a href='#" . (int)$language . "'>www.lifelovers.net</a></p>
                      </td><td align=middle valign=bottom width='578'>
                    <img id='mainImg' src='http://www.lifelovers.net/quotepics/{$imgName}' height=300 border=0 style='cursor:default'><br><br><br>
          			<span class='white'>
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
    foreach ($imgArray as $currentImage) {
        $message .= "<img src='http://www.lifelovers.net/quotepics/{$currentImage}' width=100 border=0 onclick=\"document.getElementById('imgName').value = '$currentImage';document.forms[0].submit();\"><br>";
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
    while ($entryName = readdir($myDirectory)) {
        if (is_file("./{$dir}/{$entryName}")) {
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
    while ($entryName = readdir($myDirectory)) {
        if (is_dir("./{$dir}/{$entryName}")) {
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

    $emailAddress = "info@lifelovers.net";
    $headers = "MIME-Version: 1.0\n" .
        "Content-type: text/html; charset=$CHARSET\n";
    $headers .= "From: {$userObject['vezeteknev']} {$userObject['keresztnev']} <{$userObject['email']}>\n";
    $headers .= "Reply-To: {$userObject['vezeteknev']} {$userObject['keresztnev']} <{$userObject['email']}>\n";

    mail($quoteAddressFirstName . ' <' . $quoteAddress . '>', translate("levelkuldes_2"), $message, $headers);

    $query = "update jelentkezok set sent_quotes = sent_quotes + 1 where ID = " . (int)$userObject['ID'];
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
}

function getQuoteById($id)
{
    $query = "SELECT * from idezet where ID = " . (int)$id;

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $quoteObject = mysql_fetch_assoc($result);
    return $quoteObject;
}

function getSentQuotesNumber($userId)
{
    $query = "select sent_quotes from jelentkezok where ID = " . (int)$userId;
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $row = mysql_fetch_row($result);
    return $row[0];
}

function getAllSentQuotesNumber()
{
    $query = "select sum(sent_quotes) from jelentkezok";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $row = mysql_fetch_row($result);
    return $row[0];
}

function getSurveyUserNumber()
{
    $query = "select count(distinct user_id) from user_ruins";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $row = mysql_fetch_row($result);
    return $row[0];
}

function setUserRuins($userObject, $selectedRuins)
{
    $query = "delete from user_ruins where user_id = " . (int)$userObject['ID'];
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    if (is_array($selectedRuins)) {
        foreach ($selectedRuins as $ruin_id) {
            $query = "insert into user_ruins (user_id, ruin_id, datum) values(" . (int)$userObject['ID'] . ", {$ruin_id}, NOW())";
            $result = mysql_query($query);
            if (!$result) {
                print mysql_error();
                exit("Nem siker?lt: " . $query);
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $ret = array();
    while ($row = mysql_fetch_assoc($result)) {
        $ret[] = $row;
    }
    return $ret;
}

function updateUsers()
{
    $query = "SELECT inviter, count(*) FROM jelentkezok group by inviter having count(*) > 0";
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $ret = array();
    while ($row = mysql_fetch_row($result)) {
        $ret[] = $row;
    }
    foreach ($ret as $item) {
        $plus = 50 * (int)$item[1];
        $query = "update jelentkezok set expire_number = expire_number + " . $plus . " where ID = " . (int)$item[0];
        print '<br>' . $query;
        $result = mysql_query($query);
        if (!$result) {
            print mysql_error();
            exit("Nem siker?lt: " . $query);
        }
    }
}

function getProblemBooks($ruinCategoryId)
{
    if ($ruinCategoryId) {
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $ret = array();
    $prevRuinId = -1;
    $prevCatId = -1;
    $ruinSeq = 0;
    $catSeq = 0;
    while ($row = mysql_fetch_assoc($result)) {
        if ($ruinCategoryId) {
            $row['categorySeq'] = $categoryNum;
        } else {
            if ($row['categoryId'] != $prevCatId) {
                $ruinSeq = 0;
                $catSeq++;
                $prevCatId = $row['categoryId'];
            }
            $row['categorySeq'] = $catSeq;
        }
        if ($row['ruinId'] != $prevRuinId) {
            $ruinSeq++;
            $prevRuinId = $row['ruinId'];
        }
        $row['ruinSeq'] = $ruinSeq;

        $prevId = 0;
        $chapNum = 0;
        $chapterNum = -1;
        $subChapterNum = -1;
        for ($i = 0; $i < count($taskList); $i++) {
            // ha megv?ltozott a main category
            if ($taskList[$i]['ID'] != $prevId) {
                $num = 1;
                $chapNum++;
            }
            if ($taskList[$i]['sub_ID'] == $row['bookId']) {
                $chapterNum = $chapNum;
                $subChapterNum = $num;
                break;
            }
            $num++;
            $prevId = $taskList[$i]['ID'];
        }
        if ($chapterNum > -1) {
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $num = 1;
    while ($row = mysql_fetch_row($result)) {
        if ($row[0] == $ruinCategoryId) {
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
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $number = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $number = $row['nr'];
    }
    if ($number > 0) {
        $query = "update email_log set NR = NR + 1 where DATUM = CURDATE()";
    } else {
        $query = "insert into email_log (DATUM) values(CURDATE())";
    }

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        return false;
    }
    return true;
}

function languageChanged()
{
    global $trans;
    $trans = array();
    if ($_SESSION['language'] == 'eng' and file_exists('translations_ENG.php')) {
        include('translations_ENG.php');
    } else if ($_SESSION['language'] == 'ger' and file_exists('translations_GER.php')) {
        include('translations_GER.php');
    } else if ($_SESSION['language'] == 'gr' and file_exists('translations_GR.php')) {
        ob_start();
        include('translations_GR.php');
        ob_end_clean();
    } else if ($_SESSION['language'] == 'si' and file_exists('translations_SI.php')) {
        ob_start();
        include_once('translations_SI.php');
        ob_end_clean();
    } else if (file_exists('translations_HUN.php')) {
        include('translations_HUN.php');
    }
    if ($_SESSION['language'] == 'gr') {
        //$CHARSET = "UTF-8";
        $CHARSET = "latin2";
    } else if ($_SESSION['language'] == 'si') {
        //$CHARSET = "windows-1250";
        $CHARSET = "latin2";
    } else {
        $CHARSET = "latin2";
    }
}

function getProfilePicturePath($userObject)
{
    $userId = $userObject['ID'];
    $imgPathArray = getDirectoryNamesFromDirectory('phpics');
    foreach ($imgPathArray as $currentPath) {
        if ($currentPath == '..') {
            continue;
        }
        if (strrpos($currentPath, '_' . $userId) === strlen($currentPath) - strlen($userId) - 1) {
            return $currentPath;
        }
    }
    return false;
}

function updateSubchapterSilenceMessages($chapterId, $checkedSubs)
{
    $query = "update book set is_silence_message_sent = 0 where ref_id = " . (int)$chapterId;
    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }

    if (count((array)$checkedSubs) > 0) {
        $query = "update book set is_silence_message_sent = 1 where ref_id = " . (int)$chapterId . " and id in (";
        $query .= join(",", $checkedSubs);
        $query .= ")";
        $result = mysql_query($query);
        if (!$result) {
            print mysql_error();
            exit("Nem siker?lt: " . $query);
        }
    }
}

function getBookRecordByCardNumber($cardNumber)
{
    $sql = "select ID as subChapterId, ref_ID as chapterId from book where card_number = " . (int)$cardNumber;
    $result = mysql_query($sql);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    return mysql_fetch_assoc($result);
}

function createNewDefaultExercise()
{
    $sql = "insert into book (name, ref_ID, done) values ('---', 732814, 92)";
    $result = mysql_query($sql);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
}

function getTProfilServerData($id, $type)
{
    $fill = selectQuestFill($id);
    $parentFillId = (int)$fill["ParentFillId"];
    $answers = selectQuestAnswersForAdmin($id);

    $consultations = $parentFillId > 0 ? getConsultations($parentFillId) : getConsultations($id);

    $consultant = "";

    $finalAnswers = array();
    for ($i = 0; $i < count($answers); $i++) {
        if ($answers[$i]["questionId"] == 87) {
            $consultant = mb_convert_encoding($answers[$i]["answer"], 'UTF-8', 'ISO-8859-2');
        }
        if ($type == 1 || $answers[$i]["groupId"] == 13) {
            $answers[$i]["groupName"] = mb_convert_encoding($answers[$i]["groupName"], 'UTF-8', 'ISO-8859-2');
            $answers[$i]["question"] = mb_convert_encoding($answers[$i]["question"], 'UTF-8', 'ISO-8859-2');
            $answers[$i]["answer"] = mb_convert_encoding($answers[$i]["answer"], 'UTF-8',  'ISO-8859-2');
            $answers[$i]["raw_answer"] = mb_convert_encoding($answers[$i]["raw_answer"], 'UTF-8', 'ISO-8859-2');
            $answers[$i]["questionValues"] = mb_convert_encoding($answers[$i]["questionValues"], 'UTF-8', 'ISO-8859-2');
            $answers[$i]["answer_array"] = null;
            $finalAnswers[] = $answers[$i];
        }
    }
    return array(
        'success' => true,
        'userName' => mb_convert_encoding($fill["userName"], 'UTF-8',  'ISO-8859-2'),
        'consultant' => $consultant,
        'consultations' => $consultations,
        'answers' => $finalAnswers
    );
}

function getConsultations($id)
{
    $id = (int)$id;
    $sql = "select ID, date_format(crdti, '%y.%m.%d') as CrDate from Questionarie_Fills where Id = $id or ParentFillId = $id order by Id";

    $result = mysql_query($sql);
    if (!$result) {
        print mysql_error();
        exit("Nem sikerült: " . $sql);
    }
    $list = array();
    while ($row = mysql_fetch_assoc($result)) {
        $list[] = $row;
    }
    return $list;
}

function setFillEvaluated($id)
{
    $id = (int)$id;
    $sql = "update Questionarie_Fills set IsEvaluated = 1 where ID = $id";

    $result = mysql_query($sql);
    if (!$result) {
        print mysql_error();
        exit("Nem sikerült: " . $sql);
    }
}

function selectDanceList()
{
    $query = "select id, name, type, category, toPractice from dance order by toPractice desc, category asc, name asc";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $list = array();
    while ($row = mysql_fetch_assoc($result)) {
        $list[] = $row;
    }
    return $list;
}

function setDanceToPractice($id, $toPractice)
{
    if ($toPractice)
        $toPractice = 1;
    else
        $toPractice = 0;

    $sql = "update dance set toPractice = $toPractice where id = " . (int)$id;

    $result = mysql_query($sql);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $sql);
    }
}

function storeNote($fillId, $noteType, $note)
{
    $fillId = (int)$fillId;
    $noteType = (int)$noteType;
    $note = str_replace("'", "''", $note);

    $query = "select ID from Questionarie_Fill_Notes where Questionarie_FillsID = $fillId and NoteType = $noteType";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $id = -1;
    while ($row = mysql_fetch_row($result)) {
        $id = $row[0];
    }
    if ($id >= 0) {
        $sql = "update Questionarie_Fill_Notes set Note = '$note' where ID = $id";
    } else {
        $sql = "insert into Questionarie_Fill_Notes (Questionarie_FillsID, NoteType, Note) values ($fillId, $noteType, '$note')";
    }
    $result = mysql_query($sql);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $sql);
    }
}

function getNote($fillId, $noteType)
{
    $fillId = (int)$fillId;
    $noteType = (int)$noteType;

    $query = "select Note from Questionarie_Fill_Notes where Questionarie_FillsID = $fillId and NoteType = $noteType";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    while ($row = mysql_fetch_row($result)) {
        return $row[0];
    }
}

function getFilteredNoteFillIds($txt)
{
    $query = "select Questionarie_FillsID from Questionarie_Fill_Notes where Note like '%$txt%'";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $ids = array();
    while ($row = mysql_fetch_row($result)) {
        $ids[] = $row[0];
    }
    return $ids;
}

function getRootCauses($fillId)
{
    $fillId = (int)$fillId;

    $query = "
		select c.Id, c.Name, case when a.Id is not null then 1 else 0 end IsChecked, null as Other
		from Questionarie_Root_Causes c
		left outer join Questionarie_Advisor_Answer a on a.Questionarie_Root_CausesId = c.Id and a.Questionarie_FillsID = $fillId
        
        union ALL
        
		select null, null, null, Questionarie_Root_CauseOther
		from Questionarie_Advisor_Answer
        where Questionarie_FillsID = $fillId and Questionarie_Root_CausesId is null
		order by name asc
	";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $retArray = array();
    while ($row = mysql_fetch_assoc($result)) {
        $retArray[] = $row;
    }
    return $retArray;
}

function getSolutionSteps($fillId)
{
    $fillId = (int)$fillId;

    $query = "
		select c.Id, c.Name, case when a.Id is not null then 1 else 0 end IsChecked, null as Other, a.Id as AnswerId
		from Questionarie_Solution_Steps c
		left outer join Questionarie_Advisor_Answer a on a.Questionarie_Solution_StepsId = c.Id and a.Questionarie_FillsID = $fillId
		order by 1
	";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $retArray = array();
    while ($row = mysql_fetch_assoc($result)) {
        $retArray[] = $row;
    }
    return $retArray;
}

function getEatMore($fillId)
{
    $fillId = (int)$fillId;

    $query = "
		select c.Id, c.Name, case when a.Id is not null then 1 else 0 end IsChecked, null as Other, a.Id as AnswerId
		from Questionarie_Eat_More c
		left outer join Questionarie_Advisor_Answer a on a.Questionarie_Eat_MoreId = c.Id and a.Questionarie_FillsID = $fillId
		order by 1
	";

    $result = mysql_query($query);
    if (!$result) {
        print mysql_error();
        exit("Nem siker?lt: " . $query);
    }
    $retArray = array();
    while ($row = mysql_fetch_assoc($result)) {
        $retArray[] = $row;
    }
    return $retArray;
}

function storeAdvisorCheckbox($type, $fillId, $valueId, $isChecked)
{
    $type = (int)$type;
    $fillId = (int)$fillId;
    $valueId = (int)$valueId;
    $isChecked = (bool)$isChecked;

    $fieldName = "";
    if ($type === 1) {
        $fieldName = "Questionarie_Root_CausesId";
    } else if ($type === 2) {
        $fieldName = "Questionarie_Solution_StepsId";
    } else if ($type === 3) {
        $fieldName = "Questionarie_Eat_MoreId";
    }

    if ($isChecked) {
        $query = "select Id from Questionarie_Advisor_Answer where Questionarie_FillsID = $fillId and $fieldName = $valueId";

        $result = mysql_query($query);
        if (!$result) {
            print mysql_error();
            exit("Nem siker?lt: " . $query);
        }
        $id = -1;
        while ($row = mysql_fetch_row($result)) {
            $id = $row[0];
        }
        if (!($id > 0)) {
            $sql = "insert into Questionarie_Advisor_Answer (Questionarie_FillsID, $fieldName) values($fillId, $valueId)";
            $result = mysql_query($sql);
            if (!$result) {
                print mysql_error();
                exit("Nem siker?lt: " . $sql);
            }
        }
    } else {
        $sql = "delete from Questionarie_Advisor_Answer where Questionarie_FillsID = $fillId and $fieldName = $valueId";
        $result = mysql_query($sql);
        if (!$result) {
            print mysql_error();
            exit("Nem siker?lt: " . $sql);
        }
    }
}

function storeAdvisorText($type, $fillId, $value)
{
    $type = (int)$type;
    $fillId = (int)$fillId;

    $sqlValue = str_replace("'", "''", $value);

    $fieldName = "";
    if ($type === 1) {
        $fieldName = "Questionarie_Root_CauseOther";
    }

    if ($value != null) {
        $query = "select Id from Questionarie_Advisor_Answer where Questionarie_FillsID = $fillId and $fieldName is not null";

        $result = mysql_query($query);
        if (!$result) {
            print mysql_error();
            exit("Nem siker?lt: " . $query);
        }
        $id = -1;
        while ($row = mysql_fetch_row($result)) {
            $id = $row[0];
        }

        if (!($id > 0)) {
            $sql = "insert into Questionarie_Advisor_Answer (Questionarie_FillsID, $fieldName) values($fillId, '$sqlValue')";
        } else {
            $sql = "update Questionarie_Advisor_Answer set $fieldName = '$sqlValue' where Id = $id";
        }
        $result = mysql_query($sql);
        if (!$result) {
            print mysql_error();
            exit("Nem siker?lt: " . $sql);
        }
    } else {
        $sql = "delete from Questionarie_Advisor_Answer where Questionarie_FillsID = $fillId and $fieldName is not null";
        $result = mysql_query($sql);
        if (!$result) {
            print mysql_error();
            exit("Nem siker?lt: " . $sql);
        }
    }
}
