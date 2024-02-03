$(document).ready(function(){
    
    let receptId = ''
    let elnevezes = ''
    let hozzavalok = ''
    let elkeszites = ''
    let forras = ''
    let kep = ''
    let adag = ''

    // RECEPTKIVÁLASZTÁS ÉS BETÖLTÉS
    $(document).on('click', '.listItem', function() {
        $('.elnevezes').css('background-color', 'white')
        $('.elnevezes',this).css('background-color', 'lightgrey')
        receptId = $('.id',this).val()
        elnevezes = $('.elnevezes2',this).val()
        $('#elnevezes_cb').show()
        document.getElementById('elnevezes_cb').innerHTML = elnevezes
        kep = $('.kep',this).val()

        if (kep != '') {
            kep = "<img class='image' src='img_uploads/" + kep + "'></img>"
            $('#kep_cb').show()
            document.getElementById('kep_cb').innerHTML = kep
        } else { $('#kep_cb').hide() }

        hozzavalok = $('.hozzavalok',this).val()
        hozzavalok = '&#x2022&nbsp' + hozzavalok.replace(/(?:\r\n|\r|\n)/g, "<br>&#x2022&nbsp")
        document.getElementById('hozzavalok_cb').innerHTML = hozzavalok
        elkeszites = $('.elkeszites',this).val()
        elkeszites = elkeszites.replace(/(?:\r\n|\r|\n)/g, "<br><br>")
        document.getElementById('elkeszites_cb').innerHTML = elkeszites
        adag = $('.adag',this).val()
        adag = adag.replace(/(?:\r\n|\r|\n)/g, "<br><br>")
        document.getElementById('adag_cb').innerHTML = adag
        forras = $('.forras',this).val()
        document.getElementById('forras_cb').innerHTML = "Forrás: " + forras
        $('.label').show()
    })

    
})