<!-- *********************************************************** -->
<!-- ******** NYISD MEG MAGYAR KÓDOLÁSBAN!!! ******************* -->
<!-- *********************************************************** -->

<?php
    session_start();
    include_once('functions.php');   
    include_once('functions_new.php');
    if(!$userObject){
        include_once('index.php');
        exit;
    }
?>
<!DOCTYPE html>
<head>
<title>SEARCH</title>
<?php include_once('headLinks.php'); ?>
<style>
    div {
        border: 0px solid black;
    }
    .search {
        text-align: center;
        margin-top: 100px;
    }
    .list {
        display: flex;
        flex-direction: column;
        align-items: center; 
    }
    #list {
        display: flex;
        flex-direction: column;
        align-items: left; 
        width: 400px;
    }
    input {
        width: 396px;
        margin-bottom: 5px;
        text-align: left;
        font-size: 5rem;
    }

</style>
</head>

</php>
<body>
<?php include("adminmenu.php"); ?>

    <div class='search'><input id='input' type="text"></div>
    <div class='list'>
        <div id="list"></div>
    </div>

<script>

    let recipesList = [] 
    document.addEventListener('DOMContentLoaded', async function() {
        recipesList = await downloadRecipes()
        displayRecipes(recipesList)
        searchBoxActions()
    })

    async function downloadRecipes() {
        const response = await fetch('https://healers.digital/members/getRecipes.php')
        return response.json()
    }

    // Milyen esetben lehet elõbb a function és utána a meghívása?
    const displayRecipes = (recipesList) => {
        let listToDisplay = ''
        recipesList.forEach((recipe, index) => {
            listToDisplay += `<div>${recipe[index,0]}</div>`
        })
        document.getElementById('list').innerHTML = listToDisplay
    }
    
    const searchBoxActions = () => {
        const searchBox = document.getElementById('input')
        searchBox.addEventListener('keyup', function() {
            const searchedString = searchBox.value.toLowerCase()
            searchBehaviour(searchedString)
        })
    }

    async function searchBehaviour(searchedString) {
        let tempRecipesList = []
        recipesList.forEach((recipe, index) => { 
            if (recipe[index,0].toLowerCase().startsWith(searchedString))
            tempRecipesList.push([recipe[index,0]])
        })
        displayRecipes(tempRecipesList)
    }

</script>
</body>
</html>
