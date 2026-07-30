<?php

namespace App\Services;

class PaymentService
{
        public function generateNumber(string $prefix): string
    {
        return $prefix .now()->format('YmdHis');
    }
}