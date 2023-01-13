<?php
/**
 * User: marcosamano
 * Date: 19/07/18
 * Time: 1:52 PM
 */

require __DIR__.'/../../../vendor/autoload.php';

use \MwbExporter\Formatter\Doctrine2\Annotation\Formatter;

$setup = array(
    Formatter::CFG_USE_LOGGED_STORAGE        => false,
    Formatter::CFG_INDENTATION               => 4,
    Formatter::CFG_FILENAME                  => '%entity%.%extension%',
    Formatter::CFG_ANNOTATION_PREFIX         => 'ORM\\',
    Formatter::CFG_BUNDLE_NAMESPACE          => 'MyBundle',
    Formatter::CFG_ENTITY_NAMESPACE          => 'Entity',
    Formatter::CFG_REPOSITORY_NAMESPACE      => '',
    Formatter::CFG_AUTOMATIC_REPOSITORY      => true,
    Formatter::CFG_SKIP_GETTER_SETTER        => false,
);

$outputType = 'file';// 'zip';
$target = 'doctrine2-annotationsf3';
//$target = 'doctrine2-annotationsf4';
// lets do it
try {
    // lets stop the time
    $end = microtime(true);
    $filename = __DIR__.'/data/sakila.mwb';
    $outDir   = __DIR__.'/result';
    $logFile  = $outDir.'/log.txt';
    $bootstrap = new Ast\MwbExporterExtra\Bootstrap();
    $formatter = $bootstrap->getFormatter($target);
    $formatter->setup(array_merge(array(\MwbExporter\Formatter\Formatter::CFG_LOG_FILE => $logFile), $setup));
    $document  = $bootstrap->export($formatter, $filename, $outDir,$outputType);


    // show the time needed to parse the mwb file
    $end = microtime(true);

    if ($document) {
        echo sprintf("<h1>%s</h1>\n", $document->getFormatter()->getTitle());

        // show some information
        echo "<h2>Information:</h2>\n";
        echo "<ul>\n";
        echo sprintf("<li>Filename: %s</li>\n", basename($document->getWriter()->getStorage()->getResult()));
        echo sprintf("<li>Memory usage: %0.3f MB</li>\n", (memory_get_peak_usage(true) / 1024 / 1024));
        echo sprintf("<li>Time: %0.3f second(s)</li>\n", $time);
        echo "</ul>\n";

        // show a simple text box with the output
        echo "<h2>Result:</h2>\n";
        echo "<textarea cols=\"100\" rows=\"50\">\n";
        echo $document->getWriter()->getStorage()->getLogs()."\n";
        echo "</textarea>\n";
    } else {
        echo "<p>Export not performed, please review your code.</p>\n";
    }

} catch (Exception $e) {
    echo "<h2>Error:</h2>\n";
    echo "<textarea cols=\"100\" rows=\"5\">\n";
    echo $e->getMessage()."\n";
    echo "</textarea>\n";
}