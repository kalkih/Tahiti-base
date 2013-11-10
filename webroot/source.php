<?php 
/**
 * This is a Tahiti pagecontroller.
 *
 */
// Include the essential config-file which also creates the $tahiti variable with its defaults.
include(__DIR__.'/config.php'); 

// Add style for csource
$tahiti['stylesheets'][] = 'css/source.css';
 
 // Create the object to display sourcecode
//$source = new CSource();
$source = new CSource(array('secure_dir' => '..', 'base_dir' => '..'));
 
// Do it and store it all in variables in the Tahiti container.
$tahiti['title'] = "Visa källkod";

$tahiti['header'] = <<<EOD
<img class='sitelogo' src='img/tahiti.png' alt='Tahiti Logo'/>
<span class='sitetitle'>Tahiti webbtemplate</span>
<span class='siteslogan'>Framework för webbutveckling med PHP</span>
EOD;

$tahiti['main'] = "<h1>Visa källkod</h1>\n" . $source->View();

$tahiti['footer'] = <<<EOD
<footer><span class='sitefooter'>Copyright (c) Kalle Kihlström (me@kalkih.se) | <a href='http://validator.w3.org/unicorn/check?ucn_uri=referer&amp;ucn_task=conformance'>Unicorn</a></span></footer>
EOD;

// Finally, leave it all to the rendering phase of Tahiti.
include(TAHITI_THEME_PATH);