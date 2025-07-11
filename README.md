# Rabo Smart Pay PHP SDK

This repository contains the official PHP SDK for [Rabo Smart Pay](https://www.rabobank.nl/bedrijven/betalen/klanten-laten-betalen/rabo-smart-pay).

Rabo Smart Pay offers merchants an all-in-one solution to receive payments on your physical and online locations. It includes a dashboard that puts you in full control of your Rabo Smart Pay and all products included in it: Rabo OnlineKassa, payment terminals, Rabo PinBox, Rabo SmartPin, Retourpinnen, Rabo PinTegoed, Rabo Betaalverzoek Plus and payment brands such as: Maestro, V PAY, iDEAL, MasterCard, Visa and PayPal.

The PHP SDK allows PHP developers to integrate their web shop with Rabo Smart Pay to handle online payments. Note that besides using an SDK Rabo Smart Pay also provides other ways to integrate that may require less effort. More information on this topic can be found on the [Developer Portal](https://developer.rabobank.nl/overview/rabo-omnikassa) of Rabobank.

Detailed developer documentation on how to use the PHP SDK as well as contact information can be found in the [SDK manual](https://github.com/rabobank-nederland/omnikassa-sdk-doc/blob/main/README.md).

## Release notes

### Version 1.17.0
* Added support for setting the partnerReference id and user agent in the `X-Api-User-Agent` header.
* Added new optional `shopperBankstatementReference` field to order announcement

### Version 1.16.0
* Extended SDK to support refunds.

### Version 1.15.0
* Extended SDK to support refunds.
* Extended SDK to support transactions in order announcement result.

### Version 1.14.0
* Extended SDK to support Sofort as a payment brand.

### Version 1.13.0
* Extended SDK to retrieve a list of iDEAL issuers.
* Extended SDK to allow the iDEAL issuer to be passed in the order announcement.
* Extended SDK to allow the payment result page (also known as the 'thank-you' page) to be skipped in the payment process.
* Extended SDK to allow the name of the customer to be passed in the order announcement.
* Added support for PHP 8.0.
* Updated some dependencies to recent versions.

### Version 1.12.1
* Fixed rounding problem (ROFE-4592).

### Version 1.12.0
* First release via Packagist.
* Updated some dependencies.

### Version 1.11.0
* Extended SDK to retrieve a list of configured payment brands.
* Allow the initiating party to be passed in the order announcement.

### Version 1.9.0
* Fixes in the documentation.
