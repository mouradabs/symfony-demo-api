<?php

namespace AppBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use AppBundle\Entity\Comment;
use AppBundle\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class CommentSubscriber implements EventSubscriberInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        TokenStorageInterface $tokenStorage
    )
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => [['onCreate', EventPriorities::PRE_VALIDATE]]
        ];
    }

    public function onCreate(GetResponseEvent $event)
    {
        $entity = $event->getRequest()->attributes->get('data');

        if (!$entity instanceof Comment) {
            return;
        }

        if ($event->getRequest()->getMethod() !== Request::METHOD_POST) {
            return;
        }

        if (!$this->authorizationChecker->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('You must be logged-in to publish or modify a comment');
        }

        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        $entity->setAuthorEmail($user->getEmail());
    }
}