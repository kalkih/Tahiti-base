<?php
/**
 * Theme related functions. 
 *
 */
 
/**
 * Get title for the webpage by concatenating page specific title with site-wide title.
 *
 * @param string $title for this page.
 * @return string/null wether the favicon is defined or not.
 */
function get_title($title) {
  global $tahiti;
  return $title . (isset($tahiti['title_append']) ? $tahiti['title_append'] : null);
}


/**
 * Create a navigation bar / menu for the site.
 *
 * @param string $menu for the navigation bar.
 * @return string as the html for the menu.
 */
function get_navbar($menu) {
  $html = "<nav class='{$menu['class']}'>\n";
  foreach($menu['items'] as $item) {
    $selected = $menu['callback_selected']($item['url']) ? "selected" : null;
    $html .= "<a class='{$selected}' href='{$item['url']}'>{$item['text']}</a>\n";
  }
  $html .= "<div class='clear'></div>\n</nav>\n";
  return $html;
}