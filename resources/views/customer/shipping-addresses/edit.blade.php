@extends('layouts.customer')

@section('title', 'Edit Address')

@section('breadcrumb')
    <li class="text-zinc-500 hover:text-zinc-950 transition-colors cursor-pointer"><a
            href="{{ route('customer.addresses.index') }}">Vault / Addresses</a></li>
    <li class="text-zinc-950 italic underline underline-offset-4">/ Edit</li>
@endsection

@section('content')
    <div class="max-w-4xl mx-auto px-6 py-12" x-data="editAddressForm()">
        {{-- Header Page --}}
        <div class="mb-12 border-b border-zinc-100 pb-8">
            <h1 class="text-4xl font-black italic tracking-tighter uppercase">Edit <span class="text-zinc-400">Address</span>
            </h1>
            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.3em] mt-2">Update your shipping destination.
            </p>
        </div>

        <div class="bg-white border border-zinc-100 rounded-3xl p-8">
            <form action="{{ route('customer.addresses.update', ['shippingAddress' => $shippingAddress, 'redirect' => request('redirect')]) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Warning if used in active order -->
                @if($shippingAddress->orders()->whereIn('status', ['pending', 'processed', 'shipped'])->count() > 0)
                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mb-6">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <div>
                                <h4 class="text-sm font-bold text-amber-900">Active Order Warning</h4>
                                <p class="text-sm text-amber-700 mt-1">This address is used in an active order. Be careful when updating it.</p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="space-y-3">
                        <label for="recipient_name"
                            class="block text-[10px] font-black uppercase tracking-widest text-zinc-950">Recipient Name
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="recipient_name" id="recipient_name"
                            class="w-full bg-zinc-50 border border-zinc-200 rounded-xl p-4 text-sm font-bold tracking-wide focus:ring-1 focus:ring-zinc-950 focus:border-zinc-950 transition-all duration-300 hover:border-zinc-300 @error('recipient_name') border-red-500 @enderror"
                            value="{{ old('recipient_name', $shippingAddress->recipient_name) }}" placeholder="e.g. John Doe" required>
                        @error('recipient_name')
                            <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-3">
                        <label for="phone"
                            class="block text-[10px] font-black uppercase tracking-widest text-zinc-950">Phone Number <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="phone" id="phone"
                            class="w-full bg-zinc-50 border border-zinc-200 rounded-xl p-4 text-sm font-bold tracking-wide focus:ring-1 focus:ring-zinc-950 focus:border-zinc-950 transition-all duration-300 hover:border-zinc-300 @error('phone') border-red-500 @enderror"
                            value="{{ old('phone', $shippingAddress->phone) }}" placeholder="e.g. 08123456789" required>
                        @error('phone')
                            <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2 space-y-3">
                        <label for="address"
                            class="block text-[10px] font-black uppercase tracking-widest text-zinc-950">Full Address <span
                                class="text-red-500">*</span></label>
                        <textarea name="address" id="address"
                            class="w-full bg-zinc-50 border border-zinc-200 rounded-xl p-4 text-sm font-medium tracking-wide focus:ring-1 focus:ring-zinc-950 focus:border-zinc-950 transition-all duration-300 hover:border-zinc-300 @error('address') border-red-500 @enderror"
                            rows="3" placeholder="Street name, building, house number..." required>{{ old('address', $shippingAddress->address) }}</textarea>
                        @error('address')
                            <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Province select -->
                    <div class="space-y-3">
                        <label for="province_id"
                            class="block text-[10px] font-black uppercase tracking-widest text-zinc-950">Province <span
                                class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="province_id" id="province_id"
                                class="w-full bg-zinc-50 border border-zinc-200 rounded-xl p-4 text-sm font-bold tracking-wide focus:ring-1 focus:ring-zinc-950 focus:border-zinc-950 appearance-none cursor-pointer transition-all duration-300 hover:border-zinc-300 @error('province_id') border-red-500 @enderror"
                                x-model="selectedProvinceId" @change="onProvinceChange()" required>
                                <option value="">-- Select Province --</option>
                                <template x-for="prov in provinces" :key="prov.id">
                                    <option :value="prov.id" x-text="prov.name" :selected="prov.id == selectedProvinceId"></option>
                                </template>
                            </select>
                            <div
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-zinc-950">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        <input type="hidden" name="province" :value="selectedProvinceName">
                        @error('province_id')
                            <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                        <div class="form-text mt-2 flex items-center gap-2" x-show="loadingProvinces">
                            <span
                                class="inline-block w-4 h-4 border-2 border-zinc-950 border-t-transparent rounded-full animate-spin"></span>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Loading
                                provinces...</span>
                        </div>
                        <div class="mt-2 flex items-center gap-2" x-show="errorProvinces && !loadingProvinces" x-cloak>
                            <span class="text-[10px] font-bold text-red-500" x-text="errorProvinces"></span>
                            <button type="button" @click="retryProvinces()" class="text-[10px] font-black uppercase tracking-widest text-zinc-950 underline underline-offset-2 hover:text-zinc-600 transition-colors">Retry</button>
                        </div>
                    </div>

                    <!-- City select -->
                    <div class="space-y-3">
                        <label for="city_id"
                            class="block text-[10px] font-black uppercase tracking-widest text-zinc-950">City/Regency <span
                                class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="city_id" id="city_id"
                                class="w-full bg-zinc-50 border border-zinc-200 rounded-xl p-4 text-sm font-bold tracking-wide focus:ring-1 focus:ring-zinc-950 focus:border-zinc-950 appearance-none cursor-pointer transition-all duration-300 hover:border-zinc-300 @error('city_id') border-red-500 @enderror disabled:opacity-50 disabled:cursor-not-allowed"
                                x-model="selectedCityId" @change="onCityChange()"
                                :disabled="!selectedProvinceId || loadingCities" required>
                                <option value="">-- Select City --</option>
                                <template x-for="city in cities" :key="city.id">
                                    <option :value="city.id" x-text="city.name" :selected="city.id == selectedCityId"></option>
                                </template>
                            </select>
                            <div
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-zinc-950">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        <input type="hidden" name="city" :value="selectedCityName">
                        @error('city_id')
                            <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                        <div class="form-text mt-2 flex items-center gap-2" x-show="loadingCities">
                            <span
                                class="inline-block w-4 h-4 border-2 border-zinc-950 border-t-transparent rounded-full animate-spin"></span>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Loading
                                cities...</span>
                        </div>
                        <div class="mt-2 flex items-center gap-2" x-show="errorCities && !loadingCities" x-cloak>
                            <span class="text-[10px] font-bold text-red-500" x-text="errorCities"></span>
                            <button type="button" @click="retryCities()" class="text-[10px] font-black uppercase tracking-widest text-zinc-950 underline underline-offset-2 hover:text-zinc-600 transition-colors">Retry</button>
                        </div>
                    </div>

                    <!-- District (Kecamatan) select -->
                    <div class="space-y-3" x-show="districts.length > 0 || loadingDistricts">
                        <label for="district_id"
                            class="block text-[10px] font-black uppercase tracking-widest text-zinc-950">District /
                            Kecamatan <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="district_id" id="district_id"
                                class="w-full bg-zinc-50 border border-zinc-200 rounded-xl p-4 text-sm font-bold tracking-wide focus:ring-1 focus:ring-zinc-950 focus:border-zinc-950 appearance-none cursor-pointer transition-all duration-300 hover:border-zinc-300 disabled:opacity-50 disabled:cursor-not-allowed @error('district_id') border-red-500 @enderror"
                                x-model="selectedDistrictId" @change="onDistrictChange()"
                                :disabled="!selectedCityId || loadingDistricts" required>
                                <option value="">-- Select District --</option>
                                <template x-for="district in districts" :key="district.id">
                                    <option :value="district.id" x-text="district.name" :selected="district.id == selectedDistrictId"></option>
                                </template>
                            </select>
                            <div
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-zinc-950">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        <input type="hidden" name="district" :value="selectedDistrictName">
                        @error('district_id')
                            <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                        <div class="form-text mt-2 flex items-center gap-2" x-show="loadingDistricts">
                            <span
                                class="inline-block w-4 h-4 border-2 border-zinc-950 border-t-transparent rounded-full animate-spin"></span>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Loading
                                districts...</span>
                        </div>
                        <div class="mt-2 flex items-center gap-2" x-show="errorDistricts && !loadingDistricts" x-cloak>
                            <span class="text-[10px] font-bold text-red-500" x-text="errorDistricts"></span>
                            <button type="button" @click="retryDistricts()" class="text-[10px] font-black uppercase tracking-widest text-zinc-950 underline underline-offset-2 hover:text-zinc-600 transition-colors">Retry</button>
                        </div>
                    </div>

                    <!-- Sub-district (Kelurahan) select -->
                    <div class="space-y-3" x-show="subdistricts.length > 0 || loadingSubdistricts">
                        <label for="subdistrict_id"
                            class="block text-[10px] font-black uppercase tracking-widest text-zinc-950">Sub-district /
                            Kelurahan</label>
                        <div class="relative">
                            <select name="subdistrict_id" id="subdistrict_id"
                                class="w-full bg-zinc-50 border border-zinc-200 rounded-xl p-4 text-sm font-bold tracking-wide focus:ring-1 focus:ring-zinc-950 focus:border-zinc-950 appearance-none cursor-pointer transition-all duration-300 hover:border-zinc-300 disabled:opacity-50 disabled:cursor-not-allowed"
                                x-model="selectedSubdistrictId" @change="onSubdistrictChange()"
                                :disabled="!selectedDistrictId || loadingSubdistricts">
                                <option value="">-- Select Sub-district --</option>
                                <template x-for="sub in subdistricts" :key="sub.id">
                                    <option :value="sub.id" x-text="sub.name" :selected="sub.id == selectedSubdistrictId"></option>
                                </template>
                            </select>
                            <div
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-zinc-950">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        <input type="hidden" name="subdistrict" :value="selectedSubdistrictName">
                        @error('subdistrict_id')
                            <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                        <div class="form-text mt-2 flex items-center gap-2" x-show="loadingSubdistricts">
                            <span
                                class="inline-block w-4 h-4 border-2 border-zinc-950 border-t-transparent rounded-full animate-spin"></span>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">Loading
                                subdistricts...</span>
                        </div>
                        <div class="mt-2 flex items-center gap-2" x-show="errorSubdistricts && !loadingSubdistricts" x-cloak>
                            <span class="text-[10px] font-bold text-red-500" x-text="errorSubdistricts"></span>
                            <button type="button" @click="retrySubdistricts()" class="text-[10px] font-black uppercase tracking-widest text-zinc-950 underline underline-offset-2 hover:text-zinc-600 transition-colors">Retry</button>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label for="postal_code"
                            class="block text-[10px] font-black uppercase tracking-widest text-zinc-950">Postal Code <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="postal_code" id="postal_code"
                            class="w-full bg-zinc-50 border border-zinc-200 rounded-xl p-4 text-sm font-bold tracking-wide focus:ring-1 focus:ring-zinc-950 focus:border-zinc-950 transition-all duration-300 hover:border-zinc-300 @error('postal_code') border-red-500 @enderror"
                            x-model="selectedPostalCode" placeholder="e.g. 55561" required>
                        @error('postal_code')
                            <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-4 pt-6 border-t border-zinc-100">
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 bg-zinc-950 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-xl hover:bg-white hover:text-zinc-950 border border-zinc-950 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="!selectedProvinceId || !selectedCityId || !selectedDistrictId">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Update Address
                    </button>
                    <a href="{{ request('redirect') === 'checkout' ? route('customer.checkout.index') : route('customer.addresses.index') }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-zinc-950 text-[10px] font-black uppercase tracking-[0.2em] rounded-xl border border-zinc-200 hover:border-zinc-950 transition-all duration-300">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editAddressForm() {
            return {
                provinces: [],
                cities: [],
                districts: [],
                subdistricts: [],

                selectedProvinceId: '{{ old('province_id', $shippingAddress->province_id ?? '') }}',
                selectedProvinceName: '{{ old('province', $shippingAddress->province ?? '') }}',
                selectedCityId: '{{ old('city_id', $shippingAddress->city_id ?? '') }}',
                selectedCityName: '{{ old('city', $shippingAddress->city ?? '') }}',
                selectedDistrictId: '{{ old('district_id', $shippingAddress->subdistrict_id ?? '') }}', // database subdistrict_id is Kecamatan
                selectedDistrictName: '',
                selectedSubdistrictId: '{{ old('subdistrict_id', '') }}',
                selectedSubdistrictName: '',
                selectedPostalCode: '{{ old('postal_code', $shippingAddress->postal_code ?? '') }}',
                storedSubdistrictString: '{{ $shippingAddress->subdistrict ?? '' }}',

                loadingProvinces: false,
                loadingCities: false,
                loadingDistricts: false,
                loadingSubdistricts: false,

                errorProvinces: '',
                errorCities: '',
                errorDistricts: '',
                errorSubdistricts: '',

                init() {
                    this.fetchProvinces();
                },

                async fetchProvinces() {
                    this.loadingProvinces = true;
                    this.errorProvinces = '';
                    try {
                        const res = await fetch('{{ route('customer.api.rajaongkir.provinces') }}');
                        const data = await res.json();
                        if (data.success && data.data.length > 0) {
                            this.provinces = data.data;
                            
                            // Resolve province name
                            const prov = this.provinces.find(p => p.id == this.selectedProvinceId);
                            if (prov) {
                                this.selectedProvinceName = prov.name;
                            }
                        } else {
                            this.errorProvinces = data.message || 'Gagal memuat provinsi. Klik untuk coba lagi.';
                        }
                        if (this.selectedProvinceId) {
                            await this.fetchCities(this.selectedProvinceId);
                        }
                    } catch (e) {
                        console.error('Failed to load provinces:', e);
                        this.errorProvinces = 'Koneksi gagal. Klik untuk coba lagi.';
                    } finally {
                        this.loadingProvinces = false;
                    }
                },

                async fetchCities(provinceId) {
                    this.loadingCities = true;
                    this.errorCities = '';
                    try {
                        const res = await fetch(
                            `{{ route('customer.api.rajaongkir.cities', ['provinceId' => ':provinceId']) }}`
                            .replace(':provinceId', provinceId));
                        const data = await res.json();
                        if (data.success && data.data.length > 0) {
                            this.cities = data.data;
                            
                            // Resolve city name
                            const city = this.cities.find(c => c.id == this.selectedCityId);
                            if (city) {
                                this.selectedCityName = city.name;
                            }
                        } else {
                            this.errorCities = data.message || 'Gagal memuat kota. Klik untuk coba lagi.';
                        }
                        if (this.selectedCityId) {
                            await this.fetchDistricts(this.selectedCityId);
                        }
                    } catch (e) {
                        console.error('Failed to load cities:', e);
                        this.errorCities = 'Koneksi gagal. Klik untuk coba lagi.';
                    } finally {
                        this.loadingCities = false;
                    }
                },

                async fetchDistricts(cityId) {
                    this.loadingDistricts = true;
                    this.errorDistricts = '';
                    try {
                        const res = await fetch(
                            `{{ route('customer.api.rajaongkir.districts', ['cityId' => ':cityId']) }}`
                            .replace(':cityId', cityId));
                        const data = await res.json();
                        if (data.success && data.data.length > 0) {
                            this.districts = data.data;
                            
                            // Resolve district name
                            const district = this.districts.find(d => d.id == this.selectedDistrictId);
                            if (district) {
                                this.selectedDistrictName = district.name;
                            }
                        } else {
                            this.errorDistricts = data.message || 'Gagal memuat kecamatan. Klik untuk coba lagi.';
                        }
                        if (this.selectedDistrictId) {
                            await this.fetchSubdistricts(this.selectedDistrictId);
                        }
                    } catch (e) {
                        console.error('Failed to load districts:', e);
                        this.errorDistricts = 'Koneksi gagal. Klik untuk coba lagi.';
                    } finally {
                        this.loadingDistricts = false;
                    }
                },

                async fetchSubdistricts(districtId) {
                    this.loadingSubdistricts = true;
                    this.errorSubdistricts = '';
                    try {
                        const res = await fetch(
                            `{{ route('customer.api.rajaongkir.subdistricts', ['districtId' => ':districtId']) }}`
                            .replace(':districtId', districtId));
                        const data = await res.json();
                        if (data.success && data.data.length > 0) {
                            this.subdistricts = data.data;
                            
                            // Resolve stored subdistrict (Kelurahan) name and ID by matching string
                            const parts = this.storedSubdistrictString.split(', ');
                            const kelurahanName = parts[1] || '';
                            if (kelurahanName) {
                                const found = this.subdistricts.find(s => s.name.toUpperCase() === kelurahanName.toUpperCase());
                                if (found) {
                                    this.selectedSubdistrictId = found.id;
                                    this.selectedSubdistrictName = found.name;
                                }
                            }
                        } else {
                            this.errorSubdistricts = data.message || 'Gagal memuat kelurahan. Klik untuk coba lagi.';
                        }
                    } catch (e) {
                        console.error('Failed to load subdistricts:', e);
                        this.errorSubdistricts = 'Koneksi gagal. Klik untuk coba lagi.';
                    } finally {
                        this.loadingSubdistricts = false;
                    }
                },

                retryProvinces() {
                    this.provinces = [];
                    this.fetchProvinces();
                },

                retryCities() {
                    this.cities = [];
                    if (this.selectedProvinceId) {
                        this.fetchCities(this.selectedProvinceId);
                    }
                },

                retryDistricts() {
                    this.districts = [];
                    if (this.selectedCityId) {
                        this.fetchDistricts(this.selectedCityId);
                    }
                },

                retrySubdistricts() {
                    this.subdistricts = [];
                    if (this.selectedDistrictId) {
                        this.fetchSubdistricts(this.selectedDistrictId);
                    }
                },

                onProvinceChange() {
                    this.selectedCityId = '';
                    this.selectedCityName = '';
                    this.selectedDistrictId = '';
                    this.selectedDistrictName = '';
                    this.selectedSubdistrictId = '';
                    this.selectedSubdistrictName = '';
                    this.selectedPostalCode = '';
                    this.cities = [];
                    this.districts = [];
                    this.subdistricts = [];
                    this.errorCities = '';
                    this.errorDistricts = '';
                    this.errorSubdistricts = '';

                    const prov = this.provinces.find(p => p.id == this.selectedProvinceId);
                    this.selectedProvinceName = prov ? prov.name : '';

                    if (this.selectedProvinceId) {
                        this.fetchCities(this.selectedProvinceId);
                    }
                },

                onCityChange() {
                    this.selectedDistrictId = '';
                    this.selectedDistrictName = '';
                    this.selectedSubdistrictId = '';
                    this.selectedSubdistrictName = '';
                    this.selectedPostalCode = '';
                    this.districts = [];
                    this.subdistricts = [];
                    this.errorDistricts = '';
                    this.errorSubdistricts = '';

                    const city = this.cities.find(c => c.id == this.selectedCityId);
                    this.selectedCityName = city ? city.name : '';

                    if (this.selectedCityId) {
                        this.fetchDistricts(this.selectedCityId);
                    }
                },

                onDistrictChange() {
                    this.selectedSubdistrictId = '';
                    this.selectedSubdistrictName = '';
                    this.subdistricts = [];
                    this.errorSubdistricts = '';

                    const district = this.districts.find(d => d.id == this.selectedDistrictId);
                    this.selectedDistrictName = district ? district.name : '';

                    if (this.selectedDistrictId) {
                        this.fetchSubdistricts(this.selectedDistrictId);
                    }
                },

                onSubdistrictChange() {
                    const sub = this.subdistricts.find(s => s.id == this.selectedSubdistrictId);
                    this.selectedSubdistrictName = sub ? sub.name : '';
                    if (sub && sub.postal_code) {
                        this.selectedPostalCode = sub.postal_code;
                    }
                },
            }
        }
    </script>
@endsection
