<?php
# database connection details
$user = "phpuser";
$password = "PW=";
$database = "DBNAME";
$table_ratings = "ratings";
$table_demographics = "demographics";
//$table_recruitment_codes = "recruitment_codes";
$required_no_ratings = 17;


# try to grab the user id from the url parameters
try {
    $raw_userID = $_GET['userID'];
    $userID = preg_replace('/[^0-9A-Za-z]/', '', $raw_userID);
    $age = (int)$_GET['age'];
    $raw_gender = $_GET['gender'];
    $gender = preg_replace('/[^A-Za-z]/', '', $raw_gender);
    $raw_lang = $_GET['lang'];
    $lang = preg_replace('/[^A-Za-z]/', '', $raw_lang);
    $raw_fones = $_GET['fones'];
    $fones = preg_replace('/[^A-Za-z]/', '', $raw_fones);
    $raw_amtCode = $_GET['amtCode'];
    $amtCode = preg_replace('/[^0-9A-Za-z]/', '', $raw_amtCode);
    //$raw_recruitmentID = $_GET['recruitmentID'];
    //$recruitmentID = str_replace(' ', '', $raw_recruitmentID);
    } 
catch (PDOException $e) {
    echo "Error!: " . $e->getMessage() . "<br/>";
    die();
}

try {
    # connect to the database
    $db = new PDO("mysql:host=localhost;dbname=$database", $user, $password);
    
    # get the number of ratings the user made
    $user_ratings = $db->query("SELECT COUNT(*) AS no_ratings_user_made FROM $table_ratings WHERE userID = '$userID'")->fetch(PDO::FETCH_ASSOC);
    $no_ratings_user_made = (int)$user_ratings['no_ratings_user_made'];

    # check if user has submitted a recruitmentID already
    //$no_userID_occur = $db->query("SELECT * FROM $table_recruitment_codes WHERE userID = '$userID'")->fetchAll(PDO::FETCH_COLUMN);
    //$matchFound = mysql_num_rows($recruitmentID_exists) > 0 ? 'yes' : 'no';
    
    //if (($no_ratings_user_made >= $required_no_ratings) && (count($no_userID_occur) == 0)) {
    if ($no_ratings_user_made >= $required_no_ratings) {
        # put demographics into the demographics table
        $put_demographics = $db->query("INSERT INTO $table_demographics (userID, age, gender, lang, fones, amtCode) VALUES('$userID', '$age', '$gender', '$lang', '$fones', '$amtCode');");
        if ($put_demographics->rowCount() == 0) {
            echo("Warning! Could not put demographics into database.<br/>");
        }

        # put recruitment code into the recruitment code table
        //$put_code = $db->query("INSERT INTO $table_recruitment_codes (recruitmentID, userID) VALUES('$recruitmentID', '$userID');");
        //if ($put_code->rowCount() == 0) {
        //    echo("Warning! Could not put recruitment code into database.<br/>");
        //}
    }
    
    # close the database connection
    $db = null;
} catch (PDOException $e) {
    echo "Error!: " . $e->getMessage() . "<br/>";
    die();
}