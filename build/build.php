<?php
require('config.php');

define('SPROCKETIZED', './js/tmp.' . VERSION . '.js');
define('PRODUCTION', './js/jsbin-' . VERSION . '.js');

// sprocketize the code in to _jsbin.VERSION.js
require_once('lib/sprockets/sprocket.php');

echo "Sprocketizing...\n";

$filePath = './js/jsbin.js';
// prepare sprocket
$sprocket = new Sprocket($filePath, array(    
  'contentType' => '', // keeps debug quiet
  'baseUri' => '../js',
  'baseFolder' => array('./js/vendor', './js/vendor/codemirror'),
  'assetFolder' => '..',
  'debugMode' => true, // forces to always show
  'autoRender' => false
));

// having to hack the source path to get it work properly.
// $sprocket->filePath = '.' . $sprocket->filePath);

// concat complete
echo "Rendering...\n";
$js = $sprocket->render(true);

// write concat to js dir
echo "Writing concatenated file...\n";
file_put_contents(SPROCKETIZED, $js);

// neeed to build the base files for codemirror
$filePath = './js/editors/codemirror.js';
// prepare sprocket
$sprocket = new Sprocket($filePath, array(    
  'contentType' => '', // keeps debug quiet
  'baseUri' => '../js',
  'baseFolder' => array('./js/vendor', './js/vendor/codemirror'),
  'assetFolder' => '..',
  'debugMode' => true, // forces to always show
  'autoRender' => false
));

echo "Rendering code mirror basefile...\n";
$js = $sprocket->render(true);

// write concat to js dir
echo "Writing codemirror basefile...\n";
file_put_contents('./js/vendor/codemirror/basefiles.js', $js);


// google compile in to jsbin.VERSION.js
echo "Google compiler compressing...\n";
system('java -jar "./lib/compiler.jar" --js="' . SPROCKETIZED . '" --js_output_file="' . PRODUCTION . '" --warning_level=QUIET');

unlink(SPROCKETIZED);
echo "Compressed: " . PRODUCTION . "\nFile size: " . filesize(PRODUCTION) . " bytes.\n";
?>