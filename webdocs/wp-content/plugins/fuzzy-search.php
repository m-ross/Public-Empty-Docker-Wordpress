<?php
/*
    Plugin Name: Fuzzy Search
    Plugin URI: https://github.con/PartnerComm/Public-Empty-Docker-Wordpress
    Description: Tries to compensate for misspellings in searches by looking at past users' searches
    Version: 0.1.0
    Author: Marcus Ross
*/

session_start();

$prompt = 'Did you mean:'; // prompt when displaying a recommendation
$recommend = ''; // search term to recommend
$recommendOnlyOnce = 0; // this is a kluge to avoid recommending twice on the same page in cases where a bad search term returns zero results

function setupDatabase() {
    $dbHost = 'mysqldb'; // sql server
    $dbUser = 'root'; // sql username
    $dbPass = 'root'; // sql password
    $db = new mysqli($dbHost, $dbUser, $dbPass);
    if ($db->connect_error) {
        die('Database connection failed: ' . $db->connect_error);
    }
    
//     $sqlQuery = 'SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = \'fuzzySearch\''; // does database exist?
//     $sqlResult = $db->query($sqlQuery); // check for existence
//     if ($sqlResult->num_rows == 0) { // if database doesn't exist
    $sqlQuery = 'CREATE DATABASE IF NOT EXISTS fuzzySearch'; // create database if it doesn't exist
    if (!$db->query($sqlQuery)) {
        die('Database creation failed');
    }

    $sqlQuery = 'CREATE TABLE IF NOT EXISTS fuzzySearch.searchTerms(
        term      CHAR(20) PRIMARY KEY,
        hitNum    INT      UNSIGNED NOT NULL,
        searchNum INT      UNSIGNED NOT NULL,
        recommend CHAR(20))'; // create table if it doesn't exist
    if (!$db->query($sqlQuery)) {
        die('Table creation failed');
    }
    
    $db->close();
}
setupDatabase();

function recommendDecide() { // decide on recommendation to make, if necessary
    global $recommend;
    $hitsMax = -1;
    $distanceMin = 255;
    $distThreshold = 0.2;
    $termClosest = '';
    $recommend = '';
    $dbHost = 'mysqldb'; // sql server
    $dbUser = 'root'; // sql username
    $dbPass = 'root'; // sql password
    $db = new mysqli($dbHost, $dbUser, $dbPass);
    if ($db->connect_error) {
        die('Database connection failed: ' . $db->connect_error);
    }
    
    $_SESSION['fuzzyTerms'][] = get_search_query(); // add user's search term as element in array in session variable
    $escapeTerm = $db->escape_string(end($_SESSION['fuzzyTerms'])); // sanitise search term for mysql
    $sqlQuery = 'SELECT recommend FROM fuzzySearch.searchTerms WHERE term = \'' . $escapeTerm . '\'';
    $sqlResult = $db->query($sqlQuery); // does term already have a recommendation?
    if ($sqlResult) { // if query didn't fail
        if ($sqlResult->num_rows > 0) { // if the query returns a result
            $row = $sqlResult->fetch_assoc(); // there is at most one row in result because term is a unique column
            $recommend = $row['recommend'];
            $sqlQuery = 'UPDATE fuzzySearch.searchTerms SET searchNum = searchNum + 1 WHERE term = \'' . $escapeTerm . '\'';
            $db->query($sqlQuery); // increment # times the term has been searched
        } else { // if the term was not in database, add it
            $sqlQuery = 'INSERT INTO fuzzySearch.searchTerms (term, hitNum, searchNum) VALUES (\'' . $escapeTerm . '\', 0, 1)';
            $db->query($sqlQuery);
        }
    }
    
    if (empty($recommend)) { // if a recommendation wasn't found above
        $sqlQuery = 'SELECT term, searchNum FROM fuzzySearch.searchTerms WHERE recommend IS NULL';
        $sqlResult = $db->query($sqlQuery); // get terms to compare with the user's search, excluding any "known" misspellings
        while ($row = $sqlResult->fetch_assoc()) { // while there are terms remaining to check
            if ($row['term'] === $escapeTerm) { // if the same word is encountered, don't bother checking distance
                continue;
            }
            
            $distance = levenshtein($row['term'], $escapeTerm); // compare database term with user's input
             // term is the closest, if: (distance is smallest) or (distance is smallest or tied for smallest but term has the most searches)
            if ($distance < $distanceMin or $row['hitNum'] > $hitMax && $distance <= $distanceMin) {
                $termClosest = $row['term'];
                $distanceMin = $distance;
                $hitMax = $row['hitNum'];
            }
        }
        
        if ($distThreshold > $distanceMin / strlen($termClosest)) { // closest word must still be closer than a threshold to be accepted
            $recommend = $termClosest;
            $sqlQuery = 'UPDATE fuzzySearch.searchTerms SET recommend = \'' . $recommend . '\' WHERE term = \'' . $escapeTerm . '\'';
            $db->query($sqlQuery); // add the new recommendation to database
        }
    }
    
    $db->close();
}
add_action('pre_get_posts', 'recommendDecide', 10, 0);

function recommendPlace($post, $query) { // place the recommendation in the appropriate spot on the search results page (before the first result)
    if ($query->is_search() && $query->is_main_query() && $query->current_post === 0) {
        do_action('recommendPlaceHook');
    }
}
add_action('the_post', 'recommendPlace', 11, 2);

function recommendNoPosts() { // place the recommendation before the search form on pages that have no search results to display
    global $wp_query;
    if (!$wp_query->have_posts() && $recommendOnlyOnce == 0) {
        do_action('recommendPlaceHook');
        $recommendOnlyOnce++;
    } else {
        $recommendOnlyOnce--;
    }
}
add_action('pre_get_search_form', 'recommendNoPosts', 11, 2);

function recommendInsert() { // insert the search term recommendation into the search results page
    global $recommend, $prompt;
    if (!empty($recommend)) { // if there is a recommendation to make
        echo '<div style="padding-top:10px; padding-bottom:20px"><i>' . $prompt . ' <a href="' . get_search_link($recommend) . '">' . $recommend . '</i></a></div>';
    }
    $recommend = '';
}
add_action('recommendPlaceHook', 'recommendInsert', 11, 0);

?>
