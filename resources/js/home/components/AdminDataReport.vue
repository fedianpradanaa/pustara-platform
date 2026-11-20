<template>
    <div class="w-100 h-100 mx-auto overflow-visible rounded-lg bg-white shadow-md">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 p-4">
            <div>
                <h1 class="text-lg font-semibold text-gray-900">{{ report.title }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ report.description }}</p>
            </div>
            <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2" @click="exportExcel">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V3.75m0 12.75 4.5-4.5m-4.5 4.5L7.5 12m-3.75 6.75h16.5" />
                </svg>
                Export Excel
            </button>
        </div>

        <div class="p-4 sm:p-6">
            <div v-if="reportType === 'books'" class="mb-5 flex flex-wrap items-end gap-3 rounded-xl border border-gray-200 bg-slate-50 p-4">
                <div v-for="group in bookFilterGroups" :key="group.key" class="min-w-[12rem] flex-1 sm:flex-none">
                    <p class="mb-1 text-xs font-medium text-gray-600">{{ group.label }}</p>
                    <details ref="bookFilterDropdowns" class="group relative">
                        <summary class="flex h-10 w-full cursor-pointer list-none items-center justify-between gap-3 rounded-md border border-gray-300 bg-white px-3 text-sm text-gray-700 shadow-sm hover:border-gray-400 sm:w-56 [&::-webkit-details-marker]:hidden">
                            <span>{{ selectedFilters[group.key].length ? selectedFilters[group.key].length + ' dipilih' : 'Semua' }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 transition group-open:rotate-180">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </summary>
                        <div class="absolute left-0 top-full z-40 mt-2 w-72 max-w-[calc(100vw-2rem)] rounded-lg border border-gray-200 bg-white p-3 shadow-lg">
                            <input v-model="filterSearch[group.key]" type="search" :aria-label="'Cari ' + group.label.toLowerCase()" :placeholder="'Cari ' + group.label.toLowerCase() + '...'" class="w-full rounded-md border-gray-300 py-2 text-sm shadow-sm focus:border-sky-500 focus:ring-sky-500" />
                            <div class="my-2 flex justify-between gap-2">
                                <button type="button" class="text-xs font-medium text-sky-700 hover:underline" @click="selectAllFilter(group.key)">Pilih semua</button>
                                <button type="button" class="text-xs font-medium text-gray-600 hover:underline" @click="clearFilter(group.key)">Kosongkan pilihan</button>
                            </div>
                            <div class="max-h-48 space-y-1 overflow-y-auto rounded-md border border-gray-100 p-2">
                                <label v-for="option in group.options" :key="option.value" class="flex cursor-pointer items-center gap-2 rounded-md px-2 py-1.5 text-sm text-gray-700 hover:bg-sky-50">
                                    <input v-model="selectedFilters[group.key]" type="checkbox" :value="option.value" class="size-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500" />
                                    <span class="truncate">{{ option.label }}</span>
                                </label>
                                <p v-if="group.options.length === 0" class="px-2 py-3 text-center text-sm text-gray-500">Tidak ada pilihan.</p>
                            </div>
                        </div>
                    </details>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" class="h-10 rounded-md bg-orange-500 px-4 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" @click="applyBookFilters">Tampilkan Data</button>
                    <button type="button" class="h-10 rounded-md px-3 text-sm font-medium text-gray-600 hover:bg-white" @click="resetBookFilters">Reset</button>
                </div>
                <p class="w-full text-xs text-gray-500">Pilih satu atau beberapa opsi, lalu tekan Tampilkan Data. Pilihan kosong berarti semua data.</p>
            </div>

            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <input v-model="searchQuery" type="search" placeholder="Cari data..." aria-label="Cari data" class="w-full max-w-md rounded-lg border-gray-300 py-2 text-sm shadow-sm focus:border-sky-500 focus:ring-sky-500" />
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    Baris per halaman
                    <select v-model.number="rowsPerPage" class="rounded-lg border-gray-300 py-2 text-sm shadow-sm focus:border-sky-500 focus:ring-sky-500">
                        <option v-for="option in perPageOptions" :key="option" :value="option">{{ option }}</option>
                    </select>
                </label>
            </div>

            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 bg-white text-sm">
                    <thead class="bg-slate-100 text-left">
                        <tr>
                            <th v-for="header in report.headers" :key="header.key" class="whitespace-nowrap px-4 py-3 font-semibold text-gray-700">
                                <button type="button" class="inline-flex items-center gap-1 hover:text-sky-700" @click="sortBy(header.key)">
                                    {{ header.label }}
                                    <span v-if="sortKey === header.key" aria-hidden="true">{{ sortAsc ? '↑' : '↓' }}</span>
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="row in paginatedData" :key="row[report.key]" class="even:bg-gray-50 hover:bg-sky-50/50">
                            <td v-for="header in report.headers" :key="header.key" class="max-w-xs whitespace-nowrap px-4 py-3 text-gray-700">
                                <button v-if="header.key === 'path_cover' && row.path_cover" type="button" class="rounded border border-gray-200 p-1 hover:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500" @click="showCover(row.path_cover)">
                                    <img :src="row.path_cover" class="h-16 w-12 rounded object-cover" alt="Cover buku" />
                                </button>
                                <span v-else-if="header.key === 'path_cover'" class="text-gray-400">-</span>
                                <span v-else class="block truncate" :title="formatValue(header.key, row[header.key])">{{ formatValue(header.key, row[header.key]) }}</span>
                            </td>
                        </tr>
                        <tr v-if="paginatedData.length === 0">
                            <td :colspan="report.headers.length" class="px-4 py-10 text-center text-gray-500">Tidak ada data untuk ditampilkan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                <p class="text-sm text-gray-600">Menampilkan {{ startEntry }}–{{ endEntry }} dari {{ filteredData.length }} data</p>
                <div class="flex items-center gap-2">
                    <button type="button" class="rounded-md border border-gray-300 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50" :disabled="currentPage <= 1" @click="currentPage--">Sebelumnya</button>
                    <span class="text-sm text-gray-600">Halaman {{ currentPage }} / {{ totalPages }}</span>
                    <button type="button" class="rounded-md border border-gray-300 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50" :disabled="currentPage >= totalPages" @click="currentPage++">Berikutnya</button>
                </div>
            </div>
        </div>
    </div>

    <TransitionRoot as="template" :show="showCoverModal">
        <Dialog class="relative z-50" @close="showCoverModal = false">
            <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-slate-900/80 transition-opacity"></div>
            </TransitionChild>
            <div class="fixed inset-0 z-50 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 translate-y-4 sm:scale-95" enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200" leave-from="opacity-100 translate-y-0 sm:scale-100" leave-to="opacity-0 translate-y-4 sm:scale-95">
                        <DialogPanel class="relative max-w-full overflow-hidden rounded-lg bg-white p-5 text-left shadow-xl">
                            <DialogTitle as="h3" class="text-base font-semibold text-gray-900">Cover Buku</DialogTitle>
                            <img :src="coverImage" class="mt-3 rounded-lg object-contain shadow-md" :style="{ height: 'min(75vh, 450px)', width: 'auto', maxWidth: 'calc(100vw - 3rem)' }" alt="Pratinjau cover buku" />
                            <div class="mt-4 flex justify-end">
                                <button type="button" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50" @click="showCoverModal = false">Tutup</button>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script>
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'

const reportDefinitions = {
    client: {
        title: 'Data Client',
        description: 'Data client terdaftar untuk kebutuhan analisis.',
        endpoint: '/report/admin-data/clients',
        exportEndpoint: '/report/admin-data/clients/export',
        filename: 'data_client.xlsx',
        key: 'client_id',
        headers: [
            { label: 'ID Client', key: 'client_id' },
            { label: 'Nama Instansi', key: 'instansi_name' },
            { label: 'Nama Aplikasi', key: 'application_name' },
            { label: 'Distributor', key: 'distributor_name' },
            { label: 'Alamat', key: 'address' },
            { label: 'Negara', key: 'country_name' },
            { label: 'Provinsi', key: 'provinsi_name' },
            { label: 'Kabupaten/Kota', key: 'kabupaten_name' },
            { label: 'Kecamatan', key: 'kecamatan_name' },
            { label: 'Kelurahan', key: 'kelurahan_name' },
            { label: 'Kode Pos', key: 'kodepos' },
            { label: 'NPWP', key: 'npwp' },
            { label: 'Penanggung Jawab', key: 'pers_responsible' },
            { label: 'Jabatan Penanggung Jawab', key: 'pos_pers_responsible' },
            { label: 'Penandatangan MOU', key: 'mou_sign_name' },
            { label: 'Jabatan Penandatangan', key: 'pos_sign_name' },
            { label: 'Nama Admin', key: 'administrator_name' },
            { label: 'Telepon Admin', key: 'administrator_phone' },
            { label: 'Website', key: 'web_add' },
            { label: 'Kategori', key: 'client_category' },
            { label: 'Level', key: 'client_level' },
            { label: 'Perjanjian', key: 'agreement' },
            { label: 'Status Approval', key: 'approval_status' },
            { label: 'Tanggal Dibuat', key: 'created_at' },
            { label: 'Terakhir Diubah', key: 'updated_at' },
        ],
    },
    books: {
        title: 'Data Buku',
        description: 'Data buku yang sudah terbit untuk kebutuhan analisis.',
        endpoint: '/report/admin-data/books',
        exportEndpoint: '/report/admin-data/books/export',
        filename: 'data_buku.xlsx',
        key: 'book_id',
        headers: [
            { label: 'Cover', key: 'path_cover' },
            { label: 'ID Buku', key: 'book_id' },
            { label: 'ID Supplier', key: 'supplier_id' },
            { label: 'Supplier', key: 'supplier_name' },
            { label: 'ISBN', key: 'isbn' },
            { label: 'E-ISBN', key: 'eisbn' },
            { label: 'Judul', key: 'title' },
            { label: 'Penulis', key: 'writer' },
            { label: 'Penerbit', key: 'publisher_name' },
            { label: 'Kategori', key: 'category_name' },
            { label: 'Ukuran', key: 'size' },
            { label: 'Tahun', key: 'year' },
            { label: 'Jilid', key: 'volume' },
            { label: 'Edisi', key: 'edition' },
            { label: 'Halaman', key: 'page' },
            { label: 'Harga Jual', key: 'sellprice' },
            { label: 'Harga Pinjam', key: 'rentprice' },
            { label: 'Harga Retail', key: 'retailprice' },
            { label: 'Kota', key: 'city' },
            { label: 'Format', key: 'book_format_id' },
            { label: 'Batas Usia', key: 'age' },
            { label: 'Tanggal Dibuat', key: 'createdate' },
            { label: 'Dibuat Oleh', key: 'createby' },
            { label: 'Terakhir Diubah', key: 'updatedate' },
            { label: 'Diubah Oleh', key: 'updateby' },
        ],
    },
}

export default {
    components: {
        Dialog,
        DialogPanel,
        DialogTitle,
        TransitionChild,
        TransitionRoot,
    },

    props: {
        reportType: {
            type: String,
            required: true,
        },
    },

    data() {
        return {
            data: [],
            searchQuery: '',
            rowsPerPage: 20,
            currentPage: 1,
            sortKey: '',
            sortAsc: true,
            perPageOptions: [10, 20, 50, 100],
            selectedFilters: {
                suppliers: [],
                categories: [],
                publishers: [],
            },
            appliedFilters: {
                suppliers: [],
                categories: [],
                publishers: [],
            },
            filterSearch: {
                suppliers: '',
                categories: '',
                publishers: '',
            },
            showCoverModal: false,
            coverImage: '',
        }
    },

    computed: {
        report() {
            return reportDefinitions[this.reportType] || reportDefinitions.client
        },

        filterOptions() {
            return {
                suppliers: this.uniqueFilterOptions('supplier_id', 'supplier_name'),
                categories: this.uniqueFilterOptions('category_name', 'category_name'),
                publishers: this.uniqueFilterOptions('publisher_name', 'publisher_name'),
            }
        },

        bookFilterGroups() {
            return [
                { key: 'suppliers', label: 'Supplier', options: this.filterOptions.suppliers },
                { key: 'categories', label: 'Kategori', options: this.filterOptions.categories },
                { key: 'publishers', label: 'Penerbit', options: this.filterOptions.publishers },
            ].map((group) => {
                const query = this.filterSearch[group.key].trim().toLowerCase()
                return {
                    ...group,
                    options: group.options.filter((option) => option.label.toLowerCase().includes(query)),
                }
            })
        },

        filteredData() {
            let data = [...this.data]
            const query = this.searchQuery.trim().toLowerCase()

            if (this.reportType === 'books') {
                data = data.filter((row) => {
                    const matchesSupplier = !this.appliedFilters.suppliers.length || this.appliedFilters.suppliers.includes(String(row.supplier_id))
                    const matchesCategory = !this.appliedFilters.categories.length || this.appliedFilters.categories.includes(String(row.category_name || ''))
                    const matchesPublisher = !this.appliedFilters.publishers.length || this.appliedFilters.publishers.includes(String(row.publisher_name || ''))

                    return matchesSupplier && matchesCategory && matchesPublisher
                })
            }

            if (query) {
                data = data.filter((row) => Object.values(row).some((value) => String(value == null ? '' : value).toLowerCase().includes(query)))
            }

            if (this.sortKey) {
                data.sort((first, second) => {
                    const result = String(first[this.sortKey] == null ? '' : first[this.sortKey]).localeCompare(String(second[this.sortKey] == null ? '' : second[this.sortKey]), undefined, {
                        numeric: true,
                        sensitivity: 'base',
                    })
                    return this.sortAsc ? result : -result
                })
            }

            return data
        },

        paginatedData() {
            const start = (this.currentPage - 1) * this.rowsPerPage
            return this.filteredData.slice(start, start + this.rowsPerPage)
        },

        totalPages() {
            return Math.ceil(this.filteredData.length / this.rowsPerPage) || 1
        },

        startEntry() {
            return this.filteredData.length ? (this.currentPage - 1) * this.rowsPerPage + 1 : 0
        },

        endEntry() {
            return Math.min(this.currentPage * this.rowsPerPage, this.filteredData.length)
        },
    },

    mounted() {
        this.getData()
    },

    methods: {
        getData() {
            const loader = this.$loading.show()
            this.data = []

            const config = this.reportType === 'books' ? { params: { length: -1 } } : {}

            window.axios
                .get(this.report.endpoint, config)
                .then((response) => {
                    if (response.data && response.data.error) {
                        console.error('[Data report] Server gagal memuat data. Periksa log server.')
                        loader.hide()
                        this.$notyf.error('Gagal memuat data report. Periksa log server.')
                        return
                    }

                    const rows = response.data && response.data.data
                    if (!Array.isArray(rows)) {
                        console.error('[Data Buku filter trace] Respons tidak memiliki array data.', {
                            status: response.status,
                            responseKeys: Object.keys(response.data || {}),
                        })
                        loader.hide()
                        this.$notyf.error('Format data report tidak sesuai.')
                        return
                    }

                    this.data = rows
                    if (this.reportType === 'books') {
                        const uniqueCount = (key) => new Set(rows
                            .map((row) => row[key])
                            .filter((value) => value != null && String(value).trim() !== '')
                            .map(String)).size

                        console.info('[Data Buku filter trace]', {
                            status: response.status,
                            rowCount: rows.length,
                            responseKeys: Object.keys(response.data || {}),
                            firstRowKeys: rows.length ? Object.keys(rows[0]) : [],
                            optionCounts: {
                                suppliers: uniqueCount('supplier_id'),
                                categories: uniqueCount('category_name'),
                                publishers: uniqueCount('publisher_name'),
                            },
                        })
                    }
                    this.currentPage = 1
                    loader.hide()
                })
                .catch((error) => {
                    console.error(error)
                    loader.hide()
                    this.$notyf.error('Gagal memuat data.')
                })
        },

        uniqueFilterOptions(valueKey, labelKey) {
            const options = new Map()

            this.data.forEach((row) => {
                const value = row[valueKey]
                if (value == null || value === '') {
                    return
                }

                const normalizedValue = String(value)
                const label = row[labelKey] == null || row[labelKey] === '' ? normalizedValue : String(row[labelKey])
                if (!options.has(normalizedValue)) {
                    options.set(normalizedValue, label)
                }
            })

            return Array.from(options, ([value, label]) => ({ value, label })).sort((first, second) => first.label.localeCompare(second.label, undefined, { sensitivity: 'base' }))
        },

        selectAllFilter(key) {
            this.selectedFilters[key] = this.filterOptions[key].map((option) => option.value)
        },

        clearFilter(key) {
            this.selectedFilters[key] = []
        },

        applyBookFilters() {
            this.appliedFilters = {
                suppliers: [...this.selectedFilters.suppliers],
                categories: [...this.selectedFilters.categories],
                publishers: [...this.selectedFilters.publishers],
            }
            this.currentPage = 1
            const dropdowns = this.$refs.bookFilterDropdowns || []
            dropdowns.forEach((dropdown) => {
                dropdown.open = false
            })
        },

        resetBookFilters() {
            this.selectedFilters = {
                suppliers: [],
                categories: [],
                publishers: [],
            }
            this.appliedFilters = {
                suppliers: [],
                categories: [],
                publishers: [],
            }
            this.filterSearch = {
                suppliers: '',
                categories: '',
                publishers: '',
            }
            this.currentPage = 1
        },

        showCover(image) {
            this.coverImage = image
            this.showCoverModal = true
        },

        sortBy(key) {
            if (this.sortKey === key) {
                this.sortAsc = !this.sortAsc
            } else {
                this.sortKey = key
                this.sortAsc = true
            }
        },

        formatValue(key, value) {
            if (value == null || value === '') {
                return '-'
            }

            if (key === 'approval_status') {
                return {
                    N: 'Menunggu Approval',
                    P: 'Menunggu Admin',
                    Y: 'Disetujui',
                    R: 'Ditolak',
                }[value] || value
            }

            if (key === 'agreement') {
                return value === 'Y' ? 'Ya' : 'Tidak'
            }

            return value
        },

        async exportExcel() {
            const loader = this.$loading.show()

            try {
                const request = {
                    url: this.report.exportEndpoint,
                    method: 'GET',
                    responseType: 'blob',
                }
                if (this.reportType === 'books') {
                    request.params = this.appliedFilters
                }

                const response = await window.axios(request)
                const url = window.URL.createObjectURL(new Blob([response.data]))
                const link = document.createElement('a')
                link.href = url
                link.download = this.report.filename
                document.body.appendChild(link)
                link.click()
                link.remove()
                window.setTimeout(() => window.URL.revokeObjectURL(url), 0)
            } catch (error) {
                console.error(error)
                this.$notyf.error('Gagal mengunduh file Excel.')
            } finally {
                loader.hide()
            }
        },
    },

    watch: {
        reportType() {
            this.data = []
            this.searchQuery = ''
            this.sortKey = ''
            this.resetBookFilters()
            this.getData()
        },

        searchQuery() {
            this.currentPage = 1
        },

        rowsPerPage() {
            this.currentPage = 1
        },

        totalPages() {
            this.currentPage = Math.min(this.currentPage, this.totalPages)
        },
    },
}
</script>
