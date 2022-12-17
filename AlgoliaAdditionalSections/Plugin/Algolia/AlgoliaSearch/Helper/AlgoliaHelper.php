<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Develo\AlgoliaAdditionalSections\Plugin\Algolia\AlgoliaSearch\Helper;

class AlgoliaHelper
{

    public function beforeAddObjects(
        \Algolia\AlgoliaSearch\Helper\AlgoliaHelper $subject,
        $objects,
        $indexName
    ) {
        if( strpos($indexName, 'section_brand') !== false ) {
            foreach( $objects as $key => $brand ) {
                if( !$brand["visibleOnFrontend"] ) {
                    unset($objects[$key]);
                }
            }
        }
        return [$objects, $indexName];
    }
}

