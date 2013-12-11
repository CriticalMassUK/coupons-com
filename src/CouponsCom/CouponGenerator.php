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
    protected $logger;
    protected $CPTEndpoint = 'http://cpt.coupons.com/au/encodecpt.aspx';    
    protected $couponEndpoint = 'http://bricks.couponmicrosite.net/javabricksweb/Index.aspx';
    protected $offerCode;
    protected $shortKey;
    protected $longKey;
    protected $pin;
    protected $CPT;

    public function getCouponURL()
    {
        $this->generateCPT();
        return 'http://www.yahoo.ca';
    }

    public function generatePIN()
    {
        $this->pin = uniqid();
        return $this->pin;
    }

    public function setOfferCode($offerCode)
    {
        $this->offerCode = $offerCode;
    }

    public function setShortKey($shortKey)
    {
        $this->shortKey = $shortKey;
    }

    public function setLongKey($longKey)
    {
        $this->longKey = $longKey;
    }

    protected function generateCPT() 
    {
        $requiredFields = array('offerCode', 'shortKey', 'longKey', 'pin');
        $params = array();

        foreach ($requiredFields as $field) {
            if (empty($this->$field))
            {
                throw new CouponGeneratorException($field.' must be set');
            }

            $params[$this->getParameterName($field)] = $this->$field;
        }

        $requestURL = $this->CPTEndpoint.'?'.http_build_query($params);
        $response = file_get_contents($requestURL);

        if (empty($response)) {
            throw new CouponGeneratorException('Empty response from CPT endpoint');
        }

        $this->CPT = $response;

        return $this->CPT;
    }

    protected function getParameterName($attributeName, $isCPT = false)
    {
        $translations = array(
            'offerCode' => 'oc',
            'shortKey' => 'sk',
            'longKey' => 'lk',
            'pin' => 'p'
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
