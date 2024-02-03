<?php

$conn = mysqli_connect('mysql.luciendelmar.com','luciendelmar','9CUiNwYzV3','luciendelmar');
if(!$conn) { 
	echo 'Kapcsolati hiba: ' . mysqli_connect_error(); 
	return; 
}

// GLOBAL COLOR BEÁLLÍTÁSA
//$color = '#008080';
$color = '#0047AB';

// if(!$GLOBALS["userObject"])
//     $userObject = $GLOBALS["userObject"] = $_SESSION["userObject"];

// if(!$_SESSION['language']){
//     if($_COOKIE['preflanguage']){
//         $_SESSION['language'] = $_COOKIE['preflanguage'];
//     }
//     else{
//         $_SESSION['language'] = 'hun';
//     }
// }

?>