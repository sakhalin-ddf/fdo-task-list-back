<?php

declare(strict_types=1);

namespace App\Controller\Task;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * @OpenApi\Annotations\Put(
 *      operationId="put-api-task",
 *      path="/api/task/{id}",
 *      tags={"Task"},
 *      summary="Update task",
 *      @OpenApi\Annotations\Parameter(
 *          name="id",
 *          in="path",
 *          description="Task id",
 *          required=true,
 *          @OpenApi\Annotations\Schema(type="integer")
 *      ),
 *      @OpenApi\Annotations\RequestBody(
 *          required=true,
 *          @OpenApi\Annotations\JsonContent(
 *              type="object",
 *              @OpenApi\Annotations\Property(property="is_checked", type="boolean"),
 *              @OpenApi\Annotations\Property(property="text", type="string"),
 *          )
 *      ),
 *      @OpenApi\Annotations\Response(
 *          response=200,
 *          description="OK",
 *      )
 * )
 */
class PutOneController extends AbstractController
{
    #[Required]
    public TaskRepository $repository;

    #[Route(
        path: '/api/task/{id}',
        name: 'put-api-task',
        methods: ['PUT']
    )]
    public function __invoke(Request $request): JsonResponse
    {
        /**
         * @var Task $task
         */
        $task = $this->repository->find($request->attributes->get('id'));

        if ($request->request->has('is_checked')) {
            $task->setIsChecked($request->request->get('is_checked'));
        }

        if ($request->request->has('text')) {
            $task->setText($request->request->get('text'));
        }

        $this->repository->persist($task);

        return $this->json([
            'status' => 'ok',
            'data' => $task,
        ]);
    }
}
