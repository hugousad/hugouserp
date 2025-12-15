<?php

declare(strict_types=1);

namespace App\Livewire\Pos;

use App\Models\Sale;
use Livewire\Component;

class ReceiptPreview extends Component
{
    public ?int $saleId = null;

    public ?array $receiptData = null;

    protected $listeners = ['showReceipt' => 'loadReceipt'];

    public function loadReceipt(int $saleId): void
    {
        $this->saleId = $saleId;
        $sale = Sale::with(['items.product', 'customer', 'branch', 'payments'])->find($saleId);
        
        if ($sale) {
            $this->receiptData = [
                'receipt_number' => $sale->invoice_number ?? 'INV-' . str_pad((string) $sale->id, 6, '0', STR_PAD_LEFT),
                'date' => $sale->created_at->format('Y-m-d H:i'),
                'branch' => $sale->branch?->name ?? config('app.name'),
                'customer' => $sale->customer?->name ?? __('Walk-in Customer'),
                'items' => $sale->items->map(fn ($item) => [
                    'name' => $item->product?->name ?? $item->product_name ?? 'Item',
                    'qty' => $item->quantity,
                    'price' => $item->unit_price,
                    'total' => $item->total,
                ])->toArray(),
                'subtotal' => $sale->subtotal,
                'discount' => $sale->discount_amount,
                'tax' => $sale->tax_amount,
                'total' => $sale->total,
                'payments' => $sale->payments->map(fn ($p) => [
                    'method' => ucfirst($p->payment_method),
                    'amount' => $p->amount,
                ])->toArray(),
            ];
        }
    }

    public function print(): void
    {
        if ($this->saleId) {
            $this->dispatch('printReceipt', saleId: $this->saleId);
        }
    }

    public function close(): void
    {
        $this->saleId = null;
        $this->receiptData = null;
    }

    public function render()
    {
        return view('livewire.pos.receipt-preview');
    }
}
