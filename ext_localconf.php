<?php
declare(strict_types=1);
defined('TYPO3') or die();
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

ExtensionManagementUtility::addPageTSConfig('
    @import "EXT:content_blocks_bootstrap/Configuration/page.tsconfig"
');

ExtensionManagementUtility::addTypoScript(
    'content_blocks_bootstrap',
    'setup',
    '@import "EXT:content_blocks_bootstrap/Configuration/setup.typoscript"'
);
