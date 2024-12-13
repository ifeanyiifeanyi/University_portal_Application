<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Admin;
use App\Models\Ticket;
use Twilio\Jwt\TaskRouter\Policy;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

#[Policy(Ticket::class)]
class TicketPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function delete(User $user, Ticket $ticket)
    {
        return $user->isAdmin()  || $user->admin?->role === Admin::TYPE_SUPER_ADMIN;
    }
}
