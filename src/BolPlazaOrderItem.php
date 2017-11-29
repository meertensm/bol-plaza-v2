<?php 
namespace MCS;
 
use DateTime;
use Exception;
use DOMDocument;
use DateTimeZone;

class BolPlazaOrderItem{
    
    private $client;
    public $OrderItemId;
    public $EAN;
    public $OfferReference;
    public $Title;
    public $Quantity;
    public $OfferPrice;
    public $TransactionFee;
    public $PromisedDeliveryDate;
    public $OfferCondition;
    public $CancelRequest;
    
    public function __construct(array $array, BolPlazaClient $client)
    {
        foreach ($array as $property => $value) {
            if (property_exists($this, $property)) {
                if (is_array($value)) {
                    $value = '';
                }
                $this->{$property} = $value;
            }
        }
        
        $this->Quantity = (int) $this->Quantity;
        $this->OfferPrice = (float) $this->OfferPrice;
        $this->TransactionFee = (float) $this->TransactionFee;
        $this->CancelRequest = $this->CancelRequest == 'false' ? false : true;
        
        $this->client = $client;
    }
    
    public function has($property)
    {
        if (property_exists($this, $property)) {
            return is_null($this->{$property}) ? false : true;    
        } else {
            return false;    
        }
    }

    /**
     * Cancel an OrderItem
     * @param  string $reason
     * @return array
     */
    public function cancel($reason)
    {

        $reasons = [
            "OUT_OF_STOCK",
            "REQUESTED_BY_CUSTOMER",
            "BAD_CONDITION",
            "HIGHER_SHIPCOST",
            "INCORRECT_PRICE",
            "NOT_AVAIL_IN_TIME",
            "NO_BOL_GUARANTEE",
            "ORDERED_TWICE",
            "RETAIN_ITEM",
            "TECH_ISSUE",
            "UNFINDABLE_ITEM",
            "OTHER",
        ];

        if ($reason && !in_array($reason, $reasons)) {
            throw new Exception('Cancellation reason not allowed. Use one of: ' . implode(' / ', $reasons));
        }

        $timeZone = new DateTimeZone('Etc/Greenwich');
        $format = 'Y-m-d\TH:i:sP';

        $now = new DateTime();
        $now = $now->setTimezone($timeZone);

        $xml = new DOMDocument('1.0', 'UTF-8');

        $body = $xml->appendChild(
            $xml->createElementNS('https://plazaapi.bol.com/services/xsd/v2/plazaapi.xsd', 'Cancellation')
        );
        $body->appendChild(
            $xml->createElement('DateTime', $now->format($format))
        );
        $body->appendChild(
            $xml->createElement('ReasonCode', $reason)
        );

        return $this->client->request(
            str_replace(':id', urlencode($this->OrderItemId), $this->client->endPoints['cancellations']),
            'PUT',
            $xml->saveXML()
        );
    }
}
