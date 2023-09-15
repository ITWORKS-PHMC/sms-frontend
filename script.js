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
//Alert Messages
// function showAlert() {
//   var myAlert = document.getElementById("myAlert");
//   move();

//   myAlert.className = "show";

//   setTimeout(function () {
//     hideAlert();
//   }, 5000);
// }
// function hideAlert() {
//   myAlert.className = myAlert.className.replace("show", "");
// }
// function move() {
//   let i = 0;
//   if (i == 0) {
//     var elem = document.getElementById("myAlertBar");
//     var width = 1;
//     var interval = setInterval(frame, 30);
//     function frame() {
//       if (width >= 100) {
//         clearInterval();
//         interval = 0;
//       } else {
//         width++;
//         elem.style.width = width + "vw";
//       }
//     }
//   }
// }

//Page and characters count
function countCharactersAndPages() {
  const text = document.getElementById("message").value;
  document.getElementById("charCount").textContent = text.length;
  document.getElementById("pageCount").textContent = Math.ceil(
    text.length / wordPerPageLimit
  );
}

//Limit input for the unknown number != 10, disabled the add recipient btn
function validateInput() {
  const inputElement = document.getElementById("recipientInput");
  const buttonElement = document.getElementById("submitButton");

  // Remove non-numeric characters
  const numericInput = inputElement.value.replace(/[^0-9]/g, "");
  // Update the input value with numeric characters only
  inputElement.value = numericInput;

  if (numericInput !== "" && numericInput.length === 10) {
    buttonElement.disabled = false;
  } else {
    buttonElement.disabled = true;
  }
}

// CONTACTS
//select all items in the checkbox
function checkAll(myCheckbox) {
  let checkboxes = document.querySelectorAll("input[type='checkbox']");
  if (myCheckbox.checked == true) {
    checkboxes.forEach(function (checkbox) {
      checkbox.checked = true;
    });
  } else {
    checkboxes.forEach(function (checkbox) {
      checkbox.checked = false;
    });
  }
}

// INBOX
//Pop up message
function showPopup(id) {
  document.getElementById("replyButton").value = id;
  const row = document.getElementById(`msg-${id}`);
  console.log(row);
  const cells = document.querySelectorAll(`#msg-${id} > td`);

  const sender = cells[0].textContent;
  const message = cells[1].textContent;
  const status = cells[2].textContent;
  const receiveDate = cells[3].textContent;

  const popup = document.getElementById("popup");

  const contentSender = document.getElementById("sender");
  const contentMessage = document.getElementById("message");
  const contentStatus = document.getElementById("readStatus");
  const contentDate = document.getElementById("date");

  contentSender.textContent = "Sender: " + sender;
  contentMessage.textContent = "Message: " + message;
  contentStatus.textContent = "Read Status: " + status;
  contentDate.textContent = "Date: " + receiveDate;

  /* Send the data using post with element id name and name */
  if (status == 0) {
    let update = $.post("smsInboxUpdate.php", {
      id: id,
    });

    /* Alerts the results */
    update.done(function (response) {
      console.log("RESPONSE", response);
      if (response === "Record updated successfully") {
        row.classList.remove("highlight");
        document.querySelector(`#msg-${id} > .read_status`).textContent = 1;
        document.getElementById("counterInbox").textContent =
          Number(document.getElementById("counterInbox").textContent) - 1;
        popup.style.display = "flex";
      }
    });
    
    update.fail(function () {
      console.log("Failed");
    });
  }

  popup.style.display = "flex";
}

function closePopup() {
  const popup = document.getElementById("popup");
  popup.style.display = "none";
}

// Initial character and page count
countCharactersAndPages();

// Function to check if the recipient table is empty and enable/disable the send button accordingly
// function checkRecipientTable() {
// const table = document.getElementById('');
//     const sendButton = document.getElementById('submit-msg');
//     if (table.rows.length > 0) {
//         sendButton.disabled = false;
//     } else {
//         sendButton.disabled = true;
//     }
// }
