<?php

namespace App\Services\Task;

use App\Models\Task;

final class CreateTaskService
{
    public function execute(string $title, string $description, string $status)
    {
        $task = Task::create([
            'title' => $title,
            'description' => $description,
            'status' => $status,
        ]);

        return $task;
    }
}
