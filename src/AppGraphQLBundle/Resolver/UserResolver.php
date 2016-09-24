<?php
/**
 * Created by PhpStorm.
 * User: stan
 * Date: 24/09/16
 * Time: 14:41
 */

namespace AppGraphQLBundle\Resolver;

use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use Youshido\GraphQL\Execution\ResolveInfo;

class UserResolver extends DoctrineResolver
{
    protected function getEntity() : string
    {
        return User::class;
    }
}