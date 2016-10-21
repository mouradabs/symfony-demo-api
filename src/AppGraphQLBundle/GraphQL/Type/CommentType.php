<?php
/**
 * Created by PhpStorm.
 * User: stan
 * Date: 24/09/16
 * Time: 15:43
 */

namespace AppGraphQLBundle\GraphQL\Type;

use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\DateTimeType;
use Youshido\GraphQL\Type\Scalar\StringType;

class CommentType extends AbstractObjectType
{
    public function build($config)
    {
        $config
            ->addField('post', new PostType())
            ->addField('content', new StringType())
            ->addField('authorEmail', new StringType())
            ->addField('publishedAt', new DateTimeType())
        ;
    }


}