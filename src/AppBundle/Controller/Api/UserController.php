<?php
namespace AppBundle\Controller\Api;


use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class UserController extends Controller
{
    public function meAction()
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedException();
        }

        $data = $this->get('serializer')->serialize($user, 'json', [AbstractNormalizer::GROUPS => ['user_me']]);
        $response = new Response($data, Response::HTTP_OK, ['Content-type' => 'application/json']);

        return $response;
    }
}
