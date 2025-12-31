<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

final class TaskPolicy
{
    /**
     * Global authorization hook
     * Chạy trước TẤT CẢ các method bên dưới
     */
    public function before(User $user, string $ability): ?bool
    {
        // Admin có toàn quyền
        if ($user->hasRole('admin')) {
            return true;
        }

        return null; // tiếp tục check các rule bên dưới
    }

    /**
     * Xem danh sách task
     */
    public function viewAny(User $user): bool
    {
        return $user->can('task.view');
    }

    /**
     * Xem chi tiết 1 task
     */
    public function view(User $user, Task $task): bool
    {
        // Không có quyền xem task
        if (! $user->can('task.view')) {
            return false;
        }

        // Chỉ được xem task liên quan
        return
            $task->created_by === $user->id
            || $task->assigned_to === $user->id
            || $this->isProjectManager($user, $task);
    }

    /**
     * Tạo task
     */
    public function create(User $user): bool
    {
        return $user->can('task.create');
    }

    /**
     * Cập nhật task
     */
    public function update(User $user, Task $task): bool
    {
        // Không có quyền update
        if (! $user->can('task.update')) {
            return false;
        }

        // Manager có thể update task trong project họ quản lý
        if ($this->isProjectManager($user, $task)) {
            return true;
        }

        // User thường: chỉ update task mình tạo hoặc được assign
        return
            $task->created_by === $user->id
            || $task->assigned_to === $user->id;
    }

    /**
     * Check user có phải manager của project chứa task hay không
     */
    private function isProjectManager(User $user, Task $task): bool
    {
        if (! $task->project) {
            return false;
        }

        return $task->project->created_by_user->id === $user->id;
    }
}
