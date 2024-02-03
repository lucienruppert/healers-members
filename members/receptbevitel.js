// *********************************************************** 
// ******** NYISD MEG MAGYAR KÓDOLÁSBAN!!! ******************* 
// *********************************************************** 

$(document).ready(function(){
    
    let receptId = '';
    var data = [];

    //===== RECEPT BETÖLTÉSE JAVÍTÁSRA A JOBBOLDALI RECEPT LISTÁBÓL
    $(document).on('click', '.loadRecept', function (){
        receptId = $('.id',this).val();
        //alert (receptId);
        //AZ UTOLSÓ MENTETT RECEPTET VISSZAHOZZUK A DB-BÕL ÉS BETÖLTJÜK JAVÍTÁSRA
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

    //===== A RECEPT (ÚJ (VAGY RÉGI) MENTÉSE A DB-BE, VISSZATÖLTÉS A CONTROLBOXBA ÉS A LISTA FRISSÍTÉS
    $(document).on('click', '#saveButton', function(){

    // AZ ÚJ ADATOK IDE, A JS-BE A HTML TEXT MEZÕKBÕL
        var elnevezes = $('#elnevezes').val();
        var hozzavalok = $('#hozzavalok').val();
        var elkeszites = $('#elkeszites').val();
        var alkoto = $('#alkoto').val();
        var adag = $('#adag').val();

        //MENTÉS ELÕTT MEGNÉZZÜK, HOGY MÁR KREÁLT RECEPTRÕL VAN-E SZÓ, AZAZ VAN-E ID-JA
        if (receptId == '') {
        
            //HA ÚJ RECEPT >> INSERTELJÜK AZ ÚJ ADATOKAT A DB-BE
            $.post( "postRtoDB.php", {elnevezes: elnevezes, hozzavalok: hozzavalok, elkeszites: elkeszites, alkoto: alkoto, adag: adag},
                function(response, status){
                });         

        } else {
        
            //HA RÉGI RECEPT >> UPDATELJÜK A LISTÁBÓL BEHÍVOTT RECEPT ADATAIT A DB-BEN
            $.post( "updateRtoDB.php", {elnevezes: elnevezes, hozzavalok: hozzavalok, elkeszites: elkeszites, alkoto: alkoto, adag: adag, id: receptId},
                function(response, status){
                });         
        }

        // KITÖRÖLJÜK AZ ÉRTÉKEKET AZ INPUT MEZÕKBÕL
        document.getElementById("receptSave").reset();
        $('#hozzavalok').empty();
        $('#elkeszites').empty();
        $('#id').empty();

        //KÉSLELTETÉS, HOGY AZ ADAT VISSZATÉRÉSE A MEGJELENÍTÉS ELÕTT TÖRTÉNJEN
        setTimeout(kesleltetes,3000);

    });
    
    function kesleltetes(){

        //AZ UTOLSÓ MENTETT RECEPTET VISSZAHOZZUK A DB-BÕL ÉS BETÖLTJÜK A CONTROLBOXBA
        getRecept();
        //FRISSÍTJÜK A JOBB OLDALI RECEPTLISTÁT
        $(document).ready(function(){
            $('#list').load('loadForm.php #form');
            //$('#list').load(' #form');
        });

    };  

    //===== RECEPT BETÖLTÉSE A KÉPFELTÖLTÉS UTÁN
    $(document).on('click', '#kepfeltoltes', function (){
        setTimeout(getRecept,3000);
        document.getElementById('#kepfeltoltes').reset();
    });

    // RECEPT BETÖLTÉSE FUNCTION
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
            document.getElementById("alkoto_cb").innerHTML = "Forrás: " + data[3];
            document.getElementById("id").value = data[4];
            if (data[5] != '') {
                //alert ('Sikeres képfeltöltés!');
                var kep = "<img class='image' src='img_uploads/" + data[5] + "'></img>";
                document.getElementById("kep_cb").innerHTML = kep;
            } else { document.getElementById("kep_cb").innerHTML = ''; }
        }); 
    }
}); 