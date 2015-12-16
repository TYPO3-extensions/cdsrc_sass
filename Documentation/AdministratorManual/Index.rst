.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _admin-manual:

Extension configuration
=======================

cdsrc_sass offers some basic inside the Extension Manager. 
Those are described in this chapter. 

To be able to set this configuration, 
switch to the Extension Manager and search for the extension "cdsrc_sass". 

Click on it to see the available settings.


Properties
^^^^^^^^^^

.. container:: ts-properties

	===================================================== ================================================ ===================
	Property                                              Data type                                        Default
	===================================================== ================================================ ===================
	base.css_path_                                          :ref:`t3tsref:data-type-string`                  typo3temp/sass/css
	base.cache_age_                                         :ref:`t3tsref:data-type-integer`                 604800
	base.dev_mode_                                          :ref:`t3tsref:data-type-boolean`                 FALSE
	===================================================== ================================================ ===================

Property details
^^^^^^^^^^^^^^^^ 

.. _base.css_path:

base.css_path_
""""""""""""""""""""""""""""""""

The folder where generated CSS file are stored. 

**This folder must be accessible for public and writable.** 


.. _base.cache_age:

base.cache_age_
""""""""""""""""""""""""""""""""

How many time until generated file are removed from CSS directory. (in seconds) 


.. _base.dev_mode:

base.dev_mode_
""""""""""""""""""""""""""""""""

By default file are generated only if modification date change. When using SASS inclusion, this check is not done.
This option will force generation if the file is prefixed by "//dev"

Example:

.. code-block:: css

    //dev
    .myclass{
        .mysubclass{
        }
    }
