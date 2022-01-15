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
 * @OpenApi\Annotations\Post(
 *      operationId="post-api-task",
 *      path="/api/task",
 *      tags={"Task"},
 *      summary="Create task",
 *      @OpenApi\Annotations\RequestBody(
 *          required=true,
 *          @OpenApi\Annotations\JsonContent(
 *              type="object",
 *              required={"text"},
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
class PostOneController
{
    #[Required]
    public TaskRepository $repository;

    #[Required]
    public EntityManagerInterface $em;

    #[Route(
        path: '/api/task',
        name: 'post-api-task',
        methods: ['POST']
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $task = new Task();

        $task->setIsChecked((bool) $request->request->get('is_checked', false));
        $task->setText($request->request->get('text'));

        $this->em->persist($task);
        $this->em->flush();

        return $this->json([
            'status' => 'ok',
            'data' => $task,
        ]);
    }
}
