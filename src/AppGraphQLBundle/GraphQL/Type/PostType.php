<?php

namespace AppGraphQLBundle\GraphQL\Type;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;

/**
 * Created by PhpStorm.
 * User: stan
 * Date: 23/09/16
 * Time: 00:57
 */
class PostType extends AbstractObjectType
{
    public function build($config)  // implementing an abstract function where you build your type
    {
        $config
            ->addField('title', new StringType())       // defining "title" field of type String
            ->addField('summary', new StringType());    // defining "summary" field of type String
    }

    public function getName()
    {
        return "Post";  // if you don't do getName – className without "Type" will be used
    }
}