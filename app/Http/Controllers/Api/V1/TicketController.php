<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Gate;

use function Laravel\Prompts\error;

class TicketController extends ApiController
{
    use ApiResponses;
    /**
     * Display a listing of the resource.
     */
    public function index(TicketFilter $filters)
    {
        return TicketResource::collection(Ticket::filter($filters)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {
        try {
            Gate::authorize('store', Ticket::class);
            return new TicketResource(Ticket::create($request->mappedAttributes()));

        } catch (AuthorizationException $e) {
            return $this->error('You are not authorized to create a ticket', 403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($ticket_id)
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);
            if ($this->include('author')) {
                return new TicketResource($ticket->load('user'));
            }
            return new TicketResource($ticket);
        } catch (ModelNotFoundException $e) {
            return $this->error('Ticket not found', 404);
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, $ticket_id)
    {
        //PATCH
        try {
            $ticket = Ticket::findOrFail($ticket_id);

            //policy
            Gate::authorize('update', $ticket);

            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);

        } catch (ModelNotFoundException $e) {
            return $this->error('Ticket not found', 404);
        } catch (AuthorizationException $e) {
            return $this->error('You are not authorized to update this ticket', 403);
        }
    }
    /**
     * Replace the specified resource in storage.
     */
    public function replace(ReplaceTicketRequest $request, $ticket_id)
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);
            Gate::authorize('replace', $ticket);

            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);

        } catch (ModelNotFoundException $e) {
            return $this->error('Ticket not found', 404);
        }


    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ticket_id)
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);
            Gate::authorize('delete', $ticket);
            $ticket->delete();
            return $this->ok('ticket deleted', []);
        } catch (ModelNotFoundException $e) {
            return $this->error('Ticket not found', 404);
        }
    }
}
