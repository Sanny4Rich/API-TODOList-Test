<?php

namespace App\Controller;

use App\Services\TodoListService;
use App\ValueObject\Dto\TodoListDataFactory;
use App\ViewModel\TodoListViewModel;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TodoListController extends AbstractController
{
    public function __construct(
        private TodoListService $service,
    ) {}

    #[Route('/list/{userKey}/{orderingField}/{orderingType}',
        defaults: ['userKey' => '', 'orderingField' => 'id', 'orderingType' => 'ASC']
    )]
    public function list(Request $request, TodoListViewModel $viewModel): Response
    {
        if (!$this->checkUser($request)) {
            return $this->json('User key is empty', Response::HTTP_UNAUTHORIZED);
        }

        return new Response($viewModel->list());
    }

    #[Route('/get/{userKey}/{id}')]
    public function get(Request $request, TodoListViewModel $viewModel): Response
    {
        if (!$this->checkUser($request)) {
            return $this->json('User key is empty', Response::HTTP_UNAUTHORIZED);
        }

        return new Response($viewModel->get());
    }

    #[Route('/create/{userKey}', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        if (!$this->checkUser($request)) {
            return $this->json('User key is empty', Response::HTTP_UNAUTHORIZED);
        }

        $dto = TodoListDataFactory::fromRequest($request);

        try {
            $this->service->create($dto);
            return $this->json('Task successfully created', Response::HTTP_CREATED);
        } catch (Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route('/update/{userKey}/{id}', methods: ["POST"])]
    public function update(Request $request): JsonResponse
    {
        if (!$this->checkUser($request)) {
            return $this->json('User key is empty', Response::HTTP_UNAUTHORIZED);
        }

        $dto = TodoListDataFactory::fromRequest($request);

        $id = $request->attributes->getInt('id');

        try {
            $this->service->update($dto, $id);
            return $this->json('Entity successfully updated', Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route('/delete/{userKey}/{id}', methods: ["POST"])]
    public function delete(Request $request): JsonResponse
    {
        if (!$this->checkUser($request)) {
            return $this->json('User key is empty', Response::HTTP_UNAUTHORIZED);
        }

        $id = $request->attributes->getInt('id');
        $userKey = $request->attributes->get('userKey');

        try {
            $this->service->delete($id, $userKey);
            return $this->json(['success' => true], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    private function checkUser(Request $request): bool {
        return $request->attributes->get('userKey') !== '';
    }
}
