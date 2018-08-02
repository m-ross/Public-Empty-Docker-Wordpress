<?php
/*
    Plugin Name: Fuzzy Search
    Plugin URI: https://github.con/m-ross/Public-Empty-Docker-Wordpress
    Description: Attempts to compensate for misspellings in searches by looking at users' past searches
    Version: 0.1.0
    Author: Marcus Ross
*/

define('DB_NAME', 'wordpress');
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');
define('DB_HOST', 'mysqldb');

class FuzzySearch {
    protected $prompt; // prompt when displaying a recommendation
    protected $recommend; // search term to recommend
    protected $recommendOnlyOnce; // kluge to avoid recommending twice on the same page in cases where a search term returns zero results
    
    public function __construct() {
        $this->prompt = 'Did you mean: ';
        $this->recommend = '';
        $this->recommendOnlyOnce = 0;
        $this->databaseSetup();
        add_action('recommendPlaceHook', array($this, 'recommendInsert'), 11, 0);
        add_action('pre_get_posts', array($this, 'recommendDecide'), 10, 0);
        add_action('the_post', array($this, 'recommendPlace'), 11, 2);
        add_action('pre_get_search_form', array($this, 'recommendNoPosts'), 11, 2);
    }
    
    private function databaseConnect() {
        $db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD);
        if ($db->connect_error) {
            die('Fuzzy Search failed to connect to database: ' . $db->connect_error);
        } else {
            return $db;
        }
    }
    
    private function databaseSetup() {
        $db = $this->databaseConnect();
        $sqlQuery = 'CREATE TABLE IF NOT EXISTS ' . DB_NAME . '.FuzzySearch(
            term      CHAR(20),
            searchNum INT      UNSIGNED NOT NULL,
            recommend CHAR(20),
            PRIMARY KEY (term),
            INDEX terms (term(10), recommend(10)))';
        if (!$db->query($sqlQuery)) {
            die('Fuzzy Search table creation failed');
        }
        $db->close();
    }
    
    public function recommendDecide() { // decide on recommendation to make, if necessary
        if (empty(get_search_query())) { // stop if no search was made
            return;
        }
        
        $searchesMax = -1;
        $distanceMin = 255;
        $distThreshold = 0.2;
        $termClosest = '';
        
        $db = $this->databaseConnect();
        $_SESSION['fuzzyTerms'][] = get_search_query(); // add user's search term as element in array in session variable
        $escapeTerm = $db->escape_string(end($_SESSION['fuzzyTerms'])); // sanitise search term for mysql
        $sqlQuery = 'SELECT recommend FROM ' . DB_NAME . '.FuzzySearch WHERE term = \'' . $escapeTerm . '\'';
        $sqlResult = $db->query($sqlQuery); // does term already have a recommendation?
        if ($sqlResult) { // if query didn't fail
            if ($sqlResult->num_rows > 0) { // if the query returns a result
                $row = $sqlResult->fetch_assoc(); // there is at most one row in result because term is a unique column
                $this->recommend = $row['recommend'];
                $sqlQuery = 'UPDATE ' . DB_NAME . '.FuzzySearch SET searchNum = searchNum + 1 WHERE term = \'' . $escapeTerm . '\'';
                $db->query($sqlQuery); // increment # times the term has been searched
            } else { // if the term was not in database, add it
                $sqlQuery = 'INSERT INTO ' . DB_NAME . '.FuzzySearch (term, searchNum) VALUES (\'' . $escapeTerm . '\', 1)';
                $db->query($sqlQuery);
            }
        }
        
        if (empty($this->recommend)) { // if a recommendation wasn't found above
            $sqlQuery = 'SELECT term, searchNum FROM ' . DB_NAME . '.FuzzySearch WHERE recommend IS NULL';
            $sqlResult = $db->query($sqlQuery); // get terms to compare with the user's search, excluding any "known" misspellings
            while ($row = $sqlResult->fetch_assoc()) { // while there are terms remaining to check
                if ($row['term'] === $escapeTerm) { // if the same word is encountered, don't bother checking distance
                    continue;
                }
                
                $distance = levenshtein($row['term'], $escapeTerm); // compare database term with user's input
                 // term is the closest, if: (distance is smallest) or (distance is smallest or tied for smallest but term has the most searches)
                if ($distance < $distanceMin or $row['searchNum'] > $searchesMax && $distance <= $distanceMin) {
                    $termClosest = $row['term'];
                    $distanceMin = $distance;
                    $searchesMax = $row['searchNum'];
                }
            }
            
            if (!empty($termClosest) and $distThreshold > $distanceMin / strlen($termClosest) and $searchesMax >= 10) { // closest word must still be closer than a threshold to be accepted and must have been searched several times before
                $this->recommend = $termClosest;
                $sqlQuery = 'UPDATE ' . DB_NAME . '.FuzzySearch SET recommend = \'' . $this->recommend . '\' WHERE term = \'' . $escapeTerm . '\'';
                $db->query($sqlQuery); // add the new recommendation to database
            }
        }
        
        $db->close();
    }

    public function recommendPlace($post, $query) { // place the recommendation in the appropriate spot on the search results page (before the first result)
        if ($query->is_search() && $query->is_main_query() && $query->current_post === 0) {
            do_action('recommendPlaceHook');
        }
    }

    public function recommendNoPosts() { // place the recommendation before the search form on pages that have no search results to display
        global $wp_query;
        if (!$wp_query->have_posts() && $this->recommendOnlyOnce == 0) {
            do_action('recommendPlaceHook');
            $this->recommendOnlyOnce++;
        } else {
            $this->recommendOnlyOnce--;
        }
    }
    

    public function recommendInsert() { // insert the search term recommendation into the search results page
        if (!empty($this->recommend)) { // if there is a recommendation to make
            echo '<div style="padding-top:10px; padding-bottom:20px"><i>' . $this->prompt . '<a href="' . get_search_link($this->recommend) . '">' . $this->recommend . '</i></a></div>';
        }
        $this->recommend = ''; // reset before next search
    }
}

session_start();
$fuzzySearch = new FuzzySearch();

?>
