let nowProtocols = []
let laterProtocols = []

async function loadCustomProtocols(clientId) {
    const clientProtocolData = await isClientProtocolData(clientId)
    const initialProtocolNames = await downloadAllProtocolNames()
    if (clientProtocolData) {
		const nowProtocolNames = clientProtocolData.protocol_Now ? await downloadClientProtocolNames(clientProtocolData.protocol_Now) : [];
		const nowProtocolTargetDivId = 'nowProtocols'
		displayClientProtocolNames(nowProtocolNames, nowProtocolTargetDivId)

		const laterProtocolNames = clientProtocolData.protocol_Later ? await downloadClientProtocolNames(clientProtocolData.protocol_Later) : [];
		const laterProtocolTargetDivId = 'laterProtocols'
		displayClientProtocolNames(laterProtocolNames, laterProtocolTargetDivId)

        const remainingProtocols = calculateRemainingProtocolData(clientProtocolData, initialProtocolNames)
        displayInitialProtocolNames(remainingProtocols)

		const recommendationsNames = clientProtocolData.protocol_Recomm ? await downloadRecommendationsNames(clientProtocolData.protocol_Recomm) : [];
		displayRecommendations(recommendationsNames)

		const supplementsNames = clientProtocolData.protocol_Supp ? await downloadSupplementsNames(clientProtocolData.protocol_Supp) : [];
		displaySupplements(supplementsNames)
    } else {
		displayClientProtocolNames([], 'nowProtocols');
		displayClientProtocolNames([], 'laterProtocols');
        displayInitialProtocolNames(initialProtocolNames);
    }
    defineDraggableButtonsFromDOM()
    fillNowAndLaterArrays()
    defineContainersAndBehaviour()
    readyButtonActions(clientId)	
    generateButtonActions(clientId)	
}
async function isClientProtocolData(clientId) {

    const response = await fetch(`https://healers.digital/members/getClientProtocolData.php?clientId=${clientId}`)
    return response.json()
}
async function downloadAllProtocolNames() {

    const response = await fetch('https://healers.digital/members/getAllProtocolNames.php')
    return response.json()
}
const displayInitialProtocolNames = (initialProtocolNames) => {

    let protocolButtons = ''
        initialProtocolNames.forEach(protocolData => {
            protocolButtons  += `<div class='draggable megoldando color' draggable='true' id='${protocolData.Id}'>${protocolData.Name}</div>`
        })
    document.getElementById('list').innerHTML = protocolButtons
}
async function downloadClientProtocolNames(protocolIds) {

    const response = await fetch(`https://healers.digital/members/getProtocolNamesForClients.php?idList=${protocolIds}`)
    return response.json()
}
const calculateRemainingProtocolData = (clientProtocolData, initialProtocolNames) => {

    const fullClientProtocolList = clientProtocolData.protocol_Now.concat(',',clientProtocolData.protocol_Later).split(",")
    return initialProtocolNames.filter(obj => !fullClientProtocolList.includes(obj.Id));

} 
const displayClientProtocolNames = (protocolNames, targetDivId) => {

    let protocolButtons = ''
        protocolNames.forEach(protocolData => {
            protocolButtons  += `<div class='draggable megoldando color' draggable='true' id='${protocolData.Id}'>${protocolData.Name}</div>`
        })
    document.getElementById(targetDivId).innerHTML = protocolButtons
}
async function downloadRecommendationsNames(protocolStepsIds) {

    const response = await fetch(`https://healers.digital/members/getRecommendationsNames.php?idList=${protocolStepsIds}`)
    return response.json()
}
async function downloadSupplementsNames(protocolStepsIds) {

    const response = await fetch(`https://healers.digital/members/getSupplementsNames.php?idList=${protocolStepsIds}`)
    return response.json()
}
const displayRecommendations = (recommendationsNames) => {

    let protocolButtons = ''
        recommendationsNames.forEach(recommendationsData => {
            protocolButtons  += `<div class='draggable2 protocolStep color' draggable='true' id='${recommendationsData.Id}'><b>${recommendationsData.category}</b> ${recommendationsData.recommendation}</div>`
        })
    document.getElementById('recommNowProtocolSteps').innerHTML = protocolButtons
}
const displaySupplements = (supplementsNames) => {

    let protocolButtons = ''
            supplementsNames.forEach(supplementsData => {
            protocolButtons  += `<div class='draggable2 protocolStep color' draggable='true' id='${supplementsData.Id}'><b>${supplementsData.brand}</b> ${supplementsData.name}</div>`
        })
    document.getElementById('suppProtocolSteps').innerHTML = protocolButtons
}
const defineDraggableButtonsFromDOM = () => {

    const draggables = document.querySelectorAll('.draggable') 
    draggables.forEach(draggable => {

        draggable.addEventListener('dragstart', () => {
            draggable.classList.add('dragged')
        })
        draggable.addEventListener('dragend', () => {
            draggable.classList.remove('dragged')
            fillNowAndLaterArrays()
            fillProtocolStepsArray()
        })
    })       
}
const fillNowAndLaterArrays = () => { 

    nowProtocols = [...document.querySelectorAll('#nowProtocols div')]
                    .map(element => element = element.id)
    laterProtocols = [...document.querySelectorAll('#laterProtocols div')]
                    .map(element => element = element.id)
}
const defineContainersAndBehaviour = () => {

    const containers = document.querySelectorAll('.container')
    containers.forEach(container => {
        container.addEventListener('dragover', e => {
            e.preventDefault()
            const dragged = document.querySelector('.dragged')
            if (!dragged) return // Azért kell elvileg, mert a dragend a droppolt elemen elõbb fut le a dragend, mint a dragovernek a callback functionje a containeren. (vagy nem! :))) 
            const elementAfterDragged = getElementAfterDragged(container, e.clientY)
            container.insertBefore(dragged, elementAfterDragged)

        })
    })
}
const getElementAfterDragged = (container, draggedY) => {
    const nonDraggedElements = [...container.querySelectorAll('.draggable:not(.dragged)')]
    const getPositionOfElementAfterDragged = (closestElement, examinedElement) => {
        const {top, height} = examinedElement.getBoundingClientRect()
        const distance = draggedY - top - height / 2
        if (distance < 0 && distance > closestElement.offset) { 
            return { offset: distance, element: examinedElement }
        } else {
            return closestElement
        }
    }
    return nonDraggedElements.reduce(getPositionOfElementAfterDragged, { offset: Number.NEGATIVE_INFINITY }).element
}
let laterProtocolSteps = []
let nowProtocolSteps = []
let protocolSupplementIds = []
let globalClientId = null;

