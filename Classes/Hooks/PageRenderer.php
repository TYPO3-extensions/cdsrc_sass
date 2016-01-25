<?php

namespace CDSRC\CdsrcSass\Hooks;

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Toscanelli Matthias <m.toscanelli@code-source.ch>
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

use CDSRC\CdsrcSass\Utility\ConfigurationUtility;
use CDSRC\CdsrcSass\Utility\FileUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PageRenderer {

	/**
	 * @var string
	 */
	protected $cssPath = 'typo3temp/sass/css/';

	/**
	 * @var int
	 */
	protected $cacheAge = 604800; // 1 week

	/**
	 * @var bool
	 */
	protected $devMode = false;

	/**
	 * @var \CDSRC\CdsrcSass\Parser\Parser
	 */
	protected $parser = null;

	public function __construct() {
		$this->cssPath = ConfigurationUtility::getCssPath();
		$this->devMode = ConfigurationUtility::isDevelopmentMode();

		$this->parser = GeneralUtility::makeInstance( 'CDSRC\\CdsrcSass\\Parser\\Parser');
		FileUtility::clearOldFiles();
	}

	/**
	 * Execute hook on CSS files
	 *
	 * @param array $config
	 */
	public function execute( &$config ) {
		if ( is_array( $config['cssFiles'] ) ) {
			$cssFiles = array();
			foreach ( $config['cssFiles'] as $val ) {
				$val['file']              = $this->toCSS( $val['file'] );
				$cssFiles[ $val['file'] ] = $val;
			}
			$config['cssFiles'] = $cssFiles;
		}
	}

	/**
	 * Return a preformated comment for exceptions
	 *
	 * @param \Exception $exception
	 * @param string $file
	 *
	 * @return string
	 */
	protected function renderException( \Exception $exception, $file ) {
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
	 * Convert Sass/Scss to CSS and return the new reference
	 *
	 * @param string $file
	 *
	 * @return string
	 */
	protected function toCSS( $file ) {
		$match = array();
		$cssFile = FileUtility::getFinalName($file);
		if($cssFile !== null){
			if ( isset( $GLOBALS['TT'] ) ) {
				$GLOBALS['TT']->push( 'SCSS|SASS to CSS', '...' . substr( $file, - 30 ) );
			}
			$fullPath = PATH_site . $file;
			$cssFullPath  = PATH_site . $cssFile;
			if ( ! is_file( $cssFullPath ) || $this->isFileInDevMod( $fullPath ) ) {
				try {
					// Fix relative image relative path
					$newRelativePath = str_repeat( '../', count( GeneralUtility::trimExplode( '/', $this->cssPath ) ) ) . rtrim( dirname( $file ), '/' ) . '/';
					$cssContent      = preg_replace(
							'/(url\([^\.\)\/]*)\.\./i', '$1' . $newRelativePath . '..', $this->parser->parse( $fullPath ) );
				} catch ( Exception $e ) {
					$cssContent = $this->renderException( $e, $file );
				}
				GeneralUtility::writeFile( $cssFullPath, $cssContent );
				if ( is_file( $cssFullPath ) ) {
					$file = $cssFile;
				}
			} else {
				$file = $cssFile;
			}
			if ( isset( $GLOBALS['TT'] ) ) {
				$GLOBALS['TT']->pull();
			}
		}

		return $file;
	}

	/**
	 * Check if file start with comment "//debug" to force rendering
	 * NOTICE: Extension must be in debug mode to use this
	 *
	 * @param string $file
	 *
	 * @return boolean
	 */
	private function isFileInDevMod( $file ) {
		if ( $this->devMode && is_file( $file ) ) {
			if ( ( $handle = fopen( $file, 'r' ) ) !== false ) {
				$line = fgets( $handle );
				fclose( $handle );

				return preg_match( '/^\/\/dev/', $line ) ? true : false;
			}
		}

		return false;
	}

}

?>
