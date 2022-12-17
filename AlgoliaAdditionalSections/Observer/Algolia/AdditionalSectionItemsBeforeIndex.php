<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Develo\AlgoliaAdditionalSections\Observer\Algolia;

use Docpeter\Base\Model\ResourceModel\Data\CollectionFactory as DataCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class AdditionalSectionItemsBeforeIndex implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * CollectionFactory
     * @var null|CollectionFactory
     */
    protected $dataCollectionFactory = null;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        DataCollectionFactory $dataCollectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->dataCollectionFactory = $dataCollectionFactory;
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

        $section = $observer->getData('section');

        if( $section["name"] === "brand" ) {

            $record = $observer->getData('record');

            $brandName = $record->getValue();

            if( $this->brandHasLinkAndLogo($brandName) && $this->getBrandLogo($brandName) && $this->getBrandLink($brandName) ) {
                $record->setData('visibleOnFrontend', true);
                $record->setData('logoSrc', $this->getBrandLogo($brandName));
                $record->setData('url', $this->getBrandLink($brandName));
            } else {
                $record->setData('visibleOnFrontend', false);
            }

        }

    }

    /**
     * @return \Magento\Framework\DataObject[]
     */
    public function getCollection()
    {
        /** @var DataCollection $dataCollection */
        $dataCollection = $this->dataCollectionFactory->create();
        $dataCollection->addFieldToSelect('*')->load();
        $this->brandCollection = $dataCollection->getItems();
        return $dataCollection->getItems();
    }

    /**
     * @param $matchName
     * @return string|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBrandLink($matchName)
    {
        if( !$this->brandCollection ) {
            $this->getCollection();
        }

        foreach ($this->brandCollection as $brand) {
            if ($brand['url_key'] && $brand['option_value'] == $matchName) {
                return $this->storeManager->getStore()->getBaseUrl() . $brand['url_key'];
            }
        }
        return false;
    }

    /**
     * @param $matchName
     * @return string|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBrandLogo($matchName)
    {
        if( !$this->brandCollection ) {
            $this->getCollection();
        }

        foreach ($this->brandCollection as $brand) {
            if ($brand['logo_path'] && $brand['option_value'] == $matchName) {
                return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $brand['logo_path'];
            }
        }
        return false;
    }

    /**
     * @param $matchName
     * @return bool
     */
    public function brandHasLinkAndLogo($matchName): bool
    {
        foreach ($this->getCollection() as $collection) {
            if ($collection['option_value'] == $matchName) {
                if (empty($collection['url_key']) || empty($collection['logo_path']) ) {
                    return false;
                }
            }
        }
        return true;
    }

}

