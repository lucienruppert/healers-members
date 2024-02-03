<?php
    header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
	header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
	header("Cache-Control: post-check=0, pre-check=0",false);
	session_cache_limiter("must-revalidate");
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);
    if(isset($_POST['actionType']) == 'login'){
        ini_set('session.cookie_lifetime', 0);
        ini_set('session.gc_maxlifetime', 36000);
    }

    session_start();
	require_once('functions.php');

    if($_POST['actionType'] == 'login'){
		if($_POST['username'] && $_POST['email'] && ($userObject = getUserObj($_POST['username'],$_POST['email']))){
			$_SESSION['userObject'] = $userObject;
            $id = $userObject['ID'];
            logPersonalLogin($id);
            if($userObject['status'] == 9 || $userObject['status'] == 4 || $userObject['status'] == 3 || $userObject['status'] == 2 || $userObject['status'] == 5){
                header('Location: esetf.php');
			}
			else if($userObject['status'] == 8) {
				header('Location: receptbevitel.php');
			}
            exit;
        }
         else{
			print "<script>alert('A megadott felhaszn�l� nem l�tezik!');</script>";
        }
    }

    else{
        session_destroy();
    }

    ?>
<HTML>
<body>
<head>
<title>Healers Digital</title>
<?php include_once('headLinks.php'); ?>
<script>
    $(document).on('keypress', '#password', function(e){
        if ($('#email').val() != '' && $('#password').val() != ''){
            var key = e.which;
            if(key == 13)  // the enter key code
            {
            document.getElementById("click").disabled = false;
            $('#click').click();               
            }  
        }
    });
</script>
<style>
body {
	margin: 0px;
}
.contOutside {
	display: flex;
    flex-direction: column;
	justify-content: space-around;
	align-items: center;
    height: 98vh; 
}
.contInside { 
	display: flex;
	justify-content: center;
	align-items: center;
	flex-direction: column;
    font-family: 'Raleway', sans-serif;
    font-weight:100;
    background-color: <?php echo $color ?>;
    padding: 2rem;
    border-radius: 2rem;
    box-shadow: 5px 5px 5px lightgray;
}
.healers {
    color:white;
    font-size:4rem;
}
.digital {
    padding-bottom:20px;
    color:white;
    font-size:2rem;
}
#email, #password {
    border: 1px solid white;
    /* background-color:gray;
    font-size:1rem; */
    margin-top: 5px;
}
::placeholder { /* Chrome, Firefox, Opera, Safari 10.1+ */
  color: lightgray;
  opacity: 1; /* Firefox */
}
#alert {
    color: red;
    font-size: 2.5rem;
    text-align: center;
    margin: 50px 200px 0px 200px;
    display: none;
}
.contact {
    color: lightgray;
    text-align: center;
    font-size: 0.9rem;
    line-height: 1.5;
}
.developing {
    background-color: red;
    width: 500px;
    color: white;
    font-size: 1.5rem;
    padding: 20px;
    text-align: center;
    border-radius: 10px;
    line-height: 1.5;
    display: none;
}
</style>
</HEAD>
<form action='index.php' method='POST' autocomplete='on'>
<div class='contOutside'>
    <div class='contInside'>
        <div><input type='hidden' name='actionType' value='login'></div>
        <div class='healers'>Healers</div>
        <div class='digital'>Digital</div>
        <div>
            <input id='email' size='30' type='email' name='email' placeholder="email" value=''>
        </div>
        <div>
            <input id='password' size='30' type='password' name='username' placeholder='password' value='' autocomplete='on'>
        </div>
        <div>
            <input type='submit' id='click' name='click' value='login' style="display: none" disabled>
        </div>
    </div>
    <div class="developing">Az oldalt �ppen fejlesztem, n�h�ny funkci� nem biztos, hogy a megszokott m�don m�k�dik. K�lek, n�zz vissza k�s�bb!</div>
    <div class='contact'>
    hello@healers.digital
    </div>
</div>
</form>
</BODY>
</HTML>