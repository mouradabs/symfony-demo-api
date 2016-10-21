<?php

namespace AppGraphQLBundle\Resolver;
use AppBundle\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Parser\Ast\Field;
use Youshido\GraphQL\Type\Scalar\AbstractScalarType;
use Youshido\GraphQL\Type\Scalar\IntType;

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

    public static function getLimitArgs(array $args = []) : array {
        return array_merge(
                $args,
                [
                    '__limit' => [
                        'type' => new IntType()
                    ],
                    '__offset' => [
                        'type' => new IntType()
                    ]
                ]
            );
    }

    protected function handleSpecialArgs(QueryBuilder $qb, array &$args) {
        if (isset($args['__limit'])) {
            $qb->setMaxResults($args['__limit']);
            unset($args['__limit']);
        }

        if (isset($args['__offset'])) {
            $qb->setFirstResult($args['__offset']);
            unset($args['__offset']);
        }
    }

    protected function addJoinTypes(QueryBuilder $qb, ResolveInfo $info)
    {
        $type = $info->getField()
            ->getType()
            ->getNamedType();

        if ($type instanceof AbstractScalarType) {
            return;
        }

        $fieldTypeList = $type->getFields();

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

    public function resolveTotal() : int
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb->select(sprintf('count(%s)', self::ALIAS))
            ->from($this->getEntity(), self::ALIAS);

        $count = (int)$qb->getQuery()->getSingleScalarResult();

        return $count;
    }

    public function resolve($source, array $args, ResolveInfo $info)
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb->select(self::ALIAS)
            ->from($this->getEntity(), self::ALIAS);

        $this->addJoinTypes($qb, $info);

        $this->handleSpecialArgs($qb, $args);

        foreach ($args as $key => $value) {
            $qb->andWhere($qb->expr()->eq(self::ALIAS . '.' . $key, ':' . $key))
                ->setParameter(':' . $key, $value);
        }

        return $qb->getQuery()->getResult();
    }
}