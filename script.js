const wordLimit = 560;
const wordPerPageLimit = 140;
const pageLimit = 4;
let myText = document.getElementById("message");
let result = document.getElementById("result");

myText.maxLength = wordLimit;
document.getElementById("charLimit").textContent = wordLimit;
document.getElementById("pageCountLimit").textContent = pageLimit;

// LOGIN & AUTHENTICATION
//Show Password
function showPassword() {
    let x = document.getElementById("password");
    if (x.type === "password") {
      x.type = "text";
    } else {
      x.type = "password";
    }
}

// SMS
// Alert Messages
function showAlert(){
    var myAlert = document.getElementById("myAlert");
    move();

    myAlert.className = "show";

    setTimeout(function(){hideAlert(); }, 5000);
}

function hideAlert(){
    myAlert.className = myAlert.className.replace("show", "");
}

function move() {
    let i = 0;  
    if (i == 0) {
        var elem = document.getElementById("myAlertBar");
        var width = 1;
        var interval = setInterval(frame, 30);
        function frame() {
        if (width >= 100) {
            clearInterval(id);
            interval = 0;
        } else {
            width++;
            elem.style.width = width + "vw";
        }
        }
    }
}

//Page and characters count
function countCharactersAndPages() {
    const text = document.getElementById("message").value;
    document.getElementById("charCount").textContent = text.length;
    document.getElementById("pageCount").textContent = Math.ceil(text.length / wordPerPageLimit);
}

// CONTACTS
//select all items in the checkbox 
function checkAll(myCheckbox){
    let checkboxes = document.querySelectorAll("input[type='checkbox']");
    if(myCheckbox.checked == true){
        checkboxes.forEach(function(checkbox){
            checkbox.checked = true;
        });
    }
    else{
        checkboxes.forEach(function(checkbox){
            checkbox.checked = false;
        });
    }
}

// Initial character and page count
countCharactersAndPages();

// // Function to check if the recipient table is empty and enable/disable the send button accordingly
// function checkRecipientTable() {
//     // const table = document.getElementById('');
//     const sendButton = document.getElementById('submit-msg');

//     if (table.rows.length > 0) {
//         sendButton.disabled = false;
//     } else {
//         sendButton.disabled = true;
//     }
// }