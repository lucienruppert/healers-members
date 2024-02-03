$(document).ready(function(){

    //BACKUP
    $(document).on('click', '.button', function(){
        userId = $(this).attr('id');
        $.post('getBackupData.php', {userId: userId},
        function(response, status){
        //console.log(response);
        alert ("Sikerült!");
        });
    });


    
});
