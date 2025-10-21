<?php

namespace App\Livewire\Mentor;

use App\Livewire\Crud\BaseCrudComponent;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ClientsManager extends BaseCrudComponent
{
    /**
     * Ensure we only register dynamic relations once.
     */
    protected static bool $relationsRegistered = false;

    /**
     * Role instance for clients.
     */
    protected Role $clientRole;

    /**
     * Currently authenticated mentor identifiers.
     */
    protected ?int $mentorId = null;

    protected ?int $mentorOrganisationId = null;

    protected ?int $mentorLanguageId = null;

    /**
     * Determines whether inactivated clients are visible in the list.
     */
    public bool $showInactivated = false;

    /**
     * Keep query string state for search & pagination of both tables.
     */
    protected $queryString = [
        'search' => ['except' => ''],
        'activePage' => ['except' => 1],
        'inactivePage' => ['except' => 1],
    ];

    /**
     * Listen for client saved event to refresh the list
     */
    protected $listeners = ['client-saved' => '$refresh'];

    public function mount(): void
    {
        parent::mount();

        if (!isset($this->clientRole)) {
            $this->clientRole = Role::where('role', Role::CLIENT)->firstOrFail();
        }
    }

    protected function initializeCrud(): void
    {
        $this->ensureMentorContext(force: true);

        $this->clientRole = Role::where('role', Role::CLIENT)->firstOrFail();

        parent::initializeCrud();
    }

    public function startCreate(): void
    {
        $this->dispatch('open-client-form');
    }

    public function startEdit(int $recordId): void
    {
        $this->dispatch('open-client-form', clientId: $recordId);
    }

    public function viewTests(int $clientId)
    {
        session()->flash('viewingClient', $clientId);
        if(Auth::user()->isMentor())
        return redirect()->route('mentor.client-tests');
        if(Auth::user()->isAdmin())
        return redirect()->route('admin.client-tests');
    }

    /**
     * Only show enabled clients in base query
     */
    protected function baseQuery(): Builder
    {
        $this->ensureMentorContext();

        return User::query()
            ->where('mentor_id', $this->mentorId)
            ->where('organisation_id', $this->mentorOrganisationId)
            ->whereHas('roles', fn(Builder $query) => $query->where('role', Role::CLIENT))
            ->where('active', true)
            ->with([
                'language',
            ])
            ->orderBy('first_name')
            ->orderBy('last_name');
    }

    protected function inactivatedClientsQuery(): Builder
    {
        $this->ensureMentorContext();

        return User::query()
            ->where('mentor_id', $this->mentorId)
            ->where('organisation_id', $this->mentorOrganisationId)
            ->whereHas('roles', fn(Builder $query) => $query->where('role', Role::CLIENT))
            ->where('active', false)
            ->with([
                'language',
            ])
            ->orderBy('first_name')
            ->orderBy('last_name');
    }

    public function getInactivatedClientsProperty()
    {
        return $this->applySearch($this->inactivatedClientsQuery())->paginate($this->perPage(), ['*'], 'inactivePage');
    }

    public function getRecordsProperty()
    {
        return $this->applySearch($this->baseQuery())->paginate($this->perPage(), ['*'], 'activePage');
    }

    protected function applySearch(Builder $query): Builder
    {
        $term = trim($this->search);
        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($term) {
            $builder
                ->where('first_name', 'like', "%{$term}%")
                ->orWhere('last_name', 'like', "%{$term}%")
                ->orWhere('username', 'like', "%{$term}%");
        });
    }

    protected function findRecord(int $id)
    {
        $record = $this->baseQuery()->whereKey($id)->first();

        if (!$record) {
            $record = $this->inactivatedClientsQuery()->whereKey($id)->first();
        }

        abort_unless($record, 404);

        return $record;
    }

    protected function view(): string
    {
        return 'livewire.mentor.clients-manager';
    }

    protected function viewData(): array
    {
        return array_merge(parent::viewData(), [
            'inactivatedClients' => $this->inactivatedClients,
        ]);
    }

    protected function pageTitle(): string
    {
        return __('Clients');
    }

    // BaseCrudComponent requirements (handled by separate modal component)
    protected function defaultFormState(): array
    {
        return [];
    }

    protected function transformRecordToForm($record): array
    {
        return [];
    }

    public function toggleShowInactivated(): void
    {
        $this->showInactivated = !$this->showInactivated;
        $this->resetPage('activePage');
        $this->resetPage('inactivePage');
    }

    protected function ensureMentorContext(bool $force = false): void
    {
        /** @var User|null $mentor */
        $mentor = Auth::user();
        abort_unless($mentor?->isMentor(), 403);

        if ($force || !$this->mentorId) {
            $this->mentorId = $mentor->user_id;
        }

        if ($force || !$this->mentorOrganisationId) {
            $this->mentorOrganisationId = $mentor->organisation_id;
        }

        if ($force || !$this->mentorLanguageId) {
            $this->mentorLanguageId = $mentor->language_id;
        }
    }

    public function assignTests(int $clientId): void
    {
        // Open the Assign Tests modal popup and pass the client ID
        $this->dispatch('open-assign-tests-modal', $clientId);
    }
}
