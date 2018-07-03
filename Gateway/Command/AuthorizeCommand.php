<?php
namespace Creditkey\B2BGateway\Gateway\Command;

use Magento\Payment\Gateway\Command;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\CommandInterface;
use \Psr\Log\LoggerInterface;

class AuthorizeCommand implements CommandInterface
{
  protected $logger;

  public function __construct(
    LoggerInterface $logger
  ) {
    $this->logger = $logger;
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
      $payment = $paymentDO->getPayment();

      $payment->setTransactionId(md5(mt_rand(0, 1000)));
      $payment->setIsTransactionClosed(false);

      return null;
    }
}
