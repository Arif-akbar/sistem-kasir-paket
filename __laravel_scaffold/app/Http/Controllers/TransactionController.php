<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Contracts\View\View;

class TransactionController extends Controller
{
    public function index(): View
    {
        $transactions = Transaction::query()
            ->with(['package.originBranch', 'cashier'])
            ->latest('paid_at')
            ->paginate(15);

        return view('transactions.index', [
            'transactions' => $transactions,
        ]);
    }

    public function create(): View
    {
        return view('transactions.create');
    }

    public function show(Transaction $transaction): View
    {
        $transaction->load(['package.originBranch', 'package.destinationBranch', 'cashier', 'branch']);

        return view('transactions.show', [
            'transaction' => $transaction,
        ]);
    }
}
