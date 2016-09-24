<?php

namespace AppGraphQLBundle\GraphQL\Type;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\DateTimeType;
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
            ->addField('title', new StringType())
            ->addField('slug', new StringType())
            ->addField('summary', new StringType())
            ->addField('content', new StringType())
            ->addField('authorEmail', new StringType())
            ->addField('publishedAt', new DateTimeType())
        ;
    }

    public function getName()
    {
        return "Post";  // if you don't do getName – className without "Type" will be used
    }
}