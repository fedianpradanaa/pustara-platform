<?php

namespace App\Http\Controllers\Core\Upload;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\ApprovalBooksUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BookManagementController extends Controller
{
    private function supplier(Request $request): string
    {
        abort_unless((int) $request->user()->role === 1 && $request->user()->client_id, 403);

        return $request->user()->client_id;
    }

    public function availability(Request $request, string $id): JsonResponse
    {
        $supplier = $this->supplier($request);
        $data = $request->validate(['is_available' => 'required|boolean']);

        DB::transaction(function () use ($id, $supplier, $data) {
            $book = DB::table('tbook')->where('book_id', $id)->where('supplier_id', $supplier)->lockForUpdate()->first();
            abort_unless($book, 404);
            abort_if($data['is_available'] && $book->is_editing, 409, 'Buku sedang dalam proses perubahan.');

            DB::table('tbook')->where('book_id', $id)->update([
                'is_available' => $data['is_available'],
                'updatedate' => now(),
                'updateby' => auth()->user()->email,
            ]);
        });

        return response()->json(['message' => $data['is_available'] ? 'Buku diaktifkan untuk dijual.' : 'Buku ditarik dari penjualan.']);
    }

    public function revise(Request $request, string $id): JsonResponse
    {
        $supplier = $this->supplier($request);
        $rules = (new ApprovalBooksUpdateRequest())->rules();
        unset($rules['book_id']);
        $rules['publisher_id'] = ['required', Rule::exists('tpublisher', 'id')->where('client_id', $supplier)];
        $rules['category_id'] = ['required', Rule::exists('tcompany_category', 'category_id')->where('client_id', $supplier)];
        $rules['book_format_id'] = ['nullable', Rule::exists('tbook_format', 'book_format_id')];
        $data = $request->validate($rules);

        DB::transaction(function () use ($id, $supplier, $data, $request) {
            $book = DB::table('tbook')->where('book_id', $id)->where('supplier_id', $supplier)->lockForUpdate()->first();
            abort_unless($book, 404);
            $draft = DB::table('tbook_draft')->where('book_id', $id)->where('supplier_id', $supplier)->lockForUpdate()->first();
            abort_unless($draft, 404);
            abort_unless((string) $draft->status === '3' || ($book->is_editing && (string) $draft->status === '5'), 409, 'Perubahan buku masih diproses.');

            DB::table('tbook_draft')->where('book_id', $id)->update(array_merge($data, [
                'status' => '2',
                'reason' => null,
                'updatedate' => now(),
                'updateby' => $request->user()->email,
            ]));
            DB::table('tbook')->where('book_id', $id)->update(['is_editing' => true]);
        });

        return response()->json(['message' => 'Perubahan buku dikirim untuk persetujuan admin.']);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $supplier = $this->supplier($request);
        $files = DB::transaction(function () use ($id, $supplier) {
            $draft = DB::table('tbook_draft')->where('book_id', $id)->where('supplier_id', $supplier)->lockForUpdate()->first();
            abort_unless($draft, 404);
            abort_unless((string) $draft->status === '1', 409, 'Hanya buku berstatus draft yang dapat dihapus.');
            abort_if(DB::table('tbook')->where('book_id', $id)->exists()
                || DB::table('tmapping_book')->where('book_id', $id)->exists()
                || DB::table('tpo_detail')->where('book_id', $id)->exists(), 409, 'Buku memiliki riwayat publikasi atau transaksi.');

            DB::table('tbook_draft')->where('book_id', $id)->delete();

            return ['filename' => $draft->filename, 'cover' => $draft->cover];
        });

        foreach ([
            ['local', 'tmp/books_tmp/', 'filename'],
            ['local', 'books_draft/', 'filename'],
            ['public', 'tmp/covers_tmp/', 'cover'],
            ['public', 'covers_draft/', 'cover'],
        ] as [$disk, $directory, $field]) {
            $name = $files[$field];
            if (!$name || basename($name) !== $name
                || DB::table('tbook_draft')->where($field, $name)->exists()
                || DB::table('tbook')->where($field, $name)->exists()) {
                continue;
            }
            if (Storage::disk($disk)->exists($directory.$name) && !Storage::disk($disk)->delete($directory.$name)) {
                report(new \RuntimeException('Gagal menghapus file draft '.$directory.$name));
                return response()->json(['message' => 'Draft dihapus, tetapi file tidak dapat dibersihkan. Hubungi admin.'], 500);
            }
        }

        return response()->json(['message' => 'Draft buku berhasil dihapus.']);
    }
}
