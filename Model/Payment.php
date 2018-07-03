<?php
namespace Creditkey\B2BGateway\Model;

use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Model\Method\AbstractMethod;
use \Psr\Log\LoggerInterface;
 
class Payment extends AbstractMethod implements \Magento\Payment\Model\MethodInterface, \Magento\Payment\Api\Data\PaymentMethodInterface {
    const CODE = 'creditkey_gateway';

    protected $_code = self::CODE;

    protected $commandPool;
    protected $logger;

    public function __construct(
      CommandPoolInterface $commandPool,
      LoggerInterface $logger
    ) {
      $this->commandPool = $commandPool;
      $this->logger = $logger;
    }

    public function initialize($paymentAction, $stateObject) {
      $this->logger->debug('intializing!');

      $this->commandPool->get('initialize')->execute([
        'payment' => $paymentAction,
        'stateObject' => $stateObject
      ]);
      return $this;
    }

    public function capture(InfoInterface $payment, $amount) {
      //todo add functionality later
      $this->logger->debug('captured!');
    }
 
    public function authorize(InfoInterface $payment, $amount) {
      //todo add functionality later
      $this->logger->debug('authorized!');
    }

		/**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        return $this->storeId;
    }
    /**
     * {@inheritdoc}
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
}
