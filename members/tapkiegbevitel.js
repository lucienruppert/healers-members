$(document).ready(function(){

    let consId
    $.post( "getUserId.php", function(resp){
        consId = resp
    });

    let suppNames = []
    $.post( "getAllSupp.php", function(resp){
        suppNames = JSON.parse(resp)
        list = ''
        lastId= 0
        suppNames.forEach(item => {
            if (item['id'] != lastId)
                list += "<div class='item' id='" + item['id'] + "'><a href='#'><b>" + item['brand'] + "</b> " + item['name'] + "</a></div>"
            lastId = item['id']
        })
        document.getElementById('list').innerHTML = list
    })

    let suppId = ''
    let suppData = []
    $(document).on('click', '.item', function () {
        suppId = $(this).attr('id')
        $.post( "getSuppData.php", {id: suppId}, function(resp, stat){
            suppData = JSON.parse(resp)
            console.table(suppData)
            suppNames.forEach(item => {
                if (item['id'] == suppId)
                document.getElementById("nev").innerHTML = item['brand'] + '&nbsp;' + item['name']   
            })
            if (suppData.length > 0) {
                $('#adagolas').val(suppData[0]['adagolas']);
                $('#link').val(suppData[0]['link']);
            } else {
                $('#adagolas').val('');
                $('#link').val('');
            }
        })
    })

    $(document).on('click', '.saveButton', function() {
        let adagolas = $('#adagolas').val()
        let link = $('#link').val()
        if (adagolas == '') adagolas = null
        if (link == '') link = null
            $.post( "updSuppData.php", { adagolas: adagolas, link: link, id: suppId, consId: consId }, function(resp, status) { 
                if (status='success') {
                    document.getElementById('message').innerHTML = 'Sikeres mentés'
                    setTimeout(() => {
                        document.getElementById('message').innerHTML = ''
                    }, 3000);
                }
        })
        $('#adagolas').val('');
        $('#link').val('');
    })
})
