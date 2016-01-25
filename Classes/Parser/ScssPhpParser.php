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

use CDSRC\CdsrcSass\Parser\Exception\InvalidCompilerException;
use CDSRC\CdsrcSass\Utility\ConfigurationUtility;
use CDSRC\CdsrcSass\Utility\FileUtility;
use TYPO3\CMS\Core\FormProtection\Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;

FileUtility::loadDependencies( 'scssphp/scss.inc.php' );

class ScssPhpParser implements ParserInterface {

	/**
	 * @var \Leafo\ScssPhp\Compiler
	 */
	protected $compiler;

	/**
	 * ScssPhpParser constructor.
	 */
	public function __construct() {
		$this->compiler = GeneralUtility::makeInstance( 'Leafo\\ScssPhp\\Compiler' );
	}

	/**
	 * Parse file as SCSS
	 *
	 * @param string $file
	 *
	 * @return string
	 */
	public function parseScss( $file ) {
		$this->compiler->setImportPaths( array( dirname( $file ) ) );
		$this->setOutputStyle();
		return $this->compiler->compile( file_get_contents( $file ) );
	}

	/**
	 * Parse file as SASS
	 *
	 * @param string $file
	 *
	 * @return string
	 */
	public function parseSass( $file ) {
		throw new InvalidCompilerException( 'This compiler cannot compile SASS file format', 1453677036 );
	}


	/**
	 * Set output style
	 */
	protected function setOutputStyle() {
		switch ( ConfigurationUtility::getOutputStyle() ) {
			case ConfigurationUtility::STYLE_NESTED:
				$this->compiler->setFormatter(\Leafo\ScssPhp\Formatter\Nested::class);
				break;
			case ConfigurationUtility::STYLE_COMPRESSED:
				$this->compiler->setFormatter(\Leafo\ScssPhp\Formatter\Compressed::class);
				break;
			case ConfigurationUtility::STYLE_COMPACT:
				$this->compiler->setFormatter(\Leafo\ScssPhp\Formatter\Compact::class);
				break;
			case ConfigurationUtility::STYLE_EXPANDED:
				$this->compiler->setFormatter(\Leafo\ScssPhp\Formatter\Expanded::class);
				break;
		}
	}
}