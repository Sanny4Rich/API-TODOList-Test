<?php

namespace App\ViewModel;

use App\Repository\TodoListRepository;
use App\Serializer\Normalizer\TodoListNormalizer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

class TodoListViewModel
{
    public function __construct(
        private RequestStack $requestStack,
        private TodoListRepository $repository,
    ) {}

    public function list(): string {
        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();
        $query = $request->query->all();
        $orderingField = $request->attributes->get('orderingField');
        $orderingType = $request->attributes->get('orderingType');

        $user = $request->attributes->get('userKey');

        $tasks = $this->repository->findAllForUser($user, $query, $orderingField, $orderingType);

        return (new Serializer([new TodoListNormalizer()], [new JsonEncoder()]))->serialize($tasks, 'json');
    }

    public function get(): string {
        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();

        $user = $request->attributes->get('userKey');
        $id = $request->attributes->getInt('id');

        $task = $this->repository->findOneTaskByIdForUser($id, $user);

        return (new Serializer([new TodoListNormalizer()], [new JsonEncoder()]))->serialize($task, 'json');
    }
}
