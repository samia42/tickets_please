<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Traits\ApiResponses;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Gate;

class AuthorTicketsController extends ApiController
{
    use ApiResponses;
    public function index($author_id, TicketFilter $filters)
    {
        return TicketResource::collection(
            Ticket::where('user_id', $author_id)->filter($filters)->paginate());
    }
    public function store(StoreTicketRequest $request, $author_id)
    {
        try {
            Gate::authorize('store', Ticket::class);
            return new TicketResource(Ticket::create($request->mappedAttributes([
                'author' => 'user_id'
            ])));

        } catch (AuthorizationException $e) {
            return $this->error('You are not authorized to create a ticket', 403);
        }
    }

    /**
     * Replace the specified resource in storage.
     */
    public function replace(ReplaceTicketRequest $request, $author_id, $ticket_id)
    {
        //PUT
        try {

            $ticket = Ticket::
                where('id', $ticket_id)
                ->where('user_id', $author_id)
                ->firstOrFail();
            Gate::authorize('replace', $ticket);
            $ticket->update($request->mappedAttributes());
            return new TicketResource($ticket);

        } catch (ModelNotFoundException $e) {
            return $this->error('Ticket not found', 404);
        } catch (AuthorizationException $e) {
            return $this->error('You are not authorized to replace a ticket', 403);
        }


    }
    public function update(UpdateTicketRequest $request, $author_id, $ticket_id)
    {
        //PUT
        try {
            $ticket = Ticket::
                where('id', $ticket_id)
                ->where('user_id', $author_id)
                ->firstOrFail();
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
     * Remove the specified resource from storage.
     */
    public function destroy($author_id, $ticket_id)
    {
        try {
            $ticket = Ticket::
                where('id', $ticket_id)
                ->where('user_id', $author_id)
                ->firstOrFail();

            Gate::authorize('update', $ticket);

            $ticket->delete();

            return $this->ok('ticket deleted', []);
        } catch (ModelNotFoundException $e) {
            return $this->error('Ticket not found', 404);
        } catch (AuthorizationException $e) {
            return $this->error('You are not authorized to delete this ticket', 403);
        }
    }
}
