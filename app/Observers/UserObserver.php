<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $user->projects()->create([
            'name' => 'Inbox',
            'is_inbox' => true,
            'category' => 'personal',
            'status' => 'active',
        ]);
    }
}
