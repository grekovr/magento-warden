<?php

namespace Demo\ProductAttribute\Exception;

use Magento\Framework\Phrase;
use Magento\Framework\Webapi\Exception as WebapiException;

class UnprocessableEntityException extends WebapiException
{
    private const HTTP_UNPROCESSABLE_ENTITY = 422;

    public function __construct(
        Phrase $phrase,
        array $details = []
    ) {
        parent::__construct(
            $phrase,
            0,
            self::HTTP_UNPROCESSABLE_ENTITY,
            $details
        );
    }
}
