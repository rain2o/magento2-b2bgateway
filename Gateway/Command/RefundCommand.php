<?php
namespace CreditKey\B2BGateway\Gateway\Command;

use Magento\Payment\Gateway\Command;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\CommandInterface;

class RefundCommand implements CommandInterface
{
    protected $_logger;
    protected $_creditKeyApi;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \CreditKey\B2BGateway\Helper\Api $creditKeyApi
    ) {
        $this->_logger = $logger;
        $this->_creditKeyApi = $creditKeyApi;
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
        $refundAmount = $commandSubject['amount'];

        $payment = $paymentDO->getPayment();
        $ckOrderId = $payment->getAdditionalInformation('ckOrderId');

        $this->_logger->debug('Refund Credit Key Payment: '.$ckOrderId.': '.$refundAmount);

        $this->_creditKeyApi->configure();
        $ckOrder = \CreditKey\Orders::refund($ckOrderId, $refundAmount);

        return null;
    }
}
