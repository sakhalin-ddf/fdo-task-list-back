<?php

declare(strict_types=1);

namespace App\Controller\Task;

use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * @OpenApi\Annotations\Get(
 *      operationId="get-api-task",
 *      path="/api/task",
 *      tags={"Task"},
 *      summary="Get task list",
 *      @OpenApi\Annotations\Response(
 *          response=200,
 *          description="OK",
 *      )
 * )
 */
class GetAllController extends AbstractController
{
    #[Required]
    public TaskRepository $repository;

    #[Route(
        path: '/api/task',
        name: 'get-api-task',
        methods: ['GET']
    )]
    public function __invoke(): JsonResponse
    {
        return $this->json([
            'status' => 'ok',
            'data' => $this->repository->findBy([], ['id' => 'asc']),
        ]);
    }
}
