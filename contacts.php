<?php
//database connection 
include("./database/connection.php");

$conn = sqlsrv_connect($serverName, $connectionInfo);
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Contacts</title>
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap" rel="stylesheet">

    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

    <link rel="stylesheet" href="style.css">
</head>

<body>
    <ul class="navigation">
        <li class="navlistleft">PHMC SMS</li>
        <li class="navlist"><a href="logout.php">LOGOUT</a></li>
        <li class="navlist"><a href="contacts.php">CONTACTS</a></li>
        <li class="navlist"><a href="sms.php">SMS</a></li>
        <li class="navlist"><a class="active" href="home.php">HOME</a></li>
    </ul>

    <ul class="menu contacts" id="selectedContacts">
        <li class="menulist">
            <button type="button" class="select-button" onclick="getSelectedContacts()">View Selected Contacts</button>
        </li>
        <li class="menulist">
            <button type="button" class="add-button" onClick="addRecipient()">Add to Recipient</button>
        </li>

        <h3>Selected Contacts</h3>
        <ul id="selectedContactsList"></ul>
    </ul>


    <div class="container contacts">
        <button class="tablink" onclick="openPage('Contact', this, '#eff3f4')" id="defaultOpen">Contacts</button>
        <button class="tablink" onclick="openPage('CallerGroup', this, '#eff3f4')"> Caller Group </button>

        <div id="Contact" class="tabcontent">
            <h1> Contacts </h1>
            <table id="recipientTable" class="recipient-table">
                <form class="" action="" method="post">
                    <tr>
                        <td> Select <br> <input type="checkbox" class="select_all_items" id="option-all"
                                onclick="checkAll(this)"></td>
                        <td> Contact ID </td>
                        <td> Employee Number </td>
                        <td> Full Name </td>
                        <td> Phone Number </td>
                    </tr>

                    <?php
                    $tsql = "SELECT * from contacts";
                    $stmt = sqlsrv_query($conn, $tsql);

                    if ($stmt == false) {
                        echo 'ERROR';
                    }

                    while ($obj = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                        $contact = "{$obj['contact_id']}~{$obj['contact_fname']} {$obj['contact_lname']}~{$obj['mobile_no']}";
                        if ($obj['active'] == 1) {
                            echo "<tr>";
                            echo "<td> <input type='checkbox' name='selectedContacts' value='$contact'> </td>";
                            echo "<td> {$obj['contact_id']} </td>";
                            echo "<td> {$obj['employee_no']} </td>";
                            echo "<td>{$obj['contact_lname']} {$obj['contact_fname']} {$obj['contact_mname']}</td>";
                            echo "<td> {$obj['mobile_no']} </td>";
                            echo "</tr>";
                        }
                    }
                    sqlsrv_free_stmt($stmt);
                    ?>
                </form>
            </table>
        </div>

        <div id="CallerGroup" class="tabcontent">
            <h1> Caller Group </h1>
            <p> *Double click the Caller Group Code Cell to view the members </p>
            <form>
                <div class="table-containers">
                    <table class="recipient-table">
                        <thead>
                            <tr>
                                <td> Select <br> <input type="checkbox" class="select_all_items" id="option-all"
                                        onclick="checkAll(this)"></td>
                                <td> Caller Group Code </td>
                                <td> Caller Group Name </td>
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
                                echo "<tr>";
                                echo "<td> <input type='checkbox' name='selectedCaller' value=''> </td>";
                                // echo "<td class='dbl-click' ondblclick='getCallerGroupCode(this)'>" . $obj['caller_group_code'] . "</td>";
                                echo "<td class='dbl-click' ondblclick='showMembers(this)'>" . $obj['caller_group_code'] . "</td>";
                                echo "<td> {$obj['caller_group_name']} </td>";
                                echo "</tr>";
                            }
                            sqlsrv_free_stmt($stmt);
                            ?>
                        </tbody>
                    </table>

                    <!-- <table id="callerGroupMembersTable" style="display: none;" class="recipient-table"> -->
                    <table id="callerGroupMembersTable" style="display: none;">
                        <thead>
                            <tr>
                                <td> Caller Member Code </td>
                                <td> Caller Member Name </td>
                                <td> Full Name </td>
                                <td> Mobile Number </td>
                            </tr>
                        </thead>
                        <tbody id="callerGroupMembers">
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
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

        function getSelectedContacts() {
            var checkboxes = document.getElementsByName('selectedContacts');
            var selectedContacts = [];

            // Loop through checkboxes to find the selected ones
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].checked) {
                    selectedContacts.push(checkboxes[i].value);
                }
            }

            // Display selected contacts in a list
            displaySelectedContacts(selectedContacts);
        }

        function displaySelectedContacts(selectedContacts) {
            var selectedContactsList = document.getElementById('selectedContactsList');

            // Clear existing contacts
            selectedContactsList.innerHTML = '';

            // Display selected contacts in the list
            selectedContacts.forEach(function (contact) {
                var listItem = document.createElement('li');
                listItem.textContent = contact;
                selectedContactsList.appendChild(listItem);
            });
        }

        function addRecipient() {
            let contact_string = "";
            $("input:checkbox[name=selectedContacts]:checked").each(function () {
                contact_string += $(this).val() + ","
            });

            contact_string = contact_string.replace(/,+$/, '');
            window.location.href = `http://localhost/sms-frontend/sms.php?to=${contact_string}`;
        }

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