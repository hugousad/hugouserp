<?php

declare(strict_types=1);

namespace Tests\Feature\Helpdesk\Tickets;

use App\Livewire\Helpdesk\Tickets\Form;
use App\Models\Branch;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketPriority;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class TicketFormBranchAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Gate::define('helpdesk.manage', fn () => true);
    }

    protected function createCategory(): TicketCategory
    {
        return TicketCategory::create([
            'name' => 'General',
            'slug' => 'general',
            'is_active' => true,
        ]);
    }

    protected function createPriority(): TicketPriority
    {
        return TicketPriority::create([
            'name' => 'Medium',
            'slug' => 'medium',
            'level' => 2,
            'color' => '#FFA500',
            'is_active' => true,
        ]);
    }

    public function test_manager_cannot_edit_ticket_from_other_branch(): void
    {
        $branchA = Branch::factory()->create();
        $branchB = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => $branchA->id]);
        $ticket = Ticket::create([
            'ticket_number' => 'TKT-900001',
            'subject' => 'Branch B Ticket',
            'description' => 'Test',
            'status' => 'new',
            'priority_id' => $this->createPriority()->id,
            'category_id' => $this->createCategory()->id,
            'branch_id' => $branchB->id,
        ]);

        $this->expectException(HttpException::class);
        Livewire::actingAs($user)
            ->test(Form::class, ['ticket' => $ticket]);
    }

    public function test_branch_remains_unchanged_when_ticket_is_updated(): void
    {
        $branch = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => $branch->id]);
        $ticket = Ticket::create([
            'ticket_number' => 'TKT-900002',
            'subject' => 'Original',
            'description' => 'Test',
            'status' => 'new',
            'priority_id' => $this->createPriority()->id,
            'category_id' => $this->createCategory()->id,
            'branch_id' => $branch->id,
        ]);

        Livewire::actingAs($user)
            ->test(Form::class, ['ticket' => $ticket])
            ->set('subject', 'Updated Subject')
            ->set('status', 'open')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame($branch->id, $ticket->fresh()->branch_id);
    }
}
