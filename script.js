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

// CONTACTS
//select all items in the checkbox 
function checkAll(myCheckbox){
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

// //count characters
// result.textContent = 0 + "/" + limit;
// let x = []

// myText.addEventListener("input",function(){
//     var textLength = myText.value.length;
//     result.textContent = textLength + "/" + limit;
//     if(textLength > limit){
        
//         myText.style.borderColor = "#ff2851";
//         result.style.color = "#ff2851";
//     }
//     else{
//         myText.style.borderColor = "#b2b2b2";
//         result.style.color = "#737373";
//     }
// });

function countCharactersAndPages() {
    const textArea = document.getElementById("message");
    const text = textArea.value;
    const limit = 140;
  
    // Update character count
    const charCountElement = document.getElementById("charCount");
    charCountElement.textContent = text.length;
  
    // Remove any existing pages
    const pageContainer = document.getElementById("pageContainer");
    pageContainer.innerHTML = "";
  
    // Split the text into pages
    const numPages = Math.ceil(text.length / limit);
    for (let i = 0; i < numPages; i++) {
      const start = i * limit;
      const pageText = text.slice(start, start + limit);
      const pageDiv = document.createElement("div");
      pageDiv.textContent = pageText;
      pageContainer.appendChild(pageDiv);
    }
  
    // Update page count
    const pageCountElement = document.getElementById("pageCount");
    pageCountElement.textContent = numPages;
  }
  
  // Initial character and page count
  countCharactersAndPages();
  

//count pages 

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