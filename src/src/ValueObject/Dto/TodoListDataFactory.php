<?php

namespace App\ValueObject\Dto;

use App\Utils\StatusConverter;
use Symfony\Component\HttpFoundation\Request;

class TodoListDataFactory
{
    public static function fromRequest(Request $request): TodoListData
    {
        $userKey = $request->attributes->get('userKey');
        $title = $request->request->get('title','');
        $description = $request->request->get('description');
        $status = $request->request->get('status', StatusConverter::TODO_STATUS);
        $priority = $request->request->getInt('priority');
        $parentId = $request->request->get('parentId');

        return new TodoListData($status, $priority, $title, $description, $userKey, $parentId);
    }
}
