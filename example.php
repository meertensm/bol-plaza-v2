<?php

    require_once 'vendor/autoload.php';

    $publicKey = '<publicKey>';
    $privateKey = '<privateKey>';

    // For live enviroment, set the 3rd parameter to false or remove it
    $client = new MCS\BolPlazaClient($publicKey, $privateKey, true);

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

    // Request a csv export containing all your products. 
    $offerFile = $client->requestOfferFile();

    // Wait up to 15 minutes.
    $offers = $client->getOffers($offerFile);

    //Update an offer's stock
    $offerId = 'k001';
    $quantity = 20;
    $update = $client->updateOfferStock($offerId, $quantity);
    if ($update) {
        echo 'Offer stock updated';    
    }

    $update = $client->updateOffer('k001', [
        'Price' => 12.95,
        'DeliveryCode' => '24uurs-21', // https://developers.bol.com/documentatie/plaza-api/appendix-c-delivery-codes/
        'Publish' => true,
        'ReferenceCode' => 'sku001',
        'Description' => 'Description...'
    ]);
    if ($update) {
        echo 'Offer updated';    
    }

    $created = $client->createOffer('k002', [
        'EAN' => '8711145678987',
        'Condition' => 'NEW', // https://developers.bol.com/documentatie/plaza-api/appendix-b-conditions/
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

    $delete = $client->deleteOffer('k001');
    if ($delete) {
        echo 'Offer deleted';    
    }
