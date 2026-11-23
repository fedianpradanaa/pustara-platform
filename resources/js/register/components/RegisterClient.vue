<template>
    <RegistrationLayout title="Client">
                    <VeeForm ref="form" v-slot="{ handleSubmit, errors }" as="div">
                        <form class="mt-8 grid grid-cols-6 gap-4" @submit.prevent="handleSubmit($event, submit)">
                            <p class="col-span-6 text-xs text-gray-500"><span class="font-semibold text-red-600">*</span> Wajib diisi</p>
                            <div class="col-span-6">
                                <label for="name" class="relative block rounded-md border shadow-sm focus-within:border-blue-600 focus-within:ring-1 focus-within:ring-blue-600" :class="errors.name ? 'border-red-500' : 'border-gray-200'">
                                    <Field type="text" id="name" class="peer w-full border-none bg-transparent placeholder-transparent focus:border-transparent focus:outline-none focus:ring-0" placeholder="Nama Instansi" name="name" v-model="form.field.name" required autocomplete="name" autofocus />

                                    <span class="pointer-events-none absolute start-2.5 top-0 -translate-y-1/2 bg-white p-0.5 text-xs text-gray-700 transition-all peer-placeholder-shown:top-1/2 peer-placeholder-shown:text-sm peer-focus:top-0 peer-focus:text-xs">Nama Instansi <span class="text-red-600">*</span></span>
                                </label>
                                <ErrorMessage name="name" class="mt-1 block text-xs text-red-600 dark:text-red-500" />
                            </div>

                            <div class="col-span-6">
                                <label for="email" class="relative block rounded-md border shadow-sm focus-within:border-blue-600 focus-within:ring-1 focus-within:ring-blue-600" :class="errors.email ? 'border-red-500' : 'border-gray-200'">
                                    <Field type="email" id="email" class="peer w-full border-none bg-transparent placeholder-transparent focus:border-transparent focus:outline-none focus:ring-0" placeholder="Email" name="email" v-model="form.field.email" required autocomplete="email" aria-describedby="email-help" />
                                    <span class="pointer-events-none absolute start-2.5 top-0 -translate-y-1/2 bg-white p-0.5 text-xs text-gray-700">Email <span class="text-red-600">*</span></span>
                                </label>
                                <p id="email-help" class="mt-2 text-xs leading-5 text-slate-600">Gunakan alamat email aktif untuk menerima pemberitahuan pendaftaran dan status pengajuan.</p>
                                <ErrorMessage name="email" class="mt-1 block text-xs text-red-600" />
                            </div>

                            <div class="col-span-6">
                                <label for="name_apps" class="relative block rounded-md border shadow-sm focus-within:border-blue-600 focus-within:ring-1 focus-within:ring-blue-600" :class="errors.name_apps ? 'border-red-500' : 'border-gray-200'">
                                    <Field type="text" id="name_apps" class="peer w-full border-none bg-transparent placeholder-transparent focus:border-transparent focus:outline-none focus:ring-0" placeholder="Nama Aplikasi" name="name_apps" v-model="form.field.name_apps" required autocomplete="name_apps" autofocus />

                                    <span class="pointer-events-none absolute start-2.5 top-0 -translate-y-1/2 bg-white p-0.5 text-xs text-gray-700 transition-all peer-placeholder-shown:top-1/2 peer-placeholder-shown:text-sm peer-focus:top-0 peer-focus:text-xs">Nama Aplikasi <span class="text-red-600">*</span></span>
                                </label>
                                <ErrorMessage name="name_apps" class="mt-1 block text-xs text-red-600 dark:text-red-500" />
                            </div>

                            <div class="col-span-6">
                                <label for="web_add" class="mb-1 block text-sm font-medium text-gray-700">Alamat Website Perpustakaan <span class="text-red-600">*</span></label>
                                <div class="flex min-w-0 items-stretch">
                                    <Field type="text" id="web_add" class="min-w-0 flex-1 rounded-l-xl border-gray-300 text-sm focus:border-blue-600 focus:ring-blue-600" :class="errors.web_add ? 'border-red-500' : 'border-gray-300'" placeholder="nama-perpustakaan" name="web_add" v-model="form.field.web_add" required aria-describedby="website-help" />
                                    <span class="inline-flex items-center rounded-r-xl border border-l-0 border-gray-300 bg-slate-50 px-2 text-xs text-slate-600 sm:px-3 sm:text-sm">{{ $brand.website_suffix }}</span>
                                </div>
                                <p id="website-help" class="mt-2 text-xs text-slate-500">Masukkan nama subdomain perpustakaan Anda.</p>
                                <ErrorMessage name="web_add" class="mt-1 block text-xs text-red-600 dark:text-red-500" />
                            </div>

                            <div class="col-span-6">
                                <label for="address" class="mb-1 block text-sm font-medium text-gray-700">Alamat <span class="text-red-600">*</span></label>
                                <Field as="textarea" id="address" class="w-full rounded-lg align-top shadow-sm sm:text-sm" :class="errors.address ? 'border-red-500' : 'border-gray-200'" rows="3" placeholder="Masukkan alamat..." name="address" v-model="form.field.address" />
                                <ErrorMessage name="address" class="mt-1 block text-xs text-red-600 dark:text-red-500" />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="country" class="mb-1 block text-sm font-medium text-gray-700">Negara <span class="text-red-600">*</span></label>
                                <Field as="select" name="country" id="country" class="relative w-full rounded-md text-gray-700 shadow-sm focus-within:border-blue-600 focus-within:ring-1 focus-within:ring-blue-600 sm:text-sm" :class="errors.country ? 'border-red-500' : 'border-gray-300'" v-model="form.field.country" @change="getProvinsi">
                                    <option value="">Silakan pilih Negara</option>
                                    <option value="IDN">Indonesia</option>
                                </Field>
                                <ErrorMessage name="country" class="mt-1 block text-xs text-red-600 dark:text-red-500" />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="province" class="mb-1 block text-sm font-medium text-gray-700">Provinsi <span class="text-red-600">*</span></label>
                                <Field as="select" name="province" id="province" class="relative w-full rounded-md text-gray-700 shadow-sm focus-within:border-blue-600 focus-within:ring-1 focus-within:ring-blue-600 sm:text-sm" :class="errors.province ? 'border-red-500' : 'border-gray-300'" v-model="form.field.province" @change="getKabupaten">
                                    <option value="">Silakan pilih Provinsi</option>
                                    <option v-for="(value, key) in option.province" :key="key" :value="value.id">{{ value.name }}</option>
                                </Field>
                                <ErrorMessage name="province" class="mt-1 block text-xs text-red-600 dark:text-red-500" />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="regency" class="mb-1 block text-sm font-medium text-gray-700">Kabupaten/Kota <span class="text-red-600">*</span></label>
                                <Field as="select" name="regency" id="regency" class="relative w-full rounded-md text-gray-700 shadow-sm focus-within:border-blue-600 focus-within:ring-1 focus-within:ring-blue-600 sm:text-sm" :class="errors.regency ? 'border-red-500' : 'border-gray-300'" v-model="form.field.regency" @change="getKecamatan">
                                    <option value="">Silakan pilih Kabupaten/Kota</option>
                                    <option v-for="(value, key) in option.regency" :key="key" :value="value.id">{{ value.name }}</option>
                                </Field>
                                <ErrorMessage name="regency" class="mt-1 block text-xs text-red-600 dark:text-red-500" />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="district" class="mb-1 block text-sm font-medium text-gray-700">Kecamatan <span class="text-red-600">*</span></label>
                                <Field as="select" name="district" id="district" class="relative w-full rounded-md text-gray-700 shadow-sm focus-within:border-blue-600 focus-within:ring-1 focus-within:ring-blue-600 sm:text-sm" :class="errors.district ? 'border-red-500' : 'border-gray-300'" v-model="form.field.district" @change="getKelurahan">
                                    <option value="">Silakan pilih Kecamatan</option>
                                    <option v-for="(value, key) in option.district" :key="key" :value="value.id">{{ value.name }}</option>
                                </Field>
                                <ErrorMessage name="district" class="mt-1 block text-xs text-red-600 dark:text-red-500" />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="subdistrict" class="mb-1 block text-sm font-medium text-gray-700">Kelurahan <span class="text-red-600">*</span></label>
                                <Field as="select" name="subdistrict" id="subdistrict" class="relative w-full rounded-md text-gray-700 shadow-sm focus-within:border-blue-600 focus-within:ring-1 focus-within:ring-blue-600 sm:text-sm" :class="errors.subdistrict ? 'border-red-500' : 'border-gray-300'" v-model="form.field.subdistrict">
                                    <option value="">Silakan pilih Kelurahan</option>
                                    <option v-for="(value, key) in option.subdistrict" :key="key" :value="value.id">{{ value.name }}</option>
                                </Field>
                                <ErrorMessage name="subdistrict" class="mt-1 block text-xs text-red-600 dark:text-red-500" />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="postal_code" class="relative block rounded-md border shadow-sm focus-within:border-blue-600 focus-within:ring-1 focus-within:ring-blue-600" :class="errors.postal_code ? 'border-red-500' : 'border-gray-200'">
                                    <Field type="text" id="postal_code" class="peer w-full border-none bg-transparent placeholder-transparent focus:border-transparent focus:outline-none focus:ring-0" placeholder="Kode Pos" name="postal_code" v-model="form.field.postal_code" />

                                    <span class="pointer-events-none absolute start-2.5 top-0 -translate-y-1/2 bg-white p-0.5 text-xs text-gray-700 transition-all peer-placeholder-shown:top-1/2 peer-placeholder-shown:text-sm peer-focus:top-0 peer-focus:text-xs">Kode Pos <span class="text-red-600">*</span></span>
                                </label>
                                <ErrorMessage name="postal_code" class="mt-1 block text-xs text-red-600 dark:text-red-500" />
                            </div>

                            <div class="col-span-6">
                                <label for="npwp" class="relative block rounded-md border shadow-sm focus-within:border-blue-600 focus-within:ring-1 focus-within:ring-blue-600" :class="errors.npwp ? 'border-red-500' : 'border-gray-200'">
                                    <Field type="text" id="npwp" class="peer w-full border-none bg-transparent placeholder-transparent focus:border-transparent focus:outline-none focus:ring-0" placeholder="No. NPWP" name="npwp" v-model="form.field.npwp" />

                                    <span class="pointer-events-none absolute start-2.5 top-0 -translate-y-1/2 bg-white p-0.5 text-xs text-gray-700 transition-all peer-placeholder-shown:top-1/2 peer-placeholder-shown:text-sm peer-focus:top-0 peer-focus:text-xs">No. NPWP <span class="text-red-600">*</span></span>
                                </label>
                                <ErrorMessage name="npwp" class="mt-1 block text-xs text-red-600 dark:text-red-500" />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="pers_responsible" class="relative block rounded-md border shadow-sm focus-within:border-blue-600 focus-within:ring-1 focus-within:ring-blue-600" :class="errors.pers_responsible ? 'border-red-500' : 'border-gray-200'">
                                    <Field type="text" id="pers_responsible" class="peer w-full border-none bg-transparent placeholder-transparent focus:border-transparent focus:outline-none focus:ring-0" placeholder="Nama Penanggung Jawab" name="pers_responsible" v-model="form.field.pers_responsible" />

                                    <span class="pointer-events-none absolute start-2.5 top-0 -translate-y-1/2 bg-white p-0.5 text-xs text-gray-700 transition-all peer-placeholder-shown:top-1/2 peer-placeholder-shown:text-sm peer-focus:top-0 peer-focus:text-xs">Nama Penanggung Jawab <span class="text-red-600">*</span></span>
                                </label>
                                <ErrorMessage name="pers_responsible" class="mt-1 block text-xs text-red-600 dark:text-red-500" />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="pos_pers_responsible" class="relative block rounded-md border shadow-sm focus-within:border-blue-600 focus-within:ring-1 focus-within:ring-blue-600" :class="errors.pos_pers_responsible ? 'border-red-500' : 'border-gray-200'">
                                    <Field type="text" id="pos_pers_responsible" class="peer w-full border-none bg-transparent placeholder-transparent focus:border-transparent focus:outline-none focus:ring-0" placeholder="Jabatan Penanggung Jawab" name="pos_pers_responsible" v-model="form.field.pos_pers_responsible" />

                                    <span class="pointer-events-none absolute start-2.5 top-0 -translate-y-1/2 bg-white p-0.5 text-xs text-gray-700 transition-all peer-placeholder-shown:top-1/2 peer-placeholder-shown:text-sm peer-focus:top-0 peer-focus:text-xs">Jabatan Penanggung Jawab <span class="text-red-600">*</span></span>
                                </label>
                                <ErrorMessage name="pos_pers_responsible" class="mt-1 block text-xs text-red-600 dark:text-red-500" />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="mou_sign_name" class="relative block rounded-md border shadow-sm focus-within:border-blue-600 focus-within:ring-1 focus-within:ring-blue-600" :class="errors.mou_sign_name ? 'border-red-500' : 'border-gray-200'">
                                    <Field type="text" id="mou_sign_name" class="peer w-full border-none bg-transparent placeholder-transparent focus:border-transparent focus:outline-none focus:ring-0" placeholder="Nama Penandatanganan MOU" name="mou_sign_name" v-model="form.field.mou_sign_name" />

                                    <span class="pointer-events-none absolute start-2.5 top-0 -translate-y-1/2 bg-white p-0.5 text-xs text-gray-700 transition-all peer-placeholder-shown:top-1/2 peer-placeholder-shown:text-sm peer-focus:top-0 peer-focus:text-xs">Nama Penandatanganan MOU <span class="text-red-600">*</span></span>
                                </label>
                                <ErrorMessage name="mou_sign_name" class="mt-1 block text-xs text-red-600 dark:text-red-500" />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="pos_sign_name" class="relative block rounded-md border shadow-sm focus-within:border-blue-600 focus-within:ring-1 focus-within:ring-blue-600" :class="errors.pos_sign_name ? 'border-red-500' : 'border-gray-200'">
                                    <Field type="text" id="pos_sign_name" class="peer w-full border-none bg-transparent placeholder-transparent focus:border-transparent focus:outline-none focus:ring-0" placeholder="Jabatan Penandatanganan MOU" name="pos_sign_name" v-model="form.field.pos_sign_name" />

                                    <span class="pointer-events-none absolute start-2.5 top-0 -translate-y-1/2 bg-white p-0.5 text-xs text-gray-700 transition-all peer-placeholder-shown:top-1/2 peer-placeholder-shown:text-sm peer-focus:top-0 peer-focus:text-xs">Jabatan Penandatanganan MOU <span class="text-red-600">*</span></span>
                                </label>
                                <ErrorMessage name="pos_sign_name" class="mt-1 block text-xs text-red-600 dark:text-red-500" />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="administrator_name" class="relative block rounded-md border shadow-sm focus-within:border-blue-600 focus-within:ring-1 focus-within:ring-blue-600" :class="errors.administrator_name ? 'border-red-500' : 'border-gray-200'">
                                    <Field type="text" id="administrator_name" class="peer w-full border-none bg-transparent placeholder-transparent focus:border-transparent focus:outline-none focus:ring-0" placeholder="Nama Pengelola (Admin)" name="administrator_name" v-model="form.field.administrator_name" />

                                    <span class="pointer-events-none absolute start-2.5 top-0 -translate-y-1/2 bg-white p-0.5 text-xs text-gray-700 transition-all peer-placeholder-shown:top-1/2 peer-placeholder-shown:text-sm peer-focus:top-0 peer-focus:text-xs">Nama Pengelola (Admin) <span class="text-red-600">*</span></span>
                                </label>
                                <ErrorMessage name="administrator_name" class="mt-1 block text-xs text-red-600 dark:text-red-500" />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="administrator_phone" class="relative block rounded-md border shadow-sm focus-within:border-blue-600 focus-within:ring-1 focus-within:ring-blue-600" :class="errors.administrator_phone ? 'border-red-500' : 'border-gray-200'">
                                    <Field type="text" id="administrator_phone" class="peer w-full border-none bg-transparent placeholder-transparent focus:border-transparent focus:outline-none focus:ring-0" placeholder="Nomor HP/WA (Admin)" name="administrator_phone" v-model="form.field.administrator_phone" />

                                    <span class="pointer-events-none absolute start-2.5 top-0 -translate-y-1/2 bg-white p-0.5 text-xs text-gray-700 transition-all peer-placeholder-shown:top-1/2 peer-placeholder-shown:text-sm peer-focus:top-0 peer-focus:text-xs">Nomor HP/WA (Admin) <span class="text-red-600">*</span></span>
                                </label>
                                <ErrorMessage name="administrator_phone" class="mt-1 block text-xs text-red-600 dark:text-red-500" />
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="mb-1 block text-sm font-medium text-gray-700">Logo Besar <span class="text-red-600">*</span></label>
                                <file-pond name="logo_big" ref="logo_big" label-idle="Upload Logo Besar disini..." v-bind:allow-multiple="false" accepted-file-types="image/png" maxFileSize="1000KB" v-on:init="handleFilePondInit" credits="false" v-on:updatefiles="getFile" />
                                <ErrorMessage name="logo_big" class="mt-1 block text-xs text-red-600 dark:text-red-500" />
                                <dt class="text-xs font-medium text-slate-500">Max. logo size: 1000 kb</dt>
                                <img v-if="$brand.logo_path" class="rounded-lg shadow-sm dark:shadow-gray-800" :src="'/' + $brand.logo_path" :alt="$brand.name" />
                                <dt class="mt-2 text-xs font-medium text-slate-500">Contoh logo besar</dt>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="mb-1 block text-sm font-medium text-gray-700">Logo Kecil <span class="text-red-600">*</span></label>
                                <file-pond name="logo_small" ref="logo_small" label-idle="Upload Logo Kecil disini..." v-bind:allow-multiple="false" accepted-file-types="image/png" maxFileSize="1000KB" v-on:init="handleFilePondInit" credits="false" v-on:updatefiles="getFileSmall" />
                                <ErrorMessage name="logo_small" class="mt-1 block text-xs text-red-600 dark:text-red-500" />
                                <dt class="text-xs font-medium text-slate-500">Max. logo size: 1000 kb</dt>
                                <img v-if="$brand.favicon_path" class="h-[125px] w-[125px] rounded-lg shadow-sm dark:shadow-gray-800" :src="'/' + $brand.favicon_path" :alt="$brand.name" />
                                <dt class="mt-2 text-xs font-medium text-slate-500">Contoh logo kecil</dt>
                            </div>

                            <div class="col-span-6">
                                <label for="noref" class="relative block rounded-md border shadow-sm focus-within:border-blue-600 focus-within:ring-1 focus-within:ring-blue-600" :class="errors.noref ? 'border-red-500' : 'border-gray-200'">
                                    <Field type="text" id="noref" class="peer w-full border-none bg-transparent placeholder-transparent focus:border-transparent focus:outline-none focus:ring-0" placeholder="Kode Referensi" name="noref" v-model="form.field.noref" required aria-describedby="reference-help" />

                                    <span class="pointer-events-none absolute start-2.5 top-0 -translate-y-1/2 bg-white p-0.5 text-xs text-gray-700 transition-all peer-placeholder-shown:top-1/2 peer-placeholder-shown:text-sm peer-focus:top-0 peer-focus:text-xs">Kode Referensi <span class="text-red-600">*</span></span>
                                </label>
                                <p id="reference-help" class="mt-2 text-xs leading-5 text-slate-600">Kode referensi diberikan oleh distributor yang menawarkan aplikasi perpustakaan digital. Jika belum mengetahui kode Anda, silakan hubungi distributor atau admin {{ $brand.name }} untuk mendapatkannya.</p>
                                <ErrorMessage name="noref" class="mt-1 block text-xs text-red-600 dark:text-red-500" />
                            </div>

                            <div class="col-span-6">
                                <label for="agreement" class="flex gap-4">
                                    <input type="checkbox" id="agreement" name="agreement" v-model="form.field.agreement" class="size-5 rounded-md border-gray-200 bg-white shadow-sm" disabled />

                                    <span class="text-sm text-gray-700"> Saya telah memahami dan menyetujui ketentuan dan persyaratan Pendaftaran Perpustakaan Digital Berbasis Website yang diajukan oleh pihak {{ $brand.name }}. <span class="font-semibold text-red-600">*</span></span>
                                </label>
                                <ErrorMessage name="agreement" class="mt-1 block text-sm text-red-600 dark:text-red-500" />
                            </div>

                            <div class="col-span-6 sm:flex sm:items-center sm:gap-4">
                                <button class="inline-block shrink-0 rounded-md border border-blue-600 bg-blue-600 px-12 py-3 text-sm font-medium text-white transition hover:bg-transparent hover:text-blue-600 focus:outline-none focus:ring active:text-blue-500 disabled:opacity-75" :disabled="form.field.agreement ? false : true">Daftar</button>
                            </div>
                        </form>
                    </VeeForm>
    </RegistrationLayout>

    <!-- modal -->
    <TransitionRoot as="template" :show="open">
        <Dialog class="relative z-10" static>
            <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            </TransitionChild>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200" leave-from="opacity-100 translate-y-0 sm:scale-100" leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        <DialogPanel class="relative flex max-h-[90vh] w-full max-w-2xl transform flex-col overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8">
                            <div class="flex items-start gap-4 border-b border-gray-100 px-5 py-5 sm:px-7">
                                <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-full bg-blue-50">
                                    <ShieldExclamationIcon class="h-6 w-6 text-blue-600" aria-hidden="true" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold uppercase tracking-wider text-blue-700">Syarat dan Ketentuan</p>
                                    <DialogTitle as="h3" class="mt-1 text-lg font-bold leading-snug text-gray-900 sm:text-xl">Pendaftaran Perpustakaan Digital {{ $brand.name }}</DialogTitle>
                                    <p class="mt-1 text-sm leading-5 text-gray-500">Mohon baca dan setujui ketentuan berikut untuk melanjutkan pendaftaran.</p>
                                </div>
                            </div>
                            <div class="min-h-0 flex-1 overflow-y-auto px-5 py-4 sm:px-7">
                                <dl class="space-y-3 text-sm leading-6 [&>div]:rounded-lg [&>div]:bg-gray-50 [&>div]:px-4 [&>div]:py-3 [&>div]:ring-1 [&>div]:ring-gray-100 [&_dd]:text-gray-700">
                                                    <div class="grid grid-cols-1 gap-1 py-1 sm:grid-cols-1 sm:gap-1">
                                                        <dd class="text-gray-700 sm:col-span-2">1. Perpustakaan Digital {{ $brand.name }} adalah platform aplikasi berbasis website.</dd>
                                                    </div>

                                                    <div class="grid grid-cols-1 gap-1 py-1 sm:grid-cols-1 sm:gap-1">
                                                        <dd class="text-gray-700 sm:col-span-2">2. Platform ini tidak hanya menyediakan buku digital (ebook), tetapi juga berisi banner gambar, banner video, tajuk utama, frasa, wawasan, review buku, layer penulis, titik fokus, humoria, dan fitur-fitur lainnya yang akan terus dikembangkan.</dd>
                                                    </div>

                                                    <div class="grid grid-cols-1 gap-1 py-1 sm:grid-cols-1 sm:gap-1">
                                                        <dd class="text-gray-700 sm:col-span-2">3. Nama Perpustakaan Digital pada Platform {{ $brand.name }} akan menggunakan nama Lembaga Perpustakaan, sesuai dengan ketetapan dari pihak tersebut.</dd>
                                                    </div>

                                                    <div class="grid grid-cols-1 gap-1 py-1 sm:grid-cols-1 sm:gap-1">
                                                        <dd class="text-gray-700 sm:col-span-2">4. Nama yang dipilih bersifat eksklusif dan tidak akan diberikan kepada lembaga lain.</dd>
                                                    </div>

                                                    <div class="grid grid-cols-1 gap-1 py-1 sm:grid-cols-1 sm:gap-1">
                                                        <dd class="text-gray-700 sm:col-span-2">5. Penggantian nama perpustakaan diperbolehkan, tetapi harus disertai dengan penambahan koleksi perpustakaan sesuai ketentuan yang ditetapkan oleh pemilik platform setiap tahun.</dd>
                                                    </div>

                                                    <div class="grid grid-cols-1 gap-1 py-1 sm:grid-cols-1 sm:gap-1">
                                                        <dd class="text-gray-700 sm:col-span-2">6. Apabila dua Lembaga Perpustakaan pengguna Platform {{ $brand.name }} melakukan merger (penggabungan), perpustakaan digital masing-masing dapat digabung dengan syarat pengisian surat pernyataan merger dan penambahan koleksi senilai minimal Rp. 3.000.000,-.</dd>
                                                    </div>

                                                    <div class="grid grid-cols-1 gap-1 py-1 sm:grid-cols-1 sm:gap-1">
                                                        <dd class="text-gray-700 sm:col-span-2">7. Koleksi perpustakaan yang dibeli bersifat hak milik, namun {{ $brand.name }} tidak berkewajiban melakukan pembaruan atau perbaikan kecuali jika Pihak Lembaga Perpustakaan telah melakukan penambahan koleksi selama tahun berjalan.</dd>
                                                    </div>

                                                    <div class="grid grid-cols-1 gap-1 py-1 sm:grid-cols-1 sm:gap-1">
                                                        <dd class="text-gray-700 sm:col-span-2">8. Hak milik perpustakaan ini terbatas pada usia teknologi website. Jika teknologi website tidak lagi digunakan, maka hak milik juga berakhir.</dd>
                                                    </div>

                                                    <div class="grid grid-cols-1 gap-1 py-1 sm:grid-cols-1 sm:gap-1">
                                                        <dd class="text-gray-700 sm:col-span-2">9. {{ $brand.name }} menyediakan versi offline dengan ketentuan minimal pembelian koleksi perpustakaan, sesuai dengan ketentuan terpisah.</dd>
                                                    </div>

                                                    <div class="grid grid-cols-1 gap-1 py-1 sm:grid-cols-1 sm:gap-1">
                                                        <dd class="text-gray-700 sm:col-span-2">10. Koleksi perpustakaan dapat diperoleh melalui pembelian, hibah, atau donasi.</dd>
                                                    </div>

                                                    <div class="grid grid-cols-1 gap-1 py-1 sm:grid-cols-1 sm:gap-1">
                                                        <dd class="text-gray-700 sm:col-span-2">11. Semua pembelian buku digital harus melalui Distributor yang dipilih oleh Pihak Pembeli (Lembaga Perpustakaan).</dd>
                                                    </div>

                                                    <div class="grid grid-cols-1 gap-1 py-1 sm:grid-cols-1 sm:gap-1">
                                                        <dd class="text-gray-700 sm:col-span-2">12. Setiap judul ebook yang dibeli minimal sebanyak 2 copy.</dd>
                                                    </div>

                                                    <div class="grid grid-cols-1 gap-1 py-1 sm:grid-cols-1 sm:gap-1">
                                                        <dd class="text-gray-700 sm:col-span-2">13. Jika Distributor yang dipilih tidak memenuhi kewajiban pembayaran kepada {{ $brand.name }}, maka Pihak {{ $brand.name }} berhak menurunkan koleksi terkait dari platform.</dd>
                                                    </div>

                                                    <div class="grid grid-cols-1 gap-1 py-1 sm:grid-cols-1 sm:gap-1">
                                                        <dd class="text-gray-700 sm:col-span-2">14. {{ $brand.name }} akan meningkatkan versi perpustakaan digital setiap tahun. Fitur-fitur tertentu hanya dapat diakses pada versi terbaru, dan Lembaga Perpustakaan harus melakukan pembelian koleksi untuk menikmati pembaruan otomatis.</dd>
                                                    </div>

                                                    <div class="grid grid-cols-1 gap-1 py-1 sm:grid-cols-1 sm:gap-1">
                                                        <dd class="text-gray-700 sm:col-span-2">15. Selain ebook, produk digital yang disediakan oleh {{ $brand.name }} di antaranya: ebook media interaktif, audiobook, videobook, video tutorial dan konten digital lainnya yang akan terus dikembangkan.</dd>
                                                    </div>

                                                    <div class="grid grid-cols-1 gap-1 py-1 sm:grid-cols-1 sm:gap-1">
                                                        <dd class="text-gray-700 sm:col-span-2">16. Pihak Lembaga Perpustakaan membayar Biaya Lisensi senilai Rp. 150.000,- dalam 1 tahun untuk tetap menikmati semua layanan fitur dan konten platform {{ $brand.name }}, namun jika dalam 1 tahun ada pembelian koleksi minimal senilai Rp. 1.000.000,-, maka tidak ada biaya lisensi (gratis).</dd>
                                                    </div>
                                                    <div class="grid grid-cols-1 gap-1 py-1 sm:grid-cols-1 sm:gap-1">
                                                        <dd class="text-gray-700 sm:col-span-2">17. Apabila selama 3 (tiga) tahun berturut-turut Pihak Lembaga Perpustakaan tidak melakukan pembelian koleksi e-book maupun pembayaran Biaya Lisensi, layanan perpustakaan digital bagi lembaga tersebut pada platform {{ $brand.name }} akan dinonaktifkan sementara. Apabila kondisi tersebut berlanjut hingga mencapai 5 (lima) tahun berturut-turut, layanan perpustakaan digital tersebut akan dinonaktifkan secara permanen. Dalam hal penonaktifan permanen, koleksi e-book yang telah dibeli akan diserahkan kepada Pihak Lembaga Perpustakaan dalam bentuk file yang dapat diakses secara luring (offline), dengan akses terbatas hanya pada 1 (satu) komputer.</dd>
                                                    </div>

                                                    <div class="grid grid-cols-1 gap-1 py-1 sm:grid-cols-1 sm:gap-1">
                                                        <dd class="text-gray-700 sm:col-span-2">Saya telah memahami dan menyetujui ketentuan dan persyaratan Pendaftaran Perpustakaan Digital Berbasis Website yang diajukan oleh pihak {{ $brand.name }}.</dd>
                                                    </div>
                                </dl>
                            </div>
                            <div class="border-t border-gray-100 bg-white px-5 py-4 sm:px-7">
                                <label for="agreement" class="flex cursor-pointer items-start gap-3">
                                    <input type="checkbox" id="agreement" name="agreement" v-model="form.field.agreement" class="mt-0.5 size-5 flex-shrink-0 rounded-md border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" />
                                    <span class="text-sm leading-6 text-gray-700">Saya telah membaca dan menyetujui Syarat dan Ketentuan Pendaftaran Perpustakaan Digital {{ $brand.name }}. <span class="font-semibold text-red-600">*</span></span>
                                </label>
                                <div class="mt-4 flex justify-end">
                                    <button type="button" class="inline-flex w-full justify-center rounded-md bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 sm:w-auto" @click="open = false" :disabled="!form.field.agreement">Saya Setuju</button>
                                </div>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>

    <TransitionRoot as="template" :show="open_finish">
        <Dialog class="relative z-10" @close="(open_finish = false), (open = true)">
            <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            </TransitionChild>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200" leave-from="opacity-100 translate-y-0 sm:scale-100" leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        <DialogPanel class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-emerald-100 sm:mx-0 sm:h-10 sm:w-10">
                                        <CheckBadgeIcon class="h-6 w-6 text-emerald-600" aria-hidden="true" />
                                    </div>
                                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                        <DialogTitle as="h3" class="text-base font-semibold text-gray-900">Selamat Anda Berhasil Registrasi</DialogTitle>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500">Registrasi akan di proses dalam kurun waktu 7x24 jam Terima Kasih.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" @click="(open_finish = false), (open = true)" ref="cancelButtonRef">Tutup</button>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
    <!-- modal -->
