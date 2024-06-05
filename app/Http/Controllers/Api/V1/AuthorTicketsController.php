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
    public function store($author_id, StoreTicketRequest $request)
    {

        return Ticket::create($request->mappedAttributes());
    }

    /**
     * Replace the specified resource in storage.
     */
    public function replace(ReplaceTicketRequest $request, $author_id, $ticket_id)
    {
        //PUT
        try {
            $ticket = Ticket::findOrFail($ticket_id);
            if ($ticket->user_id == $author_id) {
                $ticket->update($request->mappedAttributes());
                return new TicketResource($ticket);
            }
            //Todo:Ticket doesnot belong to author


        } catch (ModelNotFoundException $e) {
            return $this->error('Ticket not found', 404);
        }


    }
    public function update(UpdateTicketRequest $request, $author_id, $ticket_id)
    {
        //PUT
        try {
            $ticket = Ticket::findOrFail($ticket_id);
            //policy
            Gate::authorize('update', $ticket);
            if ($ticket->user_id == $author_id) {

                $ticket->update($request->mappedAttributes());
                return new TicketResource($ticket);
            }
            //Todo:Ticket doesnot belong to author


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
            $ticket = Ticket::findOrFail($ticket_id);
            if ($ticket->user_id != $author_id) {
                return $this->error('Ticket not found', 404);
            }
            $ticket->delete();
            return $this->ok('ticket deleted', []);
        } catch (ModelNotFoundException $e) {
            return $this->error('Ticket not found', 404);
        }
    }
}
