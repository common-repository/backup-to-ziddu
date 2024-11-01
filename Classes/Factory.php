<?php
/**
 * A class with functions the perform a backup of WordPress
 *
  */
class WPB2Z_Factory
{
    private static
        $objectCache = array(),
        $aliases = array();

    private static function getClassName($name)
    {
        if (isset(self::$aliases[$name])) {
            $name = self::$aliases[$name];
        }

        $class = '';
        foreach (explode('-', $name) as $bit) {
            $class .= '_' . ucfirst($bit);
        }

        return 'WPB2Z' . $class;
    }

    public static function db()
    {
        if (!isset(self::$objectCache['WPDB'])) {
            global $wpdb;

            if ($wpdb) {
                $wpdb->hide_errors();
            }

          self::$objectCache['WPDB'] = $wpdb;
        }

       return self::$objectCache['WPDB'];
    }

    public static function get($name)
    {
        $className = self::getClassName($name);

        if (!class_exists($className)) {
            return null;
        }

        if (!isset(self::$objectCache[$className])) {
            self::$objectCache[$className] = new $className();
        }

        return self::$objectCache[$className];
    }

   }

?>
