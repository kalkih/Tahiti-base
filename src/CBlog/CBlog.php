<?php
/**
 * Page class
 *
 */
class CBlog extends CContent {

    public function __construct($database) {
        $this->database = $database;
    }

    public function showBlog($slug, $limit = null) {

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
        FROM Blog
        WHERE
        type = 'post' AND
        $slugSql AND
        published <= NOW()
        ORDER BY published DESC";
        if ($limit) {
            $sql .= " LIMIT {$limit} OFFSET 0;";
        }
        else $sql .= ';';

        $res = $db->ExecuteSelectQueryAndFetchAll($sql);
        $array = array();
        if(isset($res[0])) {
            foreach($res as $c) {
                // Sanitize content before using it.
                $title  = htmlentities($c->title, null, 'UTF-8');
                $data   = $cf->doFilter(htmlentities($c->DATA, null, 'UTF-8'), $c->FILTER);
                $id  = intval($c->id);
                $published  = htmlentities($c->published, null, 'UTF-8');
                $created  = htmlentities($c->created, null, 'UTF-8');
                $category = htmlentities($c->category, null, 'UTF-8');
                
                $array[$c->slug]['title'] = $title;
                $array[$c->slug]['data'] = $data;
                $array[$c->slug]['id'] = $id;
                $array[$c->slug]['published'] = $published;
                $array[$c->slug]['created'] = $created;
                $array[$c->slug]['category'] = $category;
            }
            return $array;
        } else if($slug) {
            $content = "Bloggposten hittades inte!";
        } else {
            $content = "Det finns inga bloggposter.";
        }
        
        return $content;
    }

    public function description($text, $url, $limit = 20) {
      if (str_word_count($text, 0) > $limit) {
          $words = str_word_count($text, 2);
          $pos = array_keys($words);
          $text = substr($text, 0, $pos[$limit]) . '... <br> <a href="' . $url . '"> Läs mer »</a>';
      }
      return $text;
    }
    
}

?>