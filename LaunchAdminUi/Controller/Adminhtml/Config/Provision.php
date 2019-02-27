<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\LaunchAdminUi\Controller\Adminhtml\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Exception\FileSystemException;
use Adobe\AxpConnector\Helper\ProvisionHelper;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class Provision
 */
class Provision extends Action implements HttpPostActionInterface
{
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var Reader
     */
    private $moduleReader;

    /**
     * @var File
     */
    private $file;

    /**
     * @var ProvisionHelper
     */
    private $provisionHelper;

    /**
     * @var Json
     */
    private $jsonSerializer;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Reader $moduleReader
     * @param File $file
     * @param ProvisionHelper $provisionHelper
     * @param Json $jsonSerializer
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Reader $moduleReader,
        File $file,
        ProvisionHelper $provisionHelper,
        Json $jsonSerializer
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->moduleReader = $moduleReader;
        $this->file = $file;
        $this->provisionHelper = $provisionHelper;
        $this->jsonSerializer = $jsonSerializer;
        parent::__construct($context);
    }

    /**
     * Execute Provisioning
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultJsonFactory->create();
        $config = $this->_getJsonConfig();
        $requestResponse = $this->_sendAPIRequests($config);

        return $result->setData($requestResponse);
    }

    /**
     * Get the JSON file
     *
     * @return mixed
     */
    private function _getJsonConfig()
    {
        $etcDir = $this->moduleReader->getModuleDir(
            \Magento\Framework\Module\Dir::MODULE_ETC_DIR,
            'Adobe_AxpConnector'
        );
        $file = $etcDir . '/adminhtml/provision_config.json';
        try {
            $string = $this->file->fileGetContents($file);
            return $this->jsonSerializer->unserialize($string);
        } catch (FileSystemException $e) {
            return ["error"=>$e->getMessage()];
        }
    }

    /**
     * Make the API calls
     *
     * @param array $config
     * @return array
     */
    private function _sendAPIRequests($config)
    {
        return $this->provisionHelper->makeRequests($config);
    }
}
