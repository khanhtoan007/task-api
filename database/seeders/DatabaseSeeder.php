<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory(10)->create();
        $projects = Project::factory(5)->create([
            'created_by' => fn () => $users->random()->id,
        ]);
        // 3. Với mỗi project → tạo task + sub-task
        $projects->each(function (Project $project) use ($users) {

            // số task ngẫu nhiên cho mỗi project
            $tasks = Task::factory(rand(3, 8))->create([
                'project_id' => $project->id,
                'created_by' => fn () => $users->random()->id,
                'assigned_to' => fn () => $users->random()->id,
                'parent_id' => null, // task cha
            ]);

            // 4. Tạo sub-task cho mỗi task
            $tasks->each(function (Task $task) use ($users, $project) {

                Task::factory(rand(0, 3))->create([
                    'project_id' => $project->id,
                    'created_by' => fn () => $users->random()->id,
                    'assigned_to' => fn () => $users->random()->id,
                    'parent_id' => $task->id, // sub-task
                ]);
            });
        });
    }
}
