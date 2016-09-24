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

    public function resolve($source, array $args, ResolveInfo $resolveInfo)
    {
        return $this->resolveType(Post::class, $args, $resolveInfo);
    }
}