</template>

<script>
import RegistrationLayout from './RegistrationLayout.vue'
import { Form as VeeForm, Field, ErrorMessage } from 'vee-validate'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { ShieldExclamationIcon } from '@heroicons/vue/24/outline'
import { CheckBadgeIcon } from '@heroicons/vue/24/outline'

export default {
    components: {
        RegistrationLayout,
        VeeForm,
        Field,
        ErrorMessage,
        Dialog,
        DialogPanel,
        DialogTitle,
        TransitionChild,
        TransitionRoot,
        ShieldExclamationIcon,
        CheckBadgeIcon,
    },

    data() {
        return {
            open: false,
            open_finish: false,
            search: '',

            option: {
                province: [],
                regency: [],
                district: [],
                subdistrict: [],
            },

            form: {
                field: {
                    name: '',
                    email: '',
                    name_apps: '',
                    web_add: '',
                    address: '',
                    country: '',
                    province: '',
                    regency: '',
                    district: '',
                    subdistrict: '',
                    postal_code: '',
                    npwp: '',
                    pers_responsible: '',
                    pos_pers_responsible: '',
                    mou_sign_name: '',
                    pos_sign_name: '',
                    administrator_name: '',
                    administrator_phone: '',
                    logo_big: '',
                    logo_small: '',
                    agreement: false,
                    noref: '',
                },
            },
        }
    },

    mounted() {
        this.open = true
    },

    methods: {
        clearForm() {
            this.form.field.name = ''
            this.form.field.email = ''
            this.form.field.name_apps = ''
            this.form.field.web_add = ''
            this.form.field.address = ''
            this.form.field.country = ''
            this.form.field.province = ''
            this.form.field.regency = ''
            this.form.field.district = ''
            this.form.field.subdistrict = ''
            this.form.field.postal_code = ''
            this.form.field.npwp = ''
            this.form.field.pers_responsible = ''
            this.form.field.pos_pers_responsible = ''
            this.form.field.mou_sign_name = ''
            this.form.field.pos_sign_name = ''
            this.form.field.administrator_name = ''
            this.form.field.administrator_phone = ''
            this.form.field.logo_big = ''
            this.form.field.logo_small = ''
            this.form.field.agreement = false
            this.form.field.noref = ''

            this.$refs.logo_big.removeFiles()
            this.$refs.logo_small.removeFiles()
        },

        handleFilePondInit: function () {},

        getFile() {
            const files = this.$refs.logo_big.getFiles()
            if (files.length > 0) {
                this.form.field.logo_big = files[0].file
            } else {
                this.form.field.logo_big = null
            }
        },

        getFileSmall() {
            const files = this.$refs.logo_small.getFiles()
            if (files.length > 0) {
                this.form.field.logo_small = files[0].file
            } else {
                this.form.field.logo_small = null
            }
        },

        getProvinsi() {
            this.form.field.province = ''
            this.form.field.regency = ''
            this.form.field.district = ''
            this.form.field.subdistrict = ''
            this.option.province = []

            let loader = this.$loading.show()
            window.axios
                .get('/options/x0y0z0', {
                    params: {
                        param: 'provinsi-mst',
                    },
                })
                .then((response) => {
                    loader.hide()
                    this.option.province = response.data
                })
                .catch((e) => {
                    console.error(e)
                })
        },

        getKabupaten() {
            this.form.field.regency = ''
            this.form.field.district = ''
            this.form.field.subdistrict = ''
            this.option.regency = []

            let loader = this.$loading.show()
            window.axios
                .get('/options/x0y0z0', {
                    params: {
                        param: 'kabupaten-mst',
                        provinsi: this.form.field.province,
                    },
                })
                .then((response) => {
                    loader.hide()
                    this.option.regency = response.data
                })
                .catch((e) => {
                    console.error(e)
                })
        },

        getKecamatan() {
            this.form.field.district = ''
            this.form.field.subdistrict = ''
            this.option.district = []

            let loader = this.$loading.show()
            window.axios
                .get('/options/x0y0z0', {
                    params: {
                        param: 'kecamatan-mst',
                        provinsi: this.form.field.province,
                        kabupaten: this.form.field.regency,
                    },
                })
                .then((response) => {
                    loader.hide()
                    this.option.district = response.data
                })
                .catch((e) => {
                    console.error(e)
                })
        },

        getKelurahan() {
            this.form.field.subdistrict = ''
            this.option.subdistrict = []

            let loader = this.$loading.show()
            window.axios
                .get('/options/x0y0z0', {
                    params: {
                        param: 'kelurahan-mst',
                        provinsi: this.form.field.province,
                        kabupaten: this.form.field.regency,
                        kecamatan: this.form.field.district,
                    },
                })
                .then((response) => {
                    loader.hide()
                    this.option.subdistrict = response.data
                })
                .catch((e) => {
                    console.error(e)
                })
        },

        async submit() {
            let loader = this.$loading.show()
            try {
                let form_data = new FormData()
                Object.keys(this.form.field).forEach((value) => {
                    form_data.append(value, this.form.field[value])
                })
                const response = await window.axios.post('/pendaftaran-client', form_data)
                this.clearForm()
                loader.hide()
                this.open_finish = true
                this.$notyf.success(response.data.message || 'Register successful!')
            } catch (e) {
                loader.hide()
                if (e.response && e.response.data && e.response.data.errors) {
                    this.$refs.form.setErrors(e.response.data.errors)
                }

                if (e.response && e.response.data && e.response.data.message) {
                    this.$notyf.error(e.response.data.message)
                } else {
                    this.$notyf.error(e.message || 'An error occurred.')
                }
            }
        },
    },

    computed: {},
}
</script>
