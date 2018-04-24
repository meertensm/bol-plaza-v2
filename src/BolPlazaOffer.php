<?php 
namespace MCS;
 
use MCS\BolPlazaOfferException;

class BolPlazaOffer {

    public $EAN;
    public $Condition;
    public $Price;
    public $DeliveryCode;
    public $QuantityInStock;
    public $UnreservedStock;
    public $Publish;
    public $ReferenceCode;
    public $Description;
    public $Title;
    public $FulfillmentMethod;
    public $Published;
    public $ReasonCode;
    public $ReasonMessage;
    
    public function __construct($array = [], $export = false)
    {
        if (isset($array['Status'])) {
            $array['Published'] = $array['Status']['Published'];
            $array['ReasonCode'] = $array['Status']['ErrorCode'];
            $array['ReasonMessage'] = $array['Status']['ErrorMessage'];
            unset($array['Status']);
        }
        
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = null;    
            }
            
            if (property_exists($this, $key)) {
                $this->{$key} = $value;   
            } else {
                switch ($key) {
                    case 'Stock':
                        $this->QuantityInStock = $value;
                        break;
                    case 'Reference':
                        $this->ReferenceCode = $value;
                        break;
                    case 'Deliverycode':
                        $this->DeliveryCode = $value;
                        break;
                }    
            }
        }
        
        $this->format($export);
    }
    
    private function format($export)
    {
        if (strlen($this->EAN) != 13) {
            throw new BolPlazaOfferException('EAN should be 13 characters long');           
        }
       
        $Conditions = [
            'NEW', 'AS_NEW', 'GOOD', 'REASONABLE', 'MODERATE'
        ];
        
        if (!in_array($this->Condition, $Conditions)) {
            throw new BolPlazaOfferException(
                'Condition should be one of: ' . implode($Conditions, ',')
            );                   
        }
        
        $this->Price = (float) str_replace(',', '.', $this->Price);
        
        if ($this->Price > 9999.99) {
            throw new BolPlazaOfferException('Price higher than 9999.99');    
        }
        
        $DeliveryCodes = [
            '24uurs-23', '24uurs-22', '24uurs-21', '24uurs-20',
            '24uurs-19', '24uurs-18', '24uurs-17', '24uurs-16',
            '24uurs-15', '24uurs-14', '24uurs-13', '24uurs-12',
            '1-2d', '2-3d', '3-5d', '4-8d', '1-8d',
            'MijnLeverbelofte'
        ];
        
        if (!in_array($this->DeliveryCode, $DeliveryCodes)) {
            throw new BolPlazaOfferException(
                'DeliveryCode should be one of: ' . implode($DeliveryCodes, ',')
            );
        }
        
        $this->QuantityInStock = (int) $this->QuantityInStock;
        
        if ($this->UnreservedStock != '') {
            $this->UnreservedStock = (int) $this->UnreservedStock;    
        }
        
        if (($this->Publish !== true) and ($this->Publish !== false)) {
            $this->Publish = strtolower($this->Publish);
            if ($this->Publish == 'true') {
                $this->Publish = true;    
            } else if ($this->Publish == 'false') {
                $this->Publish = false;    
            } else {
                $this->Publish = (bool) $this->Publish;    
            }
        }
        
        if (($this->Published !== true) and ($this->Published !== false)) {
            $this->Published = strtolower($this->Published);
            if ($this->Published == 'true') {
                $this->Published = true;    
            } else if ($this->Published == 'false') {
                $this->Published = false;    
            } else {
                $this->Published = (bool) $this->Published;    
            }
        }
        
        $this->ReferenceCode = htmlspecialchars($this->ReferenceCode);    
        
        if (strlen($this->ReferenceCode) > 20) {
            throw new BolPlazaOfferException('ReferenceCode length should not exceed 20 characters');       
        }
           
        if ($this->Condition != 'NEW') {
            $this->Description = htmlspecialchars($this->Description);    
            if ($this->Description == '') {
                if (!$export) {
                    throw new BolPlazaOfferException('Description is required if condition is not NEW');        
                }
            } else if (strlen($this->Description) > 2000) {
                throw new BolPlazaOfferException('Description length should not exceed 2000 characters');       
            } 
        } else {
            $this->Description = null;    
        }

        $this->Title = htmlspecialchars($this->Title);    
        
        if ($this->Title == '') {
            if (!$export) {
                throw new BolPlazaOfferException('Title is required');           
            }
        } else if (strlen($this->Title) > 500) {
            throw new BolPlazaOfferException('Title length should not exceed 500 characters');       
        }
        
        $FulfillmentMethods = [
            'FBR', 'FBB'
        ];
        
        if (!in_array($this->FulfillmentMethod, $FulfillmentMethods)) {
            throw new BolPlazaOfferException(
                'FulfillmentMethod should be one of: ' . implode($FulfillmentMethods, ',')
            );
        }   
    }
}
