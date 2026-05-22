@extends('admin.layouts.app')

@section('title', 'Manajemen Kategori')
@section('page-title', 'Manajemen Kategori')
@section('page-subtitle', 'Kelola kategori produk')

@section('content')
<div class="grid lg:grid-cols-3 gap-5">

    {{-- Add Category Form --}}
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
        <h3 class="font-bold text-white mb-4">+ Tambah Kategori</h3>
        <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="text-xs text-slate-400 block mb-1">Nama *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-slate-200 focus:ring-2 focus:ring-sky-500 outline-none">
                @error('name') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-xs text-slate-400 block mb-1">Icon (Emoji)</label>
                <input type="text" name="icon" value="{{ old('icon') }}" placeholder="📱"
                    class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-slate-200 focus:ring-2 focus:ring-sky-500 outline-none">
            </div>
            <div>
                <label class="text-xs text-slate-400 block mb-1">Deskripsi</label>
                <textarea name="description" rows="2" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-slate-200 focus:ring-2 focus:ring-sky-500 outline-none resize-none">{{ old('description') }}</textarea>
            </div>
            <div>
                <label class="text-xs text-slate-400 block mb-1">Urutan</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                    class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-slate-200 focus:ring-2 focus:ring-sky-500 outline-none">
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" checked class="accent-sky-500">
                <span class="text-xs text-slate-300">Aktif</span>
            </label>
            <button type="submit" class="w-full py-2.5 bg-gradient-to-r from-sky-500 to-indigo-600 text-white font-bold rounded-xl text-sm transition-all hover:shadow-lg">Tambah Kategori</button>
        </form>
    </div>

    {{-- Categories List --}}
    <div class="lg:col-span-2">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-800">
                <h3 class="font-bold text-white">Daftar Kategori</h3>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-800">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-400">Kategori</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400">Produk</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($categories as $cat)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">{{ $cat->icon }}</span>
                                <div>
                                    <p class="text-sm font-semibold text-slate-200">{{ $cat->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $cat->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="text-sm text-slate-400">{{ $cat->products_count }} produk</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $cat->is_active ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-700 text-slate-500' }}">
                                {{ $cat->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="editCategory({{ $cat->id }}, '{{ $cat->name }}', '{{ $cat->icon }}', '{{ $cat->description }}', {{ $cat->sort_order }}, {{ $cat->is_active ? 1 : 0 }})"
                                    class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs rounded-lg transition-colors">Edit</button>
                                @if($cat->products_count == 0)
                                <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-red-500/10 hover:bg-red-500/20 text-red-400 text-xs rounded-lg transition-colors">Hapus</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div id="edit-cat-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-700 rounded-2xl p-6 w-full max-w-md">
        <h3 class="font-bold text-white mb-4">Edit Kategori</h3>
        <form id="edit-cat-form" method="POST" class="space-y-3">
            @csrf @method('PUT')
            <input type="text" name="name" id="edit-cat-name" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-slate-200 focus:ring-2 focus:ring-sky-500 outline-none">
            <input type="text" name="icon" id="edit-cat-icon" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-slate-200 focus:ring-2 focus:ring-sky-500 outline-none">
            <textarea name="description" id="edit-cat-desc" rows="2" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-slate-200 focus:ring-2 focus:ring-sky-500 outline-none resize-none"></textarea>
            <input type="number" name="sort_order" id="edit-cat-order" min="0" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-slate-200 focus:ring-2 focus:ring-sky-500 outline-none">
            <label class="flex items-center gap-2"><input type="checkbox" name="is_active" id="edit-cat-active" value="1" class="accent-sky-500"><span class="text-sm text-slate-300">Aktif</span></label>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2.5 bg-sky-500 text-white font-bold rounded-xl text-sm">Perbarui</button>
                <button type="button" onclick="document.getElementById('edit-cat-modal').classList.add('hidden')" class="flex-1 py-2.5 bg-slate-800 text-slate-300 rounded-xl text-sm">Batal</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function editCategory(id, name, icon, desc, order, active) {
    document.getElementById('edit-cat-form').action = `/admin/categories/${id}`;
    document.getElementById('edit-cat-name').value = name;
    document.getElementById('edit-cat-icon').value = icon;
    document.getElementById('edit-cat-desc').value = desc;
    document.getElementById('edit-cat-order').value = order;
    document.getElementById('edit-cat-active').checked = active === 1;
    document.getElementById('edit-cat-modal').classList.remove('hidden');
}
</script>
@endpush
@endsection
