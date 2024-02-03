 $(document).ready(function(){

    let consId
    $.post( "getUserId.php", function(resp){
        consId = resp
    });

    // LEHOZZUK AZ ?SSZES RECEPT ID-J?T ?s VISSZAALAK?TJUK T?MBB?
    let idList = [] 
    let originalIdList = []
    let listLength = ''
    let clickedSzurok = []
    let newDataList = []

    let originaletkezesekArray = []
    let etkezesekArray = [
        [], //0 - foetkezes
        [], //1 - reggeli    
        [], //2 - koztes
        [], //3 - desszert
        [], //4 - leves
        [], //5 - smoothie
        [], //6 - foetel
        [] //7 - k?ret
    ]  
    $.post( "getAllReceptFilterDataCut.php", function(response, status){
        // 1. Sz?tbontjuk Recept+Filter elemekre
        response = response.split("~")
        // 2. Tov?bb bontjuk kisebb t?mb?kre, lesz egy nagy multidimenzi?s t?mb?nk
        response.forEach(element => {
            element = element.split("@") 
            newDataList.push(element)        
        });
        newDataList.pop()
        // 3. Kiszedj?k bel?le az ?tkez?sfajt?k darabsz?mait
        lastId = 0
        newDataList.forEach(element => {
            //Hogy minden ?tel csak egyszer szerepeljen az idListben
            if (element[0] != lastId) {
                listLength++
                idList.push(element[0])
            }
            //Sz?tbontjuk ?tkez?sfajt?kra, ?s be?rjuk az id-kat egy multiarrayes t?mbbbe.
            for ( a=1; a <= 8; a++) {
                if (element[1] == a && element[2] == 1)
                    etkezesekArray[a-1].push(element[0])
            }
            lastId = element[0]
        })
        //Randomiz?ljuk az ?sszes allist?t
        function shuffle(array) {
            return array.sort(() => Math.random() - 0.5);
        }
        etkezesekArray.forEach(element => {
            shuffle(element)
        });
        originalIdList = idList
        // Ez fontos, mert ez lesz az alap ?rt?k, viszont a sima egyenl?s?gjel nem duplik?ln?:
        originaletkezesekArray = Array.from(etkezesekArray);

        // MOST NEM JELEZZ?K KI EGYEL?RE
        // document.getElementById('osszes').innerHTML = listLength + ' ?tel'
        // document.getElementById('foetkezes').innerHTML = etkezesekArray[0].length + ' f??tkez?s'
        // document.getElementById('reggeli').innerHTML = etkezesekArray[1].length + ' reggeli'
        // document.getElementById('koztes').innerHTML = etkezesekArray[2].length + ' k?ztes'
    })
    // LEHOZUNK MINDEN SZ?R?H?Z TARTOZ? IDLIST?T

        // ?thozzuk a php-b?l a sz?r?k neveit
        tojasSzuro = $('#tojas').val()
        zabSzuro = $('#zab').val()
        halSzuro = $('#hal').val()
        olajosSzuro = $('#olajos').val()
        burgonyaSzuro = $('#burgonya').val()
        majSzuro = $('#maj').val()
        kolesSzuro = $('#koles').val()  
        
        // Lehozzuk minden sz?r?h?z a hozz?juk tartoz? ?telID list?j?t ?s sz?moss?gukat
        const SzuroArray = [tojasSzuro, zabSzuro, halSzuro, olajosSzuro, burgonyaSzuro, majSzuro, kolesSzuro]

        let szurtIdList = '' 
        let IdListTojas
        let IdListZab
        let IdListHal
        let IdListOlajos
        let IdListBurgonya
        let IdListMaj
        let IdListKoles

        SzuroArray.forEach(function(tempSzuro, index){
            $.post( "getSzurtReceptList.php", {szuro: tempSzuro}, function(response, status){
                response = response.split("@")
                response.pop() 
                szurtIdList = response
                if (index == 0) {IdListTojas = szurtIdList}
                else if (index == 1) {IdListZab = szurtIdList}
                else if (index == 2) {IdListHal = szurtIdList}
                else if (index == 3) {IdListOlajos = szurtIdList}
                else if (index == 4) {IdListBurgonya = szurtIdList}
                else if (index == 5) {IdListMaj = szurtIdList}
                else if (index == 6) {IdListKoles = szurtIdList}
            })  
        })
    
    // A SZ?R?SEK M?K?DTET?SE
    let filterType = ''
    let clickedTojas = 0 
    let clickedZab = 0
    let clickedHal = 0
    let clickedOlajos = 0  
    let clickedBurgonya = 0 
    let clickedMaj = 0 
    let clickedKoles = 0  

    setTimeout(() => { 
        clickedSzurok = [
            [IdListTojas, 0], //0
            [IdListZab, 0], //1
            [IdListHal, 0], //2
            [IdListOlajos, 0], //3
            [IdListBurgonya, 0], //4
            [IdListMaj, 0], //5
            [IdListKoles, 0] //6
        ]    
    }, 1000) 

    // AMIKOR SZ?R?RE KLIKKEL
    $(document).on('click', '.filter', function() { 
        //Vissza?ll?tjuk az idListet ?s az etkezesekArrayt is az (eredetileg kisz?molt) alaphelyzetbe
        idList = originalIdList
        //Kinyerj?k a sz?r? t?pus?t
        filterType = $(this).attr("alt")
        //Lekezelj?k a gomb sz?nv?lt?s?t
        if (filterType == 'tojas') {
            clickedTojas++
            clickedSzurok[0][1] = clickedTojas
            if (clickedTojas % 2 != 0) { 
                styleChange = 'buttonWhite filterButton'
            } else { 
                styleChange = 'buttonGreen filterButton' 
            }        
        } else if (filterType == 'zab') {
            clickedZab++
            clickedSzurok[1][1] = clickedZab
            if (clickedZab % 2 != 0) { 
                styleChange = 'buttonWhite filterButton'
            } else { 
                styleChange = 'buttonGreen filterButton' 
            }
        } else if (filterType == 'hal') {
            clickedHal++
            clickedSzurok[2][1] = clickedHal
            if (clickedHal % 2 != 0) { 
                styleChange = 'buttonWhite filterButton'
            } else { 
                styleChange = 'buttonGreen filterButton' 
            }
        } else if (filterType == 'olajos') {
            clickedOlajos++
            clickedSzurok[3][1] = clickedOlajos
            if (clickedOlajos % 2 != 0) { 
                styleChange = 'buttonWhite filterButton'
            } else { 
                styleChange = 'buttonGreen filterButton' 
            }
        } else if (filterType == 'burgonya') {
            clickedBurgonya++
            clickedSzurok[4][1] = clickedBurgonya
            if (clickedBurgonya % 2 != 0) { 
                styleChange = 'buttonWhite filterButton'
            } else { 
                styleChange = 'buttonGreen filterButton' 
            }
        } else if (filterType == 'maj') {
            clickedMaj++
            clickedSzurok[5][1] = clickedMaj
            if (clickedMaj % 2 != 0) { 
                styleChange = 'buttonWhite filterButton'
            } else { 
                styleChange = 'buttonGreen filterButton' 
            }
        } else if (filterType == 'koles') {
            clickedKoles++
            clickedSzurok[6][1] = clickedKoles
            if (clickedKoles % 2 != 0) { 
                styleChange = 'buttonWhite filterButton'
            } else { 
                styleChange = 'buttonGreen filterButton' 
            }
        }
        document.getElementById(filterType).className = styleChange 
        //Lekezelj?k a (f?) IdList ?s az ?ssz ?telsz?m updatel?s?t (minden klikkel?s ut?n. Egy helyen, s nem a ki-be klikkel?sekkel foglalkozunk, hanem mindig megn?zz?k az aktu?lisan benyomott gombokat, s vel?k foglalkozunk csak.)
        for (i = 0; i < clickedSzurok.length; i++ ) {
            if (clickedSzurok[i][1] % 2 != 0) { 
                // Sz?ri, azaz meghagyja, ami nincs includolva. Teh?t pl. ha a clickedSzuro be van kapcsolva a toj?sn?l, akkor a toj?sra visszaad indexeket, ?s a nemtoj?sosakat tartja meg.
                idList = idList.filter(element => !clickedSzurok[i][0].includes(element))
            } 
        }
        listLength = idList.length
        document.getElementById('osszes').innerHTML = listLength + ' ?tel' 
        //Lekezelj?k a v?gleges idList (t?mb)-b?l az ?tkez?st?pus list?k (reggeli, f??tkez?s stb.) kivon?s?t, ?s a megv?ltozott sz?mok kijelz?s?t. Azaz updatelj?k az etkezesekArrayt!
        for ( b=0; b < 3; b++) {
            // Kisz?ri, azaz meghagyja, ami includolva van. Itt fontos az originaletkezesekArray --> mindig az eredeti ?rt?kb?l kell levonni!
            etkezesekArray[b] = originaletkezesekArray[b].filter(element => idList.includes(element))
        }
        document.getElementById('foetkezes').innerHTML = etkezesekArray[0].length + ' f??tkez?s'
        document.getElementById('reggeli').innerHTML = etkezesekArray[1].length + ' reggeli'
        document.getElementById('koztes').innerHTML = etkezesekArray[2].length + ' k?ztes'
    })

    // A MEALEK KIV?LASZT?SA
    let mealsDefault = ['reggeli','koztes1','ebed','koztes2','vacsora']
    let meals = ['reggeli','koztes1','ebed','koztes2','vacsora']
    
    $(document).on('click', '.meal', function() {
        let id = $(this).attr("alt")
        if (!meals.includes(id)) {
            meals.push(id)
            $('#meal',this).removeClass('buttonWhite')
            $('#meal',this).addClass('buttonGreen')
        } 
        else if (meals.includes(id)) { 
            meals = meals.filter(item => item !== id)
            $('#meal',this).removeClass('buttonGreen')
            $('#meal',this).addClass('buttonWhite')
        }
        // H?ny napra el?g az ?trend tartalom kisz?mol?s - K?S?BB
        // if (meals.includes('ebed') && meals.includes('vacsora'))
        //     console.log(xDays = Math.round(etkezesekArray[0].length/2))
        // $('.xDays').text(xDays + ' nap')
    })


    let = foodNamesX = []
    let etrend = [
        [], //0 - reggeli
        [], //1 - koztes1    
        [], //2 - ebed
        [], //3 - koztes2
        [] //4 - vacsora
    ]  

    let sideDishData
    // AZ ?TREND V?ZLAT L?TREHOZ?SA (ID-b?l)
    $(document).on('click', '#generate1', function() { 
        let display = ''
        //================>>>>
        // EZ A V?GS? LISTA L?TREHOZ?S?HOZ SZ?KS?GES (Ebben benne vannak a 7 napos ?trendhez kiv?lasztott ?telek is)
        // for (var q = 0; q <= 2; q++) {
        //     $.ajax({
        //         type: 'POST',
        //         url: 'getMealNames.php',
        //         data: {ids: etkezesekArray[q]},
        //         async: false,
        //         success: function(resp) {
        //             resp = JSON.parse(resp)
        //             foodNamesX[q] = resp
        //         }
        //     })
        // }
        //================>>>>

        // L?trehozzuk a heti ?trend t?mb?t a bejel?lt ?telfajt?k (reggeli stb.) alapj?n
        document.getElementById('menu').innerHTML = ''
        for (m=0; m<=6; m++) {
            if (meals.includes('reggeli')) {
                etrend[0].push(etkezesekArray[1][m])
                etkezesekArray[1].shift()
            }
            if (meals.includes('koztes1')) {
                etrend[1].push(etkezesekArray[2][m])
                etkezesekArray[2].shift()
            }
            if (meals.includes('ebed')) {
                etrend[2].push(etkezesekArray[0][m])
                etkezesekArray[0].shift()
            }
            if (meals.includes('koztes2')) {
                etrend[3].push(etkezesekArray[2][m])
                etkezesekArray[2].shift()
            }
            if (meals.includes('vacsora')) {
                // A vacsor?ban ne legyen leves
                if (etkezesekArray[4].includes(etkezesekArray[0][m]))
                etkezesekArray[0].shift()
                etrend[4].push(etkezesekArray[0][m])
                etkezesekArray[0].shift()
            }
        }
            //================>>>>
            // EZ AZ OVERLAYEN MEGJELEN?TETT ?TELN?VLIST?HOZ SZ?KS?GES
            let = foodNames = []
            for (var q = 0; q <= 2; q++) {
                $.ajax({
                    type: 'POST',
                    url: 'getMealNames.php',
                    data: {ids: etkezesekArray[q]},
                    async: false,
                    success: function(resp) {
                        resp = JSON.parse(resp)
                        foodNames[q] = resp
                    }
                })
            }
            //================>>>>

        // "Kiegyenes?tj?k" a multidimenzion?lis t?mb?t (etrendet)
        let etrFlat = []
        function flatten(arr) {
            return arr.concat.apply([], arr);
        }
        etrFlat = flatten(etrend)

        // Lehozzuk az ID-khoz tartoz? ?telneveket
        let mealNames = []
        let finalMeals = []
        let finalMeals2 = [
            [], //0 - reggeli
            [], //1 - koztes1    
            [], //2 - ebed
            [], //3 - koztes2
            [] //4 - vacsora
        ]  
        $.post( "getMealNames.php", {ids: etrFlat}, function(resp, stat){
            mealNames = JSON.parse(resp)
            // Mivel az esetleges duplik?ci?k miatt kevesebb nevet hoz le, az?rt elk?sz?t?nk egy ?j, imm?r hi?nytalan, de egydimenzi?s t?mb?t (a lej?v? ID-kat nem haszn?ljuk fel)
            etrFlat.forEach(element => {
                mealNames.forEach(element2 => {
                    if (element == element2[0])
                    finalMeals.push(element2[1])
                });
            });

            // L?trehozzuk a v?gs?, t?bbdimenzi?s t?mb?t az ?telnevekkel
            let count=0
            let index //mealsDefaultot figyeli
            let index2=0 //Ez meg a mealset figyeli
            finalMeals.forEach(elem => {
                // mealsDefault = ?sszes ?telfajta ?s ehhez ?sszehasonl?tjuk, a meals-t, azaz a kiv?lasztottakat
                // Az index2-?t csak akkor n?velj?k, ha ki lett ?rva mind a 7 kaja.
                // Az indexet n?velj?k, ha nincs egyez?s?g (azaz nincs kiv?lasztva az a kaja)
                for (index=0; index<mealsDefault.length; index++) {
                    if (mealsDefault[index] == meals[index2]) {
                        finalMeals2[index].push(elem)
                        count++
                        // Ha el?rj?k a 7-et, azaz az ?sszes nap kaj?j?t be?rtuk
                        if (count == 7) { 
                            count=0
                            index2++
                        }
                        // ha megtal?lta a kajat?pust, akkor ne vizsg?lja tov?bb
                        break
                    } 
                }

            })

            // ?s v?g?l ki?rjuk a htmlbe   
            // ( Az Alt=0/1/2 az alternat?v?k megjelen?t?s?hez kell az overlayen.)  
            let sorszam
            let sorszam2 = -1
            for ( x=0; x<=6; x++) { 
                // Meal t?pus
                sorszam = -1
                // A nap sorsz?ma
                sorszam2++
                display += "<div class='daily'>"
                display += "<div class='day'>" + parseInt(x+1) + ". nap</div>"
                sorszam++

                if (finalMeals2[0][x] != undefined) {
                    display += "<div class='food' id='" + sorszam + sorszam2 + "'><div alt='1' id='" + etrend[0][x] + "'>" + finalMeals2[0][x] + "</div><div class='sideDishAdd' id='SD" + etrend[0][x] + "'>+</div></div>"
                }
                sorszam++

                if (finalMeals2[1][x] != undefined) {
                    display += "<div class='food' id='" + sorszam + sorszam2 + "'><div alt='2' id='" + etrend[1][x] + "'>" + finalMeals2[1][x] + "</div><div class='sideDishAdd' id='SD" + etrend[1][x] + "'>+</div></div>"
                }
                sorszam++

                if (finalMeals2[2][x] != undefined) {
                    display += "<div class='food' id='" + sorszam + sorszam2 + "'><div alt='0' id='" + etrend[2][x] + "'>" + finalMeals2[2][x] + "</div><div class='sideDishAdd' id='SD" + etrend[2][x] + "'>+</div></div>"
                }
                sorszam++

                if (finalMeals2[3][x] != undefined) { 
                    display += "<div class='food' id='" + sorszam + sorszam2 + "'><div alt='2' id='" + etrend[3][x] + "'>" + finalMeals2[3][x] + "</div><div class='sideDishAdd' id='SD" + etrend[3][x] + "'>+</div></div>"
                }
                sorszam++

                if (finalMeals2[4][x] != undefined) {
                    display += "<div class='food' id='" + sorszam + sorszam2 + "'><div alt='0' id='" + etrend[4][x] + "'>" + finalMeals2[4][x] + "</div><div class='sideDishAdd' id='SD" + etrend[4][x] + "'>+</div></div>"
                }
                display += "</div>"
            }
            document.getElementById('menu').innerHTML = display

            $.post( "getMealNames.php", {ids: etkezesekArray[7]}, function(resp){
                sideDishData = JSON.parse(resp)
            })
        })  
    })   

    // AZ ALTERNAT?V?K MEGJELEN?T?SE (OVERLAY) ?S BESZ?R?SA
    let overlayActive = false
    let swap
    let swapId
    let toDisp
    let swapIx1
    let swapIx2
    let chosen
    let chosenId
    let order
    let chosenMealAddId
    let chosenSideDishId
    let chosenSideDishName
    let childElementCount
    let parentElement

    // A K?RETV?LASZT?S LEKEZEL?SE
    $(document).on('click', '.sideDishAdd', function(event) { 
        event.stopPropagation();
        chosenMealAddId = $(this).attr('id')
        const addingElement = document.getElementById(chosenMealAddId)
        parentElement = addingElement.parentNode
        childElementCount = parentElement.childElementCount
        overlayActive = true
        const displaySideDishChoices = (sideDishData) => {
            $('#overlay').css("display","block")
            toDisplay = "<div class='overList'>"
            sideDishData.forEach(element => {
                    toDisplay += "<div class='overItem2' id='" + element[0] + "'><a href='#'>" + element[1] + "</a></div>"
                });
            toDisplay += "</div>"
            document.getElementById('overlay').innerHTML = toDisplay
        }
        displaySideDishChoices(sideDishData)
    })

    // KLIKK az overlayen a k?retre
    $(document).on('click', '.overItem2', function() { 
        chosenSideDishId = ''
        chosenSideDishName = ''
        chosenSideDishId = $(this).attr('id')
        chosenSideDishName = $(this).text()
        $('#overlay').css("display","none") 
        overlayActive = false         
        if (childElementCount === 3) {
            const lastChildElement = parentElement.lastChild
            parentElement.removeChild(lastChildElement)            
        }
        $('#'+chosenMealAddId).after(`<div id='${chosenSideDishId}'>${chosenSideDishName}</div>`);
    })   

    // A F??TEL LECSER?L?SE

    $(document).on('click', '.food', function() { 
        overlayActive = true
        swap = ''
        swapId = ''
        toDisp = ''
        swapIx1 = ''
        swapIx2 = ''
        order = $(this).attr('id')
        childDiv = $(this).find('div')
        swap = $(childDiv).attr('alt')   
        swapId = $(childDiv).attr('id')  
        $('#' + order).css("background-color","lightgray")

        $('#searchBoxLayer').css("display","block")
        searchToDisplay = "<div class='search'><input id='input' type='text' placeholder='Keres?s'></div>"
        document.getElementById('searchBoxLayer').innerHTML = searchToDisplay

        const foodNamesToDisplay = foodNames[swap]
        const displayMealList = (foodNamesToDisplay) => {
            $('#overlay').css("display","block")
            toDisp = "<div class='overList'>"
            foodNamesToDisplay.forEach(element => {
                    toDisp += "<div class='overItem' alt='" + element[1] + "' id='" + element[0] + "'><a href='#'>" + element[1] + "</a></div>"
                });
            toDisp += "</div>"
            document.getElementById('overlay').innerHTML = toDisp
        }
        displayMealList(foodNamesToDisplay)

        // Megkeress?k a lecser?lend? elem pontos hely?t a multidimenzion?lis t?mbben
        swapIx1 = order.substring(0,1)
        swapIx2 = order.substring(1,2) 

        const searchBoxActions = () => {
            const searchBox = document.getElementById('input')
            searchBox.addEventListener('keyup', function() {
                const searchedString = searchBox.value.toLowerCase()
                searchBehaviour(searchedString)
            })
        }
        searchBoxActions()

        const searchBehaviour = (searchedString) => {
            let tempRecipesList = []
            foodNamesToDisplay.forEach(foodNameAndId => { 
                if (foodNameAndId[1].toLowerCase().startsWith(searchedString))
                tempRecipesList.push(foodNameAndId)
            })
            displayMealList(tempRecipesList)
        }
    })  

    // KLIKK az overlayen a csereelemre
    $(document).on('click', '.overItem', function() { 
        chosen = ''
        chosenId = ''
        $('#' + order).css("background-color","white")
        chosen = $(this).attr('alt')
        chosenId = $(this).attr('id')
        $('#overlay').css("display","none") 
        $('#searchBoxLayer').css("display","none") 
        overlayActive = false         
        //Minden inf?t kicser?l?nk a divben
        document.getElementById(order).innerHTML = "<div alt='" + swap + "' id='" + chosenId + "'>" + chosen + "</div><div class='sideDishAdd' id='SD" + chosenId + "'>+</div>"
        // Itt pedig updatelj?k a t?mb?t
        etrend[swapIx1][swapIx2] = chosenId
    })   

    //Hogy a list?n K?V?L is lehessen klikkelni
    $(document).on('click','#overlay', function(){

        if(overlayActive)  {
            $('#' + order).css("background-color","white")
            $('#overlay').css("display","none")
            $('#searchBoxLayer').css("display","none") 
            overlayActive = false
        }
    }) 
      
    // A V?GS? ?TREND-GENER?L?S
    $(document).on('click', '#generate2', function() {

        // EL KELL K?SZ?TEN?NK A K?RETEK MULTIDIMENZION?LIS T?MBJ?T
        const foodElements = document.querySelectorAll('.food')
        const sideDishArray = []
        foodElements.forEach(foodElement => {
            const subArray = []
            foodElement.querySelectorAll('div').forEach(subElement => {
                subArray.push(subElement.id)
            })
            if(subArray.length === 3) {
                subArray.splice(1,1)
                sideDishArray.push(subArray)
            }
        })
        localStorage.setItem("etrend", JSON.stringify(etrend))
        localStorage.setItem("koretek", JSON.stringify(sideDishArray))
        // Az ?trendhez tartoz? n?v kiv?laszt?sa
        // let clients1
        // let clients2 
        // let clients3
        // $.post( "getConsClients.php", {consId: consId}, function(resp) {
        //     clients1 = JSON.parse(resp)
        //     clients2 = (clients1.toString()).split(",")            
        //     // Megszabadulunk a duplik?ci?kt?l
        //     clients3 = (Array.from(new Set(clients2))).sort()
        // });

        // Az el?k?sz?tett ID list?t elt?roljuk localstorageban ?s ?tadjuk a v?gs? oldalnak
        //localStorage.setItem("etrend",etrend)

        window.open('generateMenu.php', '_blank')
    })
        //window.localStorage.removeItem('etkezesfajtak')

})

/*
K?RETV?LASZT?S

    1. EL KELL MENTENI A K?RETEKET EGY OBJEKTUMBA A SORREND ID SZERINT
    2. AZ ?TREND GENER?L?SN?L ?KET IS KI KELL JELEZNI

*/