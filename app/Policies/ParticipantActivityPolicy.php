<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ParticipantActivity;
use Illuminate\Auth\Access\HandlesAuthorization;

class ParticipantActivityPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ParticipantActivity');
    }

    public function view(AuthUser $authUser, ParticipantActivity $participantActivity): bool
    {
        return $authUser->can('View:ParticipantActivity');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ParticipantActivity');
    }

    public function update(AuthUser $authUser, ParticipantActivity $participantActivity): bool
    {
        return $authUser->can('Update:ParticipantActivity');
    }

    public function delete(AuthUser $authUser, ParticipantActivity $participantActivity): bool
    {
        return $authUser->can('Delete:ParticipantActivity');
    }

    public function restore(AuthUser $authUser, ParticipantActivity $participantActivity): bool
    {
        return $authUser->can('Restore:ParticipantActivity');
    }

    public function forceDelete(AuthUser $authUser, ParticipantActivity $participantActivity): bool
    {
        return $authUser->can('ForceDelete:ParticipantActivity');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ParticipantActivity');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ParticipantActivity');
    }

    public function replicate(AuthUser $authUser, ParticipantActivity $participantActivity): bool
    {
        return $authUser->can('Replicate:ParticipantActivity');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ParticipantActivity');
    }

}