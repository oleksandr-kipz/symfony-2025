<?php

namespace App\EventListener;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: Product::class)]
class ProductListener
{

    public function postUpdate(Product $product, PostUpdateEventArgs $event): void
    {
        $test = 1;
    }

}