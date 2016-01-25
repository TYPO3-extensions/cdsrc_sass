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
use CDSRC\CdsrcSass\Parser\Exception\InvalidSassGemPathException;
use CDSRC\CdsrcSass\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Utility\CommandUtility;

class SassGemParser implements ParserInterface {

	protected $sass;

	public function __construct() {
		$returnValue = 0;
		$this->sass  = ConfigurationUtility::getPathToSassGem();
	}

	/**
	 * Parse file as SCSS
	 *
	 * @param string $file
	 *
	 * @return string
	 */
	public function parseScss( $file ) {
		return $this->parse( $file );
	}

	/**
	 * Parse file as SASS
	 *
	 * @param string $file
	 *
	 * @return string
	 */
	public function parseSass( $file ) {
		return $this->parse( $file );
	}

	protected function parse( $file ) {
		if ( strlen( $this->sass ) === 0 ) {
			throw new InvalidSassGemPathException( 'Path to Sass GEM must be specified!', 1453713105 );
		}
		$output      = array();
		$returnValue = 0;
		$outputStyle = $this->getOutputStyle();
		$commandLine = $this->sass . ' -C ' . ( $outputStyle !== null ? ' --style ' . $outputStyle . ' ' : '' ) . $file;
		exec( $commandLine, $output, $returnValue );
		$result = implode( "\n", $output );
		if ( $returnValue > 0 ) {
			throw new ParsingException( 'Unable to parse file with Sass Gem: ' . $result, 1453718872 );
		}

		return $result;
	}

	/**
	 * Get output style
	 *
	 * @return string
	 */
	protected function getOutputStyle() {
		switch ( ConfigurationUtility::getOutputStyle() ) {
			case ConfigurationUtility::STYLE_NESTED:
				return 'nested';
			case ConfigurationUtility::STYLE_COMPRESSED:
				return 'compressed';
			case ConfigurationUtility::STYLE_COMPACT:
				return 'compact';
			case ConfigurationUtility::STYLE_EXPANDED:
				return 'expanded';
		}

		return null;
	}
}