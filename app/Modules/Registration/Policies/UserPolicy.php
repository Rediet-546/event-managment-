<?php

namespace App\Modules\Registration\Policies;

use App\Modules\Registration\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view users');
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasPermissionTo('view users') || $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create users');
    }

    public function update(User $user, User $model): bool
    {
        return $user->hasPermissionTo('edit users') || $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->hasPermissionTo('delete users') && $user->id !== $model->id;
    }

    public function approve(User $user, User $model): bool
    {
        return $user->hasPermissionTo('approve creators') && $model->user_type === 'event_creator';
    }

    public function reject(User $user, User $model): bool
    {
        return $user->hasPermissionTo('reject creators') && $model->user_type === 'event_creator';
    }
}