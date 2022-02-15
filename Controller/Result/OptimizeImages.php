<?php
/**
 * Copyright (c) 2021. All rights reserved.
 * @author: Volodymyr Hryvinskyi <mailto:volodymyr@hryvinskyi.com>
 */

declare(strict_types=1);

namespace Hryvinskyi\SeoImageOptimizerFrontendUi\Controller\Result;

use Hryvinskyi\SeoImageOptimizerApi\Model\ImageParserInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\Layout;

class OptimizeImages
{
    private ImageParserInterface $convertor;

    /**
     * @param ImageParserInterface $convertor
     */
    public function __construct(ImageParserInterface $convertor)
    {
        $this->convertor = $convertor;
    }

    /**
     * @param Layout $subject
     * @param Layout $result
     * @param ResponseInterface $httpResponse
     * @return Layout
     * @noinspection PhpUnusedParameterInspection
     */
    public function afterRenderResult(Layout $subject, Layout $result, ResponseInterface $httpResponse): Layout
    {
        if ($httpResponse instanceof Http) {
            $content = (string)$httpResponse->getContent();
            $httpResponse->setContent($this->convertor->execute($content));
        }

        return $result;
    }
}
