<?php

namespace App\Exports\Report\ReportClient;

use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportClientExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private Builder $clientQuery)
    {
    }

    public function query(): Builder
    {
        return clone $this->clientQuery;
    }

    public function headings(): array
    {
        return [
            'ID Client',
            'Nama Instansi',
            'Nama Aplikasi',
            'ID Distributor',
            'Nama Distributor',
            'Alamat',
            'Negara',
            'Provinsi',
            'Kabupaten/Kota',
            'Kecamatan',
            'Kelurahan',
            'Kode Pos',
            'NPWP',
            'Penanggung Jawab',
            'Jabatan Penanggung Jawab',
            'Penandatangan MOU',
            'Jabatan Penandatangan',
            'Nama Admin',
            'Telepon Admin',
            'Website',
            'Kategori',
            'Level',
            'Perjanjian',
            'Status Approval',
            'Tanggal Dibuat',
            'Terakhir Diubah',
        ];
    }

    public function map($client): array
    {
        return [
            $client->client_id,
            $client->instansi_name,
            $client->application_name,
            $client->company_id,
            $client->distributor_name,
            $client->address,
            $client->country_name,
            $client->provinsi_name,
            $client->kabupaten_name,
            $client->kecamatan_name,
            $client->kelurahan_name,
            $client->kodepos,
            $client->npwp,
            $client->pers_responsible,
            $client->pos_pers_responsible,
            $client->mou_sign_name,
            $client->pos_sign_name,
            $client->administrator_name,
            $client->administrator_phone,
            $client->web_add,
            $client->client_category,
            $client->client_level,
            $client->agreement,
            $client->approval_status,
            $client->created_at,
            $client->updated_at,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
