<?php

namespace App\Http\Controllers\Core\Transactions;

use App\Http\Controllers\Controller;
use App\Services\PurchaseOrderWorkflow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BookMappingController extends Controller
{
    private function authorizeAdmin(Request $request): void
    {
        abort_unless((string) $request->user()->role === '0', 403);
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);

        return response()->json([
            'clients' => DB::table('tclient')->where('flag_appr', 'Y')->orderBy('instansi_name')->get(['client_id', 'instansi_name']),
            'books' => DB::table('tbook as book')
                ->join('tcompany as supplier', 'book.supplier_id', '=', 'supplier.id')
                ->whereIn('supplier.type', ['1', '3'])
                ->where('book.is_available', true)->where('book.is_editing', false)
                ->orderBy('book.title')
                ->get(['book.book_id', 'book.title', 'book.sellprice', 'supplier.name as supplier_name']),
        ]);
    }

    public function store(Request $request, PurchaseOrderWorkflow $workflow): JsonResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate([
            'client_id' => ['required', Rule::exists('tclient', 'client_id')->where('flag_appr', 'Y')],
            'po_type' => ['required', Rule::in(['1', '2'])],
            'persentase_supplier' => ['required_if:po_type,1', 'nullable', 'numeric', 'gt:0', 'max:100'],
            'books' => ['required', 'array', 'min:1'],
            'books.*.book_id' => ['required', 'string', 'distinct'],
            'books.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        $order = DB::transaction(function () use ($data, $request, $workflow) {
            $client = DB::table('tclient')->where('client_id', $data['client_id'])->where('flag_appr', 'Y')->lockForUpdate()->first();
            abort_unless($client, 409, 'Client tidak lagi tersedia.');
            $ids = collect($data['books'])->pluck('book_id')->sort()->values()->all();
            $books = DB::table('tbook')->whereIn('book_id', $ids)->orderBy('book_id')->lockForUpdate()->get()->keyBy('book_id');
            abort_unless($books->count() === count($ids), 409, 'Satu atau lebih buku tidak ditemukan.');
            abort_if($books->contains(fn ($book) => !$book->is_available || $book->is_editing), 409, 'Satu atau lebih buku tidak tersedia untuk mapping baru.');
            $supplierIds = $books->pluck('supplier_id')->unique()->values();
            $supplierCount = DB::table('tcompany')->whereIn('id', $supplierIds)->whereIn('type', ['1', '3'])->count();
            abort_unless($supplierCount === $supplierIds->count(), 409, 'Supplier buku tidak ditemukan.');

            $type = (string) $data['po_type'];
            abort_if($type === '1' && $books->contains(fn ($book) => !is_numeric($book->sellprice) || $book->sellprice < 0), 409, 'Harga jual buku tidak valid.');
            $order = (object) [
                'client_id' => $client->client_id,
                'po_number' => 'MAP-'.Str::uuid()->toString(),
                'po_date' => now('Asia/Jakarta')->toDateString(),
                'po_type' => $type,
            ];
            $email = $request->user()->email;
            DB::table('tpo_header')->insert([
                'client_id' => $order->client_id,
                'po_number' => $order->po_number,
                'po_date' => $order->po_date,
                'po_type' => $type,
                'po_amount' => 0,
                'po_discount' => 0,
                'persentase_supplier' => $type === '1' ? $data['persentase_supplier'] : 0,
                'distributor_id' => $client->company_id,
                'status' => $type === '1' ? '2' : '3',
                'created_at' => now(),
                'created_by' => $email,
            ]);
            foreach ($data['books'] as $item) {
                DB::table('tpo_detail')->insert([
                    'client_id' => $order->client_id,
                    'po_number' => $order->po_number,
                    'po_date' => $order->po_date,
                    'book_id' => $item['book_id'],
                    'qty' => $item['qty'],
                    'sellprice' => $type === '2' ? 0 : $books[$item['book_id']]->sellprice,
                    'created_at' => now(),
                    'created_by' => $email,
                ]);
            }
            $workflow->mapBooks($order);
            if ($type === '2') {
                $workflow->sendToSuppliers($order, $email);
            }

            return $order;
        });

        return response()->json(['message' => 'Mapping dan PO berhasil dibuat.', 'po_number' => $order->po_number], 201);
    }
}
