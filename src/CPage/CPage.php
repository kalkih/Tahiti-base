<?php
/**
 * Page class
 *
 */
class CPage {

    public function __construct($database) {
        $this->database = $database;
    }

    public function getPage($url) {
        $db = new CDatabase($this->database);
        
        // Get content
        $sql = "
        SELECT *
        FROM Content
        WHERE
              type = 'page' AND
              url = ? AND
              published <= NOW();
        ";

        $res = $db->ExecuteSelectQueryAndFetchAll($sql, array($url));
        if(isset($res[0])) {
            $c = $res[0];
        }
        else {
            die('Misslyckades: innehållet finns inte!');
        }

        // Sanitize content before using it.
        $filter = new CTextFilter();
        $title  = htmlentities($c->title, null, 'UTF-8');
        $data   = $filter->doFilter(htmlentities($c->DATA, null, 'UTF-8'), $c->FILTER);
        $id     = htmlentities($c->id, null, 'UTF-8');
        
        return array('id'=>$id,'title'=>$title,'data'=>$data);
    }
    
}

?>