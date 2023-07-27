var checkboxes = document.querySelectorAll("input[type = 'checkbox']");
var myText = document.getElementById("message");
var result = document.getElementById("result");
var limit = 140;

//In
function showAlert(){
    var myAlert = document.getElementById("myAlert");
    move();

    myAlert.className = "show";

    setTimeout(function(){hideAlert(); }, 5000);
}

function hideAlert(){
    myAlert.className = myAlert.className.replace("show", "");
}
  
var i = 0;
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
            elem.style.width = width + "%";
        }
        }
    }
}

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

//count characters
result.textContent = 0 + "/" + limit;
let x = []
myText.addEventListener("input",function(){
    var textLength = myText.value.length;
    result.textContent = textLength + "/" + limit;

    if(textLength > limit){
        myText.style.borderColor = "#ff2851";
        result.style.color = "#ff2851";
    }
    else{
        myText.style.borderColor = "#b2b2b2";
        result.style.color = "#737373";
    }
});

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







