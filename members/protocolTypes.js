$(document).ready(function(){
    
    let receptId = '';
    let elnevezes = '';
    let hozzavalok = '';
    let elkeszites = '';
    let forras = '';
    let kep = '';

    // RECEPTKIVÁLASZTÁS ÉS BETÖLTÉS
    $(document).on('click', '.listItem', function() {
        $('.elnevezes').css('background-color', 'white');
        $('.elnevezes',this).css('background-color', 'lightgrey');
        receptId = $('.id',this).val();
        elnevezes = $('.elnevezes2',this).val();
        $('#elnevezes_cb').show();
        document.getElementById('elnevezes_cb').innerHTML = elnevezes;
        kep = $('.kep',this).val();
        if (kep != '') {
        kep = "<img class='image' src='img_uploads/" + kep + "'></img>";
        $('#kep_cb').show();
        document.getElementById('kep_cb').innerHTML = kep;
        } else { $('#kep_cb').hide(); }
        hozzavalok = $('.hozzavalok',this).val();
        hozzavalok = '&#x2022;&nbsp;' + hozzavalok.replace(/(?:\r\n|\r|\n)/g, "<br>&#x2022;&nbsp;");
        document.getElementById('hozzavalok_cb').innerHTML = hozzavalok;
        elkeszites = $('.elkeszites',this).val();
        elkeszites = elkeszites.replace(/(?:\r\n|\r|\n)/g, "<br><br>");
        document.getElementById('elkeszites_cb').innerHTML = elkeszites;
        forras = $('.forras',this).val();
        document.getElementById('forras_cb').innerHTML = "Forrás: " + forras;
        $('.label').show();
    });

    let filterState;

    // INDEXELÉS: ÚJ RECORDOT HOZ LÉTRE VAGY TÖRLI ÉS FELÜLÍRJA A RÉGIT.
    $(document).on('click', '.filterSelect', function() {
        filterId = $(this).attr("id"); 
        etelId = $(this).attr("alt");
        igenId = filterId + 'igen' + etelId;
        nemId = filterId + 'nem' + etelId;
        cssId = filterId + etelId;  
        $('#'+cssId).css('background-color','white');   
        igenButton = document.getElementById(igenId).checked;
        nemButton = document.getElementById(nemId).checked;
        if (igenButton == true) { 
            filterState = 1;
            $.post( "updateFilters.php", {etelId: etelId, filterId: filterId, filterState: filterState},
            function(response, status){
            });
        }      
        if (nemButton == true) { 
            filterState = 0;
            $.post( "updateFilters.php", {etelId: etelId, filterId: filterId, filterState: filterState},
            function(response, status){
            });
        }    
    });
});
