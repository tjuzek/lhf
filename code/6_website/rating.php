<?php
$user = "phpuser";
$password = "PW=";
$database = "DBNAME";
$table_tracker = "item_tracker";
$table_fillers = "fillers";
$table_banned_ips = "banned_ips";
$raw_clientIP = $_SERVER['REMOTE_ADDR'];
$clientIP = preg_replace('/[^0-9.:]/', '', $raw_clientIP);
$number_of_items = 5;
$size_local_item_pool = 10;
$number_of_fillers = 10;

try {
    $raw_userID = $_GET['userID'];
    $userID = preg_replace('/[^0-9a-z]/', '', $raw_userID);

    $db = new PDO("mysql:host=localhost;dbname=$database", $user, $password);
    $item_pool = $db->query("SELECT itemID, isequence, no_ratings FROM $table_tracker ORDER BY no_ratings ASC LIMIT $size_local_item_pool")->fetchAll(PDO::FETCH_ASSOC);
    $fillers = $db->query("SELECT itemID, isequence, no_ratings FROM $table_fillers ORDER BY no_ratings ASC LIMIT $number_of_fillers")->fetchAll(PDO::FETCH_ASSOC);
    $banned_ips = $db->query("SELECT clientIP FROM $table_banned_ips")->fetchAll(PDO::FETCH_COLUMN);

    if (count($item_pool) < $size_local_item_pool) {
        echo "Error!: Not enough items in the pool.<br/>";
        die();
    }
    if (count($fillers) < $number_of_fillers) {
        echo "Error!: Not enough fillers.<br/>";
        die();
    }
    $randomSQLRows = array_rand($item_pool, $number_of_items);
    foreach ($randomSQLRows as $index) {
        $randomSQLRowsArray[] = $item_pool[$index];
    }
    
    foreach ($fillers as $item) {
        $randomSQLRowsArray[] = $item;
    }
    $randomSQLRowsArrayJSON = json_encode($randomSQLRowsArray);
    $db = null; # close connection
} catch (PDOException $e) {
    echo "Error!: " . $e->getMessage() . "<br/>";
    die();
}

