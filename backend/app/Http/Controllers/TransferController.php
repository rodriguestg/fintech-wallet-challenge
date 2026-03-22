<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferRequest;
use App\Services\TransferService;
use App\Models\User;

class TransferController extends Controller
{
    private TransferService $transferService;

    public function __construct(TransferService $transferService)
    {
        $this->transferService = $transferService;
        $this->middleware('auth:sanctum');
    }

    public function store(TransferRequest $request)
    {
        $sender = auth()->user();
        $recipient = User::where('email', $request->recipient_email)->firstOrFail();

        try{
            $result = $this->transferService->transferUser($sender, $recipient, $request->amount);


            if($result['success']) {
                return response()->json($result, 200);
            }

            return response()->json($result, 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Erro ao processar a transferência: " . $e->getMessage()
            ], 500);
        }
    }
}
