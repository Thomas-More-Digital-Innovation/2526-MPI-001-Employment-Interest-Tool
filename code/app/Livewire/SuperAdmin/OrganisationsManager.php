<?php

namespace App\Livewire\SuperAdmin;

use App\Livewire\Crud\BaseCrudComponent;
use App\Models\Organisation;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class OrganisationsManager extends BaseCrudComponent
{
    protected Role $adminRole;

    // Modal state for admin management
    public bool $manageAdminsVisible = false;
    public ?int $managingOrganisationId = null;
    public array $organisationAdmins = [];
    public string $newAdminUsername = '';
    public string $newAdminFirstName = '';
    public string $newAdminLastName = '';
    public string $newAdminPassword = '';

    protected function view(): string
    {
        return 'livewire.superadmin.organisations-manager';
    }

    protected function initializeCrud(): void
    {
        parent::initializeCrud();

        if (!isset($this->adminRole)) {
            $this->adminRole = Role::where('role', Role::ADMIN)->firstOrFail();
        }
    }

    protected function defaultFormState(): array
    {
        return [
            'name' => '',
            'active' => true,
            'expire_date' => null,
        ];
    }

    protected function baseQuery(): Builder
    {
        return Organisation::query()->orderBy('name');
    }

    protected function findRecord(int $id)
    {
        return Organisation::where('organisation_id', $id)->firstOrFail();
    }

    public function transformRecordToForm($record): array
    {
        return [
            'name' => $record->name,
            'active' => (bool) $record->active,
            'expire_date' => $record->expire_date ? $record->expire_date->format('Y-m-d') : null,
        ];
    }

    protected function rules(): array
    {
        $orgId = $this->editingId;

        return [
            'form.name' => ['required', 'string', 'max:255', Rule::unique('organisation', 'name')->ignore($orgId, 'organisation_id')],
            'form.active' => ['boolean'],
            'form.expire_date' => ['nullable', 'date'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $isEditing = (bool) $this->editingId;

        if ($this->editingId) {
            $org = $this->findRecord($this->editingId);
            $org->update([
                'name' => $this->form['name'],
                'active' => (bool) $this->form['active'],
                'expire_date' => $this->form['expire_date'] ?: null,
            ]);
            session()->flash('status', ['message' => __('Organisation updated.'), 'type' => 'success']);
        } else {
            Organisation::create([
                'name' => $this->form['name'],
                'active' => (bool) $this->form['active'],
                'expire_date' => $this->form['expire_date'] ?: null,
            ]);
            session()->flash('status', ['message' => __('Organisation created.'), 'type' => 'success']);
        }

        $this->resetFormState();
        $this->resetPage();
        $this->dispatch('modal-close', name: 'organisation-form');
        $this->dispatch('crud-record-saved');
    }

    public function requestToggle(int $id): void
    {
        $this->editingId = $id;
        $this->dispatch('modal-open', name: 'organisation-toggle-confirm');
    }

    public function confirmToggle(): void
    {
        if (!$this->editingId) {
            return;
        }

        $org = $this->findRecord($this->editingId);
        $org->active = ! $org->active;
        $org->save();

        session()->flash('status', ['message' => $org->active ? __('Organisation activated.') : __('Organisation inactivated.'), 'type' => 'success']);

        $this->dispatch('crud-record-updated', id: $org->organisation_id, active: $org->active);
        $this->resetFormState();
        $this->dispatch('modal-close', name: 'organisation-toggle-confirm');
    }

    /** Admin management **/
    public function openManageAdmins(int $organisationId): void
    {
        $this->managingOrganisationId = $organisationId;
        $this->manageAdminsVisible = true;
        $this->loadOrganisationAdmins();
        $this->dispatch('modal-open', name: 'manage-organisation-admins');
    }

    protected function loadOrganisationAdmins(): void
    {
        if (! $this->managingOrganisationId) {
            $this->organisationAdmins = [];
            return;
        }

        $admins = User::query()
            ->where('organisation_id', $this->managingOrganisationId)
            ->whereHas('roles', fn(Builder $q) => $q->where('role', Role::ADMIN))
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $this->organisationAdmins = $admins->map(fn($u) => [
            'id' => $u->user_id,
            'first_name' => $u->first_name,
            'last_name' => $u->last_name,
            'username' => $u->username,
            'active' => (bool) $u->active,
        ])->all();
    }

    public function addExistingUserAsAdmin(int $userId): void
    {
        $user = User::where('user_id', $userId)->firstOrFail();

        if ($user->organisation_id !== $this->managingOrganisationId) {
            session()->flash('status', ['message' => __('User does not belong to this organisation.'), 'type' => 'error']);
            return;
        }

        $user->roles()->syncWithoutDetaching([$this->adminRole->role_id]);
        session()->flash('status', ['message' => __('Admin added.'), 'type' => 'success']);
        $this->loadOrganisationAdmins();
        $this->dispatch('crud-record-saved');
    }

    public function removeAdmin(int $userId): void
    {
        $user = User::where('user_id', $userId)->firstOrFail();

        // Do not allow removing last super admin or user's primary safety checks could be added here
        $user->roles()->detach($this->adminRole->role_id);

        session()->flash('status', ['message' => __('Admin removed.'), 'type' => 'success']);
        $this->loadOrganisationAdmins();
        $this->dispatch('crud-record-updated', id: $userId);
    }

    public function createAdminForOrganisation(): void
    {
        $this->validateAdminCreation();

        DB::transaction(function () {
            $user = User::create([
                'first_name' => trim($this->newAdminFirstName),
                'last_name' => trim($this->newAdminLastName),
                'username' => trim($this->newAdminUsername),
                'password' => Hash::make($this->newAdminPassword),
                'organisation_id' => $this->managingOrganisationId,
                'first_login' => true,
                'active' => true,
            ]);

            $user->roles()->syncWithoutDetaching([$this->adminRole->role_id]);
        });

        session()->flash('status', ['message' => __('Admin created.'), 'type' => 'success']);
        $this->newAdminUsername = '';
        $this->newAdminFirstName = '';
        $this->newAdminLastName = '';
        $this->newAdminPassword = '';

        $this->loadOrganisationAdmins();
        $this->dispatch('crud-record-saved');
    }

    protected function validateAdminCreation(): void
    {
        $this->validate([
            'newAdminFirstName' => ['required', 'string', 'max:255'],
            'newAdminLastName' => ['nullable', 'string', 'max:255'],
            'newAdminUsername' => ['required', 'string', 'max:255', Rule::unique('users', 'username')],
            'newAdminPassword' => ['required', 'string', Password::defaults()],
        ]);
    }

    public function closeManageAdmins(): void
    {
        $this->manageAdminsVisible = false;
        $this->managingOrganisationId = null;
        $this->organisationAdmins = [];
        $this->dispatch('modal-close', name: 'manage-organisation-admins');
    }
}
