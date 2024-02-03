<?php
print "<div id='divMenuCont' style='background-color:" . $color . ";padding-right:50px;'>
    <div id='divMenu' style='height:50px;display:flex;justify-content:flex-end;align-items:center;gap:50px;flex-grow:5;'>
        <div style='color:white;font-size:20px;flex:5;font-align:left;padding-left:20px'\">Szia kedves " . $userObject['keresztnev'] . "!
        </div>";    

    // !!!! AZ ESETFELMÉRÕ (ÜGYFELEK) ALAPBÓL MINDENKINEK MEGJELENIK, HISZEN AZ A DEFAULT OLDAL. ÍGY HA NINCS MÁS MENÜPONTJUK, AKKOR NEM KELL NEKIK EZ A MENÜPONT

// Niki a receptbevitelhez
if($userObject['status'] == 8){  

    print "<div><a href='#' style='color:white;font-size:13px;text-decoration:none;' onclick=\"location.href='receptbevitel.php';\">RECEPTBEVITEL</a></div>";
    print "<div><a href='#' style='color:white;font-size:13px;text-decoration:none;' onclick=\"location.href='receptindex.php';\">RECEPTINDEX</a></div>";
}

// Élelmiszerlistások (3) és Tanácsadók (2)
if($userObject['status'] == 3 || $userObject['status'] == 2){  

    print "<div><a href='#' style='color:white;font-size:13px;text-decoration:none;' onclick=\"location.href='esetf.php';\">ÜGYFELEK</a></div>";         
    print "<div><a href='#' style='color:white;font-size:13px;text-decoration:none;' onclick=\"location.href='recepttar.php';\">RECEPTTÁR</a></div>";
    print "<div><a href='#' style='color:white;font-size:13px;text-decoration:none;' onclick=\"location.href='menuplanner.php';\">ÉTRENDTERVEZÕ</a></div>";
}

// Csak Tanácsadók
if($userObject['status'] == 2){  

    print "<div><a href='#' style='color:white;font-size:13px;text-decoration:none;' onclick=\"location.href='tapkiegbevitel.php';\">KIEGÉSZÍTÕK</a></div>
    <div><a href='#' style='color:white;font-size:13px;text-decoration:none;' onclick=\"location.href='defaultProtocols.php';\">PROTOKOLLOK</a></div>";         
}
//lucien és Anita és Ági
if($userObject['status'] == 9 || $userObject['ID'] == 6938|| $userObject['ID'] == 6940 ){   
    print "<div><a href='#' style='color:white;font-size:13px;text-decoration:none;' onclick=\"location.href='tprofil.php';\">T-PROFIL</a></div>";
}

//lucien
if($userObject['status'] == 9){   

    print "<div><a href='#' style='color:white;font-size:13px;text-decoration:none;' onclick=\"location.href='esetf.php';\">ÜGYFELEK</a></div>";    
    print "<div><a href='#' style='color:white;font-size:13px;text-decoration:none;' onclick=\"location.href='matrix.php';\">TUDÁSTÁR</a></div>";  
    print "<div class='dropdown'>
    <a style='color:white;font-size:13px;text-decoration:none;' href='#'>PROTOKOLLOK</a>
        <div class='dropdown-content' style='background-color:" . $color . "'>
            <a href='#' style='color:white;font-size:13px;text-decoration:none;' onclick=\"location. href='tapkiegbevitel.php';\">KIEGÉSZÍTÕK</a>      
            <a href='#' style='color:white;font-size:13px;text-decoration:none;' onclick=\"location.href='defaultProtocols.php';\">PROTOKOLLOK</a>            
        </div>
    </div>";          

    print "<div class='dropdown'>
        <a style='color:white;font-size:13px;text-decoration:none;' href='#'>ÉTRENDTERVEZÕ</a>
        <div class='dropdown-content' style='background-color:" . $color . "'>
            <a href='#' style='color:white;font-size:13px;text-decoration:none;' onclick=\"location.href='elelmiszer.php';\">ÉLELMISZERLISTA</a>                
            <a href='#' style='color:white;font-size:13px;text-decoration:none;' onclick=\"location.href='receptbevitel.php';\">RECEPTBEVITEL</a>
            <a href='#' style='color:white;font-size:13px;text-decoration:none;' onclick=\"location.href='recepttar.php';\">RECEPTTÁR</a>
            <a href='#' style='color:white;font-size:13px;text-decoration:none;' onclick=\"location.href='receptindex.php';\">RECEPTINDEXELÕ</a>
            <a href='#' style='color:white;font-size:13px;text-decoration:none;' onclick=\"location.href='menuplanner.php';\">ÉTRENDTERVEZÕ</a>
        </div>
    </div>";          

    print "<div><a href='#' style='color:white;font-size:13px;text-decoration:none;' onclick=\"location.href='users.php';\">FELHASZNÁLÓK</a></div>";
}

//EZ MINDENKINEK MEGJELENIK
print "<div><a href='#' style='color:white;font-size:13px;text-decoration:none;font-weight:bold' onclick=\"location.href='logout.php';\">LOG-OUT</a></div></div></div>"; 

?>
