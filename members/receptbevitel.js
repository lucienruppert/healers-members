// *********************************************************** 
// ******** NYISD MEG MAGYAR K�DOL�SBAN!!! ******************* 
// *********************************************************** 

$(document).ready(function(){
    
    let receptId = '';
    var data = [];

    //===== RECEPT BET�LT�SE JAV�T�SRA A JOBBOLDALI RECEPT LIST�B�L
    $(document).on('click', '.loadRecept', function (){
        receptId = $('.id',this).val();
        //alert (receptId);
        //AZ UTOLS� MENTETT RECEPTET VISSZAHOZZUK A DB-B�L �S BET�LTJ�K JAV�T�SRA
        $.post( "getRfromDB.php", {id: receptId},
            function(response){
                //receptId = '';
                var data = response.split("@");
                document.getElementById("elnevezes").value = data[0];
                document.getElementById("hozzavalok").innerHTML = data[1];
                document.getElementById("elkeszites").innerHTML = data[2];
                document.getElementById("alkoto").value = data[3];
                document.getElementById("id").value = data[4]; 
                document.getElementById("adag").value = data[6];       
            }); 
    });

    //===== A RECEPT (�J (VAGY R�GI) MENT�SE A DB-BE, VISSZAT�LT�S A CONTROLBOXBA �S A LISTA FRISS�T�S
    $(document).on('click', '#saveButton', function(){

    // AZ �J ADATOK IDE, A JS-BE A HTML TEXT MEZ�KB�L
        var elnevezes = $('#elnevezes').val();
        var hozzavalok = $('#hozzavalok').val();
        var elkeszites = $('#elkeszites').val();
        var alkoto = $('#alkoto').val();
        var adag = $('#adag').val();

        //MENT�S EL�TT MEGN�ZZ�K, HOGY M�R KRE�LT RECEPTR�L VAN-E SZ�, AZAZ VAN-E ID-JA
        if (receptId == '') {
        
            //HA �J RECEPT >>�INSERTELJ�K AZ �J ADATOKAT A DB-BE
            $.post( "postRtoDB.php", {elnevezes: elnevezes, hozzavalok: hozzavalok, elkeszites: elkeszites, alkoto: alkoto, adag: adag},
                function(response, status){
                });         

        } else {
        
            //HA R�GI RECEPT >> UPDATELJ�K A LIST�B�L BEH�VOTT RECEPT ADATAIT A DB-BEN
            $.post( "updateRtoDB.php", {elnevezes: elnevezes, hozzavalok: hozzavalok, elkeszites: elkeszites, alkoto: alkoto, adag: adag, id: receptId},
                function(response, status){
                });         
        }

        // KIT�R�LJ�K AZ �RT�KEKET AZ INPUT MEZ�KB�L
        document.getElementById("receptSave").reset();
        $('#hozzavalok').empty();
        $('#elkeszites').empty();
        $('#id').empty();

        //K�SLELTET�S, HOGY AZ ADAT VISSZAT�R�SE A MEGJELEN�T�S EL�TT T�RT�NJEN
        setTimeout(kesleltetes,3000);

    });
    
    function kesleltetes(){

        //AZ UTOLS� MENTETT RECEPTET VISSZAHOZZUK A DB-B�L �S BET�LTJ�K A CONTROLBOXBA
        getRecept();
        //FRISS�TJ�K A JOBB OLDALI RECEPTLIST�T
        $(document).ready(function(){
            $('#list').load('loadForm.php #form');
            //$('#list').load(' #form');
        });

    };  

    //===== RECEPT BET�LT�SE A K�PFELT�LT�S UT�N
    $(document).on('click', '#kepfeltoltes', function (){
        setTimeout(getRecept,3000);
        document.getElementById('#kepfeltoltes').reset();
    });

    // RECEPT BET�LT�SE FUNCTION
    function getRecept() {

        $.post( "getRfromDB.php", {id: receptId},
        function(response, status){
            receptId = '';
            var data = response.split("@");
            document.getElementById("elnevezes_cb").innerHTML = data[0];
            $('.label').show();
            hozzavalok = '&#x2022;&nbsp;' + data[1].replace(/(?:\r\n|\r|\n)/g, "<br>&#x2022;&nbsp;");
            //hozzavalok = hozzavalok.substring(0,hozzavalok.length-1);
            document.getElementById("hozzavalok_cb").innerHTML = hozzavalok;
            elkeszites = data[2].replace(/(?:\r\n|\r|\n)/g, "<br><br>");
            document.getElementById("elkeszites_cb").innerHTML = elkeszites;
            document.getElementById("adag_cb").innerHTML = data[6];
            document.getElementById("alkoto_cb").innerHTML = "Forr�s: " + data[3];
            document.getElementById("id").value = data[4];
            if (data[5] != '') {
                //alert ('Sikeres k�pfelt�lt�s!');
                var kep = "<img class='image' src='img_uploads/" + data[5] + "'></img>";
                document.getElementById("kep_cb").innerHTML = kep;
            } else { document.getElementById("kep_cb").innerHTML = ''; }
        }); 
    }
}); 