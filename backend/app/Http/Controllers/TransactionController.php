<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use Illuminate\Http\Request;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function __contructor()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $transactions = Transaction::where('user_id', $user->id);

        if($request->has('type') && in_array($request->type, ['sent', 'received'])) {
            $transactions->where('type', $request->type);
        }

        if($request->has('start_date')) {
            $transactions->whereDate('created_at', '>=', $request->start_date);
        }

        if($request->has('end_date')) {
            $transactions->whereDate('created_at', '<=', $request->end_date);
        }

        $extract = $transactions->with(['sender:id,name,email', 'recipient:id,name,email'])->orderBy('created_at', 'desc')->paginate(15);

        return TransactionResource::collection($extract)->response()->setStatusCode(200);
    }

    public function show($id)
    {
        $user = auth()->user();
        $transaction = Transaction::where(function($q) use ($user) {
            $q->where('sender_id', $user->id)
              ->orWhere('recipient_id', $user->id);
        })->findOrFail($id);

        $transaction->load(['sender:id,name,email', 'recipient:id,name,email']);

        return response()->json($transaction, 200);
    }
}
