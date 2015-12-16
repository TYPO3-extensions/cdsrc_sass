<?php

namespace Cdsrc\Sass\Hooks;

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Toscanelli Matthias <mt@accessible.ch>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

use TYPO3\CMS\Core\Utility\GeneralUtility;

require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('cdsrc_sass') . 'Classes/Libs/phpsass/SassFile.php');
require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('cdsrc_sass') . 'Classes/Libs/SassParserExt.php');

class PageRenderer {

    protected $cssPath = 'typo3temp/sass/css/';
    protected $cacheAge = 604800; // 1 week
    protected $devMode = FALSE;
    protected $parserSass = null;
    protected $parserScss = null;

    public function __construct() {
        $extConf = @unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['cdsrc_sass']);

        if (preg_match('/^typo3temp\//', $extConf['css_path'])) {
            $this->cssPath = rtrim($extConf['css_path'], '/');
        }
        if (!is_dir(PATH_site . $this->cssPath)) {
            GeneralUtility::mkdir_deep(PATH_site, $this->cssPath);
        }

        $this->cacheAge = $extConf['cache_age'];

        $this->devMode = (bool) $extConf['dev_mode'];

        $this->parserSass = GeneralUtility::makeInstance('Cdsrc\\Sass\\Libs\\SassParserExt', array('syntax' => \SassFile::SASS, 'vendor_properties' => TRUE));
        $this->parserScss = GeneralUtility::makeInstance('Cdsrc\\Sass\\Libs\\SassParserExt', array('syntax' => \SassFile::SCSS, 'vendor_properties' => TRUE));
        $this->clearOldFile();
    }

    /**
     * Execute hook on CSS files
     * @param array $config
     */
    public function execute(&$config) {
        if (is_array($config['cssFiles'])) {
            $cssFiles = array();
            foreach ($config['cssFiles'] as $val) {
                $val['file'] = $this->toCSS($val['file']);
                $cssFiles[$val['file']] = $val;
            }
            $config['cssFiles'] = $cssFiles;
        }
    }

    /**
     * Keep cache directory clean of old file.
     *
     */
    protected function clearOldFile() {
        $files = GeneralUtility::getFilesInDir(PATH_site . $this->cssPath, 'css', true);
        foreach ($files as $file) {
            $filemtime = filemtime($file);
            if ($GLOBALS['EXEC_TIME'] - $filemtime > $this->cacheAge) {
                unlink($file);
            }
        }
    }

    /**
     * Return a preformated comment for exceptions
     *
     * @param \Exception $exception
     * @param string $file
     * @return string
     */
    protected function renderException(\Exception $exception, $file) {
        return "/**************************************************************\n" .
                " *** \n" .
                " *** EXCEPTION: \n" .
                " ***  Parser has not been able to parse this file:\n" .
                " ***   " . $file . "\n ***\n" .
                " ***  Parser message: \n" .
                " ***   " . $exception->getMessage() . "\n ***\n" .
                " **************************************************************/";
    }

    /**
     * Check if file start with comment "//debug" to force rendering
     * NOTICE: Extension must be in debug mode to use this
     *
     * @param string $file
     * @return boolean
     */
    private function isFileInDevMod($file) {
        if ($this->devMode && is_file($file)) {
            if (($handle = fopen($file, 'r')) !== FALSE) {
                $line = fgets($handle);
                fclose($handle);
                return preg_match('/^\/\/dev/', $line) ? TRUE : FALSE;
            }
        }
        return FALSE;
    }

    /**
     * Convert Sass/Scss to CSS and return the new reference
     *
     * @param string $file
     * @return string
     */
    protected function toCSS($file) {
        $match = array();
        if (is_string($file) && preg_match('/^(.*)\.(sass|scss)$/i', $file, $match)) {
            $fullpath = PATH_site . $file;
            if(is_file($fullpath)){
                $GLOBALS['TT']->push('SCSS to CSS', '...' . substr($file, -30));
                $filename = basename($match[1]);
                $ext = strtolower($match[2]);
                $md5_path = substr(md5($file), 0, 5);
                $md5_file = md5_file($fullpath);
                $cssFile = $this->cssPath . '/' . $filename . '_' . $md5_path . '_' . $md5_file . '.css';
                $cssFull = PATH_site . $cssFile;
                if (!is_file($cssFull) || $this->isFileInDevMod($fullpath)) {
                    try {
                        $parser = 'parser' . ucfirst($ext);
                        // Fix relative image relative path
                        $newRelativePath = str_repeat('../', count(GeneralUtility::trimExplode('/', $this->cssPath))) . rtrim(dirname($file), '/') . '/';
                        $cssContent = preg_replace(
                                '/(url\([^\.\)\/]*)\.\./i', '$1' . $newRelativePath . '..', $this->$parser->toCss($fullpath));
                    } catch (Exception $e) {
                        $cssContent = $this->renderException($e, $file);
                    }
                    GeneralUtility::writeFile($cssFull, $cssContent);
                    if (is_file($cssFull)) {
                        $file = $cssFile;
                    }
                } else {
                    $file = $cssFile;
                }
                $GLOBALS['TT']->pull();
            }
        }
        return $file;
    }

}

?>
