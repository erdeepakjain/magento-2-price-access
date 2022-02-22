<?php

namespace Ascure\PriceAccess\Plugin;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;

class ProductAttributeModifier
{
    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    private $authorization;

    public function __construct(
        \Magento\Framework\AuthorizationInterface $authorization
    ) {
        $this->authorization = $authorization;
    }

    public function afterModifyMeta(
        \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\Eav $subject,
        array $meta
    ) {

        $parsedMeta = [];
        foreach ($meta as $groupCode => $group) {
            if ($groupCode === AbstractModifier::DEFAULT_GENERAL_PANEL) {
                if(!$this->canEditPriceInBackend() && isset($group['children']['container_price']['arguments']['data']['config'])) {
                    $group['children']['container_price']['arguments']['data']['config']['disabled'] = true;
                }

                $parsedMeta[$groupCode] = $group;
            } else {
                $parsedMeta[$groupCode] = $group;
            }
        }

        if(!$this->canEditSpecialPriceInBackend() && isset($parsedMeta['advanced-pricing']['children']['container_special_price']['children']['special_price']['arguments']['data']['config'])) {
            $parsedMeta['advanced-pricing']['children']['container_special_price']['children']['special_price']['arguments']['data']['config']['disabled'] = true;
        }

        return $parsedMeta;
    }

    /**
     * @return bool
     */
    private function canEditPriceInBackend(): bool
    {
        return $this->authorization->isAllowed('Ascure_PriceAccess::AccessPrice_Price');
    }

    /**
     * @return bool
     */
    private function canEditSpecialPriceInBackend(): bool
    {
        return $this->authorization->isAllowed('Ascure_PriceAccess::AccessPrice_Special');
    }
}