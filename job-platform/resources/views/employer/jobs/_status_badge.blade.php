@php
    $statusMap = [
        'draft' => [
            'label' => 'Bản nháp',
            'classes' => 'bg-[#F7FAFC] text-[#142B47] border border-slate-300',
        ],
        'pending' => [
            'label' => 'Chờ duyệt',
            'classes' => 'bg-[#EAF2FA] text-[#2A81CB] border border-[#2A81CB]/30',
        ],
        'approved' => [
            'label' => 'Đã duyệt',
            'classes' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
        ],
        'needs_revision' => [
            'label' => 'Cần chỉnh sửa',
            'classes' => 'bg-amber-50 text-amber-700 border border-amber-200',
        ],
        'rejected' => [
            'label' => 'Từ chối',
            'classes' => 'bg-rose-50 text-rose-700 border border-rose-200',
        ],
        'banned' => [
            'label' => 'Bị khóa',
            'classes' => 'bg-[#142B47] text-white border border-[#142B47]',
        ],
    ];

    $meta = $statusMap[$status ?? 'draft'] ?? $statusMap['draft'];
@endphp

<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $meta['classes'] }}">
    {{ $meta['label'] }}
</span>

