<?php

declare(strict_types=1);

namespace Tests\Feature\Helpdesk\Tickets;

use App\Livewire\Helpdesk\TicketDetail;
use App\Models\Branch;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketPriority;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Tests\TestCase;

class TicketAssignmentBranchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Gate::define('helpdesk.view', fn () => true);
        Gate::define('helpdesk.assign', fn () => true);
    }

    protected function createCategory(): TicketCategory
    {
        return TicketCategory::create([
            'name' => 'Support',
            'slug' => 'support',
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

    public function test_assigning_ticket_to_other_branch_user_is_rejected(): void
    {
        $branchA = Branch::factory()->create();
        $branchB = Branch::factory()->create();

        $agentA = User::factory()->create(['branch_id' => $branchA->id]);
        $agentB = User::factory()->create(['branch_id' => $branchB->id]);

        $ticket = Ticket::create([
            'ticket_number' => 'TKT-910001',
            'subject' => 'Assignment test',
            'description' => 'Test',
            'status' => 'open',
            'priority_id' => $this->createPriority()->id,
            'category_id' => $this->createCategory()->id,
            'branch_id' => $branchA->id,
        ]);

        Livewire::actingAs($agentA)
            ->test(TicketDetail::class, ['ticket' => $ticket])
            ->set('assignToUser', $agentB->id)
            ->call('assignTicket')
            ->assertHasErrors(['assignToUser' => 'exists']);

        $this->assertNull($ticket->fresh()->assigned_to);
    }
}
