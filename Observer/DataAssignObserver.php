<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Creditkey\B2BGateway\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;
use \Psr\Log\LoggerInterface;

class DataAssignObserver extends AbstractDataAssignObserver
{
    protected $logger;

    public function __construct(
      LoggerInterface $logger
    ) {
      $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
				$data = $this->readDataArgument($observer);

				$additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
				if (!is_array($additionalData)) {
						return;
				}

				$paymentModel = $this->readPaymentModelArgument($observer);

				$paymentModel->setAdditionalInformation(
						$additionalData
				);
    }
}
