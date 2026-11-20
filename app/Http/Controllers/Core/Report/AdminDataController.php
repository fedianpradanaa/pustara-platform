<?php

namespace App\Http\Controllers\Core\Report;

use App\Exports\Report\ReportBook\ReportBookExport;
use App\Exports\Report\ReportClient\ReportClientExport;
use App\Http\Controllers\Controller;
use Closure;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Yajra\DataTables\Facades\DataTables;

class AdminDataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function (Request $request, Closure $next) {
            abort_unless((int) $request->user()->role === 0, 403);

            return $next($request);
        });
    }

    public function clients(): JsonResponse
    {
        return DataTables::of($this->clientQuery())
            ->escapeColumns()
            ->toJson();
    }

    public function exportClients(): BinaryFileResponse
    {
        return Excel::download(new ReportClientExport($this->clientQuery()), 'data_client.xlsx');
    }

    public function books(Request $request): JsonResponse
    {
        $filters = $this->validateBookFilters($request);

        return DataTables::of($this->bookQuery($filters))
            ->escapeColumns()
            ->addColumn('path_cover', function ($book) {
                if (!$book->cover) {
                    return '';
                }

                $locations = ['published', 'temporary', 'draft'];
                $disk = Storage::disk('public');
                $coverPaths = [
                    'published' => 'covers/'.$book->cover,
                    'temporary' => 'tmp/covers_tmp/'.$book->cover,
                    'draft' => 'covers_draft/'.$book->cover,
                ];

                foreach ($locations as $location) {
                    if ($disk->exists($coverPaths[$location])) {
                        return route('encrypt-books-cover', [
                            'location' => $location,
                            'filename' => $book->cover,
                        ]);
                    }
                }

                return '';
            })
            ->toJson();
    }

    public function exportBooks(Request $request): BinaryFileResponse
    {
        $filters = $this->validateBookFilters($request);

        return Excel::download(new ReportBookExport($this->bookQuery($filters)), 'data_buku.xlsx');
    }

    private function clientQuery(): Builder
    {
        return DB::table('tclient as a')
            ->select(
                'a.client_id as client_id',
                'a.instansi_name as instansi_name',
                'a.application_name as application_name',
                'a.company_id as company_id',
                'g.name as distributor_name',
                'a.address as address',
                'b.country_name as country_name',
                'c.provinsi_name as provinsi_name',
                'd.kabupaten_name as kabupaten_name',
                'e.kecamatan_name as kecamatan_name',
                'f.kelurahan_name as kelurahan_name',
                'a.kodepos as kodepos',
                'a.npwp as npwp',
                'a.pers_responsible as pers_responsible',
                'a.pos_pers_responsible as pos_pers_responsible',
                'a.mou_sign_name as mou_sign_name',
                'a.pos_sign_name as pos_sign_name',
                'a.administrator_name as administrator_name',
                'a.administrator_phone as administrator_phone',
                'a.web_add as web_add',
                'a.client_category as client_category',
                'a.client_level as client_level',
                'a.agreement as agreement',
                'a.flag_appr as approval_status',
                'a.created_at as created_at',
                'a.updated_at as updated_at'
            )
            ->leftJoin('tcountry as b', 'a.country_id', '=', 'b.country_id')
            ->leftJoin('tprovinsi as c', 'a.provinsi_id', '=', 'c.provinsi_id')
            ->leftJoin('tkabupaten as d', 'a.kabupaten_id', '=', 'd.kabupaten_id')
            ->leftJoin('tkecamatan as e', 'a.kecamatan_id', '=', 'e.kecamatan_id')
            ->leftJoin('tkelurahan as f', 'a.kelurahan_id', '=', 'f.kelurahan_id')
            ->leftJoin('tcompany as g', function ($join) {
                $join->on('a.company_id', '=', 'g.id')
                    ->where('g.type', '=', '2');
            })
            ->orderBy('a.client_id');
    }

    private function validateBookFilters(Request $request): array
    {
        return $request->validate([
            'suppliers' => ['sometimes', 'array'],
            'suppliers.*' => ['string', 'max:50'],
            'categories' => ['sometimes', 'array'],
            'categories.*' => ['string', 'max:100'],
            'publishers' => ['sometimes', 'array'],
            'publishers.*' => ['string', 'max:100'],
        ]);
    }

    private function bookQuery(array $filters = []): Builder
    {
        $query = DB::table('tbook as a')
            ->select(
                'a.book_id as book_id',
                'a.supplier_id as supplier_id',
                's.name as supplier_name',
                'a.cover as cover',
                'a.isbn as isbn',
                'a.eisbn as eisbn',
                'a.title as title',
                'a.writer as writer',
                'a.publisher_id as publisher_id',
                'p.description as publisher_name',
                'a.category_id as category_id',
                'c.category_desc as category_name',
                'a.size as size',
                'a.year as year',
                'a.volume as volume',
                'a.edition as edition',
                'a.page as page',
                'a.sinopsis as sinopsis',
                'a.sellprice as sellprice',
                'a.rentprice as rentprice',
                'a.retailprice as retailprice',
                'a.city as city',
                'a.book_format_id as book_format_id',
                'a.age as age',
                'a.createdate as createdate',
                'a.createby as createby',
                'a.updatedate as updatedate',
                'a.updateby as updateby'
            )
            ->leftJoin('tcompany as s', function ($join) {
                $join->on('a.supplier_id', '=', 's.id')
                    ->where('s.type', '=', '1');
            })
            ->leftJoin('tcompany_category as c', function ($join) {
                $join->on('a.supplier_id', '=', 'c.client_id')
                    ->on('a.category_id', '=', 'c.category_id');
            })
            ->leftJoin('tpublisher as p', function ($join) {
                $join->on('a.supplier_id', '=', 'p.client_id')
                    ->on('a.publisher_id', '=', 'p.id');
            });

        if (!empty($filters['suppliers'])) {
            $query->whereIn('a.supplier_id', $filters['suppliers']);
        }

        if (!empty($filters['categories'])) {
            $query->whereIn('c.category_desc', $filters['categories']);
        }

        if (!empty($filters['publishers'])) {
            $query->whereIn('p.description', $filters['publishers']);
        }

        return $query->orderBy('a.book_id');
    }
}
