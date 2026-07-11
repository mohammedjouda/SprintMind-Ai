<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Observers\UserObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);

        // Auto-migration & retroactive fix for Inbox pattern
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('projects') && ! \Illuminate\Support\Facades\Schema::hasColumn('projects', 'is_inbox')) {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);

                // Retroactive fix for existing users
                \App\Models\User::all()->each(function ($user) {
                    if (!$user->projects()->where('is_inbox', true)->exists()) {
                        $user->projects()->create([
                            'name' => 'Inbox',
                            'is_inbox' => true,
                            'category' => 'personal',
                            'status' => 'active',
                        ]);
                    }
                });
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Auto-migration/Inbox fix failed: ' . $e->getMessage());
        }
    }
}
