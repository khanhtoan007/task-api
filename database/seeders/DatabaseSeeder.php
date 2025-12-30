<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('123123123'),
        ]);

        $admin->assignRole('admin');
        $managers = User::factory(3)->create();
        foreach ($managers as $manager) {
            $manager->assignRole('manager');
        }
        $members = User::factory(7)->create();
        foreach ($members as $member) {
            $member->assignRole('member');
        }
        $projects = Project::factory(5)->create([
            'created_by' => fn () => $managers->random()->id,
        ]);
        // 3. Với mỗi project → tạo task + sub-task
        $projects->each(function (Project $project) use ($members) {

            // số task ngẫu nhiên cho mỗi project
            $tasks = Task::factory(rand(3, 8))->create([
                'project_id' => $project->id,
                'created_by' => fn () => $members->random()->id,
                'assigned_to' => fn () => $members->random()->id,
                'parent_id' => null, // task cha
            ]);

            // 4. Tạo sub-task cho mỗi task
            $tasks->each(function (Task $task) use ($members, $project) {

                Task::factory(rand(0, 3))->create([
                    'project_id' => $project->id,
                    'created_by' => fn () => $members->random()->id,
                    'assigned_to' => fn () => $members->random()->id,
                    'parent_id' => $task->id, // sub-task
                ]);
            });
        });
    }
}
