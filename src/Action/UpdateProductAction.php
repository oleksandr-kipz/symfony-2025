<?php

namespace App\Action;

use App\Entity\Product;

class UpdateProductAction
{

    /**
     * @param Product $product
     * @return Product
     */
    public function __invoke(Product $product): Product
    {
        // TODO send email and write log

        return $product;
    }

}
