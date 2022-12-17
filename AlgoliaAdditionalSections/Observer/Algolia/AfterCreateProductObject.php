<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Develo\AlgoliaAdditionalSections\Observer\Algolia;

use Magento\Catalog\Model\ProductFactory;
use Magento\Store\Model\StoreManagerInterface;

class AfterCreateProductObject implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var ProductFactory
     */
    protected ProductFactory $_productLoader;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        ProductFactory $_productLoader,
        StoreManagerInterface $storeManager
    ) {
        $this->_productLoader = $_productLoader;
        $this->storeManager = $storeManager;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {

        $data = $observer->getData('custom_data');
        $product = $observer->getData('productObject');
        $resource = $product->getResource();
        $tipoFarmaco = $resource->getAttributeRawValue($product->getId(), 'tipo_farmaco', $this->getStoreId());

        if( $tipoFarmaco ) {

            if( $tipoFarmaco === "ETI" || $tipoFarmaco === "ETV" ) {
                $data->setData('purchasableWithoutPrescription', false);
            } else {
                $data->setData('purchasableWithoutPrescription', true);
            }

        }
    
    }

    public function getStoreId() {
        $storeId = $this->storeManager->getStore()->getId();
    }

}

