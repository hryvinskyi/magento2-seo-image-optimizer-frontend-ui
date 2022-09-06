<?php

declare(strict_types=1);

namespace Hryvinskyi\SeoImageOptimizerFrontendUi\Plugin;

use Hryvinskyi\SeoImageOptimizerApi\Model\ConfigInterface;
use Hryvinskyi\SeoImageOptimizerApi\Model\Convertor\ConvertorListing;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable;

class AddOptimizedImagesToConfigurableProductGallery
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
     * @param Configurable $subject
     * @param string $galleryImagesJson
     * @return string
     */
    public function afterGetJsonConfig(Configurable $subject, string $galleryImagesJson): string
    {
        if ($this->config->isEnabled() === false) {
            return $galleryImagesJson;
        }

        $images = $this->serializer->unserialize($galleryImagesJson);

        if (isset($images['images']) === false) {
            return $galleryImagesJson;
        }

        foreach ($images['images'] as $id => $imagesData) {
            foreach ($imagesData as $imageDataIndex => $imageData) {
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

                $imagesData[$imageDataIndex] = $imageData;
            }

            $images['images'][$id] = $imagesData;
        }

        return $this->serializer->serialize($images);
    }
}
