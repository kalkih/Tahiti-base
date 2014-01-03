<?php 
/**
 * This is a Tahiti pagecontroller.
 *
 */
// Include the essential config-file which also creates the $tahiti variable with its defaults.
include(__DIR__.'/config.php'); 
 
 
// Do it and store it all in variables in the Tahiti container.
$tahiti['title'] = "404";
$tahiti['main'] = "This is a Tahiti 404. Page was not found!.";
 
// Send the 404 header 
header("HTTP/1.0 404 Not Found");
 
 
// Finally, leave it all to the rendering phase of Tahiti.
include(TAHITI_THEME_PATH);