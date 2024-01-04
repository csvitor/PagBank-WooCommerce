<?php

namespace RM_PagBank\Helpers;

use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;
use WC_Cart;
use WC_Order;

class Recurring
{
    
    /**
     * Checks if the $cart or the current cart contains recurring products
     * @param WC_Cart|null $cart
     *
     * @return bool
     */
    public function isCartRecurring(WC_Cart $cart = null): bool
    {
        if (!$cart) $cart = WC()->cart;
        foreach ($cart->get_cart() as $cartItem) {
            $product = $cartItem['data'];
            if ($product->get_meta('_recurring_enabled') == 'yes') 
                return true;
        }
        
        return false;
    }

    /**
     * Calculates the DateTime for the next billing date
     *
     * @param string $frequency Accepted values are: 'daily', 'weekly', 'monthly', 'yearly'
     * @param int $cycle 
     *
     * @return DateTime The next billing date GMT timezone
     * @throws Exception
     */
    public function calculateNextBillingDate(string $frequency, int $cycle): DateTime
    {
        $date = new DateTime('now', new DateTimeZone('GMT'));
        switch ($frequency){
            case 'daily':
                $frequency = 'D';
                break;
            case 'weekly':
                $frequency = 'W';
                break;
            case 'monthly':
                $frequency = 'M';
                break;
            case 'yearly':
                $frequency = 'Y';
                break;
        }
        $interval = new DateInterval('P' . $cycle . $frequency);
        return $date->add($interval);
    }

    public function getStatusFromOrder(WC_Order $order): string
    {
        switch ($order->get_status()){
            case 'processing':
            case 'completed':
                return 'ACTIVE';
            case 'cancelled':
                return 'CANCELED';
            case 'on-hold':
            default:
                return 'PENDING';
        }
    }
    
}