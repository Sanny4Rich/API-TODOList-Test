<?php

namespace App\Serializer\Normalizer;

use App\Entity\TodoList;
use SebastianBergmann\CodeCoverage\Report\Xml\Totals;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class TodoListNormalizer implements NormalizerInterface
{

    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        /** @var TodoList $object */
        $data = [
            'id' => $object->getId(),
            'status' => $object->getStatus(),
            'priority'  => $object->getPriority(),
            'title' => $object->getTitle(),
            'description' => $object->getDescription(),
            'createdAt' => $object->getCreatedAt(),
            'doneAt' => $object->getDoneAt(),
        ];

        foreach ($object->getSubTask() as $subTask) {
            $data['subTasks'][] = $this->normalize($subTask);
        }

        return $data;
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return $data instanceof TodoList;
    }
}
