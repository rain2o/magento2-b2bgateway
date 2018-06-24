<?php

class OrderObserver implements \Magento\Framework\Event\ObserverInterface {
  public function execute(\Magento\Framework\Event\Observer $observer) {
    $order = $observer->getEvent()->getOrder();
    echo $orderId = $order->getId();
    exit;
  }
}
