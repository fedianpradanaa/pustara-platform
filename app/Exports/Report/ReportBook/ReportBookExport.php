<?php

namespace App\Exports\Report\ReportBook;

use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportBookExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private Builder $bookQuery)
    {
    }

    public function query(): Builder
    {
        return clone $this->bookQuery;
    }

    public function headings(): array
    {
        return [
            'ID Buku',
            'ID Supplier',
            'Supplier',
            'ISBN',
            'E-ISBN',
            'Judul',
            'Penulis',
            'Penerbit',
            'Kategori',
            'Ukuran',
            'Tahun',
            'Jilid',
            'Edisi',
            'Halaman',
            'Sinopsis',
            'Harga Jual',
            'Harga Pinjam',
            'Harga Retail',
            'Kota',
            'Format',
            'Batas Usia',
            'Tanggal Dibuat',
            'Dibuat Oleh',
            'Terakhir Diubah',
            'Diubah Oleh',
        ];
    }

    public function map($book): array
    {
        return [
            $book->book_id,
            $book->supplier_id,
            $book->supplier_name,
            $book->isbn,
            $book->eisbn,
            $book->title,
            $book->writer,
            $book->publisher_name,
            $book->category_name,
            $book->size,
            $book->year,
            $book->volume,
            $book->edition,
            $book->page,
            $book->sinopsis,
            $book->sellprice,
            $book->rentprice,
            $book->retailprice,
            $book->city,
            $book->book_format_id,
            $book->age,
            $book->createdate,
            $book->createby,
            $book->updatedate,
            $book->updateby,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
