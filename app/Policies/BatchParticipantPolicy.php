<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\BatchParticipant;
use Illuminate\Auth\Access\HandlesAuthorization;

class BatchParticipantPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:BatchParticipant');
    }

    public function view(AuthUser $authUser, BatchParticipant $batchParticipant): bool
    {
        return $authUser->can('View:BatchParticipant');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:BatchParticipant');
    }

    public function update(AuthUser $authUser, BatchParticipant $batchParticipant): bool
    {
        return $authUser->can('Update:BatchParticipant');
    }

    public function delete(AuthUser $authUser, BatchParticipant $batchParticipant): bool
    {
        return $authUser->can('Delete:BatchParticipant');
    }

    public function restore(AuthUser $authUser, BatchParticipant $batchParticipant): bool
    {
        return $authUser->can('Restore:BatchParticipant');
    }

    public function forceDelete(AuthUser $authUser, BatchParticipant $batchParticipant): bool
    {
        return $authUser->can('ForceDelete:BatchParticipant');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:BatchParticipant');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:BatchParticipant');
    }

    public function replicate(AuthUser $authUser, BatchParticipant $batchParticipant): bool
    {
        return $authUser->can('Replicate:BatchParticipant');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:BatchParticipant');
    }

}