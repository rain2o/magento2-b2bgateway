<?php

class OrderObserver implements \Magento\Framework\Event\ObserverInterface {
  public function execute(\Magento\Framework\Event\Observer $observer) {
    $order = $observer->getEvetn()->getOrder();
    echo $orderId = $order->getId();
    exit;
  }
}
