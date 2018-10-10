<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Adobe\AxpConnector\Block;

class Cart extends \Magento\Framework\View\Element\Template
{
  public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
    \Adobe\AxpConnector\Helper\Data $helper,
    \Magento\Checkout\Model\Cart $cartModel,
    array $data
  )
  {
    parent::__construct($context, $data);
    $this->cartModel = $cartModel;
    $this->helper = $helper;
  }

  public function datalayer() {
    return $this->helper->cartViewedPushData($this->cartModel);
  }

  public function datalayerJson() {
    $datalayer = $this->datalayer();
    return $this->helper->jsonify($datalayer);
  }
}
