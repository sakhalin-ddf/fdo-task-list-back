<?php

declare(strict_types=1);

namespace App\Controller\Task;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * @OpenApi\Annotations\Delete(
 *      operationId="delete-api-task",
 *      path="/api/task/{id}",
 *      tags={"Task"},
 *      summary="Delete task",
 *      @OpenApi\Annotations\Parameter(
 *          name="id",
 *          in="path",
 *          description="Task id",
 *          required=true,
 *          @OpenApi\Annotations\Schema(type="integer")
 *      ),
 *      @OpenApi\Annotations\Response(
 *          response=200,
 *          description="OK",
 *      )
 * )
 */
class DeleteOneController
{
    #[Required]
    public TaskRepository $repository;

    #[Required]
    public EntityManagerInterface $em;

    #[Route(
        path: '/api/task/{id}',
        name: 'delete-api-task',
        methods: ['DELETE']
    )]
    public function remove(Request $request): JsonResponse
    {
        /**
         * @var Task $task
         */
        $task = $this->repository->find($request->attributes->get('id'));

        $this->em->remove($task);
        $this->em->flush();

        return $this->json([
            'status' => 'ok',
        ]);
    }
}
