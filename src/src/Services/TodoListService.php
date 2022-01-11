<?php

namespace App\Services;

use App\Entity\TodoList;
use App\Repository\TodoListRepository;
use App\Utils\StatusConverter;
use App\ValueObject\Dto\TodoListData;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TodoListService
{
    public function __construct(
        private TodoListRepository $repository,
        private ValidatorInterface $validator,
        private EntityManagerInterface $em,
    ) {}

    public function create(TodoListData $data): void
    {
        $parentTask = null;
        if ($data->parentId && !$parentTask = $this->repository->findOneTaskByIdForUser($data->parentId, $data->userKey)) {
           throw new RuntimeException('Parent task not found');
        }

        $task = new TodoList();

        $this->process($data, $task, $parentTask);
    }

    public function update(TodoListData $data, int $id): void
    {
        $task = $this->repository->findOneTaskByIdForUser($id, $data->userKey);
        if (!$task) {
            throw new RuntimeException('Task not found');
        }

        $parentTask = null;
        if ($data->parentId && !$parentTask = $this->repository->findOneTaskByIdForUser($data->parentId, $data->userKey)) {
            throw new RuntimeException('Parent task not found');
        }

        $this->process($data, $task, $parentTask);
    }

    private function process(TodoListData $data,TodoList $task, ?TodoList $parentTask): void
    {
        $task->setParentTask($parentTask)
            ->setPriority($data->priority)
            ->setTitle($data->title)
            ->setDescription($data->description)
            ->setUserKey($data->userKey)
            ->setStatus($data->status)
            ->setDoneAt(StatusConverter::toBoolean($data->status) ? new DateTime() : null);

        $violations = $this->validator->validate($task);
        if ($violations->count() > 0) {
            throw new RuntimeException($violations[0]->getMessage());
        }

        $this->em->persist($task);
        $this->em->flush();
    }

    public function delete(int $taskId, string $userKey): void
    {
        $task = $this->repository->findOneTaskWithChild($taskId, $userKey);
        if (!$task) {
            throw new RuntimeException('Task not found');
        }

        if ($task->getBooleanStatus()) {
            throw new RuntimeException("Can't delete task with status DONE");
        }

        $this->checkChildTask($task);

        $this->em->remove($task);
        $this->em->flush();
    }

    private function checkChildTask(TodoList $list): void
    {
        /** @var TodoList $subTask */
        foreach ($list->getSubTask() as $subTask) {
            if ($subTask->getBooleanStatus()) {
                throw new RuntimeException("Subtask with ID: {$subTask->getId()} cannot be deleted, because has status: {$subTask->getStatus()}");
            }
            if ($subTask->getSubTask()->count() > 0) {
                $this->checkChildTask($subTask);
            }
        }
    }
}
