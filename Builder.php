<?php
/**
 * Base class for any code builders/generators for Soup
 *
 * @package     Soup
 * @subpackage  Builder
 * @link        https://github.com/canerdogan/Soup
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @since       1.0
 * @version     1.0
 * @author      Can Erdogan <can@canerdogan.net>
 */
class Soup_Builder
{
    /**
     * Special function for var_export()
     * The normal code which is returned is malformed and does not follow Soup standards
     * So we do some string replacing to clean it up
     *
     * @param string $var
     * @return void
     */
    public function varExport($var)
    {
        $export = var_export($var, true);
        $export = str_replace("\n", PHP_EOL . str_repeat(' ', 50), $export);
        $export = str_replace('  ', ' ', $export);
        $export = str_replace('array (', 'array(', $export);
        $export = str_replace('array( ', 'array(', $export);
        $export = str_replace(',)', ')', $export);
        $export = str_replace(', )', ')', $export);
        $export = str_replace('  ', ' ', $export);

        return $export;
    }
}