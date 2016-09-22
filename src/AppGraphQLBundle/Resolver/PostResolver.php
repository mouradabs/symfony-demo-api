<?php

namespace AppGraphQLBundle\Resolver;
use AppBundle\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Created by PhpStorm.
 * User: stan
 * Date: 23/09/16
 * Time: 00:48
 */
class PostResolver
{

    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    public function find($source, $args, $info)
    {

        $post = $this->entityManager->
                getRepository(Post::class)
            ->find($args['id']);

        $data = [
            'title' => $post->getTitle(),
            'summary' => $post->getSummary()
        ];

        return $data;
    }

    public function findAll($source, $args, $info)
    {

        $postList = $this->entityManager->
        getRepository(Post::class)
            ->findBy($args);

        $data = [];
        foreach ($postList as $post) {
            $data[] = [
                'title' => $post->getTitle(),
                'summary' => $post->getSummary()
            ];
        }

        return $data;
    }
}