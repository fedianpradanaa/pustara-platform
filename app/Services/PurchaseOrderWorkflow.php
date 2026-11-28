<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PurchaseOrderWorkflow
{
    public function mapBooks(object $order): void
    {
        $details = DB::table('tpo_detail')
            ->where('client_id', $order->client_id)
            ->where('po_number', $order->po_number)
            ->where('po_date', $order->po_date)
            ->get();

        abort_if($details->isEmpty(), 409, 'PO tidak memiliki buku.');

        foreach ($details as $detail) {
            DB::table('tmapping_book')->insert([
                'client_id' => $detail->client_id,
                'po_number' => $detail->po_number,
                'book_id' => $detail->book_id,
                'copy' => $detail->qty,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function sendToSuppliers(object $order, string $email): void
    {
        $details = DB::table('tpo_detail as detail')
            ->leftJoin('tbook as book', 'detail.book_id', '=', 'book.book_id')
            ->where('detail.client_id', $order->client_id)
            ->where('detail.po_number', $order->po_number)
            ->where('detail.po_date', $order->po_date)
            ->select('book.supplier_id')
            ->get();

        abort_if($details->isEmpty() || $details->contains(fn ($detail) => !$detail->supplier_id), 409, 'Supplier buku pada PO tidak ditemukan.');

        foreach ($details->pluck('supplier_id')->unique() as $supplierId) {
            DB::table('tpo_paid_off')->insert([
                'supplier_id' => $supplierId,
                'client_id' => $order->client_id,
                'po_number' => $order->po_number,
                'po_date' => $order->po_date,
                'payment_image' => '',
                'created_at' => now(),
                'created_by' => $email,
            ]);
        }
    }
}
