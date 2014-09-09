.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.


.. include:: ../../Includes.txt

.. highlight:: typoscript

==============================
Reference
==============================

Constants
^^^^^^^^^

.. ### BEGIN~OF~TABLE ###

.. container:: table-row

   Property
         enable
   
   Data type
         boolean
   
   Description
         En-/disable the titletag extension
   
   Default
         0


.. ###### END~OF~TABLE ######


Setup
^^^^^

Extended features for CONFIG
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

For detailed options description, visit the official typoscript
reference at `http://docs.typo3.org/typo3cms/TyposcriptReference/Setup/Config/Index.html
<http://docs.typo3.org/typo3cms/TyposcriptReference/Setup/Config/Index.html>`_

.. ### BEGIN~OF~TABLE ###

.. container:: table-row

   Property
         noPageTitle
   
   Data type
         boolean/stdWrap
   
   Description
         Extended functions: Option is now a stdWrap also
   
   Default
         -

.. container:: table-row

   Property
         pageTitleFirst
   
   Data type
         boolean/stdWrap
   
   Description
         Extended functions: Option is now a stdWrap also
   
   Default
         -

.. container:: table-row

   Property
         pageTitleSeparator
   
   Data type
         string/stdWrap
   
   Description
         Extended functions: Option is now a stdWrap also
         
         Special: adds this feature to earlier TYPO3 versions, it was
         officially added in version 4.7
   
   Default
         -

.. container:: table-row

   Property
         tx\_titletag\_enable
   
   Data type
         boolean
   
   Description
         En-/disable the titletag extension
   
   Default
         {$plugin.tx\_titletag.enable}


.. ###### END~OF~TABLE ######


Example
"""""""

::

   config.pageTitleSeparator = |
   config.pageTitleSeparator.noTrimWrap = | | |


Plugin setup
~~~~~~~~~~~~

.. ### BEGIN~OF~TABLE ###

.. container:: table-row

   Property
         overridePagetitle
   
   Data type
         string / stdWrap
   
   Description
         Overrides the title of the current page record (but not the site title
         from the template record)
   
   Default
         -

.. container:: table-row

   Property
         forceTitle
   
   Data type
         string / stdWrap
   
   Description
         Overrides the whole content of the title tag
   
   Default
         -

.. container:: table-row

   Property
         pagetitle_stdWrap
   
   Data type
         stdWrap
   
   Description
         stdWrap that is applied to the plain page title. This does not affect
         the value of page.title nor any setting in config.pageTitleFirst,
         overridePageTitle or forceTitle
   
   Default
         -

.. container:: table-row

   Property
         sitetitle_stdWrap
   
   Data type
         stdWrap
   
   Description
         stdWrap that is applied to the plain site title. This does not affect
         the value of page.title nor any setting in config.pageTitleFirst,
         overridePageTitle or forceTitle
   
   Default
         -

.. ###### END~OF~TABLE ######


Example
"""""""
::

   # Force the string 'Welcome to my homepage!' on page #1
   plugin.tx_titletag.forceTitle.cObject = TEXT
   plugin.tx_titletag.forceTitle.cObject {
       value = Welcome to my homepage!
       if.value = 1
       if.equals.data = TSFE:id
   }
   
   # Append the string 'DETAILS AND SPECS' to the page title for direct subpages
   # of page #2 (which might be a product overview e.g)
   plugin.tx_titletag.pagetitle_stdWrap {
       noTrimWrap = || DETAILS AND SPECS|
       if {
           value = 2
           equals.data = page:pid
       }
   }
