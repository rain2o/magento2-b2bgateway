<?php
namespace CreditKey\B2BGateway\Gateway\Command;

use Magento\Payment\Gateway\Command;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\CommandInterface;

class CaptureCommand implements CommandInterface
{
    protected $_logger;
    protected $_creditKeyApi;
    protected $_creditKeyData;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \CreditKey\B2BGateway\Helper\Api $creditKeyApi,
        \CreditKey\B2BGateway\Helper\Data $creditKeyData
    ) {
        $this->_logger = $logger;
        $this->_creditKeyApi = $creditKeyApi;
        $this->_creditKeyData = $creditKeyData;
    }

    /**
     * Executes command basing on business object
     *
     * @param array $commandSubject
     * @return null|Command\ResultInterface
     * @throws CommandException
     */
    public function execute(array $commandSubject) {
        $paymentDO = $commandSubject['payment'];
        $captureAmount = $commandSubject['amount'];
        $payment = $paymentDO->getPayment();
        $ckOrderId = $payment->getAdditionalInformation('ckOrderId');

        $this->_logger->debug('Capture Credit Key Payment: '.$ckOrderId.': '.$captureAmount);

        $order = $payment->getOrder();
        $merchantOrderId = $order->getIncrementId();
        $merchantOrderStatus = $order->getStatus();
        $cartContents = $this->_creditKeyData->buildCartContents($order);
        $charges = $this->_creditKeyData->buildChargesWithUpdatedGrandTotal($order, $captureAmount);

        $this->_creditKeyApi->configure();
        $ckOrder = \CreditKey\Orders::confirm($ckOrderId, $merchantOrderId, $merchantOrderStatus,
            $cartContents, $charges);

        $payment->setParentTransactionId($payment->getLastTransId());
        $payment->setTransactionId($ckOrder->getOrderId());
        $payment->setShouldCloseParentTransaction(true);

        return null;
    }
}
