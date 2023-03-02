// constants for title of asset
const TITLE = document.getElementById("assetTitle");
const TITLE_CHARS = document.getElementById("titleChars");
const MAX_TITLE_LENGTH = 60;

// constants for description of asset
const DESCRIPTION = document.getElementById("assetDescription");
const DESCRIPTION_CHARS = document.getElementById("descriptionChars");
const MAX_DESCRIPTION_LENGTH = 1000;

function titleLength(){
    let enteredCharacters = TITLE.value.length;
    let charsLeft = MAX_TITLE_LENGTH - enteredCharacters;

    if(charsLeft >= 0){
        TITLE_CHARS.textContent = charsLeft + " left";
        TITLE_CHARS.style.color = "green";
    } else {
        TITLE_CHARS.textContent = charsLeft + " left! Too many characters!";
        TITLE_CHARS.style.color = "red";
    }
}

function descriptionLength(){
    let enteredCharacters = DESCRIPTION.value.length;
    let charsLeft = MAX_DESCRIPTION_LENGTH - enteredCharacters;

    if(charsLeft >= 0){
        DESCRIPTION_CHARS.textContent = charsLeft + " left";
        DESCRIPTION_CHARS.style.color = "green";
    } else {
        DESCRIPTION_CHARS.textContent = charsLeft + " left! Too many characters!";
        DESCRIPTION_CHARS.style.color = "red";
    }
}

// Add new item to cart for current user
function addToCart(assetID){
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            // Typical action to be performed when the document is ready:
            //document.getElementById("demo").innerHTML = xhttp.responseText;
            console.log(xhttp.responseText);
        }
    }
    xhttp.open("GET", "PHP/addShoppingCart.php?asset="+assetID, true);
	xhttp.send();
}

// Remove a item from the cart
function removeFromCart(assetID){
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            // Typical action to be performed when the document is ready:
            document.getElementById(assetID).remove();
            console.log(xhttp.responseText);
        }
    }
    xhttp.open("GET", "removeShoppingCart.php?asset="+assetID, true);
	xhttp.send();
}

function replyComment(commentID){
    console.log("test");
    document.getElementById(commentID).style.display = "block";
}


// Handle star rating

const ratingValue = document.querySelector(".stars input")
const star = document.querySelectorAll(".stars .star")

star.forEach((item, index) => {
    item.addEventListener('click', function() {
        ratingValue.value = index + 1

        for(let i = 0; i < star.length; i++){
            if(i <= index) {
                star[i].classList.remove('disable')
            } else {
                star[i].classList.add('disable')
            }
        }
    })
})


// Sort by

function SortBy(){
    var url = window.location.href
    var sortType = document.getElementById("products").value
    url = new URLSearchParams(url.search)
    url.delete('sort')
    url += '?sort='+sortType
    window.location.href = url
}

function startValue(optionID){
    //console.log("test");
    //console.log(optionID);
    document.getElementById(optionID).selected = true;
}

titleLength();
descriptionLength();
TITLE.addEventListener("keyup", titleLength);
DESCRIPTION.addEventListener("keyup", descriptionLength);