if (strlen($userID) != 12 || in_array($clientIP, $banned_ips)) {
    header('Location: rating.html');
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="shortcut icon" href="#">
<title>Study</title>
</head>

<body onload = initialise()>
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
        <img onclick="rating(1)" style="width: 35px; height: 35px;" alt="button1" src="button1.jpg" id="button1">
        </td>
        <td style="height: 50px; width: 10px;"></td>
        <td style="vertical-align: middle; text-align: center; height: 75px; width: 530px;">
        <img onclick="rating(7)" style="width: 35px; height: 35px;" alt="button7" src="button7.jpg" id="button7">
        </td>
        <td style="height: 75px; width: 50px;"></td>
    </tr>
    <tr>
        <td style="height: 75px; width: 50px;"></td>
        <td style="vertical-align: top; text-align: center; height: 75px; width: 530px;" id="scale_legend_left">left is better</td>
        <td style="height: 50px; width: 10px;"></td>
        <td style="vertical-align: top; text-align: center; height: 75px; width: 530px;" id="scale_legend_right">right is better</td>
        <td style="height: 75px; width: 50px;"></td>
    </tr>
    </tbody>
</table>


<script>
var userID = "<?php echo $userID; ?>";
var sequencesArray = <?php echo $randomSQLRowsArrayJSON; ?>;

// shuffle sequencesArray:
for (let i = sequencesArray.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [sequencesArray[i], sequencesArray[j]] = [sequencesArray[j], sequencesArray[i]];
}


const default_legend_left = document.getElementById("scale_legend_left").innerHTML;
const default_legend_right = document.getElementById("scale_legend_right").innerHTML;

var number_of_items = 0;
var item_counter = 0;
var itemID = 0;
var time_item_loaded = 0;
var rating_time = 0;
var mogl_zeit = 0;
var mogl_habdich = 0;
var threshold = 0;
var langprofic_ratings = [];
var direction_focal_word = "left";


function initialise() {
    addExtraItems();
    newItem();
}


function addExtraItems() {
    // gotcha items; 200001 = click left (expect low rating), 200007 = click right (expect high rating)
    sequencesArray.splice(randomarrpos(), 0, {"itemID": 200001, "isequence": "In utero exposure to Bisphenol A (BPA) is linked to developmental brain deficits like sexual differentiation and behavior issues, but its long-term effects remain unclear. Our study with a mouse model shows that, exposure to an environmentally relevant dose of BPA causes the following. This is not a real item, please click on the left button. Persistent locomotor deficits, anxiety-like behavior, and memory impairments into old age (18 months), along with permanent alterations in brain gene expression, exhibiting regional and sexual variations.#Research using a mouse model revealed that exposure to Bisphenol A (BPA) during fetal development leads to lasting impairments in sexual differentiation, behavior, and motor coordination. This includes deficits in locomotor function, increased anxiety, and impaired memory in offspring up to 18 months old. This is not a real item, please click on the left button. Additionally, BPA exposure altered global brain gene expression with notable sex and region-specific differences.", "no_ratings": 0});    
    sequencesArray.splice(randomarrpos(), 0, {"itemID": 200007, "isequence": "The 2017 Hypertension Clinical Practice Guidelines classify a blood pressure of 130 to 139/80 to 89 mm Hg as Stage I hypertension and recommend drug treatment for patients aged ≥65 years without cardiovascular disease, a stance not echoed in the 2018 Chinese guidelines. This is not a real item, please click on the right-is-better button. This study uses a microsimulation model to assess the lifetime cost-effectiveness of drug versus nondrug treatments for this Chinese patient subgroup from a government affordability perspective.#The 2017 Hypertension Clinical Practice Guidelines classify a blood pressure of 130-139/80-89 mm Hg as stage I hypertension. Patients over 65 without cardiovascular disease are advised drug treatment under these guidelines, unlike the 2018 Chinese guidelines. This is not a real item, please click on the right-is-better button. A microsimulation model was created to evaluate the cost-effectiveness of drug versus nondrug treatment for this Chinese demographic from a government perspective.", "no_ratings": 0});
    
    // language proficiency items; 300001 = right is bad (expect low rating), 300007 = left is bad (expect high rating)
    sequencesArray.splice(randomarrpos(), 0, {"itemID": 300001, "isequence": "Unlike metal-modified zeolites, metal-organic framework materials (MOFs) offer a promising platform for designing catalysts with uniform metal coordination environments and catalytic functions. In this study, we demonstrate the potential of MOFs by demonstrating MIL-100(Fe) as a prototype catalyst comprising solely of active tri-iron moieties. Our results show that this heterogeneous catalyst can efficiently catalyze the oxidation of methane to methanol at low temperatures and sub-ambient methane pressures.#Unlike metal-modify zeolite, metal-organic framework material (MOF) offer promising platform for to design catalyst with uniform metal coordination environments and catalytic function. In this study, we demonstrate potential of MOFs by demonstrating MIL-100(Fe) as prototype catalyst comprising solely of active tri-iron moieties. Our result show heterogeneous catalyst efficiently catalyze the oxidation of methane to methanol at low temperature and sub-ambient methane pressure.", "no_ratings": 0});
    sequencesArray.splice(randomarrpos(), 0, {"itemID": 300007, "isequence": "In 1934, Gertrude Fife, president of National Association of Nurse Anesthetists (NANA), noticed need to improve standards of anesthesia practice and unify education of nurse anesthetists. The early members of the association to take action by searching for schools, establishing educational criterium, creating process for school approval. This eventually lead to establishment of Council on Accreditation of Nurse Anesthesia Educational Programs (COA), 1975.#In 1934, Gertrude Fife, president of the National Association of Nurse Anesthetists (NANA), noticed the need to improve the standards of anesthesia practice and unify the education of nurse anesthetists. The early members of the association took action by searching for schools, establishing educational criteria, and creating a process for school approval. This eventually led to the establishment of the Council on Accreditation of Nurse Anesthesia Educational Programs (COA) in 1975.", "no_ratings": 0});

    // calibration item for the first item
    sequencesArray.splice(0, 0, {"itemID": 100001, "isequence": "In this paper, we detail the creation and assessment of a macrocyclic peptidomimetic targeting enterovirus, A71 (EV71), featuring a 15-membered ring finalized through Yamaguchi esterification instead of the usual Ru-catalyzed method. Crystallographic analysis confirmed the interaction with EV71's 3C protease, supporting the development of new anti-EV71 agents.#In this paper, present development and test of macrocyclic peptidomimetic aimed at combating enterovirus A71 (EV71). The compound, structure with 15-membered macrocyclic ring, were synthesize with Yamaguchi esterification. It is effectiveness is confirmed with crystallographic study of interaction with EV71's 3C protease, more anti-EV71 drug development.", "no_ratings": 0});

    number_of_items = sequencesArray.length;
}


function randomarrpos() {
    return Math.floor((sequencesArray.length * 0.33) + (Math.random() * sequencesArray.length * 0.63)); // 0.63 instead of 0.67 to avoid array gaps in small item pools
}


function newItem() {
    console.log("Loading new item.");
    if (item_counter < number_of_items) {
        console.log("Item counter: " + item_counter);
        itemID = sequencesArray[item_counter].itemID;
        // split sequencesArray[item_counter].isequence; by #
        var split_sequence = sequencesArray[item_counter].isequence.split("#");
        // flip a coin to decide which side to show first
        if (Math.random() < 0.5) {
            document.getElementById("sequence_cell_left").innerHTML = split_sequence[0];
            document.getElementById("sequence_cell_right").innerHTML = split_sequence[1];
            direction_focal_word = "left";
        } else {
            document.getElementById("sequence_cell_left").innerHTML = split_sequence[1];
            document.getElementById("sequence_cell_right").innerHTML = split_sequence[0];
            direction_focal_word = "right";
        }
        if (itemID == 200001 || itemID == 200007 || itemID == 300001 || itemID == 300007) {
            document.getElementById("sequence_cell_left").innerHTML = split_sequence[0];
            document.getElementById("sequence_cell_right").innerHTML = split_sequence[1];
            direction_focal_word = "left";
        }
        document.getElementById("counter_cell").innerHTML = (item_counter + 1) + "/" + number_of_items;

        time_item_loaded = new Date().getTime();
        if (itemID == 200001 || itemID == 200007) {
            threshold = 1000;
        } else {
            threshold = Math.floor(((sequencesArray[item_counter].isequence.length * 25) + 225)/2.5);
        }
    } else {
        console.log("End of items.");
        document.getElementById("scale_legend_left").innerHTML = "You will be redirected shortly.";
        document.getElementById("scale_legend_right").innerHTML = "";
        document.getElementById("counter_cell").innerHTML = "~";
        hideAllImages();
        check_langprofic_items();
        setTimeout(function() {window.location = "done.php?userID=" + userID},2000);
    }
    item_counter += 1;
}


function rating(rating) {
    rating_time = (new Date().getTime()) - time_item_loaded;
    if (direction_focal_word == "right") {
        if (rating == 1) {
            rating = 7;
        } else if (rating == 7) {
            rating = 1;
        }
    }

    if (rating_time < threshold) {
        mogl_zeit += 1;
        if (mogl_zeit > 3) {
            known_user(0);
        }
        moglAction("You are going too fast.");
    }
    if ((itemID == 200001 && rating > 1) || (itemID == 200007 && rating < 7)) {
        mogl_habdich += 1;
        if (mogl_habdich > 0) {
            known_user(1);
        }
        moglAction("Please pay attention to the study.");
    }
    if (itemID == 300001 || itemID == 300007) {
        langprofic_ratings[itemID] = rating;
    }
    
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "https://WEBSITE.com/submit_rating.php?userID=" + userID + "&itemID=" + itemID + "&rating=" + rating + "&msecs=" + rating_time, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                //console.log("Rating submitted.");
                //pass
            } else {
                console.log("Rating failed.")
            }
        }
    };
    xhr.send();
    
    newItem();
}


