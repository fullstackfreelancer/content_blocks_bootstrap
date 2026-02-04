<?php
declare(strict_types=1);
defined('TYPO3') or die();
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItemGroup(
    'tt_content',
    'CType',
    'content_blocks_bootstrap',
    'Bootstrap Components',
    'before:default',
);

$GLOBALS['TCA']['tt_content']['types']['bootstrap_cards_subpages']['previewRenderer'] = KOHLERCODE\ContentBlocksBootstrap\Preview\CardsOfSubpages::class;
