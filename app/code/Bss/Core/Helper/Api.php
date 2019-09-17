<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_Core
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\Core\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Api extends AbstractHelper
{
    const GRAPHQL_ENDPOINT = 'https://bsscommerce.com/graphql';

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $json;

    /**
     * Api constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Serialize\Serializer\Json $json
    )
    {
        parent::__construct($context);
        $this->json = $json;
    }

    /**
     * @return array
     */
    public function getModules()
    {
        try {
            $query =
                'query {
                    modules {
                        items {
                            name
                            product_name
                            entity_id
                            product_url
                            user_guide
                            packages {
                                title
                            }
                        }
                        count
                    }
	            }';
            return $this->graphQlQuery($query)['data']['modules']['items'];
        } catch (\Exception $exception) {
            $this->_logger->critical($exception->getMessage());
            return [];
        }
    }

    /**
     * @return array
     */
    public function getConfigs()
    {
        try {
            $query = '
        query {
            configs {
                popup_expire_time
                popup_delay_open_time
                theme_header_block
                theme_popup_block
            }
	    }';
            return $this->graphQlQuery($query)['data']['configs'];
        } catch (\Exception $exception) {
            $this->_logger->critical($exception->getMessage());
            return [];
        }
    }

    /**
     * @return array
     */
    public function getNewProducts()
    {
        try {
            $query = '
        query {
            new_products {
                name
                sku
                image
                link
            }
	    }';
            return $this->graphQlQuery($query)['data']['new_products'];
        } catch (\Exception $exception) {
            $this->_logger->critical($exception->getMessage());
            return [];
        }
    }

    /**
     * @param $productIds
     * @return array
     * @throws \Exception
     */
    public function getRelatedProducts($productIds)
    {
        try {
            $productIds = implode(",", $productIds);
            $query = "
        query {
            related_products (product_ids: [$productIds]) {
                main_product
                related {
                    name
                    sku
                    image
                    link
                }
            }
	    }";
            return $this->graphQlQuery($query)['data']['related_products'];
        } catch (\Exception $exception) {
            return [];
        }
    }

    /**
     * @param string $query
     * @param array $variables
     * @param null|string $token
     * @return array
     */
    protected function graphQlQuery(string $query, array $variables = [], ?string $token = null): array
    {
        $endpoint = self::GRAPHQL_ENDPOINT;
        if ($debugEndpoint = $this->_request->getParam('gql_endpoint')) {
            $endpoint = $debugEndpoint;
        }

        $headers = ['Content-Type: application/json'];
        if (null !== $token) {
            $headers[] = "Authorization: bearer $token";
        }

        try {
            if (false === $data = file_get_contents($endpoint, false, stream_context_create([
                    'http' => [
                        'method' => 'POST',
                        'header' => $headers,
                        'content' => $this->json->serialize(['query' => $query, 'variables' => $variables]),
                    ]
                ]))) {
                $error = error_get_last();
                throw new \ErrorException($error['message'], $error['type']);
            }

            return $this->json->unserialize($data);
        } catch (\ErrorException $exception) {
            $this->_logger->critical($exception->getMessage());
            return [];
        } catch (\Exception $exception) {
            $this->_logger->critical($exception->getMessage());
            return [];
        }
    }
}
