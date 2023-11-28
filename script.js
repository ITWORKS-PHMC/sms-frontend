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
function checkAll(myCheckbox, name="") {
  let checkboxes = document.querySelectorAll(`input[type='checkbox'][name='${name}']`);
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

// Initial character and page count
countCharactersAndPages();

