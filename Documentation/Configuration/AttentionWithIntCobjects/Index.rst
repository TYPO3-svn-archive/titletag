.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.


.. include:: ../../Includes.txt

.. highlight:: typoscript

==============================
Attention with \_INT cObjects
==============================

Since Version 0.2.0 of titletag it supports \*\_INT cObjects in the
title generation config (although this is **bad practice** and should only be
used in special cases).

A title is masked through htmlspecialchars() by TYPO3 page rendering.
But in TYPO3 **before 6.2**, a \*\_INT cObject is rendered **after** the page
rendering and **not** htmlspecialchared. So please make
sure, you mask the output of any \_INT cObject yourself in TYPO3 < 6.2!

Consider this example (I know there might be more concrete scenarios - it's just an example..):

::

   plugin.tx_titletag.overridePagetitle = COA_INT
   plugin.tx_titletag.overridePagetitle {
      10 = TEXT
      10.value = The current timestamp is
      20 = TEXT
      20.data = date:U
      20.stdWrap.noTrimWrap = | >>|<<|
   }

Will result in this title (on page #1):

::

  The current timestamp is >>1403112716<<

Better do it this way:

::

   plugin.tx_titletag.overridePagetitle = COA_INT
   plugin.tx_titletag.overridePagetitle {
      10 = TEXT
      10.value = The current timestamp is
      20 = TEXT
      20.data = date:U
      20.stdWrap.noTrimWrap = | >>|<<|
      stdWrap.htmlSpecialChars = 1
   }

This will generate a correct output:

::

  The current timestamp is &gt;&gt;1403112716&lt;&lt;

