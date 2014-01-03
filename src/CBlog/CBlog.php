<?php
/**
 * Page class
 *
 */
class CBlog extends CContent {

    public function __construct($database) {
        $this->database = $database;
    }

    public function showBlog($slug) {

        $db = new CDatabase($this->database);
        $cf = new CTextFilter();
        // Get content
        if($slug) {
            $slugSql = "slug = '$slug'";
        } else {
            $slugSql = 1;
        }
        $sql = "
        SELECT *
        FROM Content
        WHERE
        type = 'post' AND
        $slugSql AND
        published <= NOW()
        ORDER BY updated DESC
        ;
        ";

        $res = $db->ExecuteSelectQueryAndFetchAll($sql);
        $array = array();
        if(isset($res[0])) {
            foreach($res as $c) {
                // Sanitize content before using it.
                $title  = htmlentities($c->title, null, 'UTF-8');
                $data   = $cf->doFilter(htmlentities($c->DATA, null, 'UTF-8'), $c->FILTER);
                $id  = intval($c->id);
                $created  = htmlentities($c->created, null, 'UTF-8');
                
                $array[$c->slug]['title'] = $title;
                $array[$c->slug]['data'] = $data;
                $array[$c->slug]['id'] = $id;
                $array[$c->slug]['created'] = $created;
            }
            return $array;
        } else if($slug) {
            $content = "Bloggposten hittades inte!";
        } else {
            $content = "Det finns inga bloggposter.";
        }
        
        return $content;
    }
    
}

?>