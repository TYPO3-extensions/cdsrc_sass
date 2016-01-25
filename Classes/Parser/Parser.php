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

class Parser {

	/**
	 * @var \CDSRC\CdsrcSass\Parser\ParserInterface
	 */
	protected $parser;

	/**
	 * Parser constructor.
	 */
	public function __construct() {
		switch(ConfigurationUtility::getCompiler()){
			case ConfigurationUtility::COMPILER_PHPSASS:
				$this->parser = new PhpSassParser(array('vendor_properties' => ConfigurationUtility::isEnableCustomVendorProperties()));
				break;
			case ConfigurationUtility::COMPILER_SCSSPHP:
				$this->parser = new ScssPhpParser();
				break;
			case ConfigurationUtility::COMPILER_SASSGEM:
				$this->parser = new SassGemParser();
				break;
		}
	}

	/**
	 * @param $file
	 *
	 * @return null|string
	 */
	public function parse( $file ) {
		$extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
		switch($extension){
			case 'scss':
				return $this->parser->parseScss($file);
			case 'sass':
				return $this->parser->parseSass($file);
		}
		return null;
	}

	/**
	 * @return ParserInterface
	 */
	public function getParser() {
		return $this->parser;
	}


}