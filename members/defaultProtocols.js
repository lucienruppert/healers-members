let fullSupplementList
let fullRecommendationList
let supplements = []
let recommendations = []
document.addEventListener('DOMContentLoaded', async function() {

    [fullSupplementList, fullRecommendationList] = await Promise.all([downloadSupplementList(),downloadRecommendationList()])
    displaySupplementList(fullSupplementList)
    displayRecommendationList(fullRecommendationList)
    defineDraggableButtonsFromDOM()
    defineContainersAndBehaviour()
    
})
async function downloadSupplementList() {

    const response = await fetch('https://healers.digital/members/getSupplements.php')
    return response.json()
}
async function downloadRecommendationList() {

    const response = await fetch('https://healers.digital/members/getRecommendations.php')
    return response.json()
}
function displaySupplementList(fullSupplementList) {

  let displayedSupplementList = ''
  fullSupplementList.forEach(ButtonData => {
      displayedSupplementList  += `<div class='draggable supplement color' draggable='true' id='${ButtonData['id']}'><b>${ButtonData['brand']}</b> ${ButtonData['name']}</div>`
  });
  document.getElementById('supplementsSource').innerHTML = displayedSupplementList
}
function displayRecommendationList(fullRecommendationList) {

  let displayedRecommendationList = ''
  fullRecommendationList.forEach(ButtonData => {
      displayedRecommendationList  += `<div class='draggable recommendation color' draggable='true' id='${ButtonData['id']}'><b>${ButtonData['category']}</b> ${ButtonData['recommendation']}</div>`
  });
  document.getElementById('recommendationsSource').innerHTML = displayedRecommendationList
}
let diseaseId
const selectProtocolActions = async () => {

    diseaseId = document.getElementById('select').value
    let displayMode = 'none'
    if (diseaseId == 0) {
        displayMode = 'none'
    } else {
        displayMode = 'block'
        const downloadedIdLists = await downloadStoredRecommAndSuppIdList(diseaseId)
         actionsBasedOnDownload(downloadedIdLists)
    }
    document.getElementById('recommendationsTarget').style.display = displayMode
    document.getElementById('supplementsTarget').style.display = displayMode
    document.getElementById('save').style.display = displayMode
}
document.getElementById("select").addEventListener("change", selectProtocolActions)
async function downloadStoredRecommAndSuppIdList(diseaseId) {

    const response = await fetch(`https://healers.digital/members/getRecommAndSuppIdList.php?diseaseId=${diseaseId}`)
    return downloadedIdLists = await response.json()
}
const actionsBasedOnDownload = async (downloadedIdLists) => {

    if(downloadedIdLists.recommendationArray) {
        const recommNamesForIds = await downloadRecommNamesForIds(downloadedIdLists)
        displayRecommButtons(recommNamesForIds)
        recalculateRecommList(fullRecommendationList, recommNamesForIds)
        displayRecommendationList(modifiedRecommendationList)
        defineDraggableButtonsFromDOM()
    } else {
        displayRecommButtons()
        displayRecommendationList(fullRecommendationList)
        defineDraggableButtonsFromDOM()
    }
    if(downloadedIdLists.supplementArray) {
        const suppNamesForIds = await downloadSuppNamesForIds(downloadedIdLists)
        displaySuppButtons(suppNamesForIds)
        recalculateSuppList(fullSupplementList, suppNamesForIds)
        displaySupplementList(modifiedSupplementList)
        defineDraggableButtonsFromDOM()
    } else {
        displaySuppButtons()
        displaySupplementList(fullSupplementList)
        defineDraggableButtonsFromDOM()
    }
    fillSuppAndRecommArrays()
}
let modifiedRecommendationList = []
const recalculateRecommList = (fullRecommendationList, recommNamesForIds) => {

    modifiedRecommendationList = fullRecommendationList.filter(checkedItem => 
            !recommNamesForIds.some(item => item.id === checkedItem.id)
    )
}
let modifiedSupplementList = []
const recalculateSuppList = (fullSupplementList, suppNamesForIds) => {

    modifiedSupplementList = fullSupplementList.filter(checkedItem => 
            !suppNamesForIds.some(item => item.id === checkedItem.id)
    )
}
async function downloadRecommNamesForIds(downloadedIdLists) {

    let recommendationIds = downloadedIdLists.recommendationArray.split(',')
    let response = await fetch(`https://healers.digital/members/getRecommendationsNames.php?idList=${recommendationIds}`)
    return await response.json()

}
async function downloadSuppNamesForIds(downloadedIdLists) {

    let supplementIds = downloadedIdLists.supplementArray.split(',')
    let response = await fetch(`https://healers.digital/members/getSupplementsNames.php?idList=${supplementIds}`)
    return await response.json()

}
const displayRecommButtons = (recommendationIdsWithNames) => {

    let recommendationButtons = ''
    if (recommendationIdsWithNames) {
        recommendationIdsWithNames.forEach(Button => {
            recommendationButtons  += `<div class='draggable recommendation color' draggable='true' id='${Button['id']}'><b>${Button['category']}</b> ${Button['recommendation']}</div>`
        })
    }
    document.getElementById('recommendationsTarget').innerHTML = recommendationButtons

}
const displaySuppButtons = (supplementIdsWithNames) => {

    let supplementButtons = ''
    if (supplementIdsWithNames) {
        supplementIdsWithNames.forEach(Button => {
            supplementButtons  += `<div class='draggable supplement color' draggable='true' id='${Button['id']}'><b>${Button['brand']}</b> ${Button['name']}</div>`
        })
    }
    document.getElementById('supplementsTarget').innerHTML = supplementButtons
    
}
const defineDraggableButtonsFromDOM = () => {

    const draggables = document.querySelectorAll('.draggable') 
    draggables.forEach(draggable => {

        draggable.addEventListener('dragstart', () => {
            draggable.classList.add('dragged')
            const containers = Array.from(document.querySelectorAll('.container'))
            containers.filter(container => !isSameType(container, draggable))
                .forEach(container => container.classList.add('not-allowed'))
        })
        draggable.addEventListener('dragend', () => {
            draggable.classList.remove('dragged')
            fillSuppAndRecommArrays()
            const containers = Array.from(document.querySelectorAll('.container'))
            containers.filter(container => !isSameType(container, draggable))
                .forEach(container => container.classList.remove('not-allowed'))
        })
    })          
}
const fillSuppAndRecommArrays = () => { 

    supplements = [...document.querySelectorAll('#supplementsTarget div')]
                    .map(element => element = element.id)
    recommendations = [...document.querySelectorAll('#recommendationsTarget div')]
                    .map(element => element = element.id)
}
const defineContainersAndBehaviour = () => {

    const containers = document.querySelectorAll('.container')
    containers.forEach(container => {
        container.addEventListener('dragover', e => {
            e.preventDefault()
            const dragged = document.querySelector('.dragged')
            if (!dragged) return
            if (!isSameType(container, dragged)) return
            const elementToDropBefore = getDropPosition(container, e.clientY)
            if (elementToDropBefore == null) {
                container.appendChild(dragged)
            } else {
                container.insertBefore(dragged, elementToDropBefore)
            }
        })
    })
}
const getDraggedType = (dragged) => {

    return dragged.classList.contains('recommendation') ? 'recommendation' : 'supplement' 
}
const isSameType = (container, dragged) => {

    return container.id.includes(getDraggedType(dragged))
}
const getDropPosition = (container, y) => {

    const draggableElements = [...container.querySelectorAll('.draggable:not(.dragged)')]
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
const saveButton = document.querySelector('.saveButton')
saveButton.addEventListener('click', () => {
    saveProtocol(diseaseId, recommendations, supplements)
})
async function saveProtocol(diseaseId, recommendations, supplements) {

    let response = await fetch(`https://healers.digital/members/updateRecommAndSuppData2.php?diseaseId=${diseaseId}&recommendations=${recommendations}&supplements=${supplements}`)
    if(response) alert('Sikeres mentés!')

}

