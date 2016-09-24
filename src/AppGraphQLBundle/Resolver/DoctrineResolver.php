<?php

namespace AppGraphQLBundle\Resolver;
use AppBundle\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Parser\Ast\Field;
use Youshido\GraphQL\Type\Scalar\AbstractScalarType;

abstract class DoctrineResolver
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    const ALIAS = 'e';

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    abstract protected function getEntity() : string;

    protected function addJoinTypes(QueryBuilder $qb, ResolveInfo $info)
    {
        $fieldTypeList = $info->getField()
            ->getType()
            ->getNamedType()
            ->getFields();

        $fieldList = $info->getFieldASTList();

        /** @var Field $field */
        $joinIndex = 0;
        foreach ($fieldList as $field) {
            $fieldType = $fieldTypeList[$field->getName()]->getType();
            if (!$fieldType instanceof AbstractScalarType) {
                $qb->leftJoin(self::ALIAS . '.' . $field->getName(), 'r_' . $joinIndex)
                    ->addSelect('r_' . $joinIndex);
                $joinIndex++;
            }
        }
    }

    public function resolve($source, array $args, ResolveInfo $info) : array
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb->select(self::ALIAS)
            ->from($this->getEntity(), self::ALIAS);

        $this->addJoinTypes($qb, $info);

        foreach ($args as $key => $value) {
            $qb->andWhere($qb->expr()->eq(self::ALIAS . '.' . $key, ':' . $key))
                ->setParameter(':' . $key, $value);
        }

        $results = $qb->getQuery()->getArrayResult();

        return $results;
    }
}