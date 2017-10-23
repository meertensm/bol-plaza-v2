<?php 
namespace MCS;
 
class BolPlazaOrderAddress{

    public $SalutationCode;
    public $Firstname;
    public $Surname;
    public $Streetname;
    public $Housenumber;
    public $HousenumberExtended;
    public $AddressSupplement;
    public $ExtraAddressInformation;
    public $ZipCode;
    public $City;
    public $CountryCode;
    public $Email;
    public $DeliveryPhoneNumber;
    public $Company;
    public $VatNumber;
    
    public function __construct(array $array)
    {
        foreach ($array as $property => $value) {
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
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
