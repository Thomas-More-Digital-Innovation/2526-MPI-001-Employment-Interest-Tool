<?php

namespace App\Livewire\SuperAdmin;

use App\Livewire\Crud\BaseCrudComponent;
use App\Models\Organisation;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use App\Models\Test;

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

    /**
     * Available tests that can be enabled for an organisation.
     * Populated from the DB and exposed to the view.
     * @var \Illuminate\Database\Eloquent\Collection|array
     */
    public $availableTests = [];

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

        // Load available tests once when initializing the component
        $this->loadAvailableTests();
    }

    /**
     * Load active tests from the database and cache them on the component.
     */
    protected function loadAvailableTests(): void
    {
        $this->availableTests = Test::query()->where('active', true)->orderBy('test_name')->get();
    }

    protected function defaultFormState(): array
    {
        return [
            'name' => '',
            'active' => true,
            'expire_date' => null,
            // tests keyed by test_id => bool
            'tests' => [],
        ];
    }

    protected function baseQuery(): Builder
    {
        return Organisation::query()->where('active', true)->orderBy('name');
    }

    protected function applySearch(Builder $query): Builder
    {
        $term = trim($this->search);
        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', "%{$term}%");
        });
    }

    public function startEdit(int $recordId): void
    {
        // ensure we have the list of available tests before transforming the record
        $this->loadAvailableTests();
        parent::startEdit($recordId);

        // dispatch modal open for the UI
        $this->dispatch('modal-open', name: 'organisation-form');
    }

    public function startCreate(): void
    {
        // ensure tests are loaded and form contains test keys
        $this->loadAvailableTests();
        parent::startCreate();

        // initialize test flags to false for all available tests
        $this->form['tests'] = [];
        foreach ($this->availableTests as $t) {
            $this->form['tests'][$t->test_id] = false;
        }
    }

    /**
     * Whether inactive organisations are visible in the list.
     */
    public bool $showInactivated = false;

    /**
     * Toggle the visibility of inactive organisations.
     */
    public function toggleShowInactivated(): void
    {
        $this->showInactivated = ! $this->showInactivated;
        $this->resetPage();
    }

    /**
     * Query for inactive organisations.
     */
    protected function inactivatedQuery(): Builder
    {
        return Organisation::query()->where('active', false)->orderBy('name');
    }

    /**
     * Paginated inactive records with search applied.
     */
    public function getInactivatedRecordsProperty()
    {
        return $this->applySearch($this->inactivatedQuery())->paginate($this->perPage());
    }

    protected function findRecord(int $id)
    {
        return Organisation::where('organisation_id', $id)->firstOrFail();
    }

    public function transformRecordToForm($record): array
    {
        // Ensure available tests are loaded - if not, load them here as a fallback.
        if (empty($this->availableTests)) {
            $this->loadAvailableTests();
        }

        // Build tests mapping keyed by test_id => bool (enabled for this organisation)
    // Avoid ambiguous column name by prefixing with the table name (as used elsewhere in the codebase)
    $enabledTestIds = $record->tests()->pluck('test.test_id')->all();
        $testsMap = [];
        foreach ($this->availableTests as $t) {
            $testsMap[$t->test_id] = in_array($t->test_id, $enabledTestIds, true);
        }

        return [
            'name' => $record->name,
            'active' => (bool) $record->active,
            'expire_date' => $record->expire_date ? $record->expire_date->format('Y-m-d') : null,
            'tests' => $testsMap,
        ];
    }

    protected function rules(): array
    {
        $orgId = $this->editingId;

        return [
            'form.name' => ['required', 'string', 'max:255', Rule::unique('organisation', 'name')->ignore($orgId, 'organisation_id')],
            'form.active' => ['boolean'],
            'form.expire_date' => ['nullable', 'date'],
            'form.tests' => ['nullable', 'array'],
            'form.tests.*' => ['boolean'],
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
            $org = Organisation::create([
                'name' => $this->form['name'],
                'active' => (bool) $this->form['active'],
                'expire_date' => $this->form['expire_date'] ?: null,
            ]);
            session()->flash('status', ['message' => __('Organisation created.'), 'type' => 'success']);
        }

        // Sync organisation tests. Determine the organisation model instance.
        // $org should already be set above for either branch; if not, fetch it safely
        if (! isset($org)) {
            $org = $this->editingId ? $this->findRecord($this->editingId) : Organisation::where('name', $this->form['name'])->orderBy('organisation_id', 'desc')->first();
        }

        if ($org) {
            $tests = $this->form['tests'] ?? [];
            // convert to array of test ids where value truthy
            $selected = array_keys(array_filter($tests));

            // Filter selected IDs against actual tests (only active tests) to avoid syncing arbitrary ids
            $valid = Test::query()->whereIn('test_id', $selected)->where('active', true)->pluck('test_id')->toArray();

            $org->tests()->sync($valid);
        }

        $this->resetFormState();
        $this->resetPage();
        $this->dispatch('modal-close', name: 'organisation-form');
        $this->dispatch('crud-record-saved');
    }

    public function requestToggle(int $id): void
    {
        $this->editingId = $id;
        $this->dispatch('modal-open', name: 'organisation-deactivate-confirm');
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
        $this->dispatch('modal-close', name: 'organisation-deactivate-confirm');
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
