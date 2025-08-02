<?php
# database connection details
$user = "phpuser";
$password = "PW=";
$database = "DBNAME";
$table_tracker = "item_tracker";
$table_fillers = "fillers";
$table_ratings = "ratings";
$table_ratings_per_ip = "ratings_per_ip";
$table_banned_ips = "banned_ips";
$raw_clientIP = $_SERVER['REMOTE_ADDR'];
$clientIP = preg_replace('/[^0-9.:]/', '', $raw_clientIP);
$max_ratings_per_ip = 38;


# try to grab the user id and ratings from the url parameters
try {
    $raw_userID = $_GET['userID'];
    $userID = preg_replace('/[^0-9a-z]/', '', $raw_userID);
    $itemID = (int)$_GET['itemID'];
    $rating = (int)$_GET['rating'];
    $msecs = (int)$_GET['msecs'];
} catch (PDOException $e) {
    echo "Error!: " . $e->getMessage() . "<br/>";
    die();
}

if ($userID == "" || $itemID == "" || $rating == "" || $msecs == "") {
    echo "Error!: Could not fetch required rating information.<br/>";
    die();
}


try {
    # connect to the database
    $db = new PDO("mysql:host=localhost;dbname=$database", $user, $password);

    $ip_ratings = $db->query("SELECT no_ratings FROM $table_ratings_per_ip WHERE clientIP = '$clientIP'")->fetch(PDO::FETCH_ASSOC);
    $no_ratings_made_by_ip = (int)$ip_ratings['no_ratings'];

    
    # update the tracker table
    if ($itemID < 100) {
        $putincr = $db->query("UPDATE $table_tracker SET no_ratings = no_ratings + 1 WHERE itemID = $itemID;");
    } else {
        $putincr = $db->query("UPDATE $table_fillers SET no_ratings = no_ratings + 1 WHERE itemID = $itemID;");
    }
    if ($putincr->rowCount() == 0) {
        echo("Warning! Could not increase item counter.<br/>");
    }
    
    # put rating into the ratings table
    $putrat = $db->query("INSERT INTO $table_ratings (userID, itemID, rating, msecs) VALUES ('$userID', $itemID, $rating, $msecs);");
    if ($putrat->rowCount() == 0) {
        echo("Warning! Could not put rating into database.<br/>");
    }

    # update the ratings per ip table
    $putip = $db->query("INSERT INTO $table_ratings_per_ip (clientIP, no_ratings) VALUES ('$clientIP', 1) ON DUPLICATE KEY UPDATE no_ratings = no_ratings + 1;");
    if ($putip->rowCount() == 0) {
        echo("Warning! Could not increase counter.<br/>");
    }

    if ($no_ratings_made_by_ip > $max_ratings_per_ip) {
        # put clientIP into the banned_ips table
        $putip = $db->query("INSERT IGNORE INTO $table_banned_ips (clientIP) VALUES ('$clientIP');");
        if ($putip->rowCount() == 0) {
            echo("Warning! Could not put i-info into database.<br/>");
        }
    }

    # close the database connection
    $db = null;

} catch (PDOException $e) {
    echo "Error!: " . $e->getMessage() . "<br/>";
    die();
}