async function readyButtonActions(clientId) {
	globalClientId = clientId;
	
    const saveButton = document.querySelector('.saveDiv')
	
	saveButton.removeEventListener('click', saveDivClick);
    saveButton.addEventListener('click', saveDivClick);
}

async function saveDivClick() {
	// const overlayDiv = document.querySelector('.page1_overlay')
	// overlayDiv.style.display = 'block'
	document.querySelector('.page2').scrollIntoView({behavior: 'smooth'})

	let nowRecommendationData = []
	let nowSupplementData = []
	if (nowProtocols.length) {

		nowProtocolSteps = await downloadAllProtocolSteps(nowProtocols)

		const nowProtocolRecommendationIds = extractProtocolRecommendationIds(nowProtocolSteps)
		if (nowProtocolRecommendationIds.length)
			nowRecommendationData = await downloadRecommendationData(nowProtocolRecommendationIds)
		
		protocolSupplementIds = extractSupplementRecommendationIds(nowProtocolSteps)            
		if (protocolSupplementIds.length) 
			nowSupplementData = await downloadNowSupplementData(protocolSupplementIds)
	}
	displayNowRecommendationList(nowRecommendationData)
	displaySupplementList(nowSupplementData)

	let laterRecommendationData = []
	if (laterProtocols.length) {

		const laterProtocolSteps = await downloadAllProtocolSteps(laterProtocols)

		const laterProtocolRecommendationIds = extractProtocolRecommendationIds(laterProtocolSteps)
		if (laterProtocolRecommendationIds.length)
		laterRecommendationData = await downloadRecommendationData(laterProtocolRecommendationIds)
	}
	displayLaterRecommendationList(laterRecommendationData)
	
	defineDraggableButtonsFromDOM()
	fillProtocolStepsArray()
	saveClientProtocolData(globalClientId)
}

