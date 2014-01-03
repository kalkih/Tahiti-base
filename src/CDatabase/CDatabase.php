<?php
/**
 * Database class
 *
 */
class CDatabase {

    // Variables
    private $options;                   // Options used when creating the PDO object
    private $db   = null;               // The PDO object
    private $stmt = null;               // The latest statement used to execute a query
    private static $numQueries = 0;     // Count all queries made
    private static $queries = array();  // Save all queries for debugging purpose
    private static $params = array();   // Save all parameters for debugging purpose

    /**
    * Constructor creating a PDO object connecting to a choosen database.
    *
    * @param array $options containing details for connecting to the database.
    *
    */
    public function __construct($options) {
        $default = array(
            'dsn' => null,
            'username' => null,
            'password' => null,
            'driver_options' => null,
            'fetch_style' => PDO::FETCH_OBJ,
        );
        $this->options = array_merge($default, $options);

        $this->db = new PDO($this->options['dsn'], $this->options['username'], $this->options['password'], $this->options['driver_options']);

        try {
            $this->db = new PDO($this->options['dsn'], $this->options['username'], $this->options['password'], $this->options['driver_options']);
        }
        catch(Exception $e) {
            //throw $e; // For debug purpose, shows all connection details
            throw new PDOException('Could not connect to database, hiding connection details.'); // Hide connection details.
        }

        $this->db->SetAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, $this->options['fetch_style']);
    }

    /**
    * Execute a select-query with arguments and return the resultset.
    * 
    * @param string $query the SQL query with ?.
    * @param array $params array which contains the argument to replace ?.
    * @param boolean $debug defaults to false, set to true to print out the sql query before executing it.
    * @return array with resultset.
    */
    public function ExecuteSelectQueryAndFetchAll($query, $params=array(), $debug=false) {

        self::$queries[] = $query; 
        self::$params[]  = $params; 
        self::$numQueries++;

        if($debug) {
            echo "<p>Query = <br/><pre>{$query}</pre></p><p>Num query = " . self::$numQueries . "</p><p><pre>".print_r($params, 1)."</pre></p>";
        }

        $this->stmt = $this->db->prepare($query);
        $this->stmt->execute($params);
        return $this->stmt->fetchAll();
    }

    /**
    * Execute a SQL-query and ignore the resultset.
    *
    * @param string $query the SQL query with ?.
    * @param array $params array which contains the argument to replace ?.
    * @param boolean $debug defaults to false, set to true to print out the sql query before executing it.
    * @return boolean returns TRUE on success or FALSE on failure. 
    */
    public function ExecuteQuery($query, $params = array(), $debug=false) {

        self::$queries[] = $query; 
        self::$params[]  = $params; 
        self::$numQueries++;

        if($debug) {
            echo "<p>Query = <br/><pre>{$query}</pre></p><p>Num query = " . self::$numQueries . "</p><p><pre>".print_r($params, 1)."</pre></p>";
        }

        $this->stmt = $this->db->prepare($query);
        return $this->stmt->execute($params);
    }

    /**
    * Get a html representation of all queries made, for debugging and analysing purpose.
    * 
    * @return string with html.
    */
    public function Dump() {
        $html  = '<p><i>You have made ' . self::$numQueries . ' database queries.</i></p><pre>';
        foreach(self::$queries as $key => $val) {
            $params = empty(self::$params[$key]) ? null : htmlentities(print_r(self::$params[$key], 1)) . '<br/></br>';
            $html .= $val . '<br/></br>' . $params;
        }
        return $html . '</pre>';
    }

    /**
    * Return last insert id.
    */
    public function LastInsertId() {
        return $this->db->lastInsertid();
    }

    /**
    * Return rows affected of last INSERT, UPDATE, DELETE
    */
    public function RowCount() {
        return is_null($this->stmt) ? $this->stmt : $this->stmt->rowCount();
    }

    /**
    * Function to create links for sorting
    *
    * @param string $column the name of the database column to sort by
    * @return string with links to order by column.
    */
    public function orderBy($column) {
        if ($column == 'title' || $column == 'year') {
            if ($column == 'title') {
                $title = 'Titel';
            }
            if ($column == 'year') {
                $title = 'År';
            }
        }
        else {
            $title = $column;
        }
        return "$title<span class='orderby'><a href='?orderby=$column&amp;order=asc'><i>▼</i></a><a href='?orderby=$column&amp;order=desc'>▲</a></span>";
    }

    /**
    * Create links for hits per page.
    *
    * @param array $hits a list of hits-options to display.
    * @return string as a link to this page.
    */
    public function getHitsPerPage($hits) {
        $nav = "Träffar per sida: ";
        foreach($hits AS $val) {
            $nav .= "<a href='" . self::getQueryString(array('hits' => $val)) . "'> $val</a>";
        }  
        return $nav;
    }

    /**
    * Use the current querystring as base, modify it according to $options and return the modified query string.
    *
    * @param array $options to set/change.
    * @param string $prepend this to the resulting query string
    * @return string with an updated query string.
    */
    public static function getQueryString($options, $prepend='?') {
        // parse query string into array
        $query = array();
        parse_str($_SERVER['QUERY_STRING'], $query);

        // Modify the existing query string with new options
        $query = array_merge($query, $options);

        // Return the modified querystring
        return $prepend . http_build_query($query);
    }


    /**
    * Create navigation among pages.
    *
    * @param integer $hits per page.
    * @param integer $page current page.
    * @param integer $max number of pages. 
    * @param integer $min is the first page number, usually 0 or 1. 
    * @return string as a link to this page.
    */
    public function getPageNavigation($hits, $page, $max, $min=1) {
        $nav  = "<a href='" . self::getQueryString(array('page' => $min)) . "'>◄◄</a> ";
        $nav .= "<a href='" . self::getQueryString(array('page' => ($page > $min ? $page - 1 : $min) )) . "'>◄</a> ";

        for($i=$min; $i<=$max; $i++) {
            $nav .= "<a href='" . self::getQueryString(array('page' => $i)) . "'>$i</a> ";
        }

        $nav .= "<a href='" . self::getQueryString(array('page' => ($page < $max ? $page + 1 : $max) )) . "'>►</a> ";
        $nav .= "<a href='" . self::getQueryString(array('page' => $max)) . "'>►►</a> ";
        return $nav;
    }
}

?>