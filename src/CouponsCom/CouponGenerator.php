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
    
    public function getCouponURL()
    {
        return 'http://www.yahoo.ca';
    }

    /* psr-3 */
    public function setLogger(LoggerInterface $logger) 
    {
        $this->logger = $logger;
    }    
}
