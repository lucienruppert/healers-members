let receptek

function Feldolgoz() {

    let mealIds = $.parseJSON(localStorage.getItem("etrend"))    
    mealIds = mealIds.flat()
    let mealIdsandNames = []
    let completeMealsList = []
    let sidedishIdsandNames = []
    const sideDishAndMeal = $.parseJSON(localStorage.getItem("koretek"))
    const sideDishIds = sideDishAndMeal.map(sideDishId => sideDishId = sideDishId[1])
    $.post( "getMealNames.php", {ids: sideDishIds}, function(resp, stat){
        sidedishIdsandNames = JSON.parse(resp)
        sideDishAndMeal.forEach(sideDishAndMealIds => {
            sidedishIdsandNames.forEach(idAndName => {
                if (sideDishAndMealIds[1] == idAndName[0])
                sideDishAndMealIds[1] = idAndName[1]
            })
        })
        // sidedishIdsandNames.forEach(idAndName => {  
        //     sideDishAndMeal.forEach(sideDishId => {
        //         if (idAndName[0] == sideDishId[1]) { 
        //             sideDishId[1] = idAndName[1]
        //         }
        //     })
        // })
    })
    $.post( "getMealNames.php", {ids: mealIds}, function(resp, stat){
        mealIdsandNames = JSON.parse(resp)
        // Mivel a duplikációk miatt az sql kevesebb nevet ad vissza, ezért az eredeti lista alapján kiegészítjük a hiányzó neveket.
        mealIds.forEach(mealId => {
            mealIdsandNames.forEach(idAndName => {
                if (mealId == idAndName[0])
                completeMealsList.push(idAndName[1])
            });
        });

        let displayMenu = `<div class='row'>`
        for (x=1; x<=7; x++) { 
            displayMenu += `<div class='day'> ${x}. nap</div>`
        }    
        displayMenu += `</div>`
        displayMenu += `<div class='row'>`
        completeMealsList.forEach((mealName, mealIndex) => {
            sideDishAndMeal.forEach(sideDishId => {
                if (mealIds[mealIndex] == sideDishId[0]) {
                    mealName += `<br>+<br>${sideDishId[1]}`
                }
            })
            displayMenu += `<div class='food' id='${mealIds[mealIndex]}'><a href='#anchor${mealIds[mealIndex]}'>${mealName}</a></div>` 
            if ((mealIndex+1)%7==0) {
                displayMenu += `</div><div class='row'>`
            }
        })
        displayMenu += `</div>`
        document.getElementById('menu').innerHTML = displayMenu
    })


    $.post( "getReceptforMenu.php", {idList: mealIds}, function(response, status) {
        receptek = response.split('~')
        final = []
        let recept = []
        let color
        for (let i = 0; i < receptek.length-1; i++) {
            recept = receptek[i].split('@')
            recept[2] = '&#x2022;&nbsp;' + recept[2].replace(/(?:\r\n|\r|\n)/g, "<br>&#x2022;&nbsp;");
            recept[3] = recept[3].replace(/(?:\r\n|\r|\n)/g, "<br>");
            let card = `<div class='card' id='anchor${recept[0]}'>
                <div class='cardTitle'>${recept[1]}</div>
                <div><img class='image' src='img_uploads/${recept[5]}'></div>
                <div class='cardIngredientsTitle'>Hozzávalók</div>
                <div class='cardIngredients'>${recept[2]}</div>
                <div class='cardInstructionsTitle'>Elkészítés</div>
                <div class='cardInstructions'>${recept[3]}</div>
                <div class='cardInstructionsTitle'>Adag</div>
                <div class='cardInstructions'>${recept[6]}</div>
                <div class='cardAuthor'>Forrás: ${recept[4]}</div>
            </div>
            <div class='pagebreak'></div>`
            final.push(card) 
        }
        document.getElementById("recipes").innerHTML = final 
    })
}

let mybutton = document.getElementById("button");
// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function() {scrollFunction()};
function scrollFunction() {
  if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
    mybutton.style.display = "block";
  } else {
    mybutton.style.display = "none";
  }
}
// When the user clicks on the button, scroll to the top of the document
function topFunction() {
  document.body.scrollTop = 0;
  document.documentElement.scrollTop = 0;
}