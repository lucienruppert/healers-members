$(document).ready(function(){
    
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
