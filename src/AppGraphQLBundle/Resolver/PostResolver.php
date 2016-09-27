<?php
/**
 * Created by PhpStorm.
 * User: stan
 * Date: 24/09/16
 * Time: 14:41
 */

namespace AppGraphQLBundle\Resolver;

use AppBundle\Entity\Post;
use Youshido\GraphQL\Execution\ResolveInfo;

class PostResolver extends DoctrineResolver
{

    public function resolveOne($source, array $args, ResolveInfo $info) : array {
        return parent::resolve($source, $args, $info)[0];
    }

    protected function getEntity() : string
    {
        return Post::class;
    }
}