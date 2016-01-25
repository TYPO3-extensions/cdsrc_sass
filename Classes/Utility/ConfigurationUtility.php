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

use TYPO3\CMS\Core\Resource\Exception\InvalidConfigurationException;
use TYPO3\CMS\Core\Resource\Exception\InvalidPathException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConfigurationUtility {

	const COMPILER_PHPSASS = 0;
	const COMPILER_SCSSPHP = 1;
	const COMPILER_SASSGEM = 2;

	const STYLE_NESTED = 0;
	const STYLE_COMPACT = 1;
	const STYLE_COMPRESSED = 2;
	const STYLE_EXPANDED = 3;

	/**
	 * @var array
	 */
	protected static $extConf;

	/**
	 * @var int
	 */
	protected static $compiler = null;

	/**
	 * @var string
	 */
	protected static $cssPath = null;

	/**
	 * @var int
	 */
	protected static $outputStyle = null;

	/**
	 * @var int
	 */
	protected static $cacheAge = null;

	/**
	 * @var bool
	 */
	protected static $developmentMode = null;

	/**
	 * @var bool
	 */
	protected static $enableCustomVendorProperties = null;

	/**
	 * @var string
	 */
	protected static $pathToSassGem = null;

	/**
	 * Default configuration values
	 *
	 * @var array
	 */
	protected static $defaultConfiguration = array(
		'compiler'                        => self::COMPILER_PHPSASS,
		'css_path'                        => 'typo3temp/sass/css/',
		'output_style'                    => self::STYLE_NESTED,
		'cache_age'                       => 604800, // 1 Week
		'dev_mode'                        => false,
		'enable_custom_vendor_properties' => true,
		'path_to_sass_gem'                => '/usr/local/bin/sass',
	);

	/**
	 * Get compiler
	 *
	 * @return int
	 * @throws \TYPO3\CMS\Core\Resource\Exception\InvalidConfigurationException
	 */
	public static function getCompiler() {
		if ( self::$compiler === null ) {
			self::initialize();
			$compiler       = isset( self::$extConf['compiler'] ) ? (int) ( self::$extConf['compiler'] ) : self::$defaultConfiguration['compiler'];
			self::setCompiler($compiler);
		}

		return self::$compiler;
	}

	/**
	 * Set compiler
	 *
	 * @param int $compiler
	 */
	public static function setCompiler( $compiler ) {
		self::$compiler = in_array( (int) $compiler, array(
			self::COMPILER_PHPSASS,
			self::COMPILER_SCSSPHP,
			self::COMPILER_SASSGEM,
		) ) ? (int) $compiler : self::$defaultConfiguration['compiler'];
	}

	/**
	 * Get cache age
	 *
	 * @return int
	 * @throws \TYPO3\CMS\Core\Resource\Exception\InvalidConfigurationException
	 */
	public static function getCacheAge() {
		if ( self::$cacheAge === null ) {
			self::initialize();
			$age            = isset( self::$extConf['cache_age'] ) ? (int) ( self::$extConf['cache_age'] ) : self::$defaultConfiguration['cache_age'];
			self::$cacheAge = $age >= 0 ? $age : 0;
		}

		return self::$cacheAge;
	}

	/**
	 * @return int
	 */
	public static function getOutputStyle() {
		if ( self::$outputStyle === null ) {
			self::initialize();
			$outputStyle       = isset( self::$extConf['output_style'] ) ? (int) ( self::$extConf['output_style'] ) : self::$defaultConfiguration['output_style'];
			self::$outputStyle = in_array( $outputStyle, array(
				self::STYLE_NESTED,
				self::STYLE_COMPACT,
				self::STYLE_COMPRESSED,
				self::STYLE_EXPANDED,
			) ) ? $outputStyle : self::$defaultConfiguration['output_style'];
		}

		return self::$outputStyle;
	}


	/**
	 * Get CSS folder
	 *
	 * @return string
	 * @throws \TYPO3\CMS\Core\Resource\Exception\InvalidConfigurationException
	 * @throws \TYPO3\CMS\Core\Resource\Exception\InvalidPathException
	 */
	public static function getCssPath() {
		if ( self::$cssPath === null ) {
			self::initialize();
			$path = isset( self::$extConf['css_path'] ) ? rtrim( self::$extConf['css_path'], '/' ) : self::$defaultConfiguration['css_path'];
			if ( ! preg_match( '/^typo3temp\//', $path ) ) {
				throw new InvalidPathException( 'CSS path must be configured to be in "typo3temp" folder', 1453311878 );
			}
			if ( ! is_dir( PATH_site . $path ) ) {
				GeneralUtility::mkdir_deep( PATH_site . $path );
			}
			self::$cssPath = $path;
		}

		return self::$cssPath;
	}

	/**
	 * Is developement mode enabled?
	 *
	 * @throws \TYPO3\CMS\Core\Resource\Exception\InvalidConfigurationException
	 */
	public static function isDevelopmentMode() {
		if ( self::$developmentMode === null ) {
			self::initialize();
			self::$developmentMode = isset( self::$extConf['dev_mode'] ) && (bool) ( self::$extConf['dev_mode'] );
		}

		return self::$developmentMode;
	}

	/**
	 * @return boolean
	 */
	public static function isEnableCustomVendorProperties() {
		if ( self::$enableCustomVendorProperties === null ) {
			self::initialize();
			self::$enableCustomVendorProperties = isset( self::$extConf['enable_custom_vendor_properties'] ) && (bool) ( self::$extConf['enable_custom_vendor_properties'] );
		}

		return self::$enableCustomVendorProperties;
	}

	/**
	 * @return string
	 */
	public static function getPathToSassGem() {
		if ( self::$pathToSassGem === null ) {
			self::initialize();
			$path                = isset( self::$extConf['path_to_sass_gem'] ) ? self::$extConf['path_to_sass_gem'] : self::$defaultConfiguration['path_to_sass_gem'];
			self::$pathToSassGem = $path;
		}

		return self::$pathToSassGem;
	}


	/**
	 * Parse extension configuration
	 *
	 * @throws \TYPO3\CMS\Core\Resource\Exception\InvalidConfigurationException
	 */
	protected function initialize() {
		if ( ! self::$extConf ) {
			self::$extConf = unserialize( $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['cdsrc_sass'] );
			if ( ! self::$extConf ) {
				self::$extConf = self::$defaultConfiguration;
			}
		}
	}


}