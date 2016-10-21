<?php

namespace AppGraphQLBundle\GraphQL\Type;


use Youshido\GraphQL\Type\ListType\AbstractListType;

class CommentListType extends AbstractListType
{
    public function getItemType()
    {
        return new CommentType();
    }
}