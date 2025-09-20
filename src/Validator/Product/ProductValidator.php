<?php

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class ProductValidator
{

    /**
     * @param array $data
     * @return void
     */
    public function validate(array $data): void
    {
        if (!isset($data['name'])) {
            throw new UnprocessableEntityHttpException("Not found name.");
        }

        if (!isset($data['description'])) {
            throw new UnprocessableEntityHttpException("Not found description.");
        }

        if (!isset($data['price'])) {
            throw new UnprocessableEntityHttpException("Not found price.");
        }
    }

}