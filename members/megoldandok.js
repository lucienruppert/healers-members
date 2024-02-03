let kezelendok = []
let kezelendokPushed = []
let kivizsgalandok = []
let kivizsgalandokPushed = []

const draggables = document.querySelectorAll('.draggable')
// Mindegyik 'container' lesz, amelyikBE �s amelyikB�L akarjuk VISSZA mozgatni
const containers = document.querySelectorAll('.container')
// Egy k�l�n classt adunk a mozgat�sban l�v�knek, hogy tudjuk �ket kezelni!
draggables.forEach(draggable => {
    draggable.addEventListener('dragstart', () => {
        draggable.classList.add('dragging')
    })
    draggable.addEventListener('dragend', () => {
        draggable.classList.remove('dragging')
        //Kinyerj�k, hogy mi van a containerekben �s k�t t�mbbe rakjuk
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

// A gomb lenyom�sa eset�n: 1. megv�ltozik a st�lusa, 2. beker�l a "pushed" t�mbbe + ki is szedhet�
$(document).on('click', '.draggable', function() {

    id = $(this).attr("id")
    //Ha m�g nincs benne > berakjuk a t�mbbe
    if (kezelendok.includes(id) && !kezelendokPushed.includes(id)) {
        kezelendokPushed.push(id)
        $('.megoldando',this).addClass('pushedButton')
    } else if (kezelendokPushed.includes(id)) { 
        //Ha m�r benne van ? akkor kivessz�k (kisz�rj�k)
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

// Figyelj�k, hogy mikor van a mozgatott elem egy C�L-container felett 
containers.forEach(container => {
    container.addEventListener('dragover', e => {
        // A default = nem lehet belerakni m�st, ezt �t�ll�tjuk
        e.preventDefault()
        const afterElement = getDragAfterElement(container, e.clientY)
        // Megszerezz�k az �ppen mozgatott elemet az oszt�ly�n kereszt�l
        const draggable = document.querySelector('.dragging')
        if (afterElement == null) {
            //Odabiggyeszti a sor v�g�re
            container.appendChild(draggable)
        } else {
            container.insertBefore(draggable, afterElement)
        }
    })
})
// Bevissz�k a f�ggyv�nybe a kurzor Y, azaz f�gg�leges poz�ci�j�t
function getDragAfterElement(container, y) {
    //Megszerz�nk minden draggablet a kont�nerben, kiv�ve azt, amit �ppen mozgatunk
    const draggableElements = [...container.querySelectorAll('.draggable:not(.dragging)')]
    //A reduce function lereduk�l egy function a bevitt �rt�kig?
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
