<?php
namespace CDSRC\CdsrcSass\Tests\Functional\Parser;

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
 */

use CDSRC\CdsrcSass\Parser\PhpSassParser;
use CDSRC\CdsrcSass\Parser\SassGemParser;
use CDSRC\CdsrcSass\Parser\ScssPhpParser;
use CDSRC\CdsrcSass\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Tests\FunctionalTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use CDSRC\CdsrcSass\Parser\Exception\InvalidCompilerException;

/**
 * Test case
 */
class ParserTest extends FunctionalTestCase {

	/**
	 * @var array
	 */
	protected $testExtensionsToLoad = array( 'typo3conf/ext/cdsrc_sass' );

	/**
	 * @var \CDSRC\CdsrcSass\Parser\Parser
	 */
	protected $parserWithPhpSass = null;
	/**
	 * @var \CDSRC\CdsrcSass\Parser\Parser
	 */
	protected $parserWithScssPhp = null;

	/**
	 * @var string
	 */
	protected $source = 'typo3conf/ext/cdsrc_sass/Tests/Functional/Fixtures/scss/source.';

	/**
	 * Set up test
	 */
	public function setUp() {
		parent::setUp();
		ConfigurationUtility::setCompiler( ConfigurationUtility::COMPILER_PHPSASS );
		$this->parserWithPhpSass = GeneralUtility::makeInstance( 'CDSRC\\CdsrcSass\\Parser\\Parser' );
		ConfigurationUtility::setCompiler( ConfigurationUtility::COMPILER_SCSSPHP );
		$this->parserWithScssPhp = GeneralUtility::makeInstance( 'CDSRC\\CdsrcSass\\Parser\\Parser' );
	}

	/**
	 * Test PhpSass parser with a Sass file
	 */
	public function testParserOnSassWithPhpSass() {
		$this->assertEquals(PhpSassParser::class, get_class($this->parserWithPhpSass->getParser()));
		$this->assertEquals(
				preg_replace( '/\t| /', '', trim( file_get_contents( $this->source . 'css' ) ) ),
				preg_replace( '/\t| /', '', trim( $this->parserWithPhpSass->parse( $this->source . 'sass' ) ) )
		);
	}

	/**
	 * Test PhpSass parser with a Scss file
	 */
	public function testParserOnScssWithPhpSass() {
		$this->assertEquals(PhpSassParser::class, get_class($this->parserWithPhpSass->getParser()));
		$this->assertEquals(
				preg_replace( '/\t| /', '', trim( file_get_contents( $this->source . 'css' ) ) ),
				preg_replace( '/\t| /', '', trim( $this->parserWithPhpSass->parse( $this->source . 'scss' ) ) )
		);
	}

	/**
	 * Test PhpSass parser with a Sass file
	 */
	public function testParserOnSassWithScssPhp() {
		$this->assertEquals(ScssPhpParser::class, get_class($this->parserWithScssPhp->getParser()));
		$this->setExpectedException(InvalidCompilerException::class);
		$this->parserWithScssPhp->parse( $this->source . 'sass' );
	}

	/**
	 * Test PhpSass parser with a Scss file
	 */
	public function testParserOnScssWithScssPhp() {
		$this->assertEquals(ScssPhpParser::class, get_class($this->parserWithScssPhp->getParser()));
		$this->assertEquals(
				preg_replace( '/\t| /', '', trim( file_get_contents( $this->source . 'css' ) ) ),
				preg_replace( '/\t| /', '', trim( $this->parserWithScssPhp->parse( $this->source . 'scss' ) ) )
		);
	}
}
