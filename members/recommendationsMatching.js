$(document).ready(async function(){

    let consultantId
    $.post( "getUserId.php", function(response){
        consultantId = response
    })

    $('#supplementsTarget').hide()
    $('#recommendationsTarget').hide()

    await Promise.all([displaySupplementList(),displayRecommendationList()])
    
    async function displaySupplementList() {

        let response = await fetch('https://healers.digital/members/getSupplements.php')
        let fullSupplementList = await response.json()
        let originalSupplementList = ''
        fullSupplementList.forEach(ButtonData => {
            originalSupplementList  += `<div class='draggable supplement color' draggable='true' id='${ButtonData['id']}'><b>${ButtonData['brand']}</b> ${ButtonData['name']}</div>`
        });
        document.getElementById('supplementsSource').innerHTML = originalSupplementList
    }

    async function displayRecommendationList() {

        let response = await fetch('https://healers.digital/members/getRecommendations.php')
        let fullRecommendationList = await response.json()       
        let originalRecommendationList = ''
        fullRecommendationList.forEach(ButtonData => {
            originalRecommendationList  += `<div class='draggable recommendation color' draggable='true' id='${ButtonData['id']}'><b>${ButtonData['category']}</b> ${ButtonData['recommendation']}</div>`
        });
        document.getElementById('recommendationsSource').innerHTML = originalRecommendationList
    }
    

    let diseaseId
    $('.diseaseList').change (function () {  
        diseaseId = $(this).find(":selected").val(); 
        if (diseaseId == 0) {
            $('#supplementsTarget').hide()
            $('#recommendationsTarget').hide()
        } else {
            $('#supplementsTarget').show()
            $('#recommendationsTarget').show()
        }
        supplements = []
        recommendations = []
        let returnedData = []

        $.post( "getRecommAndSuppIdList.php", {diseaseId: diseaseId, consultantId: consultantId}, function(response) {
            returnedData = JSON.parse(response)
            if (returnedData.recommendationArray === undefined 
                || returnedData.supplementArray === undefined 
                || returnedData.recommendationArray === '' 
                || returnedData.supplementArray === '' 
                ) {
                document.getElementById('recommendationsTarget').innerHTML = ''
                document.getElementById('supplementsTarget').innerHTML = ''                
            } else {
                let recArr = returnedData.recommendationArray.split(',')
                let suppArr = returnedData.supplementArray.split(',')
                loadStoredRecAndSuppData(recArr, suppArr)            
            }
        })

    })

    const loadStoredRecAndSuppData = function(recArr, suppArr) {

        let recommendationIdsWithNames = [] 
        let recommendationButtons = ''
        $.post( "getRecommendationsData.php", {idList: recArr}, function(response, status) {
            recommendationIdsWithNames = JSON.parse(response)
            recommendationIdsWithNames.forEach(ButtonData => {
                recommendationButtons  += `<div class='draggable recommendation color' draggable='true' id='${ButtonData['id']}'><b>${ButtonData['category']}</b> ${ButtonData['recommendation']}</div>`
            })
            document.getElementById('recommendationsTarget').innerHTML = recommendationButtons
        })

        let supplementIdsWithNames = [] 
        let supplementButtons = ''
        $.post( "getSupplementsData.php", {idList: suppArr}, function(response, status) {
            supplementIdsWithNames = JSON.parse(response)
            supplementIdsWithNames.forEach(ButtonData => {
                supplementButtons  += `<div class='draggable supplement color' draggable='true' id='${ButtonData['id']}'><b>${ButtonData['brand']}</b> ${ButtonData['name']}</div>`
            })
        document.getElementById('supplementsTarget').innerHTML = supplementButtons
        })
    }

    let supplements = []
    let recommendations = []
    let draggables = document.querySelectorAll('.draggable') 
    draggables.forEach(draggable => {

        draggable.addEventListener('dragstart', () => {
            draggable.classList.add('dragged')
        })
        draggable.addEventListener('dragend', () => {
            draggable.classList.remove('dragged')
            supplements = $("#supplementsTarget")
            .find("div")
            .map(function() { return $(this).attr("id"); })
            .get()
            recommendations = $("#recommendationsTarget")
            .find("div")
            .map(function() { return $(this).attr("id"); })
            .get()
            console.log('itt')
            console.log(supplements, recommendations)
        })
    })       

    const containers = document.querySelectorAll('.container')
    containers.forEach(container => {
        container.addEventListener('dragover', e => {
            e.preventDefault()
            const dragged = document.querySelector('.dragged')
            const afterElementPosition = getDragAfterElement(container, e.clientY)
            if (!dragged) return
            if (!isSameType(container, dragged)) 
                return
            if (afterElementPosition == null) {
                container.appendChild(dragged)
            } else {
                container.insertBefore(dragged, afterElementPosition)
            }
        })
    })

    function isSameType(container, dragged) {

        if (
            container.id === 'recommendationsTarget' && dragged.classList.contains('recommendation') 
            || container.id === 'recommendationsSource' && dragged.classList.contains('recommendation')
            || container.id === 'supplementsTarget' && dragged.classList.contains('supplement')
            || container.id === 'supplementsSource' && dragged.classList.contains('supplement')
        ) 
        return true 
    }

    function getDragAfterElement(container, y) {

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


// INNENTÕL KEZDVE NEM RAKTAM MÉG ÁT!

    $(document).on('click', '.saveButton', function () {    
        
        console.log(recommendations, supplements)
        $.post( "updateRecommAndSuppData.php", {diseaseId: diseaseId, consultantId: consultantId, recommendations: recommendations, supplements: supplements}, function(response, status) {
        if(status) 
        alert('A mentés sikerült!')
        })

    })

})
