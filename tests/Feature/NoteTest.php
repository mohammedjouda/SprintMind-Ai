<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Note;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test notes index route for authenticated users.
     */
    public function test_authenticated_user_can_access_notes_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('notes.index'));

        $response->assertStatus(200);
    }

    /**
     * Test notes index is guarded for guests.
     */
    public function test_guests_cannot_access_notes_index()
    {
        $response = $this->get(route('notes.index'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test creating a note.
     */
    public function test_user_can_create_a_note()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('notes.store'), [
            'title' => 'Test Note',
            'content' => 'Test content details that represent Markdown input.',
            'color' => 'blue',
            'tags' => ['ideas', 'sprint-ready'],
        ]);

        $response->assertRedirect(route('notes.index'));
        $this->assertDatabaseHas('notes', [
            'user_id' => $user->id,
            'title' => 'Test Note',
            'color' => 'blue',
        ]);
    }

    /**
     * Test pinning note via AJAX toggle.
     */
    public function test_user_can_toggle_note_pin_status()
    {
        $user = User::factory()->create();
        $note = Note::create([
            'user_id' => $user->id,
            'title' => 'Test Pinned Note',
            'content' => 'Content details',
            'is_pinned' => false,
        ]);

        $response = $this->actingAs($user)->patch(route('notes.toggle-pin', $note->id));

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'is_pinned' => true]);
        $this->assertTrue($note->fresh()->is_pinned);
    }

    /**
     * Test committing AI-generated tasks to the backlog.
     */
    public function test_user_can_commit_extracted_tasks_to_backlog()
    {
        $user = User::factory()->create();
        $project = Project::create([
            'user_id' => $user->id,
            'name' => 'Backlog Project',
            'category' => 'software',
        ]);

        $response = $this->actingAs($user)->post(route('notes.commit-tasks'), [
            'project_id' => $project->id,
            'tasks' => [
                [
                    'title' => 'Implement Auth System',
                    'priority' => 'high',
                    'story_points' => 5,
                    'acceptance_criteria' => 'User can log in and log out.',
                ],
                [
                    'title' => 'Setup SQLite DB',
                    'priority' => 'medium',
                    'story_points' => 2,
                    'acceptance_criteria' => 'Migration runs with no errors.',
                ],
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'count' => 2]);

        $this->assertDatabaseHas('tasks', [
            'project_id' => $project->id,
            'title' => 'Implement Auth System',
            'priority' => 'high',
            'story_points' => 5,
            'due_date' => null,
        ]);

        $this->assertDatabaseHas('acceptance_criterias', [
            'title' => 'User can log in and log out.',
        ]);
    }
}
