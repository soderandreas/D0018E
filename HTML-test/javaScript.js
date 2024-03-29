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

function addToCartProduct(assetID){
    let num = document.getElementById("amountProd").value;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            // Typical action to be performed when the document is ready:
            //document.getElementById("demo").innerHTML = xhttp.responseText;
            console.log(xhttp.responseText);
        }
    }
    xhttp.open("GET", "addShoppingCart.php?asset="+assetID+"&num="+num, true);
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

// Change items in order
function amountInOrder(test){
    console.log("test", test.target.defaultValue);
    console.log(test);
    if((test.keyCode < 106 && test.keyCode > 95) || (test.keyCode < 58 && test.keyCode > 47) || test.keyCode == 8 || (test.keyCode == 90 && test.ctrlKey)){
        if(!isNaN(test.target.valueAsNumber)){
            newAmount = test.target.valueAsNumber;
        } else {
            newAmount = 0;
        }

        test.target.value = test.target.value < 0 ? 0 : test.target.value;

        test.target.value = test.target.value > 10 ? 10 : test.target.value;

        let oriPrice = document.getElementById("oriPriceFor"+test.originalTarget.id).textContent;
        oriPrice = oriPrice.replace('$','');

        let price = document.getElementById("currPriceFor"+test.originalTarget.id).textContent;
        price = price.replace('$', '');
        let newPrice = price*newAmount;
        let prevPrice = document.getElementById("totalFor"+test.originalTarget.id).textContent;
        prevPrice = prevPrice.replace('$', '');
        document.getElementById("totalFor"+test.originalTarget.id).textContent = "$"+newPrice;

        let totalPrice = document.getElementById("totalPrice").textContent;
        totalPrice = totalPrice.replace('$', '');
        //console.log("newPrice: "+newPrice+" prevPrice: "+prevPrice);

        if(Number(test.target.defaultValue) < Number(test.target.value)){
            newPrice = test.target.defaultValue*oriPrice + (test.target.value-test.target.defaultValue)*price;
            document.getElementById("totalFor"+test.originalTarget.id).textContent = "$"+newPrice;
            console.log("newPrice1: "+newPrice);
        } else if (Number(test.target.defaultValue) >= Number(test.target.value)){
            console.log(test.target.defaultValue, " ", test.target.value);
            newPrice = test.target.value*oriPrice;
            document.getElementById("totalFor"+test.originalTarget.id).textContent = "$"+newPrice;
            console.log("newPrice2: "+newPrice);
        }

        if(prevPrice < newPrice){
            let newTotal = Number(totalPrice)+Number(newPrice)-Number(prevPrice);
            document.getElementById("totalPrice").textContent = "$"+newTotal;
        } else if (prevPrice > newPrice) {
            let newTotal = Number(totalPrice)-(Number(prevPrice)-Number(newPrice));
            document.getElementById("totalPrice").textContent = "$"+newTotal;
        }
    } else {
        
    }
}


// Change amount of item in cart
function amountInCart(test){
    console.log(test);
    if((test.keyCode < 106 && test.keyCode > 95) || (test.keyCode < 58 && test.keyCode > 47) || test.keyCode == 8 || (test.keyCode == 90 && test.ctrlKey)){
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                // Typical action to be performed when the document is ready:
                console.log(xhttp.responseText);
            }
        }
        console.log("keyup in", test.originalTarget.id, test.target.valueAsNumber);

        let newAmount = null;

        test.target.value = test.target.value < 0 ? 0 : test.target.value;

        test.target.value = test.target.value > 10 ? 10 : test.target.value;

        if(!isNaN(test.target.valueAsNumber)){
            newAmount = test.target.valueAsNumber;
        } else {
            newAmount = 0;
        }

        xhttp.open("GET", "updateCart.php?asset="+test.originalTarget.id+"&num="+newAmount, true);
        xhttp.send();

        let price = document.getElementById("priceFor"+test.originalTarget.id).textContent;
        price = price.replace('$', '');
        let newPrice = price*newAmount;
        let prevPrice = document.getElementById("totalFor"+test.originalTarget.id).textContent;
        prevPrice = prevPrice.replace('$', '');
        document.getElementById("totalFor"+test.originalTarget.id).textContent = "$"+newPrice;

        let totalPrice = document.getElementById("totalPrice").textContent;
        totalPrice = totalPrice.replace('$', '');
        console.log("newPrice: "+newPrice+" prevPrice: "+prevPrice);

        if(prevPrice < newPrice){
            let newTotal = Number(totalPrice)+Number(newPrice)-Number(prevPrice);
            document.getElementById("totalPrice").textContent = "$"+newTotal;
        } else if (prevPrice > newPrice) {
            console.log("oldTotal: "+totalPrice);
            let newTotal = Number(totalPrice)-(Number(prevPrice)-Number(newPrice));
            console.log("newTotal: "+newTotal);
            document.getElementById("totalPrice").textContent = "$"+newTotal;
        }
    } else {
        
    }
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

const AMOUNT = document.getElementsByClassName("amountIn");

for (var i = 0; i < AMOUNT.length; i++) {
    const VALUE = AMOUNT[i].value;
    AMOUNT[i].addEventListener('keyup', amountInCart, VALUE[i]);
}

const AMOUNTORDER = document.getElementsByClassName("amountInOrder");
const ORIGINALAMOUNT = AMOUNTORDER;

for (var i = 0; i < AMOUNTORDER.length; i++) {
    const VALUEORDER = AMOUNTORDER[i].value;
    console.log(VALUEORDER[i]);
    AMOUNTORDER[i].addEventListener('keyup', amountInOrder, VALUEORDER[i]);
}

// Slideshow

let slideIndex = 1;

function changeSlides(n) {
    showSlides(slideIndex += n);
}

function currentSlide(n) {
    showSlides(slideIndex = n);
}

function showSlides(n) {
    let i;
    let slides = document.getElementsByClassName("item-photo");
    if (n > slides.length) {slideIndex = 1}    
    if (n < 1) {slideIndex = slides.length}
    console.log(slideIndex);
    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";  
    }
    slides[slideIndex-1].style.display = "flex";  
}

titleLength();
descriptionLength();
TITLE.addEventListener("keyup", titleLength);
DESCRIPTION.addEventListener("keyup", descriptionLength);