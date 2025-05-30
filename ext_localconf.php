<?php
declare(strict_types=1);
defined('TYPO3') or die();
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

$versionInformation = GeneralUtility::makeInstance(Typo3Version::class);
// Only include page.tsconfig if TYPO3 version is below 12 so that it is not imported twice.
if ($versionInformation->getMajorVersion() > 13) {
   ExtensionManagementUtility::addPageTSConfig('
      @import "EXT:content_blocks_bootstrap/Configuration/page.tsconfig"
   ');
}
