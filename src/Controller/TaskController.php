<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
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
     public function getTask(int $id, EntityManagerInterface $entityManager): JsonResponse
     {

        $item = $entityManager->getRepository(Task::class)->find($id);
        $info = [];
        if ($item) {
          $this->checkStatus($item);
          $info = $this->makeObjetToArr($item);
          $item->setCountView($item->getCountView()+1);

          if ($item->getStatus() == 'new' ) {
            $item->setStatus('viewed');
          }

          $entityManager->flush();

        }
        return new JsonResponse($info);
     }



    /**
     * Create TO DO
     *
     * Add new TO DO task
     *
     * @Route("/api/addtask", methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns status",
     *     @OA\Schema (
     *       type="object",
     *       @OA\Property(title="status")
     *     )
     * )
     *
     * @OA\RequestBody(
     *  required=true,
     *  @OA\JsonContent(
     *    example={
     *      "info": "TO DO information"
     *  },
     *  @OA\Schema (
     *      type="object",
     *      @OA\Property(property="info", required=true, description="Event Status", type="string")
     *    )
     *  )
     * )
     *
     * @OA\Tag(name="Task")
     */
     public function addTask(Request $request, EntityManagerInterface $entityManager): JsonResponse
     {

         $data = json_decode($request->getContent(), true);
         $status = false;
         if (!empty($data['info']))
         {
           $task = new Task();
           $task->setInfo($data['info']);
           $task->setCreateAt(\DateTimeImmutable::createFromMutable((new \DateTime())));
           $entityManager->persist($task);
           $entityManager->flush();
           $status = true;
         }

         return new JsonResponse(['status' => $status]);
     }


  /**
   * Task list info
   *
   * desc.
   *
   * @Route("/api/tasks/{page}", methods={"GET"})
   *
   * @OA\Response(
   *     response=200,
   *     description="Returns tasks list",
   *     @Model(type=Task::class)
   * )
   * @OA\Tag(name="Task")
   */
  public function getList(int $page, EntityManagerInterface $entityManager): JsonResponse
  {

    $myLimit = 10;
    $myOffset = $myLimit * ($page -1);
    $tasks = $entityManager->getRepository(Task::class)->findBy([], [], $myLimit, $myOffset);
    $info = [];
    $needFlush = false;
    foreach ($tasks as $task) {
      if ($this->checkStatus($task)) {
        $needFlush = true;
      }
      $info[] = $this->makeObjetToArr($task);
    }

    if ($needFlush) {
      $entityManager->flush();
    }

    return new JsonResponse($info);
  }


  /**
   * Delete task
   *
   * desc.
   *
   * @Route("/api/delete/{id}", methods={"DELETE"})
   *
   * @OA\Response(
   *    response=200,
   *    description="Returns status",
   *    @OA\Schema (
   *        type="object",
   *        @OA\Property(title="status")
   *    )
   * )
   *
   * @OA\Tag(name="Task")
   */
  public function delete(int $id, EntityManagerInterface $entityManager): JsonResponse
  {
    $item = $entityManager->getRepository(Task::class)->find($id);
    if ($item)
    {
      $entityManager->remove($item);
      $entityManager->flush();
    }
    return new JsonResponse(['status' => true]);
  }

  /**
   * Done task
   *
   * desc.
   *
   * @Route("/api/done/{id}", methods={"PUT"})
   *
   * @OA\Response(
   *    response=200,
   *    description="Returns status",
   *    @OA\Schema (
   *        type="object",
   *        @OA\Property(title="status")
   *    )
   * )
   *
   * @OA\Tag(name="Task")
   */
  public function done(int $id, EntityManagerInterface $entityManager): JsonResponse
  {
    /** @var Task $item */
    $item = $entityManager->getRepository(Task::class)->find($id);
    if ($item)
    {
      $item->setIsDone(true);
      $entityManager->flush();
    }
    return new JsonResponse(['status' => true]);
  }

  /**
   * Update task
   *
   * desc.
   *
   * @Route("/api/update/{id}", methods={"PUT"})
   *
   * @OA\Response(
   *    response=200,
   *    description="Returns status",
   *    @OA\Schema (
   *        type="object",
   *        @OA\Property(title="status")
   *    )
   * )
   *
   * @OA\RequestBody(
   *   required=true,
   *   @OA\JsonContent(
   *     example={
   *       "info": "TO DO information"
   *   },
   *   @OA\Schema (
   *       type="object",
   *       @OA\Property(property="info", required=true, description="Event Status", type="string")
   *     )
   *   )
   *  )
   *
   * @OA\Tag(name="Task")
   */
  public function updateInfo(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
  {
    /** @var Task $item */
    $item = $entityManager->getRepository(Task::class)->find($id);

    $data = json_decode($request->getContent(), true);
    $status = false;

    if ($item && !$item->isDone() && !empty($data['info']))
    {
      $item->setInfo($data['info']);
      $entityManager->flush();
      $status = true;
    }
    return new JsonResponse(['status' => $status]);
  }

  private function makeObjetToArr(Task $item): array
  {
    return  [
      "id" => $item->getId() ,
      "done" => $item->isDone(),
      "info" => $item->getInfo(),
      "status" => $item->getStatus(),
      "countView" => $item->getCountView(),
      "createAt" => $item->getCreateAt()
    ];
  }

  private function checkStatus(Task $item): bool
  {
    $result = false;
    $endData = \DateTimeImmutable::createFromMutable((new \DateTime()));
    $duration = $item->getCreateAt()->diff($endData)->days;

    if ($duration > 1 && $item->getStatus() != 'asap' && !$item->isDone()) {
      $result = true;
      $item->setStatus('asap');
    }

    return $result;
  }


}