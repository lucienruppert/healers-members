<?php   

include('functions.php');

$userId = $_POST['userId'];

//ADATLEK�R�S
$query = "
	SELECT Questionarie_Fills.ID, Questionarie_Fills.username, Questionarie_Fill_Notes.NoteType, Questionarie_Fill_Notes.Note 
	FROM Questionarie_Fills 
	LEFT JOIN Questionarie_Fill_Notes ON Questionarie_Fills.ID = Questionarie_Fill_Notes.Questionarie_FillsID
	WHERE ConsultantId = $userId
	ORDER BY username ASC
";

$result = mysql_query($query);
if(!$result){
	print mysql_error();
	exit("Nem siker�lt: " . $query);
}
$backupData = array();
while($row = mysql_fetch_row($result))
{
	$backupData[] = $row;
}
/*
$result = mysqli_query($conn, $query);
$backupData = mysqli_fetch_all($result);
mysqli_free_result($result);
mysqli_close($conn);
*/
/*  [0] => ID
    [1] => N�V
    [2] => NOTE TYPE
    [3] => DATA 
*/
//BEIRAND� ADAT EL�K�SZ�T�SE
$x = 0;
$txt = '';
$actualId = 0;
while ($x < sizeof($backupData)) {
    //Megvizsg�lom az aktu�lis ID-t, �s a nevet csak egyszer �rom ki, a v�ltoz�skor (azaz csak egyszer megy�nk bele az IF-be)
    if ($backupData[$x][0] != $actualId) {
        $actualId = $backupData[$x][0];
		
        $txt .= iconv("Windows-1250", "UTF-8", $backupData[$x][1]) . "\n\r";
    }
    // Ha van jegyzet, akkor ki�rjuk a t�pus�t
    // if ($backupData[$x][3] != '') {
    //     if ($backupData[$x][2] == 1) { $txt .= 'Saj�t jegyzet' . "\n\r"; }
    //     else if ($backupData[$x][2] == 2) { $txt .= 'Saj�t jegyzet' . "\n\r"; }
    //     else if ($backupData[$x][2] == 21) { $txt .= 'Javaslatok' . "\n\r"; }  
    // }
    //$txt .= $backupData[$x][3] . "\n\r";
	$txt .= $backupData[$x][3] . "\n\r";

$x++;
}

//$txt = str_replace('ó','�',$txt);
//$txt = utf8_decode($txt);
//iconv ('LATIN2','UTF-8',$txt);
//$newtxt = mb_convert_encoding($txt, 'LATIN2', 'UTF-8');
    
//FILE L�TREHOZ�S
$filename = "$userId" . "-" . date("Y-m-d");
$file = fopen('backup_files/' . $filename . '.txt', 'w');
fwrite($file, $txt);
fclose($file);

//print_r($backupData);
?>

