<?php

namespace App\Livewire;

use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AddressManager extends Component
{
    // Form alanları
    public string $type = 'shipping';
    public string $title = '';
    public string $full_name = '';
    public string $phone = '';
    public string $city = '';
    public string $district = '';
    public string $address_line = '';
    public string $invoice_type = 'individual';
    public string $tc_no = '';
    public string $company_name = '';
    public string $tax_office = '';
    public string $tax_number = '';
    public bool $is_default = false;

    // UI durumu
    public bool $showForm = false;
    public ?int $editingId = null;

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
    }

    protected function rules()
    {
        $rules = [
            'type' => 'required|in:shipping,billing',
            'title' => 'required|string|max:100',
            'full_name' => 'required|string|min:3|max:255',
            'phone' => 'nullable|string',
            'city' => 'required|string|min:2',
            'district' => 'required|string|min:2',
            'address_line' => 'required|string|min:10',
        ];

        if ($this->type === 'billing') {
            $rules['invoice_type'] = 'required|in:individual,corporate';

            if ($this->invoice_type === 'individual') {
                $rules['tc_no'] = 'required|string|size:11|regex:/^[0-9]+$/';
            } else {
                $rules['company_name'] = 'required|string|min:2';
                $rules['tax_office'] = 'required|string|min:2';
                $rules['tax_number'] = 'required|string|min:10|max:11';
            }
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'title.required' => 'Adres başlığı zorunludur.',
            'full_name.required' => 'Ad soyad zorunludur.',
            'city.required' => 'Şehir zorunludur.',
            'district.required' => 'İlçe zorunludur.',
            'address_line.required' => 'Adres satırı zorunludur.',
            'address_line.min' => 'Adres en az 10 karakter olmalıdır.',
            'tc_no.required' => 'TC Kimlik No zorunludur.',
            'tc_no.size' => 'TC Kimlik No 11 haneli olmalıdır.',
            'tc_no.regex' => 'TC Kimlik No sadece rakamlardan oluşmalıdır.',
            'company_name.required' => 'Şirket unvanı zorunludur.',
            'tax_office.required' => 'Vergi dairesi zorunludur.',
            'tax_number.required' => 'Vergi numarası zorunludur.',
            'tax_number.min' => 'Vergi numarası en az 10 haneli olmalıdır.',
        ];
    }

    public function openNewForm(string $type = 'shipping')
    {
        $this->resetForm();
        $this->type = $type;
        $this->full_name = Auth::user()->name ?? '';
        $this->phone = Auth::user()->phone ?? '';
        if ($type === 'billing' && $this->invoice_type === 'individual') {
            $this->tc_no = Auth::user()->tc_no ?? '';
        }
        $this->showForm = true;
        $this->editingId = null;
    }

    public function editAddress(int $id)
    {
        $address = Address::where('user_id', Auth::id())->findOrFail($id);

        $this->editingId = $address->id;
        $this->type = $address->type;
        $this->title = $address->title ?? '';
        $this->full_name = $address->full_name;
        $this->phone = $address->phone ?? '';
        $this->city = $address->city;
        $this->district = $address->district;
        $this->address_line = $address->address_line;
        $this->invoice_type = $address->invoice_type ?? 'individual';
        $this->tc_no = $address->tc_no ?? '';
        $this->company_name = $address->company_name ?? '';
        $this->tax_office = $address->tax_office ?? '';
        $this->tax_number = $address->tax_number ?? '';
        $this->is_default = $address->is_default;

        $this->showForm = true;
    }

    public function saveAddress()
    {
        $this->validate();

        $userId = Auth::id();

        // Varsayılan yapılacaksa, diğerlerini kaldır
        if ($this->is_default) {
            Address::where('user_id', $userId)
                ->where('type', $this->type)
                ->update(['is_default' => false]);
        }

        $data = [
            'user_id' => $userId,
            'type' => $this->type,
            'title' => $this->title,
            'full_name' => $this->full_name,
            'phone' => $this->phone,
            'city' => $this->city,
            'district' => $this->district,
            'address_line' => $this->address_line,
            'invoice_type' => $this->type === 'billing' ? $this->invoice_type : 'individual',
            'tc_no' => $this->type === 'billing' && $this->invoice_type === 'individual' ? $this->tc_no : null,
            'company_name' => $this->type === 'billing' && $this->invoice_type === 'corporate' ? $this->company_name : null,
            'tax_office' => $this->type === 'billing' && $this->invoice_type === 'corporate' ? $this->tax_office : null,
            'tax_number' => $this->type === 'billing' && $this->invoice_type === 'corporate' ? $this->tax_number : null,
            'is_default' => $this->is_default,
        ];

        if ($this->editingId) {
            $address = Address::where('user_id', $userId)->findOrFail($this->editingId);
            $address->update($data);
            session()->flash('success', 'Adres güncellendi.');
        } else {
            // Bu tipteki ilk adres ise otomatik varsayılan yap
            $existingCount = Address::where('user_id', $userId)->where('type', $this->type)->count();
            if ($existingCount === 0) {
                $data['is_default'] = true;
            }

            Address::create($data);
            session()->flash('success', 'Adres eklendi.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function deleteAddress(int $id)
    {
        $address = Address::where('user_id', Auth::id())->findOrFail($id);
        $wasDefault = $address->is_default;
        $type = $address->type;
        $address->delete();

        // Silinen varsayılansa, ilk adresi varsayılan yap
        if ($wasDefault) {
            $first = Address::where('user_id', Auth::id())->where('type', $type)->first();
            if ($first) {
                $first->update(['is_default' => true]);
            }
        }

        session()->flash('success', 'Adres silindi.');
    }

    public function setDefault(int $id)
    {
        $address = Address::where('user_id', Auth::id())->findOrFail($id);

        // Aynı tipteki tüm varsayılanları kaldır
        Address::where('user_id', Auth::id())
            ->where('type', $address->type)
            ->update(['is_default' => false]);

        $address->update(['is_default' => true]);

        session()->flash('success', 'Varsayılan adres güncellendi.');
    }

    public function cancelForm()
    {
        $this->resetForm();
        $this->showForm = false;
    }

    protected function resetForm()
    {
        $this->reset([
            'type', 'title', 'full_name', 'phone', 'city', 'district',
            'address_line', 'invoice_type', 'tc_no', 'company_name',
            'tax_office', 'tax_number', 'is_default', 'editingId',
        ]);
        $this->resetValidation();
    }

    public function render()
    {
        $userId = Auth::id();

        $shippingAddresses = Address::where('user_id', $userId)
            ->where('type', 'shipping')
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get();

        $billingAddresses = Address::where('user_id', $userId)
            ->where('type', 'billing')
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get();

        return view('livewire.address-manager', [
            'shippingAddresses' => $shippingAddresses,
            'billingAddresses' => $billingAddresses,
        ])->layoutData([
            'title' => 'Adreslerim | BeArtShare',
        ]);
    }
}
