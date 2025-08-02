<?php
$user = "phpuser";
$password = "PW=";
$database = "DBNAME";
$table_banned_ips = "banned_ips";
$raw_clientIP = $_SERVER['REMOTE_ADDR'];
$clientIP = preg_replace('/[^0-9.:]/', '', $raw_clientIP);

try {
    $db = new PDO("mysql:host=localhost;dbname=$database", $user, $password);
    $banned_ips = $db->query("SELECT clientIP FROM $table_banned_ips")->fetchAll(PDO::FETCH_COLUMN);
    $db = null; # close connection
} 
catch (PDOException $e) {
    echo "Error!: " . $e->getMessage() . "<br/>";
    die();
}

if (in_array($clientIP, $banned_ips)) {
    header('Location: rating.html');
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="shortcut icon" href="#">
<title>Study Welcome</title>
</head>

<body onload = load_new_content()>
<table style="text-align: left; width: 1180px; height: 450px;">
    <tbody>
    <tr> 
        <td style="height: 50px; width: 50px;"></td>
        <td style="height: 50px; width: 530px;"></td>
        <td style="height: 50px; width: 10px;"></td>
        <td style="height: 50px; width: 530px;"></td>
        <td style="height: 50px; width: 50px;"></td>
    </tr>
    <tr>
        <td style="vertical-align: top; text-align: center; height: 500px; width: 50px;" id="counter_cell"></td>
        <td style="vertical-align: middle; text-align: left; height: 500px; width: 530px; font-size: 20px;" id="sequence_cell_left">
        Please enable JavaScript and reload the page.
        </td>
        <td style="height: 50px; width: 10px;"></td>
        <td style="vertical-align: middle; text-align: left; height: 500px; width: 530px; font-size: 20px;" id="sequence_cell_right">
        </td>
        <td style="height: 500px; width: 50px;"></td>
    </tr>
    <tr>
        <td style="height: 75px; width: 50px;"></td>
        <td style="vertical-align: middle; text-align: center; height: 75px; width: 530px;">
        <img onclick="load_new_content()" style="width: 35px; height: 35px;" alt="button1" src="button1.jpg" id="button1">
        </td>
        <td style="height: 50px; width: 10px;"></td>
        <td style="vertical-align: middle; text-align: center; height: 75px; width: 530px;">
        <img onclick="load_new_content()" style="width: 35px; height: 35px;" alt="button7" src="button7.jpg" id="button7">
        </td>
        <td style="height: 75px; width: 50px;"></td>
    </tr>
    <tr>
        <td style="height: 75px; width: 50px;"></td>
        <td style="vertical-align: top; text-align: center; height: 75px; width: 530px;" id="scale_legend_left"></td>
        <td style="height: 50px; width: 10px;"></td>
        <td style="vertical-align: top; text-align: center; height: 75px; width: 530px;" id="scale_legend_right"></td>
        <td style="height: 75px; width: 50px;"></td>
    </tr>
    </tbody>
</table>

<script>
const userID = (Math.random().toString(36).substr(2, 12) + Math.random().toString(36).substr(2, 12)).substr(2, 12);
while (userID.length != 12) {
    const userID = (Math.random().toString(36).substr(2, 12) + Math.random().toString(36).substr(2, 12)).substr(2, 12);
}
var step_counter = 0;

const beginning = `Welcome, and thank you for your interest!<br><br>
    You will receive your payment code/link at the end of the questionnaire.<br><br>
    Refrain from using your browser's back and refresh button: It will void your progress.<br><br>
    Click the button to begin.<br><br><br>`
const consent = `<div style="text-align: left;">Please read the following information carefully and make a copy if needed. </div><br><br>
    <span style="font-weight: bold;">CONSENT TO RESEARCH</span><br><br>
    You are about to take part in a study whose goal it is to investigate language preferences. Your task will be to express a preference when presented with two choices.<br><br>
    Your participation is your free, rescindable choice. You will not be exposed to any known risks or uncertainties, there are not any known benefits, either. You can leave this study at any time, without specifying reasons.<br>
    Note: We do remove bad actors during the study and exclude their ratings from our analyses. For this, we collect the IP addresses -only of bad actors- and store these for about 24 hours.<br><br>
    We collect basic demographic information (age, gender, region, English proficiency) but your participation is anonymous and published data does not contain any identifiable information. Anonymised data will be published on Github.<br>
    Estimated time for completion is about 15 minutes. Payment will be delivered through the recruitment platform. The exact payment amount will be based on the rate previously agreed upon via the recruitment platform.<br><br>
    If you any questions or concerns, contact the Principal Investigator: <br>
    Dr. J. Doe<br>
    <span style="font-style: italic;">I have read and considered the presented information. I confirm that I understand the purpose of the research. I understand that I may contact the Principal Investigator at any time and can withdraw my participation without prejudice.<br><br> 
    By clicking the right-arrow button, I indicate my willingness to participate in this study.</span><br>`
const intro_1 = "In the following, you will read a series of research summaries, with two alternatives next to each other. Please express which alternative you overall prefer.<br><br>Some of the items are hard, do the best you can!"
const intro_2 = `Acellular matrix (ACM) has been widely used as a biomaterial. As the main component of ACM, collagen type and content show...<br><br><div style="font-size: 14px; font-style: italic;">Press any button to continue.</div>`
const intro_3 = `<div style="font-size: 20px;">The study is about to begin. Press the button to start whenever you are ready.</div>`

const step_content = [beginning, consent, intro_1, intro_2, intro_3, "Off you go!"];
document.getElementById("button1").style.visibility = "hidden";


function load_new_content() {
    if (step_counter < 6) {
        document.getElementById("sequence_cell_left").innerHTML = step_content[step_counter];
        additional_markup();
    } else {
        document.getElementById("button1").style.visibility = "hidden";
        document.getElementById("button7").style.visibility = "hidden";
        document.getElementById("sequence_cell_left").innerHTML = `Thank you, please return to <a href="https://app.prolific.com/submissions/complete?cc=CODE">https://app.prolific.com/submissions/complete?cc=CODE</a> or use the following completion code directly: CODE .`;
        document.getElementById("sequence_cell_right").innerHTML = "";
        document.getElementById("counter_cell").innerHTML = "~";
        document.getElementById("scale_legend_left").innerHTML = "";
        document.getElementById("scale_legend_right").innerHTML = "";
        window.location = "rating.html";
    }
    step_counter += 1;
}

function additional_markup() {
    if (step_counter == 1) {
        document.getElementById("sequence_cell_left").style.fontSize = "16px";
    } else if (step_counter == 2) {
        document.getElementById("sequence_cell_left").style.fontSize = "20px";
    } else if (step_counter == 3) {
        document.getElementById("button1").style.visibility = "visible";
        document.getElementById("button7").style.visibility = "visible";
        document.getElementById("sequence_cell_right").innerHTML = `ACM has been widely used as a biomaterial and the main component of ACM is the collagen type ...<br><br><div style="font-size: 14px; font-style: italic;">Press any button to continue.</div>`;
        document.getElementById("scale_legend_left").innerHTML = "left is better";
        document.getElementById("scale_legend_right").innerHTML = "right is better";
    } else if (step_counter == 4) {
        document.getElementById("sequence_cell_left").style.fontSize = "18px";
        document.getElementById("sequence_cell_right").innerHTML = "";
        document.getElementById("button1").style.visibility = "hidden";
        document.getElementById("scale_legend_left").innerHTML = "";
        document.getElementById("scale_legend_right").innerHTML = "";
    } else if (step_counter == 5) {
        document.getElementById("scale_legend_left").innerHTML = "";
        document.getElementById("scale_legend_right").innerHTML = "";
        document.getElementById("button1").style.visibility = "hidden";
        window.location = "rating.php?userID=" + userID;
    }
}
</script>
</body>
</html>