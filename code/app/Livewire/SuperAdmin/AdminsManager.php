<?php

namespace App\Livewire\SuperAdmin;

use App\Livewire\Crud\BaseCrudComponent;
use App\Models\User;
use App\Models\Role;
use App\Models\Organisation;
use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminsManager extends BaseCrudComponent
{
    protected ?Role $adminRole = null;

    // lists used in selects
    public Collection $organisations;
    public Collection $languages;

    protected function view(): string
    {
        return 'livewire.superadmin.admins-manager';
    }

    protected function initializeCrud(): void
    {
        parent::initializeCrud();

        if (!isset($this->adminRole)) {
            $this->adminRole = Role::where('role', Role::ADMIN)->firstOrFail();
        }
        // Load organisations & languages for select inputs
        $this->organisations = Organisation::orderBy('name')->get();
        $this->languages = Language::orderBy('language_name')->get();
    }

    protected function defaultFormState(): array
    {
        return [
            'first_name' => '',
            'last_name' => '',
            'username' => '',
            'password' => '',
            'organisation_id' => null,
            'language_id' => null,
            'active' => true,
        ];
    }

    public function startCreate(): void
    {
        parent::startCreate();

        // set sensible defaults when creating a new admin
        if ($this->organisations->count() > 0) {
            $this->form['organisation_id'] = $this->organisations->first()->organisation_id;
        }
        if ($this->languages->count() > 0) {
            $this->form['language_id'] = $this->languages->first()->language_id;
        }
    }

    public function startEdit(int $recordId): void
    {
        parent::startEdit($recordId);
        // ensure the modal is opened in the frontend
        $this->dispatch('modal-open', name: 'admin-form');
    }

    protected function baseQuery(): Builder
    {
        return User::query()
            ->whereHas('roles', fn($q) => $q->where('role', Role::ADMIN))
            ->orderBy('first_name')
            ->orderBy('last_name');
    }

    protected function applySearch(Builder $query): Builder
    {
        $term = trim($this->search);
        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('first_name', 'like', "%{$term}%")
              ->orWhere('last_name', 'like', "%{$term}%")
              ->orWhere('username', 'like', "%{$term}%");
        });
    }

    protected function findRecord(int $id)
    {
        return User::where('user_id', $id)->firstOrFail();
    }

    public function transformRecordToForm($record): array
    {
        return [
            'first_name' => $record->first_name,
            'last_name' => $record->last_name,
            'username' => $record->username,
            'password' => '',
            'organisation_id' => $record->organisation_id,
            'language_id' => $record->language_id ?? null,
            'active' => (bool) $record->active,
        ];
    }

    protected function rules(): array
    {
        $userId = $this->editingId;

        return [
            'form.first_name' => ['required', 'string', 'max:255'],
            'form.last_name' => ['nullable', 'string', 'max:255'],
            'form.username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($userId, 'user_id')],
            'form.password' => [$this->editingId ? 'nullable' : 'required', 'string', Password::defaults()],
            'form.organisation_id' => ['required', 'integer'],
            'form.language_id' => ['required', 'integer'],
            'form.active' => ['boolean'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        if (!isset($this->adminRole)) {
            $this->adminRole = Role::where('role', Role::ADMIN)->firstOrFail();
        }

        if ($this->editingId) {
            $user = $this->findRecord($this->editingId);
            $update = [
                'first_name' => $this->form['first_name'],
                'last_name' => $this->form['last_name'],
                'username' => $this->form['username'],
                'organisation_id' => $this->form['organisation_id'],
                'active' => (bool) $this->form['active'],
            ];

            if (!empty($this->form['password'])) {
                $update['password'] = Hash::make($this->form['password']);
            }

            $user->update($update);
            session()->flash('status', ['message' => 'Admin updated.', 'type' => 'success']);
        } else {
            DB::transaction(function () {
                $user = User::create([
                    'first_name' => $this->form['first_name'],
                    'last_name' => $this->form['last_name'],
                    'username' => $this->form['username'],
                    'password' => Hash::make($this->form['password']),
                    'organisation_id' => $this->form['organisation_id'],
                    'language_id' => $this->form['language_id'],
                    'active' => (bool) $this->form['active'],
                    'first_login' => true,
                    'vision_type' => 'normal',
                ]);

                $user->roles()->attach($this->adminRole->role_id);
            });

            session()->flash('status', ['message' => 'Admin created.', 'type' => 'success']);
        }

        $this->resetFormState();
        $this->resetPage();
        $this->dispatch('modal-close', name: 'admin-form');
        $this->dispatch('crud-record-saved');
    }

    public function removeAdmin(int $userId): void
    {
        $user = $this->findRecord($userId);
        if (!isset($this->adminRole)) {
            $this->adminRole = Role::where('role', Role::ADMIN)->firstOrFail();
        }
        $user->roles()->detach($this->adminRole->role_id);
    session()->flash('status', ['message' => 'Admin removed.', 'type' => 'success']);
        $this->dispatch('crud-record-updated', id: $userId);
        $this->resetPage();
    }

    // Deletion confirmation state
    public ?int $deletingUserId = null;

    public function requestRemoveAdmin(int $userId): void
    {
        $this->deletingUserId = $userId;
        $this->dispatch('modal-open', name: 'admin-delete-confirm');
    }

    public function confirmRemoveAdmin(): void
    {
        if (! $this->deletingUserId) {
            return;
        }

        $this->removeAdmin($this->deletingUserId);
        $this->deletingUserId = null;
        $this->dispatch('modal-close', name: 'admin-delete-confirm');
    }

    public function cancelRemove(): void
    {
        $this->deletingUserId = null;
        $this->dispatch('modal-close', name: 'admin-delete-confirm');
    }
}
