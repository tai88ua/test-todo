<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Task;


class TaskController  extends AbstractController
{

    /**
     * Task info
     *
     * desc.
     *
     * @Route("/api/task/{id}", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Returns task item",
     *     @Model(type=Task::class)
     * )

     * @OA\Tag(name="Task")
     */
     public function getTask(int $id): JsonResponse
     {
        return new JsonResponse([]);
     }



    /**
     * Task info
     *
     * desc.
     *
     * @Route("/api/addtask", methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns category item",
     *     @Model(type=Task::class)
     * )
     *
     * @OA\Parameter(
     *       name="body",
     *       in="header",
     *       description="To Do informaion",
     *       required=false,
     *       @OA\Schema(
     *          type="object",
     *          @OA\Property(property="info", type="string")
     *      )
     *   )
     *
     * @OA\Tag(name="addTask")
     */
     public function addTask(Request $request): JsonResponse
     {
         return new JsonResponse(['body' => $request->headers->get('body')]);
     }
}