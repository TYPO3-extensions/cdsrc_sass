<?php

namespace CDSRC\CdsrcSass\Utility;

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

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FileUtility {


	/**
	 * Keep cache directory clean of old files.
	 *
	 * @throws \TYPO3\CMS\Core\Resource\Exception\InvalidPathException
	 */
	public static function clearOldFiles() {
		$files    = GeneralUtility::getFilesInDir( PATH_site . ConfigurationUtility::getCssPath(), 'css', true );
		$cacheAge = ConfigurationUtility::getCacheAge();
		foreach ( $files as $file ) {
			$filemtime = filemtime( $file );
			if ( $GLOBALS['EXEC_TIME'] - $filemtime > $cacheAge ) {
				unlink( $file );
			}
		}
	}

	/**
	 * Remove all generated css files
	 *
	 * @throws \TYPO3\CMS\Core\Resource\Exception\InvalidPathException
	 */
	public static function clearAllFiles() {
		$files = GeneralUtility::getFilesInDir( PATH_site . ConfigurationUtility::getCssPath(), 'css', true );
		foreach ( $files as $file ) {
			unlink( $file );
		}
	}

	/**
	 * Load dependencies from library
	 * NOTICE: This is used because default ExtensionManagementUtility::extPath() don't work with Functional tests
	 *
	 * @param $file
	 */
	public static function loadDependencies( $file ) {
		require_once __DIR__ . '/../../Resources/Private/Libraries/' . $file;
	}

	/**
	 * @param $file
	 *
	 * @return null|string
	 */
	public static function getFinalName( $file ) {
		if ( is_string( $file ) && preg_match( '/^(.*)\.(sass|scss)$/i', $file, $match ) ) {
			$fullPath = PATH_site . $file;
			if ( is_file( $fullPath ) ) {
				return ConfigurationUtility::getCssPath() . '/' .
				       pathinfo( $fullPath, PATHINFO_FILENAME ) . '_' .
				       substr( md5( $file ), 0, 5 ) . '_' .
				       ConfigurationUtility::getOutputStyle() . '_' .
				       md5_file( $fullPath ) . '.css';
			}
		}

		return null;
	}
}