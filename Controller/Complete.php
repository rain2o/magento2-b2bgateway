<?php
  
  namespace Creditkey\B2BGateway\Controller;

  use  \Psr\Log\LoggerInterface;

  class Complete {

    protected $logger;

    public function __construct(
      LoggerInterface $logger
    ) {
      $this->logger = $logger;
    }

    public function execute() {
      $this->logger('order complete!');
    }
  }
