<?php

namespace App\Observers;

use App\Models\Sprint;
use App\Notifications\SprintStartedNotification;

class SprintObserver
{
    /**
     * Handle the Sprint "updated" event.
     */
    public function updated(Sprint $sprint): void
    {
        if ($sprint->wasChanged('status') && $sprint->status === 'active') {
            if ($sprint->user) {
                $sprint->user->notify(new SprintStartedNotification($sprint));
            }
        }
    }
}
