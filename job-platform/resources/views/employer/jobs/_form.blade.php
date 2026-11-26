@if ($errors->any())
    <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
        <p class="font-semibold text-rose-900 mb-1">Vui lòng kiểm tra lại thông tin:</p>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="space-y-6">
    {{-- Thông tin cơ bản --}}
    <div class="bg-[#FFFFFF] rounded-xl border border-[#E1EEFA] p-6 shadow-sm">
        <h3 class="text-base font-bold text-[#142B47] mb-4 flex items-center gap-2">
            <span class="inline-block w-1.5 h-5 rounded-full bg-[#2A81CB]"></span>
            Thông tin chung
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label for="title" class="block text-sm font-semibold text-[#142B47] mb-1.5">
                    Tiêu đề tin tuyển dụng <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="title" name="title"
                    value="{{ old('title', $job->title) }}"
                    maxlength="200" required
                    placeholder="VD: Lập trình viên Laravel (Mid/Senior)"
                    class="w-full rounded-lg border-slate-300 bg-[#F7FAFC] text-[#142B47] focus:border-[#2A81CB] focus:ring-[#2A81CB] text-sm">
            </div>

            <div>
                <label for="category" class="block text-sm font-semibold text-[#142B47] mb-1.5">
                    Ngành nghề <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="category" name="category"
                    value="{{ old('category', $job->category) }}"
                    maxlength="100" required list="antdd-category-list"
                    placeholder="VD: Công nghệ thông tin"
                    class="w-full rounded-lg border-slate-300 bg-[#F7FAFC] text-[#142B47] focus:border-[#2A81CB] focus:ring-[#2A81CB] text-sm">
                <datalist id="antdd-category-list">
                    <option value="Công nghệ thông tin">
                    <option value="Kinh doanh / Bán hàng">
                    <option value="Marketing / Truyền thông">
                    <option value="Kế toán / Tài chính">
                    <option value="Nhân sự / Hành chính">
                    <option value="Thiết kế / Sáng tạo">
                    <option value="Kỹ thuật / Cơ khí">
                </datalist>
            </div>

            <div>
                <label for="location" class="block text-sm font-semibold text-[#142B47] mb-1.5">
                    Địa điểm làm việc <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="location" name="location"
                    value="{{ old('location', $job->location) }}"
                    maxlength="150" required
                    placeholder="VD: Hà Nội / TP. Hồ Chí Minh / Remote"
                    class="w-full rounded-lg border-slate-300 bg-[#F7FAFC] text-[#142B47] focus:border-[#2A81CB] focus:ring-[#2A81CB] text-sm">
            </div>

            <div>
                <label for="job_level" class="block text-sm font-semibold text-[#142B47] mb-1.5">
                    Cấp bậc
                </label>
                <select id="job_level" name="job_level"
                    class="w-full rounded-lg border-slate-300 bg-[#F7FAFC] text-[#142B47] focus:border-[#2A81CB] focus:ring-[#2A81CB] text-sm">
                    <option value="">-- Chọn cấp bậc --</option>
                    @foreach (['Thực tập sinh', 'Fresher', 'Junior', 'Middle', 'Senior', 'Trưởng nhóm / Quản lý'] as $level)
                        <option value="{{ $level }}" @selected(old('job_level', $job->job_level) === $level)>
                            {{ $level }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="employment_type" class="block text-sm font-semibold text-[#142B47] mb-1.5">
                    Hình thức làm việc
                </label>
                <select id="employment_type" name="employment_type"
                    class="w-full rounded-lg border-slate-300 bg-[#F7FAFC] text-[#142B47] focus:border-[#2A81CB] focus:ring-[#2A81CB] text-sm">
                    <option value="">-- Chọn hình thức --</option>
                    @foreach (['Toàn thời gian', 'Bán thời gian', 'Thực tập', 'Remote / Hybrid', 'Hợp đồng'] as $type)
                        <option value="{{ $type }}" @selected(old('employment_type', $job->employment_type) === $type)>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="quantity" class="block text-sm font-semibold text-[#142B47] mb-1.5">
                    Số lượng tuyển <span class="text-rose-500">*</span>
                </label>
                <input type="number" id="quantity" name="quantity" min="1" max="1000" required
                    value="{{ old('quantity', $job->quantity ?? 1) }}"
                    class="w-full rounded-lg border-slate-300 bg-[#F7FAFC] text-[#142B47] focus:border-[#2A81CB] focus:ring-[#2A81CB] text-sm">
            </div>

            <div>
                <label for="deadline" class="block text-sm font-semibold text-[#142B47] mb-1.5">
                    Hạn nộp hồ sơ <span class="text-rose-500">*</span>
                </label>
                <input type="date" id="deadline" name="deadline" required
                    value="{{ old('deadline', $job->deadline ? \Illuminate\Support\Carbon::parse($job->deadline)->format('Y-m-d') : '') }}"
                    class="w-full rounded-lg border-slate-300 bg-[#F7FAFC] text-[#142B47] focus:border-[#2A81CB] focus:ring-[#2A81CB] text-sm">
            </div>
        </div>
    </div>

    {{-- Mức lương --}}
    <div class="bg-[#FFFFFF] rounded-xl border border-[#E1EEFA] p-6 shadow-sm">
        <h3 class="text-base font-bold text-[#142B47] mb-1 flex items-center gap-2">
            <span class="inline-block w-1.5 h-5 rounded-full bg-[#2A81CB]"></span>
            Mức lương dự kiến
        </h3>
        <p class="text-xs text-slate-500 mb-4">Để trống lương tối thiểu & tối đa nếu muốn hiển thị "Thỏa thuận".</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label for="salary_min" class="block text-sm font-semibold text-[#142B47] mb-1.5">
                    Lương tối thiểu
                </label>
                <input type="number" id="salary_min" name="salary_min" min="0" step="any"
                    value="{{ old('salary_min', $job->salary_min) }}"
                    placeholder="VD: 12000000"
                    class="w-full rounded-lg border-slate-300 bg-[#F7FAFC] text-[#142B47] focus:border-[#2A81CB] focus:ring-[#2A81CB] text-sm">
            </div>

            <div>
                <label for="salary_max" class="block text-sm font-semibold text-[#142B47] mb-1.5">
                    Lương tối đa
                </label>
                <input type="number" id="salary_max" name="salary_max" min="0" step="any"
                    value="{{ old('salary_max', $job->salary_max) }}"
                    placeholder="VD: 20000000"
                    class="w-full rounded-lg border-slate-300 bg-[#F7FAFC] text-[#142B47] focus:border-[#2A81CB] focus:ring-[#2A81CB] text-sm">
            </div>

            <div>
                <label for="currency" class="block text-sm font-semibold text-[#142B47] mb-1.5">
                    Đơn vị tiền tệ <span class="text-rose-500">*</span>
                </label>
                <select id="currency" name="currency" required
                    class="w-full rounded-lg border-slate-300 bg-[#F7FAFC] text-[#142B47] focus:border-[#2A81CB] focus:ring-[#2A81CB] text-sm">
                    <option value="VND" @selected(old('currency', $job->currency ?? 'VND') === 'VND')>VND</option>
                    <option value="USD" @selected(old('currency', $job->currency ?? 'VND') === 'USD')>USD</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Chi tiết tuyển dụng --}}
    <div class="bg-[#FFFFFF] rounded-xl border border-[#E1EEFA] p-6 shadow-sm space-y-5">
        <h3 class="text-base font-bold text-[#142B47] flex items-center gap-2">
            <span class="inline-block w-1.5 h-5 rounded-full bg-[#2A81CB]"></span>
            Chi tiết công việc
        </h3>

        <div>
            <label for="description" class="block text-sm font-semibold text-[#142B47] mb-1.5">
                Mô tả công việc <span class="text-rose-500">*</span>
            </label>
            <textarea id="description" name="description" rows="5" required
                placeholder="Mô tả chi tiết trách nhiệm, nhiệm vụ chính của vị trí..."
                class="w-full rounded-lg border-slate-300 bg-[#F7FAFC] text-[#142B47] focus:border-[#2A81CB] focus:ring-[#2A81CB] text-sm">{{ old('description', $job->description) }}</textarea>
        </div>

        <div>
            <label for="requirements" class="block text-sm font-semibold text-[#142B47] mb-1.5">
                Yêu cầu ứng viên
            </label>
            <textarea id="requirements" name="requirements" rows="4"
                placeholder="Trình độ, kinh nghiệm, kiến thức chuyên môn cần có..."
                class="w-full rounded-lg border-slate-300 bg-[#F7FAFC] text-[#142B47] focus:border-[#2A81CB] focus:ring-[#2A81CB] text-sm">{{ old('requirements', $job->requirements) }}</textarea>
        </div>

        <div>
            <label for="benefits" class="block text-sm font-semibold text-[#142B47] mb-1.5">
                Quyền lợi & Phúc lợi
            </label>
            <textarea id="benefits" name="benefits" rows="4"
                placeholder="Chế độ lương thưởng, bảo hiểm, đào tạo, du lịch..."
                class="w-full rounded-lg border-slate-300 bg-[#F7FAFC] text-[#142B47] focus:border-[#2A81CB] focus:ring-[#2A81CB] text-sm">{{ old('benefits', $job->benefits) }}</textarea>
        </div>

        <div>
            <label for="skills" class="block text-sm font-semibold text-[#142B47] mb-1.5">
                Kỹ năng yêu cầu (phân tách bằng dấu phẩy)
            </label>
            <input type="text" id="skills" name="skills"
                value="{{ old('skills', $job->skills) }}"
                placeholder="VD: PHP, Laravel, Tailwind CSS, MySQL, Git"
                class="w-full rounded-lg border-slate-300 bg-[#F7FAFC] text-[#142B47] focus:border-[#2A81CB] focus:ring-[#2A81CB] text-sm">
        </div>
    </div>

    {{-- Thanh nút hành động --}}
    <div class="flex flex-wrap items-center justify-between gap-3 bg-[#EAF2FA] border border-[#E1EEFA] rounded-xl p-4">
        <a href="{{ route('employer.jobs.index') }}"
            class="inline-flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold text-[#142B47] bg-[#FFFFFF] border border-slate-300 hover:bg-[#E6F0FA] transition">
            Hủy & Quay lại
        </a>

        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" name="action_type" value="draft" formnovalidate
                class="inline-flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold text-[#142B47] bg-[#FFFFFF] border border-[#2A81CB]/40 hover:bg-[#E6F0FA] transition">
                Lưu nháp (Draft)
            </button>

            <button type="submit" name="action_type" value="preview"
                class="inline-flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold text-[#2A81CB] bg-[#FFFFFF] border border-[#2A81CB] hover:bg-[#E6F0FA] transition">
                Lưu & Xem trước
            </button>

            <button type="submit" name="action_type" value="submit"
                class="inline-flex items-center px-5 py-2.5 rounded-lg text-sm font-semibold text-white bg-[#2A81CB] hover:bg-[#142B47] shadow-sm transition">
                Gửi duyệt ngay
            </button>
        </div>
    </div>
</div>

