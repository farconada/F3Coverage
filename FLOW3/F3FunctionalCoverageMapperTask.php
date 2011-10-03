<?php
require_once 'phing/Task.php';
/**
 * @author Fernando Arconada fernando.arconada@gmail.com
 * Date: 3/10/11
 * Time: 10:32
 */

class F3FunctionalCoverageMapperTask extends Task
{
    /**
     * @var string The FLOW3 Package name
     */
    private $packageName;

    /**
     * @var string The FLOW3 Package absolute path
     */
    private $packagePath;

    /**
     * @var string Clover file name
     */
    private $cloverFile;

    /**
     * Maps a FLOW3 cache file name to the original package filename
     *
     * @param string $flow3Filename flow3 file in cache
     * @return string the filename mapped
     */
    private function mapFilename($flow3Filename){
        // If this a file aoutside the FLOW3 cache
        if(!(strpos($flow3Filename,$this->packagePath) === FALSE)){
            return $flow3Filename;
        }
        //remove the _Original suffix
        $mappedFilename = str_replace('_Original','',$flow3Filename);
        //remove the package name prefix from the file
        $mappedFilename = str_replace(str_replace('.','_',$this->packageName).'_','',$mappedFilename);
        // basename of the file
        $mappedFilename = substr($mappedFilename,strrpos($mappedFilename,'/'));
        // convert the filename in a path
        $mappedFilename = str_replace('_','/',$mappedFilename);

        // return the absolute path of the file
        return $this->packagePath. 'Classes' . $mappedFilename;
        
    }

    /**
     * The main entry point method.
     */
    public function main()
    {
        $objDOM = new DOMDocument();
        $objDOM->load($this->cloverFile);

        // for instead of foreach. see: http://www.geeksengine.com/article/xml-removechild.html
        $files = $objDOM->getElementsByTagName('file');
        $length = $files->length;
        for ($i = $length - 1; $i >= 0; $i--) {
            $file = $files->item($i);
            $class = $file->getElementsByTagName('class');
            if ($class->item(0)) {
                $package = $class->item(0)->getAttribute('package');
                if ($package != $this->packageName) {
                    // this file is not part of the package
                    $parent = $file->parentNode;
                    $parent->removeChild($file);
                }else{
                    //clean class name in <class name="">
                    $className = $class->item(0)->getAttribute('name');
                    $class->item(0)->setAttribute('name',str_replace('_Original','',$className));
                    //clean and map the filename
                    $mappedFilename = $this->mapFilename($file->getAttribute('name'));
                    $file->setAttribute('name',$mappedFilename);
                }
                
            } else {
                // if a file dont have package then remove
                $parent = $file->parentNode;
                $parent->removeChild($file);
            }
        }
        
        $objDOM->save($this->cloverFile);
    }

    /**
     * @param string $cloverFile
     */
    public function setCloverFile($cloverFile)
    {
        $this->cloverFile = $cloverFile;
    }


    /**
     * @param string $packageName
     */
    public function setPackageName($packageName)
    {
        $this->packageName = $packageName;
    }


    /**
     * @param string $packagePath
     */
    public function setPackagePath($packagePath)
    {
        if(substr($packagePath,-1) != '/'){
            $packagePath = $packagePath . '/';
        }
        $this->packagePath = $packagePath;
    }

}
