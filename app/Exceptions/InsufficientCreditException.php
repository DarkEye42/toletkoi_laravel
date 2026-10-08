<?php

namespace App\Exceptions;

use Exception;

class InsufficientCreditException extends Exception
{
    public int $creditsNeeded;
    public int $creditsAvailable;

    public function __construct(int $creditsNeeded, int $creditsAvailable, string $message = '')
    {
        $this->creditsNeeded = $creditsNeeded;
        $this->creditsAvailable = $creditsAvailable;

        $message = $message ?: "Insufficient credits. You need {$creditsNeeded} credits but only have {$creditsAvailable}.";

        parent::__construct($message);
    }

    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $this->getMessage(),
                'credits_needed' => $this->creditsNeeded,
                'credits_available' => $this->creditsAvailable,
            ], 422);
        }

        return redirect()->back()->withErrors(['credits' => $this->getMessage()]);
    }
}
