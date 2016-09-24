<?php
/**
 * Created by PhpStorm.
 * User: stan
 * Date: 24/09/16
 * Time: 14:41
 */

namespace AppGraphQLBundle\Resolver;

use AppBundle\Entity\Post;

class PostResolver extends DoctrineResolver
{
    protected function getEntity() : string
    {
        return Post::class;
    }
}