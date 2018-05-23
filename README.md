# Bol.com Plaza Api V2
[![Latest Stable Version](https://poser.pugx.org/mcs/bol-plaza-v2/v/stable)](https://packagist.org/packages/mcs/bol-plaza-v2) [![Latest Unstable Version](https://poser.pugx.org/mcs/bol-plaza-v2/v/unstable)](https://packagist.org/packages/mcs/bol-plaza-v2) [![License](https://poser.pugx.org/mcs/bol-plaza-v2/license)](https://packagist.org/packages/mcs/bol-plaza-v2) [![Total Downloads](https://poser.pugx.org/mcs/bol-plaza-v2/downloads)](https://packagist.org/packages/mcs/bol-plaza-v2)

[![paypal](https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=FQG5EWN987PJW)

Installation:
```bash
$ composer require mcs/bol-plaza-v2
```

**Note:** Bol.com requires you to use their parameter values:
- [Transporters](https://developers.bol.com/documentatie/plaza-api/appendix-a-transporters/)
- [Conditions](https://developers.bol.com/documentatie/plaza-api/appendix-b-conditions/)
- [Delivery codes](https://developers.bol.com/documentatie/plaza-api/appendix-c-delivery-codes/)
- [Reasons](https://developers.bol.com/documentatie/plaza-api/appendix-d-reasons-errors/)

### Client
```php
require_once 'vendor/autoload.php';

$publicKey = '<publicKey>';
$privateKey = '<privateKey>';

// For live enviroment, set the 3rd parameter to false or remove it
$client = new MCS\BolPlazaClient($publicKey, $privateKey, true);
```
### Order functions
```php
// Get all currently open orders
$orders = $client->getOrders();
if ($orders) {
    foreach ($orders as $order) {
        print_r($order);    
    }
}

// Get an order by it's id and ship it
$order = $client->getOrder('123');
if ($order) {

    // Bol.com requires you to add an expected deliverydate to a shipment
    $deliveryDate = new DateTime('20-6-2014');

    // This client also provides a helper function to calculate the next deliverydate
    $deliveryDate = $client->nextDeliveryDate(
        '18:00', // Until what time are orders shipped this day?
        ['Sun', 'Mon'], // On what days does the carrier not deliver packages?
        ['Sat', 'Sun'], // On what days does the carrier not pickup/collect packages?
        '12:00' // The time of the delivery
    );

    // Ship an order with track and trace. See https://developers.bol.com/documentatie/plaza-api/appendix-a-transporters/ for supported carrier codes
    $shipped = $order->ship($deliveryDate, 'TNT', '3STEST1234567');    

    // Ship an order without track and trace
    // $shipped = $order->ship($deliveryDate);

    print_r($shipped);
}
```
### Return functions
```php
// Get all returns
$returns = $client->getReturns();
if ($returns) {
    foreach ($returns as $return) {
        print_r($return);    
    }
}
```
### Product functions
```php

// Request a csv export containing all your products. 
$offerFile = $client->requestOfferFile();

// Wait up to 15 minutes.
$offers = $client->getOffers($offerFile);

//Update an offer's stock
$update = $client->updateOfferStock('k001', 20);
if ($update) {
    echo 'Offer stock updated';    
}

$update = $client->updateOffer('k001', [
    'Price' => 12.95,
    'DeliveryCode' => '24uurs-21',
    'Publish' => true,
    'ReferenceCode' => 'sku001',
    'Description' => 'Description...'
]);
if ($update) {
    echo 'Offer updated';    
}

// Create an offer
$created = $client->createOffer('k002', [
    'EAN' => '8711145678987',
    'Condition' => 'NEW',
    'Price' => 189.99,
    'DeliveryCode' => '24uurs-21',
    'QuantityInStock' => 100,
    'Publish' => true,
    'ReferenceCode' => 'sku002',
    'Description' => 'Description...'
]);
if ($created) {
    echo 'Offer created';    
}

// Delete an offer
$delete = $client->deleteOffer('k001');
if ($delete) {
    echo 'Offer deleted';    
}


```