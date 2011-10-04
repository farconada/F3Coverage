<?php
require_once 'phing/Task.php';
/**
 * Created by JetBrains PhpStorm.
 * User: fernando
 * Date: 4/10/11
 * Time: 23:13
 * To change this template use File | Settings | File Templates.
 */

/**
 * Converts a Clover code covergae file to Xdebug coverage format
 */
class CoverageClover2XdebugTask extends Task
{
    /**
     * @var string $cloverFile File to convert From
     */
    private $cloverFile;

    /**
     * @var string $xdebugFile File to convert To
     */
    private $xdebugFile;


    /**
     * @param string $cloverFile
     */
    public function setCloverFile($cloverFile)
    {
        $this->cloverFile = $cloverFile;
    }


    /**
     * @param string $xdebugFile
     */
    public function setXdebugFile($xdebugFile)
    {
        $this->xdebugFile = $xdebugFile;
    }

    /**
     * The main entry point method.
     */
    public function main()
    {
        $objDOM = new DOMDocument();
        $objDOM->load($this->cloverFile);

        $ficheros = $objDOM->getElementsByTagName('file');

        $report = array();

        foreach ($ficheros as $fichero) {
            $filename = $fichero->getAttribute('name');
            $lines = $fichero->getElementsByTagName('line');
            foreach ($lines as $line) {
                $count = $line->getAttribute('count');
                if ($count > 0) {
                    $linenum = $line->getAttribute('num');
                    $report[$filename][$linenum] = $count + 0;
                }
            }


        }

        file_put_contents($this->xdebugFile, serialize($report));
    }
}
