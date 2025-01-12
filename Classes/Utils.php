<?php
/**
 * A class with functions the get a List of directory
 *
  */
class Utils

{
    public static function listDirectory($dir)
    {
        $result = array();
        $root = scandir($dir);
        foreach($root as $value) {
            if($value === '.' || $value === '..') {
                continue;
            }

            if(is_file("$dir$value")) {
                $result[] = "$dir$value";
                continue;
            }
            if(is_dir("$dir$value")) {
                $result[] = "$dir$value/";
            }
            foreach(self::listDirectory("$dir$value/") as $value) {
                $result[] = $value;
            }
        }

    return $result;
    }





public static function Zip($source, $destination)
{
    if (!extension_loaded('zip') || !file_exists($source)) {
        return false;
    }

    $zip = new ZipArchive();
    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
        return false;
    }

    $source = str_replace('\\', '/', realpath($source));

    if (is_dir($source) === true)
    {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $file)
        {
            $file = str_replace('\\', '/', $file);

            // Ignore "." and ".." folders
            if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
                continue;

            $file = realpath($file);

            if (is_dir($file) === true)
            {
                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
            }
            else if (is_file($file) === true)
            {
                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
            }
        }
    }
    else if (is_file($source) === true)
    {
        $zip->addFromString(basename($source), file_get_contents($source));
    }

    return $zip->close();
}

}
?>
