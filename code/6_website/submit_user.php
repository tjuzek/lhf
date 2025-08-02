<?php
# database connection details
$user = "phpuser";
$password = "PW=";
$database = "DBNAME";
$table_problematic_users = "problematic_users";
$table_banned_ips = "banned_ips";
//$raw_clientIP = $_SERVER['REMOTE_ADDR'];
//$clientIP = preg_replace('/[^0-9.:]/', '', $raw_clientIP);
//$clientIP = "0.0.0.0"; # no ip banning for now


# try to grab the user id from the url parameters
try {
    $raw_userID = $_GET['userID'];
    $userID = preg_replace('/[^0-9a-z]/', '', $raw_userID);
    $raw_problemCat = (int)$_GET['category']; // 0 = too fast; 1 = attention; 2 = language
    $problemCat = preg_replace('/[^0-9]/', '', $raw_problemCat);
    } 
catch (PDOException $e) {
    echo "Error!: " . $e->getMessage() . "<br/>";
    die();
    }
//if ($problemCat == 2) {
//    $clientIP = "0.0.0.0";
//    }


try { 
    # connect to the database
    $db = new PDO("mysql:host=localhost;dbname=$database", $user, $password);
    
    # put clientIP into the banned_ips table
    //$putip = $db->query("INSERT IGNORE INTO $table_banned_ips (clientIP) VALUES ('$clientIP');");
    //if ($putip->rowCount() == 0) {
    //    echo("Warning! Could not put i-info into database.<br/>");
    //}

    # put userID into the user table if there is one and it doesn't already exist with the same problemCat
    if ($userID != "") {
        // Prepare a statement for execution
        $stmt = $db->prepare("SELECT * FROM $table_problematic_users WHERE userID = ? AND problemCat = ?");
        $stmt->execute([$userID, $problemCat]);
        if ($stmt->rowCount() == 0) {
            // Prepare the insert statement
            $insertStmt = $db->prepare("INSERT INTO $table_problematic_users (userID, problemCat) VALUES (?, ?)");
            $insertSuccess = $insertStmt->execute([$userID, $problemCat]);
            if ($insertSuccess) {
                echo("User information successfully added to the database.<br/>");
            } else {
                echo("Warning! Could not put user information into the database.<br/>");
            }
        } else {
            echo("User with this problem category already exists in the database.<br/>");
        }
    }    
    
    # close the database connection
    $db = null;
    }
catch (PDOException $e) {
    echo "Error!: " . $e->getMessage() . "<br/>";
    die();
    }