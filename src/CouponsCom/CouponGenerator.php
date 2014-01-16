<?php

namespace CouponsCom;

use Psr\Log\LogLevel;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * @package agencyrepublic/coupons-com
 */
class CouponGenerator implements LoggerAwareInterface
{
    protected $customerName = 'Personalised Coupon';
    protected $logger;
    protected $CPTEndpoint = 'http://cpt.coupons.com/au/encodecpt.aspx';    
    protected $couponEndpoint = 'http://bricks.couponmicrosite.net/javabricksweb/Index.aspx';
    protected $offerCode;
    protected $checkCode;
    protected $shortKey;
    protected $longKey;
    protected $pin;
    protected $CPT;

    public function getCouponURL()
    {
        // For real life we generate the CPT, but for testing, it can be set manually. 
        if (empty($this->CPT)) {
            $this->generateCPT();
        }

        $requiredFields = array('offerCode', 'checkCode', 'pin', 'CPT', 'customerName');
        
        $requestURL = $this->generateRequestURL($requiredFields, $this->couponEndpoint);

        return $requestURL;
    }

    public function generatePIN()
    {
        $this->pin = uniqid();
        return $this->pin;
    }

    public function setCustomerName($customerName)
    {
        $this->customerName = $customerName;
    }

    public function setOfferCode($offerCode)
    {
        $this->offerCode = $offerCode;
    }

    public function setCheckCode($checkCode)
    {
        $this->checkCode = $checkCode;
    }

    public function setShortKey($shortKey)
    {
        $this->shortKey = $shortKey;
    }

    public function setLongKey($longKey)
    {
        $this->longKey = $longKey;
    }

    public function setTestCPT($testCPT)
    {
        $this->CPT = $testCPT;
    }

    public function setPin($pin)
    {
        $this->pin = $pin;
    }

    public function getCPT()
    {
        return $this->CPT;
    }

    public function getPin()
    {
        return $this->pin;
    }

    protected function generateCPT() 
    {
        $requiredFields = array('offerCode', 'shortKey', 'longKey', 'pin');
        
        $requestURL = $this->generateRequestURL($requiredFields, $this->CPTEndpoint);

        $response = file_get_contents($requestURL);

        if (empty($response)) {
            throw new CouponGeneratorException('Empty response from CPT endpoint');
        }

        $this->CPT = $response;

        return $this->CPT;
    }

    protected function generateRequestURL($requiredFields, $endpointURL, $isCPT = false)
    {
        $params = array();

        foreach ($requiredFields as $field) {
            if (empty($this->$field))
            {
                throw new CouponGeneratorException($field.' must be set');
            }

            $params[$this->getParameterName($field, $isCPT)] = $this->$field;
        }

        $requestURL = $endpointURL.'?'.http_build_query($params);

        return $requestURL;
    }

    protected function getParameterName($attributeName, $isCPT = false)
    {
        $translations = array(
            'customerName' => 'ct',
            'checkCode' => 'c',
            'offerCode' => 'o',
            'shortKey' => 'sk',
            'longKey' => 'lk',
            'pin' => 'p',
            'CPT' => 'cpt'
        );

        // The CPT endpoint uses a different parameter name for offerCode
        if ($isCPT === true) {
            $translations['offerCode'] = 'o';
        }

        if (!array_key_exists($attributeName, $translations)) {
            throw new CouponGeneratorException('Unknown attribute: '.$attributeName);
        }

        return $translations[$attributeName];
    }

    /* PSR-3 */
    public function setLogger(LoggerInterface $logger) 
    {
        $this->logger = $logger;
    }    
}
