<?php 
namespace MCS;
 
use DateTime;
use Exception;
use DOMDocument;
use MCS\BolPlazaOrderItem;
use MCS\BolPlazaOrderAddress;

class BolPlazaOrder{

    private $client;
    public $id;
    public $date;
    public $ShippingAddress;
    public $BillingAddress;
    public $OrderItems = [];
    
    /**
     * Construct
     * @param string $id The orderId
     * @param array $ShippingAddress 
     * @param array $BillingAddress  
     * @param object BolPlazaClient $client 
     */
    public function __construct($id, $DateTimeCustomer, array $ShippingAddress, array $BillingAddress, BolPlazaClient $client)
    {
        $this->id = $id;
        $this->date = $DateTimeCustomer;
        $this->ShippingAddress = new BolPlazaOrderAddress($ShippingAddress);
        $this->BillingAddress = new BolPlazaOrderAddress($BillingAddress);
        $this->client = $client;
    }
    
    /**
     * Add an item to the order
     * @param array $item
     */
    public function addOrderItem(array $item)
    {
        $this->OrderItems[] = new BolPlazaOrderItem($item, $this->client);
    }
    
    /**
     * Ship an order
     * @param  object DateTime $expectedDeliveryDate 
     * @param  string [$carrier = false]             
     * @param  parcelnumber [$awb = false]                 
     * @return array
     */
    public function ship(DateTime $expectedDeliveryDate, $carrier = false, $awb = false, $label = false)
    {
     
        $carriers = [
            'BPOST_BRIEF', 'BRIEFPOST', 'GLS', 'FEDEX_NL',
            'DHLFORYOU', 'UPS', 'KIALA_BE', 'KIALA_NL',
            'DYL', 'DPD-NL', 'DPD-BE', 'BPOST_BE',
            'FEDEX_BE', 'OTHER', 'DHL', 'DHL_DE', 'SLV',
            'TNT', 'TNT_EXTRA', 'TNT_BRIEF',
            'TRANSMISSION'
        ];  
        
        if ($carrier && !in_array($carrier, $carriers)) {
            throw new Exception('Carrier not allowed. Use one of: ' . implode(' / ', $carriers));    
        }
        
        $format = 'Y-m-d\TH:i:sP';
        
        $now = new DateTime();
        
        $response = [];
        
        foreach ($this->OrderItems as $OrderItem) {
            $xml = new DOMDocument('1.0', 'UTF-8');

            $body = $xml->appendChild(
                $xml->createElementNS('https://plazaapi.bol.com/services/xsd/v2/plazaapi.xsd', 'ShipmentRequest')
            );
            $body->appendChild(
                $xml->createElement('OrderItemId', $OrderItem->OrderItemId)
            );
            $body->appendChild(
                $xml->createElement('ShipmentReference', $this->id)
            );
            $body->appendChild(
                $xml->createElement('DateTime', $now->format($format))
            );
            $body->appendChild(
                $xml->createElement('ExpectedDeliveryDate', $expectedDeliveryDate->format($format))
            );
            if ($label) {
                $body->appendChild(
                    $xml->createElement('ShippingLabelCode', $label)
                );
            };
    
            if ($carrier) {
                $transport = $body->appendChild(
                    $xml->createElement('Transport')
                );
                $transport->appendChild(
                    $xml->createElement('TransporterCode', $carrier)
                );
                if ($awb) {
                    $transport->appendChild(
                        $xml->createElement('TrackAndTrace', htmlspecialchars($awb))
                    );
                }
            }
            
            $response[] = $this->client->request($this->client->endPoints['shipments'], 'POST', $xml->saveXML());
        }
        
        return $response;
        
    }
}
