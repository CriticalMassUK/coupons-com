<?php

namespace CouponsCom;

use PHPUnit_Framework_TestCase;

class CouponGeneratorTest extends PHPUnit_Framework_TestCase
{
    public function testGetCouponURL() 
    {
        $customerName = 'Dick Templeman';
        $offerCode = '123456';
        $checkCode = 'o';
        $shortKey = 'shortkey';
        $longKey = 'longkey';
        $testCPT = 'penguin';

        $generator = new CouponGenerator;
        $generator->setCustomerName($customerName);
        $generator->setOfferCode($offerCode);
        $generator->setCheckCode($checkCode);
        $generator->setShortKey($shortKey);
        $generator->setLongKey($longKey);
        $generator->setTestCPT($testCPT);
        $generator->generatePin();
    
        $couponURL = $generator->getCouponURL();
        $pattern = '/^http:\/\//';

        preg_match($pattern, $couponURL, $matches);

        $this->assertTrue(is_array($matches) && $matches[0] == 'http://');

        $couponURLParts = explode('?', $couponURL); 
        
        parse_str($couponURLParts[1], $couponURLParameters);

        $expectedOutput = array(
            'o' => $offerCode,
            'c' => $checkCode,
            'p' => $generator->getPin(),
            'cpt' => $generator->getCPT(),
            'ct' => $customerName
        );

        $differences = array_diff($expectedOutput, $couponURLParameters);

        $this->assertTrue($differences == array());
    }
}
