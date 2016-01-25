<?php

if (!defined('TYPO3_MODE'))
    die('Access denied.');

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][] =
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('cdsrc_sass') . 'Classes/Hooks/PageRenderer.php:CDSRC\\CdsrcSass\\Hooks\\PageRenderer->execute';

if (TYPO3_MODE === 'BE') {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] =
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('cdsrc_sass') . 'Classes/Hooks/ClearCache.php:CDSRC\\CdsrcSass\\Hooks\\ClearCache->clearCachePostProc';
}
?>