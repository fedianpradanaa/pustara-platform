<template>
    <section class="rounded-lg bg-white p-6 shadow-md">
        <h1 class="text-xl font-semibold text-slate-800">Mapping Buku ke Client</h1>
        <p class="mt-2 text-sm text-slate-600">PO pembelian langsung disetujui dan dapat dikirim ke supplier dari Data PO. Hibah langsung dikirim ke supplier dengan harga nol.</p>

        <form class="mt-6 space-y-5" @submit.prevent="submit">
            <div class="grid gap-4 md:grid-cols-3">
                <label class="text-sm font-medium text-slate-700">Client
                    <select v-model="form.client_id" required class="mt-1 w-full rounded border-gray-300">
                        <option value="">Pilih client</option>
                        <option v-for="client in clients" :key="client.client_id" :value="client.client_id">{{ client.instansi_name }}</option>
                    </select>
                </label>
                <label class="text-sm font-medium text-slate-700">Tipe PO
                    <select v-model="form.po_type" class="mt-1 w-full rounded border-gray-300">
                        <option value="1">Pembelian</option>
                        <option value="2">Hibah</option>
                    </select>
                </label>
                <label v-if="form.po_type === '1'" class="text-sm font-medium text-slate-700">Persentase supplier (%)
                    <input v-model.number="form.persentase_supplier" type="number" min="0.01" max="100" step="any" required class="mt-1 w-full rounded border-gray-300" />
                </label>
            </div>
            <div>
                <label for="book-search" class="text-sm font-medium text-slate-700">Cari buku atau supplier</label>
                <input id="book-search" v-model="search" type="search" class="mt-1 w-full rounded border-gray-300" placeholder="Judul, ID buku, atau supplier" />
                <p class="mt-1 text-xs text-slate-500">Hanya buku yang tersedia untuk penjualan yang dapat dipilih.</p>
            </div>
            <div class="max-h-96 overflow-auto rounded border border-slate-200">
                <table class="w-full text-left text-sm">
                    <thead class="sticky top-0 bg-slate-100"><tr><th class="p-3">Pilih</th><th class="p-3">Buku</th><th class="p-3">Supplier</th><th class="p-3">Harga</th><th class="p-3">Copy</th></tr></thead>
                    <tbody>
                        <tr v-for="book in filteredBooks" :key="book.book_id" class="border-t border-slate-100">
                            <td class="p-3"><input v-model="selected" type="checkbox" :value="book.book_id" :aria-label="'Pilih ' + book.title" /></td>
                            <td class="p-3">{{ book.title }} <span class="text-slate-500">({{ book.book_id }})</span></td>
                            <td class="p-3">{{ book.supplier_name }}</td>
                            <td class="p-3">{{ form.po_type === '2' ? '0' : Number(book.sellprice).toLocaleString('id-ID') }}</td>
                            <td class="p-3"><input v-if="selected.includes(book.book_id)" v-model.number="quantities[book.book_id]" type="number" min="1" step="1" required :aria-label="'Jumlah copy ' + book.title" class="w-24 rounded border-gray-300" /></td>
                        </tr>
                        <tr v-if="!filteredBooks.length"><td colspan="5" class="p-4 text-center text-slate-500">Tidak ada buku tersedia.</td></tr>
                    </tbody>
                </table>
            </div>
            <p class="text-sm text-slate-600">{{ selected.length }} buku dipilih. Nomor dan tanggal PO dibuat otomatis.</p>
            <button type="submit" :disabled="saving || !selected.length || loading" class="rounded bg-indigo-600 px-5 py-2 text-white disabled:opacity-50">{{ saving ? 'Menyimpan...' : 'Simpan Mapping' }}</button>
        </form>
    </section>
</template>

<script>
export default {
    data() {
        return {
            clients: [], books: [], selected: [], quantities: {}, search: '', loading: true, saving: false,
            form: { client_id: '', po_type: '1', persentase_supplier: '' },
        }
    },
    computed: {
        filteredBooks() {
            const query = this.search.trim().toLocaleLowerCase()
            return this.books.filter((book) => [book.title, book.book_id, book.supplier_name].some((value) => String(value || '').toLocaleLowerCase().includes(query)))
        },
    },
    async created() {
        try {
            const response = await window.axios.get('/transactions/book-mapping')
            this.clients = response.data.clients
            this.books = response.data.books
            this.books.forEach((book) => { this.quantities[book.book_id] = 1 })
        } catch (error) {
            this.$notyf.error(error.response?.data?.message || 'Gagal memuat data mapping.')
        } finally {
            this.loading = false
        }
    },
    methods: {
        async submit() {
            if (this.saving || !this.selected.length) return
            this.saving = true
            try {
                const response = await window.axios.post('/transactions/book-mapping', {
                    ...this.form,
                    persentase_supplier: this.form.po_type === '1' ? this.form.persentase_supplier : null,
                    books: this.selected.map((book_id) => ({ book_id, qty: this.quantities[book_id] })),
                })
                this.$notyf.success(`${response.data.message} Nomor PO: ${response.data.po_number}`)
                this.$router.push({ name: 'data_po' })
            } catch (error) {
                const errors = error.response?.data?.errors
                if (errors) Object.values(errors).forEach((messages) => this.$notyf.error(messages.join(', ')))
                else this.$notyf.error(error.response?.data?.message || 'Gagal membuat mapping.')
            } finally {
                this.saving = false
            }
        },
    },
}
</script>
