<?php

namespace Cdsrc\Sass\Libs;
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

require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('cdsrc_sass') . 'Classes/Libs/phpsass/SassParser.php');

class SassParserExt extends \SassParser {

    /**
     * vendor_properties:
     * If enabled a property need only be written in the standard form and vendor
     * specific versions will be added to the style sheet.
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
     * @var array built-in vendor properties
     * @see vendor_properties
     */
    private $_vendorProperties = array(
        'border-radius' => array(
            '-moz-border-radius',
            '-webkit-border-radius',
            '-khtml-border-radius'
        ),
        'border-top-right-radius' => array(
            '-moz-border-radius-topright',
            '-webkit-border-top-right-radius',
            '-khtml-border-top-right-radius'
        ),
        'border-bottom-right-radius' => array(
            '-moz-border-radius-bottomright',
            '-webkit-border-bottom-right-radius',
            '-khtml-border-bottom-right-radius'
        ),
        'border-bottom-left-radius' => array(
            '-moz-border-radius-bottomleft',
            '-webkit-border-bottom-left-radius',
            '-khtml-border-bottom-left-radius'
        ),
        'border-top-left-radius' => array(
            '-moz-border-radius-topleft',
            '-webkit-border-top-left-radius',
            '-khtml-border-top-left-radius'
        ),
        'box-shadow' => array('-moz-box-shadow', '-webkit-box-shadow'),
        'box-sizing' => array('-moz-box-sizing', '-webkit-box-sizing'),
        'opacity' => array('-moz-opacity', '-webkit-opacity', '-khtml-opacity'),
    );

    /**
     * Constructor.
     * Sets parser options
     * @param array $options
     * @return SassParser
     */
    public function __construct($options = array()) {
        parent::__construct($options);
        if (!empty($options['vendor_properties'])) {
            if ($options['vendor_properties'] === true) {
                $this->vendor_properties = $this->_vendorProperties;
            } elseif (is_array($options['vendor_properties'])) {
                $this->vendor_properties = array_merge($this->vendor_properties, $this->_vendorProperties);
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
        $options = parent::getOptions();
        $options['vendor_properties'] = $this->vendor_properties;
        return $options;
    }
}

?>
