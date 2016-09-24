<?php

namespace AppGraphQLBundle\Resolver;
use Doctrine\ORM\EntityManagerInterface;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Field\Field;

abstract class DoctrineResolver
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    abstract protected function getAlias();

    protected function getSelectFields($alias, ResolveInfo $info) : array
    {
        $fieldList = $info->getFieldASTList();

        $selectList = [];

        /** @var Field $field */
        foreach ($fieldList as $field) {
            $selectList[] = $alias . '.' . $field->getName();
        }

        return $selectList;
    }

    public function resolve($source, array $args, ResolveInfo $info) : array
    {
        $qb = $this->entityManager->createQueryBuilder();

        $selectFieldList = $this->getSelectFields('e', $info);

        $qb->select((empty($selectFieldList) ? 'e' : $selectFieldList))
            ->from($this->getAlias(), 'e');

        foreach ($args as $key => $value) {
            $qb->andWhere($qb->expr()->eq('e.' . $key, ':' . $key))
                ->setParameter(':' . $key, $value);
        }

        $results = $qb->getQuery()->getArrayResult();

        return $results;
    }
}