<?php
$user = "phpuser";
$password = "PW=";
$database = "dbname";
$table_banned_ips = "banned_ips";
$table_demographics = "demographics";
$table_ratings = "ratings";
$raw_clientIP = $_SERVER['REMOTE_ADDR'];
$clientIP = preg_replace('/[^0-9.:]/', '', $raw_clientIP);
$required_no_ratings = 17;


try {
    $raw_userID = $_GET['userID'];
    $userID = preg_replace('/[^0-9a-z]/', '', $raw_userID);
    $db = new PDO("mysql:host=localhost;dbname=$database", $user, $password);
    $banned_ips = $db->query("SELECT clientIP FROM $table_banned_ips")->fetchAll(PDO::FETCH_COLUMN);
    $completed_userIDs = $db->query("SELECT userID FROM $table_demographics")->fetchAll(PDO::FETCH_COLUMN);
    $user_ratings = $db->query("SELECT COUNT(*) AS no_ratings_user_made FROM $table_ratings WHERE userID = '$userID'")->fetch(PDO::FETCH_ASSOC);
    $no_ratings_user_made = (int)$user_ratings['no_ratings_user_made'];
    $db = null; # close connection
} catch (PDOException $e) {
    echo "Error!: " . $e->getMessage() . "<br/>";
    die();
}

if (strlen($userID) != 12 || $no_ratings_user_made < $required_no_ratings  || in_array($userID, $completed_userIDs)) {
    header('Location: rating.html');
}


?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="shortcut icon" href="#">
<title>Almost done</title>
</head>

<body onload = load_new_content()>
<table style="text-align: left; width: 760px; height: 450px;">
    <tbody>
    <tr> 
        <td style="height: 50px; width: 50px;"></td>
        <td style="height: 50px; width: 660px; vertical-align: middle; text-align: center; font-size: 20px;" id="header_cell">Almost done!</td>
        <td style="height: 50px; width: 50px;"></td>
    </tr>
    <tr>
        <td style="vertical-align: top; text-align: center; height: 250px; width: 50px;" id="counter_cell"></td>
        <td style="vertical-align: middle; text-align: center; height: 250px; width: 660px; font-size: 18px;" id="sequence_cell">
            
        </td>
        <td style="text-align: center; height: 250px; width: 50px;"></td>
    </tr>
    <tr>
        <td style="height: 75px; width: 50px;"></td>
        <td style="vertical-align: middle; text-align: center; height: 75px; width: 660px;">
        <img onclick="submit()" style="width: 35px; height: 35px;" alt="button7" src="button7.jpg" id="button7">
        </td>
        <td style="height: 75px; width: 50px;"></td>
    </tr>
    <tr>
        <td style="height: 75px; width: 50px;"></td>
        <td style="vertical-align: top; text-align: center; height: 75px; width: 660px;" id="scale_legend">
        </td>
        <td style="height: 75px; width: 50px;"></td>
    </tr>
    </tbody>
</table>

<script>
var userID = "<?php echo $userID; ?>";
const amtCode = (Math.random().toString(36).substr(2, 12) + Math.random().toString(36).substr(2, 12)).substr(2, 12);

var step_counter = 0;
const basic_info = `Please provide the following information.<br><br>
    <div style="text-align: left;">
    &emsp;&emsp;&emsp;Please be honest, you will receive payment either way.<br>
    &emsp;&emsp;&emsp;True answers will help us to better understand the results.<br><br>

    &emsp;&emsp;&emsp;<input id="age" maxlength="2" size="3" name="age">&nbsp;Age<br><br>
    &emsp;&emsp;&emsp;What language(s) did you speak during your childhood?<br>
    &emsp;&emsp;&emsp;<input id="firstlanguages" maxlength="64" size="32" name="firstlanguages"><br><br>

    &emsp;&emsp;&emsp;How difficult did you find the language used in the items?<br>
    &emsp;&emsp;&emsp;<input name="lang" value="h" type="radio">&nbsp;entirely understandable<br>
    &emsp;&emsp;&emsp;<input name="lang" value="m" type="radio">&nbsp;moderately clear, sometimes challenging<br>
    &emsp;&emsp;&emsp;<input name="lang" value="l" type="radio">&nbsp;difficult to comprehend<br><br>

    &emsp;&emsp;&emsp;Gender<br>
    &emsp;&emsp;&emsp;<input name="gender" value="f" type="radio">&nbsp;female<br>
    &emsp;&emsp;&emsp;<input name="gender" value="m" type="radio">&nbsp;male<br>
    &emsp;&emsp;&emsp;<input name="gender" value="d" type="radio">&nbsp;diverse:&nbsp;<input id="gender_specified" maxlength="20" size="10" name="Other">&nbsp;(optional)<br><br>
    
    </div>
    <span style="font-style: italic;">Press the button to proceed.</span>`;

const thank_you = `This is it, thank you very much for your participation.<br><br>
    Please return to <a href="https://app.prolific.com/submissions/complete?cc=CODE">https://app.prolific.com/submissions/complete?cc=CODE</a> , or directly enter the following approval code: CODE .<br><br>`; //  ` + amtCode + `
const step_content = [basic_info, thank_you];
var age = "", gender = "", lang = "", firstlanguages = "";
//, recruitmentID = "";


function submit() {
    if (step_counter == 0) {
        var gender_buttons = document.getElementsByName("gender");
        var lang_buttons = document.getElementsByName("lang");
        age = document.getElementById("age").value;
        gender = get_radio_value(gender_buttons);
        gender = gender.replace(/[^a-z]/gi, '');
        lang = get_radio_value(lang_buttons);
        firstlanguages = document.getElementById("firstlanguages").value;
        firstlanguages = firstlanguages.replace(/[^a-z]/gi, '');
        if (age == "" || gender == "" || lang == "" || firstlanguages == "") {
            alert("Please fill out all fields.");
        } else {
            step_counter += 1;
            submit_demographics(); // only if no recruitmentID
            load_new_content();
        }
    } 
}

function load_new_content() {
    if (step_counter < 1) {
        document.getElementById("sequence_cell").innerHTML = step_content[step_counter];
    } else {
        document.getElementById("sequence_cell").innerHTML = step_content[1]; // only if no recruitmentID
        document.getElementById("header_cell").innerHTML = "Done!";
        document.getElementById("button7").style.visibility = "hidden";
    }
}

function get_radio_value(radio_buttons) {
    radio_value = "";
    for (let i = 0; i < radio_buttons.length; i++) {
        if (radio_buttons[i].checked) {
            radio_value = radio_buttons[i].value;
            return radio_value;
        }
    }
    return radio_value;
}

function submit_demographics() {
    var xhr = new XMLHttpRequest();
        //xhr.open("GET", "https://WEBSITE.COM/submit_demographics.php?userID=" + userID + "&age=" + age + "&gender=" + gender + "&lang=" + lang + "&recruitmentID=" + recruitmentID, true);
        xhr.open("GET", "https://WEBSITE.COM/submit_demographics.php?userID=" + userID + "&age=" + age + "&gender=" + gender + "&lang=" + lang + "&fones=" + firstlanguages + "&amtCode=" + amtCode, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    // console.log("Demographics submitted.");
                    //pass
                } else {
                    console.log("Demographics submission failed.")
                }
            }
            };
        xhr.send();
}

</script>

</body>
</html>