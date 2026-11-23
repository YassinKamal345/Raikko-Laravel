<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown during checkout when a product size has less stock than requested.
 * Its message is shown to the customer.
 */
class OutOfStockException extends RuntimeException
{
}
