<?php
/**
 * Functions for filtering content.
 *
 */
use \Michelf\MarkdownExtra;
class CTextFilter {

    public function __construct() {
    }

    /**
     * Call each filter.
     *
     * @param string $text the text to filter.
     * @param string $filter as comma separated list of filter.
     * @return string the formatted text.
     */
    function doFilter($text, $filters) {

        // Define all valid filters with their callback function.
        $valid = array(
            'bbcode'   => 'bbcode2html',
            'link'     => 'make_clickable',
            'markdown' => 'markdown',
            'nl2br'    => 'nl2br',
            ''         => '',
        );

        // Make an array of the comma separated string $filter
        $filter = preg_replace('/\s/', '', explode(',', $filters));

        foreach($filter as $val) {
            //echo $val.$all[$val]."<br />";
            if ($valid[$val] != '') {
                $text = $this->$valid[$val]($text);
            }
            
        }

        return $text;
    }

    /**
     * Helper, BBCode formatting converting to HTML.
     *
     * @param string text The text to be converted.
     * @return string the formatted text.
     * @link http://dbwebb.se/coachen/reguljara-uttryck-i-php-ger-bbcode-formattering
     */
    function bbcode2html($text) {
        $search = array(
            '/\[b\](.*?)\[\/b\]/is',
            '/\[i\](.*?)\[\/i\]/is',
            '/\[u\](.*?)\[\/u\]/is',
            '/\[img\](https?.*?)\[\/img\]/is',
            '/\[url\](https?.*?)\[\/url\]/is',
            '/\[url=(https?.*?)\](.*?)\[\/url\]/is'
        );

        $replace = array(
            '<strong>$1</strong>',
            '<em>$1</em>',
            '<u>$1</u>',
            '<img src="$1" />',
            '<a href="$1">$1</a>',
            '<a href="$1">$2</a>'
        );

        return preg_replace($search, $replace, $text);
    }

    /**
     * Make clickable links from URLs in text.
     *
     * @param string $text the text that should be formatted.
     * @return string with formatted anchors.
     * @link http://dbwebb.se/coachen/lat-php-funktion-make-clickable-automatiskt-skapa-klickbara-lankar
     */
    function make_clickable($text) {
        return preg_replace_callback(
            '#\b(?<![href|src]=[\'"])https?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#',
        create_function(
            '$matches',
            'return "<a href=\'{$matches[0]}\'>{$matches[0]}</a>";'
        ),
            $text
        );
    }

    public function nl2br($text) {
        return nl2br($text);
    }

    //use \Michelf\MarkdownExtra;
    /**
     * Format text according to Markdown syntax.
     *
     * @link http://dbwebb.se/coachen/skriv-for-webben-med-markdown-och-formattera-till-html-med-php
     * @param string $text the text that should be formatted.
     * @return string as the formatted html-text.
     */
    function markdown($text) {
        require_once('Markdown.php');
        require_once('MarkdownExtra.php');
        return MarkdownExtra::defaultTransform($text);
    }
}