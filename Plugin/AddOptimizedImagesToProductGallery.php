<?php

declare(strict_types=1);

namespace Hryvinskyi\SeoImageOptimizerFrontendUi\Plugin;

use Hryvinskyi\SeoImageOptimizerApi\Model\ConfigInterface;
use Hryvinskyi\SeoImageOptimizerApi\Model\Convertor\ConvertorListing;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Catalog\Block\Product\View\Gallery;

class AddOptimizedImagesToProductGallery
{
    private ConfigInterface $config;
    private SerializerInterface $serializer;
    private ConvertorListing $convertorListing;

    public function __construct(
        ConfigInterface $config,
        SerializerInterface $serializer,
        ConvertorListing $convertorListing
    ) {
        $this->config = $config;
        $this->serializer = $serializer;
        $this->convertorListing = $convertorListing;
    }

    /**
     * @param Gallery $subject
     * @param string $galleryImagesJson
     * @return string
     */
    public function afterGetGalleryImagesJson(Gallery $subject, string $galleryImagesJson): string
    {
        if ($this->config->isEnabled() === false) {
            return $galleryImagesJson;
        }

        $images = $this->serializer->unserialize($galleryImagesJson);
        foreach ($images as $id => $imageData) {
            foreach (['thumb', 'img', 'full'] as $imageType) {
                if (empty($imageData[$imageType])) {
                    continue;
                }

                foreach ($this->convertorListing->getConvertors() as $convertor) {
                    if ($outputUrl = $convertor->execute($imageData[$imageType])) {
                        $imageData[$imageType . '_' . $convertor->imageType()] = $outputUrl;
                    }
                }
            }
            $images[$id] = $imageData;
        }

        return $this->serializer->serialize($images);
    }
}
