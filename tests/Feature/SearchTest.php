<?php

use App\Models\User;
use App\Models\Task;
use App\Models\Sprint;
use App\Models\Project;

it('requires authentication to search', function () {
    $response = $this->getJson(route('global.search', ['q' => 'test']));
    $response->assertStatus(401);
});

it('returns empty array when query is less than 2 characters', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson(route('global.search', ['q' => 'a']));
    $response->assertStatus(200);
    $response->assertJsonCount(0);
});

it('returns tasks and sprints belonging to the user matching the query', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    // Create projects
    $project = Project::create([
        'user_id' => $user->id,
        'name' => 'User Project',
    ]);
    
    $otherProject = Project::create([
        'user_id' => $otherUser->id,
        'name' => 'Other Project',
    ]);

    // Create tasks for user
    $matchingTask = Task::create([
        'user_id' => $user->id,
        'project_id' => $project->id,
        'title' => 'Important Task Search',
        'status' => 'pending',
        'priority' => 'high',
    ]);

    $nonMatchingTask = Task::create([
        'user_id' => $user->id,
        'project_id' => $project->id,
        'title' => 'Random Job',
        'status' => 'pending',
        'priority' => 'high',
    ]);

    // Create tasks for other user with same title (should not be returned)
    $otherUserMatchingTask = Task::create([
        'user_id' => $otherUser->id,
        'project_id' => $otherProject->id,
        'title' => 'Important Task Search',
        'status' => 'pending',
        'priority' => 'high',
    ]);

    // Create sprints for user
    $matchingSprint = Sprint::create([
        'user_id' => $user->id,
        'project_id' => $project->id,
        'name' => 'Sprint Search Match',
        'status' => 'active',
    ]);

    $nonMatchingSprint = Sprint::create([
        'user_id' => $user->id,
        'project_id' => $project->id,
        'name' => 'Sprint Alpha',
        'status' => 'planned',
    ]);

    // Search query
    $response = $this->actingAs($user)->getJson(route('global.search', ['q' => 'Search']));

    $response->assertStatus(200);
    $response->assertJsonCount(2);

    $response->assertJsonFragment([
        'type' => 'task',
        'title' => $matchingTask->title,
        'url' => route('tasks.show', $matchingTask->id),
    ]);

    $response->assertJsonFragment([
        'type' => 'sprint',
        'title' => $matchingSprint->name,
        'url' => route('sprints.show', $matchingSprint->id),
    ]);
});
