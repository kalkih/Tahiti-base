<?php
/**
 * Content class
 *
 */
class CContent {

    // Variables
    protected $database;

    /**
    * Constructor
    */
    public function __construct($database) {
        $this->database = $database;
    }

    public function createDatabase() {
        // Connect to a MySQL database using PHP PDO
        $db = new CDatabase($this->database);
        $sql = "
        CREATE TABLE IF NOT EXISTS Content
        (
          id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
          slug CHAR(80) UNIQUE,
          url CHAR(80) UNIQUE,
         
          TYPE CHAR(80),
          title VARCHAR(80),
          DATA TEXT,
          FILTER CHAR(80),
         
          published DATETIME,
          created DATETIME,
          updated DATETIME,
          deleted DATETIME
         
        ) ENGINE INNODB CHARACTER SET utf8;";

        $res = $db->ExecuteQuery($sql);
        return $res;
    }

    public function getUrlToContent($content) {
        switch($content->TYPE) {
            case 'page': return "page.php?url={$content->url}"; break;
            case 'post': return "blog.php?slug={$content->slug}"; break;
            default: return null; break;
        }
    }

    public function editPost($title, $slug, $url, $data, $type, $filter, $published, $id) {
        $db = new CDatabase($this->database);
        $sql = '
            UPDATE Content SET
                title   = ?,
                slug    = ?,
                url     = ?,
                data    = ?,
                type    = ?,
                filter  = ?,
                published = ?,
                updated = NOW()
            WHERE 
                id = ?
        ';
        $params = array($title, $slug, $url, $data, $type, $filter, $published, $id);
        $res = $db->ExecuteQuery($sql, $params);
        if($res) {
            $output = 'Uppdateringen lyckades!';
        }
        else {
            $output = 'Uppdateringen misslyckades!';
        }
        return $output;
    }

    public function newPost($title, $slug, $url, $data, $type, $filter, $published) {
        $db = new CDatabase($this->database);
        $sql = '
            INSERT INTO Content(
                title,
                slug,
                url,
                data,
                type,
                filter,
                published,
                updated)
            VALUES(?,?,?,?,?,?,?,NOW())
        ';
        $params = array($title, $slug, $url, $data, $type, $filter, $published);
        $db->ExecuteQuery($sql, $params);
        return "Posten är skapad.";
    }

    public function getFormContent($id) {
        $title  = null;
        $slug   = null;
        $url    = null;
        $data   = null;
        $type   = null;
        $filter = null;
        $published = null;
        
        $db = new CDatabase($this->database);
        
        // Select from database
        $sql = 'SELECT * FROM Content WHERE id = ?';
        $res = $db->ExecuteSelectQueryAndFetchAll($sql, array($id));
        
        if(isset($res[0])) {
          $c = $res[0];
        }
        else {
          die('Misslyckades: det finns inget innehåll med sådant id.');
        }
        
        // Sanitize content before using it.
        $title  = htmlentities($c->title, null, 'UTF-8');
        $slug   = htmlentities($c->slug, null, 'UTF-8');
        $url    = $c->url;
        $data   = htmlentities($c->DATA, null, 'UTF-8');
        $type   = $c->TYPE;
        $filter = htmlentities($c->FILTER, null, 'UTF-8');
        $published = $c->published;
    
        $content = array('id'=>$id,'title'=>$title,'slug'=>$slug,'url'=>$url,'data'=>$data,'type'=>$type,'filter'=>$filter,'published'=>$published);
        
        return $content;
    }

    public function getContent() {

        $db = new CDatabase($this->database);

        $sql = "SELECT *, (published <= NOW()) AS available FROM Content;";
        $res = $db->ExecuteSelectQueryAndFetchAll($sql);

        return $res;
    }
}

?>