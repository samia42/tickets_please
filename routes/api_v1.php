<?php

use App\Http\Controllers\Api\V1\TicketController;
use App\Http\Controllers\Api\V1\AuthorsController;
use App\Http\Controllers\Api\V1\AuthorTicketsController;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::apiResource('tickets',TicketController::class)->middleware('auth:sanctum');
Route::apiResource('authors',AuthorsController::class)->middleware('auth:sanctum');
Route::apiResource('authors.tickets',AuthorTicketsController::class)->middleware('auth:sanctum');



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

