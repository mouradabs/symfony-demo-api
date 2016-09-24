<?php
/**
 * Created by PhpStorm.
 * User: stan
 * Date: 24/09/16
 * Time: 15:03
 */

namespace AppGraphQLBundle\GraphQL\Type;

use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;

class UserType extends AbstractObjectType
{
    public function build($config)  // implementing an abstract function where you build your type
    {
        $config
            ->addField('username', new StringType())
            ->addField('email', new StringType());
    }
}