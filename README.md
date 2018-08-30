# Credit Key Magento 2 Module

## Installation

From your root Magento directory, run the following commands:

```
% composer config repositories.creditkey composer https://composer.creditkey.com
% composer require creditkey/b2bgateway
```

Then enable the module with the following commands:
```
php bin/magento module:enable CreditKey_B2BGateway
php bin/magento setup:upgrade
```

## Configuration

From the Magento admin, navigate to ```Stores > Configuration > Sales > Payment Methods``` and scroll down to the ```Credit Key (Gateway)``` section.
