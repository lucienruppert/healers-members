let kezelendok = []
let kezelendokPushed = []
let kivizsgalandok = []
let kivizsgalandokPushed = []

const draggables = document.querySelectorAll('.draggable')
// Mindegyik 'container' lesz, amelyikBE és amelyikBÕL akarjuk VISSZA mozgatni
const containers = document.querySelectorAll('.container')
// Egy külön classt adunk a mozgatásban lévõknek, hogy tudjuk õket kezelni!
draggables.forEach(draggable => {
    draggable.addEventListener('dragstart', () => {
        draggable.classList.add('dragging')
    })
    draggable.addEventListener('dragend', () => {
        draggable.classList.remove('dragging')
        //Kinyerjük, hogy mi van a containerekben és két tömbbe rakjuk
        kezelendok = $("#kezelendok")
        .find("div")
        .map(function() { return $(this).attr("id"); })
        .get()
        kivizsgalandok = $("#kivizsgalandok")
        .find("div")
        .map(function() { return $(this).attr("id"); })
        .get()
    })
})

// A gomb lenyomása esetén: 1. megváltozik a stílusa, 2. bekerül a "pushed" tömbbe + ki is szedhetõ
$(document).on('click', '.draggable', function() {

    id = $(this).attr("id")
    //Ha még nincs benne > berakjuk a tömbbe
    if (kezelendok.includes(id) && !kezelendokPushed.includes(id)) {
        kezelendokPushed.push(id)
        $('.megoldando',this).addClass('pushedButton')
    } else if (kezelendokPushed.includes(id)) { 
        //Ha már benne van ? akkor kivesszük (kiszûrjük)
        kezelendokPushed = kezelendokPushed.filter(item => item !== id)
        $('.megoldando',this).removeClass('pushedButton')
    }
    if (kivizsgalandok.includes(id) && !kivizsgalandokPushed.includes(id)) {
        kivizsgalandokPushed.push(id)
        $('.megoldando',this).addClass('pushedButton')
    } else if (kivizsgalandokPushed.includes(id)) { 
        kivizsgalandokPushed = kivizsgalandokPushed.filter(item => item !== id)
        $('.megoldando',this).removeClass('pushedButton')
    }
    console.table(kezelendokPushed)
})

// Figyeljük, hogy mikor van a mozgatott elem egy CÉL-container felett 
containers.forEach(container => {
    container.addEventListener('dragover', e => {
        // A default = nem lehet belerakni mást, ezt átállítjuk
        e.preventDefault()
        const afterElement = getDragAfterElement(container, e.clientY)
        // Megszerezzük az éppen mozgatott elemet az osztályán keresztül
        const draggable = document.querySelector('.dragging')
        if (afterElement == null) {
            //Odabiggyeszti a sor végére
            container.appendChild(draggable)
        } else {
            container.insertBefore(draggable, afterElement)
        }
    })
})
// Bevisszük a függyvénybe a kurzor Y, azaz függõleges pozícióját
function getDragAfterElement(container, y) {
    //Megszerzünk minden draggablet a konténerben, kivéve azt, amit éppen mozgatunk
    const draggableElements = [...container.querySelectorAll('.draggable:not(.dragging)')]
    //A reduce function leredukál egy function a bevitt értékig?
    return draggableElements.reduce((closest, child) => {
        const box = child.getBoundingClientRect()
        const offset = y - box.top - box.height / 2
        if (offset < 0 && offset > closest.offset) {
            return { offset: offset, element: child }
        } else {
            return closest
        }
    }, { offset: Number.NEGATIVE_INFINITY }).element
}
