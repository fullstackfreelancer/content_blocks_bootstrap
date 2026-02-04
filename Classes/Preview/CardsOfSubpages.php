<?php
declare(strict_types=1);
namespace KOHLERCODE\ContentBlocksBootstrap\Preview;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use \TYPO3\CMS\Backend\Preview\PreviewRendererInterface;
use TYPO3\CMS\Backend\View\BackendLayout\Grid\GridColumnItem;
use TYPO3\CMS\Core\Domain\Record;
use TYPO3\CMS\Core\Domain\RecordFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\DebugUtility;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Extbase\Service\ImageService;

class CardsOfSubpages implements PreviewRendererInterface{

    public function renderPageModulePreviewHeader(GridColumnItem $item): string
    {
        $record = $item->getRecord();
        // Render a custom header for the content element
        return '<strong>' . htmlspecialchars($item->getRecord()['header']) . '</strong>';
    }

    public function renderPageModulePreviewContent(GridColumnItem $item): string
    {
        $pageRepository = GeneralUtility::makeInstance(PageRepository::class);
        $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        $imageService = GeneralUtility::makeInstance(ImageService::class);

        $languageService = $this->getLanguageService();
        $table = $item->getTable();
        $record = $item->getRecord();
        $recordObj = GeneralUtility::makeInstance(RecordFactory::class)->createResolvedRecordFromDatabaseRow($table, $record);
        $recordType = $recordObj->getRecordType();
        $output = '';
        $output .= $this->linkEditContent($this->generateListForMenuContentTypes($record, $recordType), $record);
        $output .= '<div class="row">';

        $subpages = $pageRepository->getMenu(explode(',', $record['pages'])[0]);

        foreach ($subpages as $page) {
            $media = $fileRepository->findByRelation('pages', 'media', $page['uid']);
            $output .= '<div class="col"><div class="card">';
            //DebugUtility::debug($media, 'recordObj');
            if($media){
                foreach ($media as $fileReference) {
                    $publicUrl = $fileReference->getPublicUrl();
                    try {
                       // Create a processed image with a width of 300px
                       $processedImage = $imageService->applyProcessingInstructions(
                           $fileReference,
                           ['width' => '200'] // 300 pixels, cropped
                       );
                       $thumbnailUrl = $imageService->getImageUri($processedImage);

                       $output .= '<img class="card-img-top" src="' . htmlspecialchars($thumbnailUrl) . '" alt="">';
                   } catch (\Exception $e) {
                       // Handle broken file reference or processing error
                       echo 'Error loading thumbnail: ' . $e->getMessage();
                   }
                }
            }
            $output .= '<div class="card-body">';
            $output .= '<h6 class="card-title fw-bold">' . $page['title'] . '</h6>';
            $output .= '<div class="card-text">' . $page['abstract'] . '</div>';
            $output .= '</div>';
            $output .= '</div></div>';
        }

        $output .= '</div>';

        //DebugUtility::debug($subpages, 'recordObj');
        return $output;
    }

    public function renderPageModulePreviewFooter(GridColumnItem $item): string
    {
        // Optional footer content
        return '';
    }

    public function wrapPageModulePreview(string $previewHeader, string $previewContent, GridColumnItem $item): string
    {
        // Combine header, content, and footer into a full preview box
        return sprintf(
            '<div class="element-preview">
                <div class="element-preview-header mb-2">%s</div>
                <div class="element-preview-content">%s</div>
            </div>',
            $previewHeader,
            $previewContent
        );
    }

    /**
     * Generates a list of selected pages or categories for the menu content types
     *
     * @param array $record row from pages
     */
    protected function generateListForMenuContentTypes(array $record, string $contentType): string
    {
        $table = 'pages';
        $field = 'pages';

        if (trim($record[$field] ?? '') === '') {
            return '';
        }
        $content = '';
        $uidList = explode(',', $record[$field]);
        foreach ($uidList as $uid) {
            $uid = (int)$uid;
            $pageRecord = BackendUtility::getRecord($table, $uid, 'title');
            if ($pageRecord) {
                $content .= '<li class="list-group-item">' . htmlspecialchars($pageRecord['title']) . ' <span class="text-body-secondary">[' . $uid . ']</span></li>';
            }
        }
        return $content ? '<ul class="list-group mb-3">' . $content . '</ul>' : '';
    }

    protected function linkEditContent(string $linkText, array $row, string $table = 'tt_content'): string
    {
        if (empty($linkText)) {
            return $linkText;
        }

        $backendUser = $this->getBackendUser();
        if ($backendUser->check('tables_modify', $table)
            && $backendUser->recordEditAccessInternals($table, $row)
            && (new Permission($backendUser->calcPerms(BackendUtility::getRecord('pages', $row['pid']) ?? [])))->editContentPermissionIsGranted()
        ) {
            $urlParameters = [
                'edit' => [
                    $table => [
                        $row['uid'] => 'edit',
                    ],
                ],
                'returnUrl' => $GLOBALS['TYPO3_REQUEST']->getAttribute('normalizedParams')->getRequestUri() . '#element-' . $table . '-' . $row['uid'],
            ];
            $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
            $url = (string)$uriBuilder->buildUriFromRoute('record_edit', $urlParameters);
            return '<a href="' . htmlspecialchars($url) . '" title="' . htmlspecialchars($this->getLanguageService()->sL('LLL:EXT:backend/Resources/Private/Language/locallang_layout.xlf:edit')) . '">' . $linkText . '</a>';
        }
        return $linkText;
    }

    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

}