function hideAllImages() {
    document.getElementById("button1").style.visibility = "hidden";
    document.getElementById("button7").style.visibility = "hidden";
    document.getElementById("sequence_cell_left").style.visibility = "hidden";
    document.getElementById("sequence_cell_right").style.visibility = "hidden";
}


function showAllImages() {
    document.getElementById("button1").style.visibility = "visible";
    document.getElementById("button7").style.visibility = "visible";
    document.getElementById("sequence_cell_left").style.visibility = "visible";
    document.getElementById("sequence_cell_right").style.visibility = "visible";
    time_item_loaded = new Date().getTime();
}


function moglAction(warning_message) {
    if (item_counter < number_of_items) {
        hideAllImages();
        document.getElementById("scale_legend_left").innerHTML = warning_message;
        document.getElementById("scale_legend_right").innerHTML = warning_message;
        setTimeout(function() {showAllImages();},2000);
        setTimeout(function() {document.getElementById("scale_legend_left").innerHTML = default_legend_left;},2000);
        setTimeout(function() {document.getElementById("scale_legend_right").innerHTML = default_legend_right;},2000);
    }
}


function known_user(category) {
    var xhr = new XMLHttpRequest();
        xhr.open("GET", "https://WEBSITE.com/submit_user.php?userID=" + userID + "&category=" + category, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    //console.log("User submitted.");
                    //pass
                } else {
                    console.log("User submission failed.")
                }
            }
            };
        xhr.send();
}


function error() {
    hideAllImages();
    document.getElementById("scale_legend_left").innerHTML = `THANK YOU, please return to <a href="https://app.prolific.com/submissions/complete?cc=CODE">https://app.prolific.com/submissions/complete?cc=CODE</a> or use the following completion code directly: CODE .`;
    document.getElementById("scale_legend_right").innerHTML = "";
    document.getElementById("counter_cell").innerHTML = "~";
}


function check_langprofic_items() {
    bad_langprofic_items = langprofic_ratings[300001];
    good_langprofic_items = langprofic_ratings[300007];
    if ((bad_langprofic_items > 1) || (good_langprofic_items < 7)) {
        known_user(2);
    }
}

</script>

</body>
</html>