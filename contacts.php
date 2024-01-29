<?php
session_start();
// Redirect to the login page if not login 
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];
$selectedCallerCode = $_SESSION['selectedCallerCode'];

// Database connection 
include("./database/connection.php");
$conn = sqlsrv_connect($serverName, $connectionInfo);
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

if (!isset($_SESSION["recipients"])) {
    $_SESSION["recipients"] = array(
        "contacts" => [],
        "groups" => []
    );
} 
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>SMS</title>
    <link rel="sms icon" type="x-icon" href="img\logo.png">
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include("./nav/navbar.php"); ?>

    <div class="content">
        <aside class="sidebar">
            <ul id="selectedContacts">
                <div class="addRecipient">
                    <li>
                        <button class="addRecipientButton" onClick="addRecipient()">Add to Recipient</button>
                    </li>
                </div>
                <h3 class="addRecipientTitle">Selected Recipients</h3>
                <div class="scrollable-content">
                    <ul id="selectedContactsList"></ul>
                </div>
            </ul>
        </aside>

        <div class="container contacts">
            <div class="contactsTabs">
                <button class="tablink" onclick="openPage('Contact', this, '#3a5a40')"
                    id="defaultOpen">Contacts</button>
                <button class="tablink" onclick="openPage('CallerGroup', this, '#3a5a40')"> Caller Group </button>
            </div>

            <div id="Contact" class="tabcontent">
                <h1> Contacts </h1>
                <table id="recipientTable" class="recipient-table">
                    <form class="" action="" method="post">
                        <tr>
                            <td>
                                <span>Select</span><br>
                                <input type="checkbox" class="select_all_items" id="option-all"
                                    onclick="checkAll(this, 'selectedContacts')" onchange="toggleSelect()">
                            </td>
                            <td>Contact ID</td>
                            <td>Full Name</td>
                            <td>Phone Number</td>
                        </tr>

                        <?php
                        $tsql = "SELECT * from contacts";
                        $stmt = sqlsrv_query($conn, $tsql);

                        if ($stmt == false) {
                            echo 'ERROR';
                        }

                        while ($obj = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                            $mobileNo = $obj['mobile_no'];
                            $contact = "{$obj['contact_id']}~{$obj['contact_fname']} {$obj['contact_lname']}~{$obj['mobile_no']}";
                            if ($obj['active'] == 1) {
                                echo "<tr>";

                                if (in_array($obj['mobile_no'], array_keys($_SESSION["recipients"]["contacts"]))) {
                                    echo "<td> <input type='checkbox' name='selectedContacts' value='$contact' onchange='toggleSelect(this)' checked> </td>";
                                } else {
                                    echo "<td> <input type='checkbox' name='selectedContacts' value='$contact' onchange='toggleSelect(this)'> </td>";
                                }
                                echo "<td> {$obj['contact_id']} </td>";
                                echo "<td>{$obj['contact_lname']} {$obj['contact_fname']} {$obj['contact_mname']}</td>";

                                $maskedMobileNo = substr($mobileNo, 0, 6) . "*****" . substr($mobileNo, -2);
                                echo "<td>{$maskedMobileNo}</td>";
                                echo "</tr>";
                            }
                        }
                        sqlsrv_free_stmt($stmt);
                        ?>
                    </form>
                </table>
            </div>

            <div id="CallerGroup" class="tabcontent">
                <h1>Caller Group</h1>
                <p>*Double click the Caller Group Code Cell to view the members</p>
                <form>
                    <div class="table-containers">
                        <table class="recipient-table">
                            <thead>
                                <tr>
                                    <td>
                                        <span>Select</span><br>
                                        <input type="checkbox" class="select_all_itemsGroup"
                                            onclick="checkAll(this, 'selectedCaller')">
                                    </td>
                                    <td>Caller Group Code</td>
                                    <td>Caller Group Name</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $tsql = "SELECT * FROM caller_group;";
                                $stmt = sqlsrv_query($conn, $tsql);
                                if ($stmt == false) {
                                    echo 'ERROR';
                                }

                                while ($obj = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                    $group_code = $obj['caller_group_code'];
                                    echo "<tr>";
                                    if (in_array($group_code, $_SESSION["recipients"]["groups"])) {
                                        echo "<td><input type='checkbox' name='selectedCaller' value='$group_code' onclick='selectCallerGroup(this)' checked /></td>";
                                    } else {
                                        echo "<td><input type='checkbox' name='selectedCaller' value='$group_code' onclick='selectCallerGroup(this)' /></td>";
                                    }
                                    echo "<td class='dbl-click' ondblclick='showMembers(this)'>$group_code</td>";
                                    echo "<td> {$obj['caller_group_name']} </td>";
                                    echo "</tr>";
                                }
                                sqlsrv_free_stmt($stmt);
                                ?>
                            </tbody>
                        </table>

                        <table id="callerGroupMembersTable" style="display: none;">
                            <thead>
                                <tr>
                                    <td>Caller Member Code</td>
                                    <td>Caller Member Name</td>
                                    <td>Full Name</td>
                                    <td>Mobile Number</td>
                                </tr>
                            </thead>
                            <tbody id="callerGroupMembers">
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        const recipients = JSON.stringify(<?php echo json_encode($_SESSION["recipients"]); ?>);

        const parsedRecipients = JSON.parse(recipients)
        console.log(parsedRecipients)
        let selectedContacts = {}
        let selectedRecipients = {
            contacts: {},
            groups: {}
        }

        for (const [key, value] of Object.entries(parsedRecipients.contacts)) {
            selectedRecipients.contacts[value["id"]] = `${value["id"]}~${value["name"]}~${key}`
        }

        parsedRecipients.groups.forEach(group => {
            selectedRecipients.groups[group] = {}
        });

        console.log('HERE', selectedRecipients)

        displaySelectedContacts()

        function showMembers(element) {
            let callerGroupCode = element.innerHTML;
            console.log(callerGroupCode);
            document.getElementById("callerGroupMembersTable").style.display = "none";

            let xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    let membersData = JSON.parse(xhr.responseText);

                    if (membersData.length > 0) {
                        let membersTable = document.getElementById("callerGroupMembersTable");
                        let membersBody = document.getElementById("callerGroupMembers");
                        membersBody.innerHTML = "";

                        membersData.forEach(function (member) {
                            let row = membersBody.insertRow();
                            console.log(row);
                            let CallerCode = row.insertCell(0);
                            let CallerName = row.insertCell(1);
                            let FullName = row.insertCell(2);
                            let MobileNumber = row.insertCell(3);

                            CallerCode.innerHTML = member.caller_group_code;
                            CallerName.innerHTML = member.caller_group_name;
                            FullName.innerHTML = member.full_name;
                            MobileNumber.innerHTML = member.mobile_no;
                        });

                        membersTable.style.display = "block";
                    }
                }
            };

            xhr.open("POST", "contactsMembers.php?caller_group_code=" + callerGroupCode, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send("caller_group_code=" + callerGroupCode);
        }

        function selectCallerGroup(element) {
            if (element.checked === false) {
                console.log("UNCHECKED")

                console.log(Object.keys(selectedRecipients.groups), element.value)
                delete selectedRecipients.groups[element.value]
                console.log(selectedRecipients)
                displaySelectedContacts()
                return
            }

            console.log("CHECKED")
            let callerGroupCode = element.value
            let xhr = new XMLHttpRequest();
            let selectedContactsList = document.getElementById('selectedContactsList');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    let membersData = JSON.parse(xhr.responseText);
                    selectedRecipients.groups[callerGroupCode] = membersData

                    displaySelectedContacts()
                }
            };

            console.log(selectedRecipients)

            xhr.open("POST", "contactsMembers.php?caller_group_code=" + callerGroupCode, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send("caller_group_code=" + callerGroupCode);
        }

        function displaySelectedContacts() {
            console.log(selectedContacts)
            let selectedContactsList = document.getElementById('selectedContactsList');

            // Clear existing contacts
            selectedContactsList.innerHTML = '';

            // Display selected contacts in the dictionary
            for (const [key, value] of Object.entries(selectedRecipients.contacts)) {
                // console.log(key, value);
                let listItem = document.createElement('li');
                console.log(value)
                let contactInfoArr = value.split('~')
                console.log(contactInfoArr)

                listItem.textContent = `${contactInfoArr[1]} - ${contactInfoArr[2].slice(0, 6) + "*****" + contactInfoArr[2].slice(-2)}`;
                selectedContactsList.appendChild(listItem);
            }

            for (const [key, value] of Object.entries(selectedRecipients.groups)) {
                // console.log(key, value);
                console.log("GROUP", value)
                let listItem = document.createElement('li');
                listItem.textContent = key;
                selectedContactsList.appendChild(listItem);
            }
        }

        function toggleSelect(element) {
            console.log(selectedRecipients)
            let checkboxes = document.getElementsByName('selectedContacts');
            selectedRecipients.contacts = {}
            // selectedContacts = {}

            // Loop through checkboxes to find the selected ones
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].checked) {

                    selectedRecipients.contacts[checkboxes[i].value.split("~")[0]] = checkboxes[i].value
                }
            }
            displaySelectedContacts();
        }

        function addRecipient() {
            let contact_string = "";
            $("input:checkbox:checked").each(function () {
                contact_string += $(this).val() + ","
            });

            contact_string = contact_string.replace(/,+$/, '');

            href_string = `http://localhost/sms-frontend/sms.php`
            // href_string = `http://phmc-sms/sms-frontend/sms.php` // server phmc-sms01
            if (contact_string !== '') {
                href_string += `?to=${contact_string}`
            }

            window.location.href = href_string;
        }

        // Function for contacts tab
        function openPage(pageName, elmnt, color) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablink");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].style.backgroundColor = "";
            }
            document.getElementById(pageName).style.display = "block";
            elmnt.style.backgroundColor = color;
        }

        // Get the element with id="defaultOpen" and click on it
        document.getElementById("defaultOpen").click();
    </script>
</body>

</html>