<?php 
namespace MCS;
 
use MCS\BolPlazaOrderAddress;

class BolPlazaReturn{
    
    public $ReturnNumber;
    public $OrderId;
    public $ShipmentId;
    public $EAN;
    public $Title;
    public $Quantity;
    public $ReturnDateAnnouncement;
    public $ReturnReason;
    public $ReturnReasonComments;
    public $CustomerDetails;
    
    public function __construct(array $array)
    {
        foreach ($array as $property => $value) {
            if (property_exists($this, $property)) {
                if ($property == 'CustomerDetails') {
                    if (isset($value['Firstname'])) {
                        $value['Firstname'] = $value['FirstName'];
                        unset($value['FirstName']);
                        if (is_array($value['Email'])) {
                            unset($value['Email']);
                        }
                    }
                    $this->CustomerDetails = new BolPlazaOrderAddress($value);
                } else {
                    $this->{$property} = $value;
                }
            }
        }
    }
    
    /**
     * Check if the address has a property
     * @param  boolean
     */
    public function has($property)
    {
        if (property_exists($this, $property)) {
            return is_null($this->{$property}) ? false : true;    
        } else {
            return false;    
        }
    }
    
    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        } else {
            return '';    
        }
    }
}
