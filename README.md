# Credit Key Magento 2 Module

## Installation

From your root Magento directory, run the following command:

```
% composer require creditkey/b2bgateway
```

Then enable the module with the following commands:
```
% php bin/magento module:enable CreditKey_B2BGateway
% php bin/magento setup:upgrade
```

## Configuration

From the Magento admin, navigate to ```Stores > Configuration > Sales > Payment Methods``` and scroll down to the ```Credit Key (Gateway)``` section.

If `title` is left blank the module will request the active promotion text from the Credit Key API.

The `Marketing Content on Product Pages` section allows you to enable the Credit Key marketing content to be displayed on the selected product detail pages. You can enable/disable this feature globally, select the specific products to allow the content to be displayed on, and select the style of the displayed content.