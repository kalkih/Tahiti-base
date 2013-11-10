<?php 
/**
 * This is a Tahiti pagecontroller.
 *
 */
// Include the essential config-file which also creates the $tahiti variable with its defaults.
include(__DIR__.'/config.php'); 
 
 
// Do it and store it all in variables in the Tahiti container.
$tahiti['title'] = "Hello World";
 
$tahiti['header'] = <<<EOD
<img class='sitelogo' src='img/tahiti.png' alt='Tahiti Logo'/>
<span class='sitetitle'>Tahiti webbtemplate</span>
<span class='siteslogan'>Framework för webbutveckling med PHP</span>
EOD;
 
$tahiti['main'] = <<<EOD
<h1>Hej Världen</h1>
<p>Detta är en exempelsida som visar hur Tahiti ser ut och fungerar.</p>
EOD;
 
$tahiti['footer'] = <<<EOD
<footer><span class='sitefooter'>Copyright (c) Kalle Kihlström (me@kalkih.se) | <a href='http://validator.w3.org/unicorn/check?ucn_uri=referer&amp;ucn_task=conformance'>Unicorn</a></span></footer>
EOD;
 
 
// Finally, leave it all to the rendering phase of Tahiti.
include(TAHITI_THEME_PATH);