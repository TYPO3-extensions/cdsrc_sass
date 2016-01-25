<?php

namespace CDSRC\CdsrcSass\Parser;

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

FileUtility::loadDependencies( 'phpsass/SassLoader.php' );

class PhpSassParser extends \SassParser implements ParserInterface {

	/**
	 * vendor_properties:
	 * If enabled a property need only be written in the standard form and vendor
	 * specific versions will be added to the style sheet.
	 *
	 * @var mixed array: vendor properties, merged with the built-in vendor
	 * properties, to automatically apply.
	 * Boolean true: use built in vendor properties.
	 *
	 * Defaults to vendor_properties disabled.
	 * @see _vendorProperties
	 */
	public $vendor_properties = array();

	/**
	 * Defines the build-in vendor properties
	 *
	 * @var array built-in vendor properties
	 * @see vendor_properties
	 */
	private $_vendorProperties = array(
		'border-radius'              => array(
			'-moz-border-radius',
			'-webkit-border-radius',
			'-khtml-border-radius'
		),
		'border-top-right-radius'    => array(
			'-moz-border-radius-topright',
			'-webkit-border-top-right-radius',
			'-khtml-border-top-right-radius'
		),
		'border-bottom-right-radius' => array(
			'-moz-border-radius-bottomright',
			'-webkit-border-bottom-right-radius',
			'-khtml-border-bottom-right-radius'
		),
		'border-bottom-left-radius'  => array(
			'-moz-border-radius-bottomleft',
			'-webkit-border-bottom-left-radius',
			'-khtml-border-bottom-left-radius'
		),
		'border-top-left-radius'     => array(
			'-moz-border-radius-topleft',
			'-webkit-border-top-left-radius',
			'-khtml-border-top-left-radius'
		),
		'box-shadow'                 => array( '-moz-box-shadow', '-webkit-box-shadow' ),
		'box-sizing'                 => array( '-moz-box-sizing', '-webkit-box-sizing' ),
		'opacity'                    => array( '-moz-opacity', '-webkit-opacity', '-khtml-opacity' ),
	);

	/**
	 * Constructor.
	 * Sets parser options
	 *
	 * @param array $options
	 *
	 * @return PhpSassParser
	 */
	public function __construct( $options = array() ) {
		parent::__construct( $options );
		if ( ! empty( $options['vendor_properties'] ) ) {
			if ( $options['vendor_properties'] === true ) {
				$this->vendor_properties = $this->_vendorProperties;
			} elseif ( is_array( $options['vendor_properties'] ) ) {
				$this->vendor_properties = array_merge( $this->vendor_properties, $this->_vendorProperties );
			}
		}
	}

	/**
	 * Return vendor properties
	 *
	 * @return array
	 */
	public function getVendor_properties() {
		return $this->vendor_properties;
	}

	/**
	 * Return options with vendor properties
	 *
	 * @return array
	 */
	public function getOptions() {
		$options                      = parent::getOptions();
		$options['vendor_properties'] = $this->vendor_properties;

		return $options;
	}

	/**
	 * Parse file as SCSS
	 *
	 * @param string $file
	 *
	 * @return string
	 */
	public function parseScss( $file ) {
		$this->syntax = \SassFile::SCSS;
		$this->setOutputStyle();

		return $this->toCss( $file );
	}

	/**
	 * Parse file as SASS
	 *
	 * @param string $file
	 *
	 * @return string
	 */
	public function parseSass( $file ) {
		$this->syntax = \SassFile::SASS;
		$this->setOutputStyle();

		return $this->toCss( $file );
	}

	/**
	 * Set output style
	 */
	protected function setOutputStyle() {
		switch ( ConfigurationUtility::getOutputStyle() ) {
			case ConfigurationUtility::STYLE_NESTED:
				$this->style = \SassRenderer::STYLE_NESTED;
				break;
			case ConfigurationUtility::STYLE_COMPRESSED:
				$this->style = \SassRenderer::STYLE_COMPRESSED;
				break;
			case ConfigurationUtility::STYLE_COMPACT:
				$this->style = \SassRenderer::STYLE_COMPACT;
				break;
			case ConfigurationUtility::STYLE_EXPANDED:
				$this->style = \SassRenderer::STYLE_EXPANDED;
				break;
		}
	}
}

?>
