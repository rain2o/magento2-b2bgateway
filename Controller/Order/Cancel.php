<?php
namespace CreditKey\B2BGateway\Controller\Order;

/**
 * Cancel order controller
 */
class Cancel extends \CreditKey\B2BGateway\Controller\AbstractCreditKeyController
{
    /**
     * Execute the cancel action
     *
     * @return $this
     */
    public function execute()
    {
        $this->_redirect('checkout');
        return $this;
    }
}
