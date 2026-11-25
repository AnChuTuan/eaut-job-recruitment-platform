<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;

class CompanyProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('employer.company.edit', [
            'company' => $request->user()->company,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:200'],
            'tax_code' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            'industry' => ['nullable', 'string', 'max:100'],
            'company_size' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:10000'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        $company = Company::firstOrNew(['user_id' => $request->user()->id]);
        $oldLogo = $company->logo_url;
        unset($validated['logo']);

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->storePublicly('companies/logos', 'public');
            if (! $logoPath) {
                throw new RuntimeException('Không thể lưu logo công ty.');
            }
            $validated['logo_url'] = $logoPath;
        }

        $company->fill($validated);
        $company->save();

        if (isset($logoPath) && $oldLogo) {
            Storage::disk('public')->delete($oldLogo);
        }

        return redirect()->route('employer.company.edit')->with('status', 'Đã cập nhật hồ sơ công ty.');
    }
}
