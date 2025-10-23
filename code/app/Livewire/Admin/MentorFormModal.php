<?php

namespace App\Livewire\Admin;

use App\Models\Language;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class MentorFormModal extends Component
{
    public array $form = [];
    public ?int $editingId = null;
    public string $mode = 'create';
    public array $languages = [];

    protected ?int $organisationId = null;
    protected ?int $defaultLanguageId = 1;
    protected Role $mentorRole;

    protected $listeners = ['open-mentor-form' => 'openForm'];

    public function mount(): void
    {
        $this->ensureContext();
        $this->loadLanguages();
        $this->resetForm();
    }

    protected function ensureContext(): void
    {
        $admin = Auth::user();
        abort_unless($admin?->isAdmin(), 403);

        $this->organisationId = $admin->organisation_id;
        $this->mentorRole = Role::where('role', Role::MENTOR)->firstOrFail();
    }

    protected function loadLanguages(): void
    {
        $languageCollection = Language::orderBy('language_name')->get();
        $this->languages = $languageCollection
            ->map(fn(Language $language) => [
                'id' => $language->language_id,
                'label' => $language->language_name,
                'code' => $language->language_code,
            ])->all();

        $this->defaultLanguageId = $languageCollection
            ->firstWhere('language_code', 'nl')?->language_id
            ?? $languageCollection->first()?->language_id;
    }

    public function openForm(?int $mentorId = null): void
    {
        $this->ensureContext();

        if ($mentorId) {
            $mentor = User::where('user_id', $mentorId)
                ->where('organisation_id', $this->organisationId)
                ->whereHas('roles', fn($q) => $q->where('role', Role::MENTOR))
                ->firstOrFail();

            $this->editingId = $mentorId;
            $this->mode = 'edit';
            $this->form = [
                'first_name' => $mentor->first_name,
                'last_name' => $mentor->last_name ?? '',
                'username' => $mentor->username,
                'password' => '',
                'language_id' => $mentor->language_id,
                'active' => (bool) $mentor->active,
            ];
        } else {
            $this->resetForm();
            $this->mode = 'create';
        }

        $this->resetErrorBag();
        $this->resetValidation();

        // Open the modal after setting up the form
        $this->dispatch('modal-open', name: 'admin-mentor-form');
    }

    protected function resetForm(): void
    {
        $this->editingId = null;
        $this->form = [
            'first_name' => '',
            'last_name' => '',
            'username' => '',
            'password' => '',
            'language_id' => $this->defaultLanguageId,
            'active' => true,
        ];
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->dispatch('modal-close', name: 'admin-mentor-form');
    }

    protected function rules(): array
    {
        return [
            'form.first_name' => ['required', 'string', 'max:255'],
            'form.last_name' => ['nullable', 'string', 'max:255'],
            'form.username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($this->editingId, 'user_id'),
            ],
            'form.password' => array_filter([
                $this->editingId ? 'nullable' : 'required',
                'string',
                Password::defaults(),
            ]),
            'form.language_id' => ['required', 'integer', 'exists:language,language_id'],
            'form.active' => ['boolean'],
        ];
    }

    public function save(): void
    {
        $this->ensureContext();
        $this->validate();

        $attributes = [
            'first_name' => trim($this->form['first_name']),
            'last_name' => trim($this->form['last_name']),
            'username' => trim($this->form['username']),
            'language_id' => (int) $this->form['language_id'],
            'active' => (bool) $this->form['active'],
            'vision_type' => 'normal',
            'organisation_id' => $this->organisationId,
        ];

        $isEditing = (bool) $this->editingId;

        DB::transaction(function () use ($attributes, $isEditing) {
            if ($this->editingId) {
                $mentor = User::where('user_id', $this->editingId)
                    ->where('organisation_id', $this->organisationId)
                    ->firstOrFail();

                $mentor->fill($attributes);

                if (!empty($this->form['password'])) {
                    $mentor->password = Hash::make($this->form['password']);
                }

                $mentor->save();
            } else {
                $mentor = new User($attributes);
                $mentor->password = Hash::make($this->form['password']);
                $mentor->first_login = true;

                $mentor->save();
                $mentor->roles()->syncWithoutDetaching([$this->mentorRole->role_id]);
            }
        });

        session()->flash('status', $isEditing
            ? __('Mentor updated successfully.')
            : __('Mentor created successfully.'));

        $this->resetForm();
        $this->dispatch('mentor-saved');
        $this->dispatch('modal-close', name: 'admin-mentor-form');
    }

    public function render()
    {
        return view('livewire.admin.mentor-form-modal');
    }
}
