<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Serializer\JsonEntityDeserializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Service\Attribute\Required;

#[Route(
    path: '/api'
)]
class TaskController extends AbstractController
{
    #[Required]
    public TaskRepository $repository;

    #[Required]
    public EntityManagerInterface $em;

    #[Route(
        path: '/task',
        name: 'get-api-task',
        methods: ['GET']
    )]
    public function list(): JsonResponse
    {
        return $this->json([
            'status' => 'ok',
            'data' => $this->repository->findAll(),
        ]);
    }

    #[Route(
        path: '/task',
        name: 'post-api-task',
        methods: ['POST']
    )]
    public function create(Request $request): JsonResponse
    {
        $task = new Task();

        $task->setTitle($request->request->get('title'));
        $task->setText($request->request->get('text'));

        $this->em->persist($task);
        $this->em->flush();

        return $this->json([
            'status' => 'ok',
            'data' => $task,
        ]);
    }
}
