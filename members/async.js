$(document).ready(async function(){
    // -- sequential way start --
    // const supplementList = await downloadSupplementList();
    // displaySupplementList(supplementList)

    // const recommendationList = await downloadRecommendationList();
    // displayRecommendationList(recommendationList)
    // -- sequential way end --

    // destructuring for objects
    // const bla = {a: 1, b: 2, c: 3, d: 'n�gy'};
    // const {a, d} = bla;

    // A1, A2
  // download, display

  // B1, B2, B3
  // main --> B1 --> B2 --> B3

  // main --> teljes recommendation list (B1 --> B2) // download
  // main --> display
  // --------------------------------
  // main --> download
  // main --> display

    const [supplementList, recommendationList] = await Promise.all([downloadSupplementList(),downloadRecommendationList()])

    displaySupplementList(supplementList)
    displayRecommendationList(recommendationList)
})

async function downloadSupplementList() {

    let response = await fetch('https://healers.digital/members/getSupplements.php')
    return response.json()
}

async function downloadRecommendationList() {

    let response = await fetch('https://healers.digital/members/getRecommendations.php')
    return response.json()
}

function displaySupplementList(supplementList) {

  let displayedSupplementList = ''
  supplementList.forEach(ButtonData => {
      displayedSupplementList  += `<div class='draggable supplement color' draggable='true' id='${ButtonData['id']}'><b>${ButtonData['brand']}</b> ${ButtonData['name']}</div>`
  });
  document.getElementById('supplementsSource').innerHTML = displayedSupplementList
}

function displayRecommendationList(recommendationList) {

  let displayedRecommendationList = ''
  recommendationList.forEach(ButtonData => {
      displayedRecommendationList  += `<div class='draggable recommendation color' draggable='true' id='${ButtonData['id']}'><b>${ButtonData['category']}</b> ${ButtonData['recommendation']}</div>`
  });
  document.getElementById('recommendationsSource').innerHTML = displayedRecommendationList
}


// download --> ids
// download (names by ids) 
//----------------------------------------
// main <--[already matched data]-- download

const selectProtocolActions = async () => {
    const diseaseId = document.getElementById('select').value
    const displayMode = diseaseId ? 'block' : 'none';

    document.getElementById('recommendationsTarget').style.display = displayMode
    document.getElementById('supplementsTarget').style.display = displayMode

    const protocolRecommendationsList = await downloadProtocolRecommendationsList(diseaseId)
    displayRecommButtons(protocolRecommendationsList)
}

async function downloadProtocolRecommendationsList(diseaseId) {
    const protocolRecommendationIds = await downloadProtocolRecommendationIds(diseaseId)
    return downloadRecommNamesForIds(protocolRecommendationIds)
}

async function downloadProtocolRecommendationIds(diseaseId) {
  let response = await fetch(`https://healers.digital/members/getRecommAndSuppIdList.php?diseaseId=${diseaseId}`)
  return response.json()
}

async function downloadRecommNamesForIds(downloadedIdLists) {

  let recommendationIdsWithNames
  if (downloadedIdLists.recommendationArray.length != 0) {
      let recommendationIds = downloadedIdLists.recommendationArray.split(',')
      let response = await fetch(`https://healers.digital/members/getRecommendationsNames.php?idList=${recommendationIds}`)
      recommendationIdsWithNames = await response.json()
  } else {
      recommendationIdsWithNames = ''
  }
  displayRecommButtons(recommendationIdsWithNames)
}

const displayRecommButtons = (listsWithNames) => {

    if (!listsWithNames.recommendationArray)
        listsWithNames.recommendationArray = ''
    if (!listsWithNames.supplementArray)
        listsWithNames.supplementArray = ''      

    // let recArr = returnedData.recommendationArray.split(',')
    // let suppArr = returnedData.supplementArray.split(',')
    document.getElementById('recommendationsTarget').innerHTML = listsWithNames.recommendationArray
    document.getElementById('supplementsTarget').innerHTML = listsWithNames.supplementArray   
    
}