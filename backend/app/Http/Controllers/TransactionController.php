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
        $transactions = Transaction::where('user_id', $user->id)->when($request->filled('type'), function ($q) use ($request) {
            return $q->where('type', $request->type);
        })
        ->when($request->filled('start_date'), function ($q) use ($request) {
            return $q->whereDate('created_at', '>=', $request->start_date);
        })
        ->when($request->filled('end_date'), function ($q) use ($request) {
            return $q->whereDate('created_at', '<=', $request->end_date);
        });

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

    public function recent(Request $request)
    {
        $user = auth()->user();

        $transactions = Transaction::query()
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->with(['sender:id,name,email', 'recipient:id,name,email'])
            ->limit(5)
            ->get();

        return TransactionResource::collection($transactions);
    }
}
