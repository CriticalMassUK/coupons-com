<?php

namespace CouponsCom;

use PHPUnit_Framework_TestCase;

require 'CouponCredentials.php';

class CouponGeneratorTest extends PHPUnit_Framework_TestCase
{
    public function testGetCouponURL() 
    {
        $customerName = 'Dick Templeman';

        $generator = new CouponGenerator;
        $generator->setCustomerName($customerName);
        $generator->setOfferCode(COUPON_OFFER_CODE);
        $generator->setCheckCode(COUPON_CHECK_CODE);
        $generator->setShortKey(COUPON_SHORT_KEY);
        $generator->setLongKey(COUPON_LONG_KEY);
        $generator->setTestCPT('penguin');
        $generator->generatePin();
    
        $couponURL = $generator->getCouponURL();
        $pattern = '/^http:\/\//';

        preg_match($pattern, $couponURL, $matches);

        $this->assertTrue(is_array($matches) && $matches[0] == 'http://');

        $couponURLParts = explode('?', $couponURL); 
        
        parse_str($couponURLParts[1], $couponURLParameters);

        $expectedOutput = array(
            'oc' => COUPON_OFFER_CODE,
            'cc' => COUPON_CHECK_CODE,
            'p' => $generator->getPin(),
            'cpt' => $generator->getCPT(),
            'ct' => $customerName
        );

        $differences = array_diff($expectedOutput, $couponURLParameters);

        $this->assertTrue($differences == array());
    }
}
