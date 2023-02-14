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

titleLength();
descriptionLength();
TITLE.addEventListener("keyup", titleLength);
DESCRIPTION.addEventListener("keyup", descriptionLength);