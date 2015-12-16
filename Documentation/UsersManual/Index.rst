.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _users-manual:

Users manual
============

This extension will hook page generation and loop on all CSS file to see if a generation is needed.

The check is done by file extension, only scss and sass file are generated.

Only local files and files relative to TYPO3 base path are generated.

This extension is fully compatible with all TYPO3 feature such as compression, because the generation is done at very begining of page generation before anything else.

Example:

.. code-block:: TypoScript

    # Will be generated
    page.includeCSS.styles = fileadmin/scss/styles.scss
    page.includeCSS.base = EXT:myext/Resources/Public/Styles/base.scss
    
    # Will NOT be generated
    page.includeCSSLibs.styles = fileadmin/scss/styles.scss
    page.includeCSSLibs.base = EXT:myext/Resources/Public/Styles/base.scss