const fillProtocolStepsArray = () => { 
    laterProtocolSteps = [...document.querySelectorAll('#recommLaterProtocolSteps div')]
                    .map(element => element = element.id)
    nowProtocolSteps = [...document.querySelectorAll('#recommNowProtocolSteps div')]
    .map(element => element = element.id)
}
async function downloadAllProtocolSteps(protocolList) {

    const response = await fetch(`https://healers.digital/members/getProtocolSteps.php?protocolList=${protocolList}`)
    return response.json() 
}
const extractProtocolRecommendationIds = (protocolSteps) => {

    const array = protocolSteps.map(oneProtocolData => oneProtocolData.recommendationArray.split(',')).flat().filter(element => element!='')
    return [...new Set(array)];
}
async function downloadRecommendationData(protocolRecommendationIds) {

    const response = await fetch(`https://healers.digital/members/getRecommendationsNames.php?idList=${protocolRecommendationIds}`)
    return response.json()
}
const displayNowRecommendationList = (nowRecommendationData) => {
    let recommendationButtons = ''
    nowRecommendationData.forEach(ButtonData => {
        recommendationButtons  += `<div class='draggable recommendation color' draggable='true' id='${ButtonData['id']}'><b>${ButtonData['category']}</b><br>${ButtonData['recommendation']}</div>`
    });
    document.getElementById('recommNowProtocolSteps').innerHTML = recommendationButtons
}
const displayLaterRecommendationList = (laterRecommendationData) => {
    let recommendationButtons = ''
    laterRecommendationData.forEach(ButtonData => {
        recommendationButtons  += `<div class='draggable recommendation color' draggable='true' id='${ButtonData['id']}'><b>${ButtonData['category']}</b><br>${ButtonData['recommendation']}</div>`
    });
    document.getElementById('recommLaterProtocolSteps').innerHTML = recommendationButtons
}
const extractSupplementRecommendationIds = (nowProtocolSteps) => {

    const array = nowProtocolSteps.map(oneSupplementData => oneSupplementData.supplementArray.split(',')).flat().filter(element => element!='')
    return [...new Set(array)];
}
async function downloadNowSupplementData(protocolSupplementIds) {

    const response = await fetch(`https://healers.digital/members/getSupplementsNames.php?idList=${protocolSupplementIds}`)
    return response.json()
}
const displaySupplementList = (nowSupplementData) => {

    let supplementButtons = ''
    nowSupplementData.forEach(ButtonData => {
        supplementButtons  += `<div class='draggable supplement color' draggable='true' id='${ButtonData['id']}'><b>${ButtonData['brand']}</b><br>${ButtonData['name']}</div>`
    });
    document.getElementById('suppProtocolSteps').innerHTML = supplementButtons
}
async function generateButtonActions(clientId) {

    const generateButton = document.querySelector('.generateDiv')
    generateButton.addEventListener('click', async function() {
        saveClientProtocolData(clientId)
        window.open("esetf_ajanlas2.php?id=" + fillId + "_blank");
    })
}
async function saveClientProtocolData(clientId) {

    await fetch(`https://healers.digital/members/saveClientProtocolData.php?nowProtocols=${nowProtocols}&laterProtocols=${laterProtocols}&nowProtocolSteps=${nowProtocolSteps}&protocolSupplementIds=${protocolSupplementIds}&clientId=${clientId}`)
    // alert ('A mentés sikerült')
} 

/* A WHAT: 

BUG: NE HÚZHASSA BELE AZ ALSÓ LÉPÉSEKET A SUPPLEMENTEKBE!!!

***** A CLIENT VIEW! *****

2. ÖSSZEGYÛJTENI ÉS ÁTADNI MINDEN ADATOT EGY OBJEKTUMBA
3. EZT ÁTADNI LOCAL STORAGEBAN?

KÉSÕBB

    - Megmutathatnánk a protokoll gombokon, h hány ajánlás/tápkieg van alárendelve
    - Kell Újrakezdõ vagy Teljes Delete gomb?
    - Ha visszarakja a protokolt, akkor ABC-sorrendbe rakja vissza.

*/


