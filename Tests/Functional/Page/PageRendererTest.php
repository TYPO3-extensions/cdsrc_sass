<?php
namespace CDSRC\CdsrcSass\Tests\Functional\Page;

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

use CDSRC\CdsrcSass\Utility\ConfigurationUtility;
use CDSRC\CdsrcSass\Utility\FileUtility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Tests\FunctionalTestCase;

/**
 * Test case
 */
class PageRendererTest extends FunctionalTestCase {

	/**
	 * @var array
	 */
	protected $testExtensionsToLoad = array( 'typo3conf/ext/cdsrc_sass' );

	/**
	 * @test
	 */
	public function pageRendererParsesSassFilesWithPhpSass() {
		ConfigurationUtility::setCompiler( ConfigurationUtility::COMPILER_PHPSASS );
		$subject = new PageRenderer();
		$cssFile = 'typo3conf/ext/cdsrc_sass/Tests/Functional/Fixtures/scss/source.sass';

		$finalFile = FileUtility::getFinalName($cssFile);

		$subject->addCssFile( $cssFile, 'stylesheet', 'print', '', false, false, 'cdsrcSassWrapBefore|cdsrcSassWrapAfter', true, '|' );
		$expectedCssFileString = '#^.*cdsrcSassWrapBefore<link[^>]+href="' . $finalFile . '(\?[^"]+)?"[^>]+/>cdsrcSassWrapAfter.*$#s';

		$renderedString = $subject->render(PageRenderer::PART_HEADER);

		$this->assertRegExp( $expectedCssFileString, $renderedString );
	}

	/**
	 * @test
	 */
	public function pageRendererParsesSassFilesWithScssPhp() {
		ConfigurationUtility::setCompiler( ConfigurationUtility::COMPILER_SCSSPHP );
		$subject = new PageRenderer();
		$cssFile = 'typo3conf/ext/cdsrc_sass/Tests/Functional/Fixtures/scss/source.sass';

		$finalFile = FileUtility::getFinalName($cssFile);

		$subject->addCssFile( $cssFile, 'stylesheet', 'print', '', false, false, 'cdsrcSassWrapBefore|cdsrcSassWrapAfter', true, '|' );
		$expectedCssFileString = '#^.*cdsrcSassWrapBefore<link[^>]+href="' . $finalFile . '(\?[^"]+)?"[^>]+/>cdsrcSassWrapAfter.*$#s';

		$renderedString = $subject->render(PageRenderer::PART_HEADER);

		$this->assertRegExp( $expectedCssFileString, $renderedString );
	}


}
