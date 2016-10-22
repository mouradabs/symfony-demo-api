<?php

namespace AppGraphQLBundle\Resolver;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Parser\Ast\Field;
use Youshido\GraphQL\Parser\Ast\Query;
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

    protected function iterateOnFields(ResolveInfo $info, \Closure $callback)
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
        foreach ($fieldList as $index => $field) {
            $fieldType = $fieldTypeList[$field->getName()]->getType();
            $callback($field, $fieldType, $info, $index);
        }
    }

    protected function addJoinTypes(QueryBuilder $qb, ResolveInfo $info)
    {
        $this->iterateOnFields($info, function($field, $fieldType, ResolveInfo $info, $index) use ($qb) {
            if (!$fieldType instanceof AbstractScalarType) {
                $fieldList = $info->getFieldASTList();
                $selectSubFields = ['id'];
                foreach ($fieldList as $mField) {
                    if ($mField->getName() === $field->getName() &&
                        $mField instanceof Query) {
                        foreach ($mField->getFields() as $subField) {
                            $selectSubFields[] = $subField->getName();
                        }
                    }
                }
                $qb->leftJoin(self::ALIAS . '.' . $field->getName(), 'r_' . $index)
                    ->addSelect('partial r_' . $index . sprintf('.{%s}', implode($selectSubFields, ',')));
            }
        });
    }

    protected function getSelectFields(ResolveInfo $info) : array
    {
        $selectList = [
            'id'
        ];

        $this->iterateOnFields($info, function($field, $fieldType) use (&$selectList) {
            if ($fieldType instanceof AbstractScalarType) {
                $selectList[] = $field->getName();
            }
        });

        return $selectList;
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

        $selectFieldList = $this->getSelectFields($info);
        $selectFieldList = sprintf('partial e.{%s}', implode($selectFieldList, ','));

        $qb->select($selectFieldList)
            ->from($this->getEntity(), 'e');

        $this->addJoinTypes($qb, $info);

        $this->handleSpecialArgs($qb, $args);

        foreach ($args as $key => $value) {
            $qb->andWhere($qb->expr()->eq(self::ALIAS . '.' . $key, ':' . $key))
                ->setParameter(':' . $key, $value);
        }

        return $qb->getQuery()->getResult();
    }
}