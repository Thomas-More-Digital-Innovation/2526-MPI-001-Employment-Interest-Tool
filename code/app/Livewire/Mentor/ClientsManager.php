<?php

namespace App\Livewire\Mentor;

use App\Livewire\Crud\BaseCrudComponent;
use App\Models\Language;
use App\Models\Option;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ClientsManager extends BaseCrudComponent
{
    /**
     * Supported vision type options (value => translation key).
     */
    protected const VISION_TYPES = [
        'normal' => 'user.vision_type_normal',
        'deuteranopia' => 'user.vision_type_deuteranopia',
        'protanopia' => 'user.vision_type_protanopia',
        'tritanopia' => 'user.vision_type_tritanopia',
    ];

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
     * Cached default language id for the form.
     */
    protected ?int $defaultLanguageId = null;

    /**
     * Cached disability option ids for detach logic.
     */
    protected array $disabilityUniverse = [];

    /**
     * Languages available for selection.
     *
     * @var list<array{id:int,label:string,code:string}>
     */
    public array $languages = [];

    /**
     * Disability options available for selection.
     *
     * @var list<array{id:int,label:string}>
     */
    public array $disabilityOptions = [];

    /**
     * Form modal state.
     */
    public bool $formModalVisible = false;

    public string $formModalMode = 'create';

    /**
     * Toggle confirmation modal state.
     */
    public bool $toggleModalVisible = false;

    public ?int $pendingToggleId = null;

    public string $toggleModalName = '';

    public bool $toggleModalWillActivate = false;

    /**
     * Determines whether inactivated clients are visible in the list.
     */
    public bool $showInactivated = false;

    public function boot(): void
    {
        $this->registerOptionRelations();
    }

    public function mount(): void
    {
        $this->registerOptionRelations();
        parent::mount();

        if (!isset($this->clientRole)) {
            $this->clientRole = Role::where('role', Role::CLIENT)->firstOrFail();
        }
    }

    public function hydrate(): void
    {
        $this->registerOptionRelations();
    }

    protected function initializeCrud(): void
    {
        $this->ensureMentorContext(force: true);
        $this->registerOptionRelations();

        $this->clientRole = Role::where('role', Role::CLIENT)->firstOrFail();

        parent::initializeCrud();
    }

    protected function defaultFormState(): array
    {
        $this->ensureMentorContext();

        return [
            'first_name' => '',
            'last_name' => '',
            'username' => '',
            'password' => '',
            'language_id' => $this->defaultLanguageId,
            'disability_ids' => [],
            'active' => true,
            'is_sound_on' => false,
            'vision_type' => $this->defaultVision(),
        ];
    }

    public function resetFormState(): void
    {
        parent::resetFormState();

        $this->formModalVisible = false;
        $this->formModalMode = 'create';
    }

    public function startCreate(): void
    {
        $this->ensureMentorContext();

        $this->resetFormState();
        $this->formModalMode = 'create';
        $this->formModalVisible = true;

        $this->dispatch('modal-open', name: 'mentor-client-form');

        $this->dispatch('crud-form-opened', mode: 'create');
    }

    public function startEdit(int $recordId): void
    {
        $this->ensureMentorContext();

        $record = $this->findRecord($recordId);
        $this->editingId = $recordId;
        $this->form = $this->transformRecordToForm($record);
        $this->formModalMode = 'edit';
        $this->formModalVisible = true;

        $this->resetErrorBag();
        $this->resetValidation();

        $this->dispatch('modal-open', name: 'mentor-client-form');
        $this->dispatch('crud-form-opened', mode: 'edit');
    }

    public function cancelForm(): void
    {
        $this->closeFormModal();
        $this->dispatch('crud-form-cancelled');
    }

    public function closeFormModal(): void
    {
        $this->resetFormState();
        $this->dispatch('modal-close', name: 'mentor-client-form');
    }

    /**
     * Only show enabled clients in base query
     */
    protected function baseQuery(): Builder
    {
        $this->registerOptionRelations();
        $this->ensureMentorContext();

        return User::query()
            ->where('mentor_id', $this->mentorId)
            ->where('organisation_id', $this->mentorOrganisationId)
            ->whereHas('roles', fn (Builder $query) => $query->where('role', Role::CLIENT))
            ->where('active', true)
            ->with([
                'language',
                'options' => fn ($query) => $query->where('type', Option::TYPE_DISABILITY),
            ])
            ->orderBy('first_name')
            ->orderBy('last_name');
    }

    protected function inactivatedClientsQuery(): Builder {
        $this->registerOptionRelations();
        $this->ensureMentorContext();

        return User::query()
            ->where('mentor_id', $this->mentorId)
            ->where('organisation_id', $this->mentorOrganisationId)
            ->whereHas('roles', fn (Builder $query) => $query->where('role', Role::CLIENT))
            ->where('active', false)
            ->with([
                'language',
                'options' => fn ($query) => $query->where('type', Option::TYPE_DISABILITY),
            ])
            ->orderBy('first_name')
            ->orderBy('last_name');
    }

    public function getInactivatedClientsProperty()
    {
        return $this->applySearch($this->inactivatedClientsQuery())->paginate($this->perPage());
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

    

    protected function transformRecordToForm($record): array
    {
        return [
            'first_name' => $record->first_name,
            'last_name' => $record->last_name ?? '',
            'username' => $record->username,
            'password' => '',
            'language_id' => $record->language_id,
            'disability_ids' => $record->options->pluck('option_id')->map(fn ($id) => (int) $id)->all(),
            'active' => (bool) $record->active,
            'is_sound_on' => (bool) $record->is_sound_on,
            'vision_type' => $this->normalizeVision($record->vision_type),
        ];
    }

    protected function view(): string
    {
        return 'livewire.mentor.clients-manager';
    }

    protected function viewData(): array
    {
        return array_merge(parent::viewData(), [
            'languages' => $this->languages,
            'disabilityOptions' => $this->disabilityOptions,
            'inactivatedClients' => $this->inactivatedClients,
            'visionTypes' => $this->visionTypeOptions(),
        ]);
    }

    protected function pageTitle(): string
    {
        return __('Clients');
    }

    protected function rules(): array
    {
        $this->ensureMentorContext();

        $userId = $this->editingId;

        return [
            'form.first_name' => ['required', 'string', 'max:255'],
            'form.last_name' => ['nullable', 'string', 'max:255'],
            'form.username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($userId, 'user_id'),
            ],
            'form.password' => array_filter([
                $this->editingId ? 'nullable' : 'required',
                'string',
                Password::defaults(),
            ]),
            'form.language_id' => ['required', 'integer', 'exists:language,language_id'],
            'form.disability_ids' => ['array'],
            'form.disability_ids.*' => ['integer', Rule::in($this->disabilityUniverse)],
            'form.active' => ['boolean'],
            'form.is_sound_on' => ['boolean'],
            'form.vision_type' => ['required', 'string', Rule::in(array_keys(self::VISION_TYPES))],
        ];
    }

    public function save(): void
    {
        $this->ensureMentorContext();

        $this->validate();

        $attributes = [
            'first_name' => trim($this->form['first_name']),
            'last_name' => $this->form['last_name'] !== '' ? trim($this->form['last_name']) : '',
            'username' => trim($this->form['username']),
            'language_id' => (int) $this->form['language_id'],
            'active' => (bool) $this->form['active'],
            'is_sound_on' => (bool) $this->form['is_sound_on'],
            'vision_type' => $this->form['vision_type'],
        ];

        $disabilityIds = collect($this->form['disability_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => in_array($id, $this->disabilityUniverse, true))
            ->unique()
            ->values()
            ->all();

        $isEditing = (bool) $this->editingId;

        $client = null;

        DB::transaction(function () use (&$client, $attributes, $disabilityIds, $isEditing) {
            if (!isset($this->clientRole)) {
                $this->clientRole = Role::where('role', Role::CLIENT)->firstOrFail();
            }

            if ($this->editingId) {
                $client = $this->findRecord($this->editingId);
                $client->fill($attributes);

                if (!empty($this->form['password'])) {
                    $client->password = Hash::make($this->form['password']);
                }

                $client->save();
            } else {
                $client = new User($attributes);
                $client->mentor_id = $this->mentorId;
                $client->organisation_id = $this->mentorOrganisationId;
                $client->password = Hash::make($this->form['password']);
                $client->first_login = true;

                $client->save();
                $client->roles()->syncWithoutDetaching([$this->clientRole->role_id]);
            }

            $client->options()->sync($disabilityIds);
        });

        $this->resetFormState();
        $this->resetPage();
        session()->flash('status', $isEditing ? __('Client updated successfully.') : __('Client created successfully.'));
        $this->dispatch('crud-record-saved');
        $this->dispatch('modal-close', name: 'mentor-client-form');
    }

    public function requestToggle(int $recordId): void
    {
        $this->ensureMentorContext();

        $client = $this->findRecord($recordId);

        $this->pendingToggleId = $recordId;
        $name = trim($client->first_name . ' ' . $client->last_name);
        $this->toggleModalName = $name !== '' ? $name : $client->username;
        $this->toggleModalWillActivate = !$client->active;
        $this->toggleModalVisible = true;

        $this->dispatch('modal-open', name: 'mentor-client-toggle');
    }

    public function confirmToggle(): void
    {
        if (!$this->pendingToggleId) {
            return;
        }

        $this->ensureMentorContext();

        $client = $this->findRecord($this->pendingToggleId);
        $client->active = $this->toggleModalWillActivate;
        $client->save();

        $this->dispatch('crud-record-updated', id: $client->user_id, active: $client->active);

        session()->flash('status', $client->active ? __('Client enabled successfully.') : __('Client inactivated successfully.'));

        $this->closeToggleModal();
    }

    public function closeToggleModal(): void
    {
        $this->toggleModalVisible = false;
        $this->pendingToggleId = null;
        $this->toggleModalName = '';
        $this->toggleModalWillActivate = false;
        $this->dispatch('modal-close', name: 'mentor-client-toggle');
    }

    public function toggleShowInactivated(): void
    {
        $this->showInactivated = !$this->showInactivated;
        $this->resetPage();
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

        if ($force || empty($this->languages)) {
            $languageCollection = Language::orderBy('language_name')->get();
            $this->languages = $languageCollection
                ->map(fn (Language $language) => [
                    'id' => $language->language_id,
                    'label' => $language->language_name,
                    'code' => $language->language_code,
                ])->all();

            $this->defaultLanguageId = $languageCollection
                ->firstWhere('language_code', 'nl')?->language_id
                ?? $this->mentorLanguageId;
        } elseif (!$this->defaultLanguageId) {
            $this->defaultLanguageId = $this->mentorLanguageId;
        }

        if ($force || empty($this->disabilityOptions) || empty($this->disabilityUniverse)) {
            $disabilityCollection = Option::query()
                ->where('type', Option::TYPE_DISABILITY)
                ->orderBy('option_name')
                ->get();

            $this->disabilityOptions = $disabilityCollection
                ->map(fn (Option $option) => [
                    'id' => $option->option_id,
                    'label' => $option->option_name,
                ])->all();

            $this->disabilityUniverse = $disabilityCollection
                ->pluck('option_id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }
    }

    protected function defaultVision(): string
    {
        return array_key_first(self::VISION_TYPES) ?? 'normal';
    }

    protected function normalizeVision(?string $vision): string
    {
        return array_key_exists($vision, self::VISION_TYPES) ? $vision : $this->defaultVision();
    }

    protected function visionTypeOptions(): array
    {
        $options = [];
        foreach (self::VISION_TYPES as $value => $translationKey) {
            $options[$value] = __($translationKey);
        }

        return $options;
    }

    protected function registerOptionRelations(): void
    {
        if (self::$relationsRegistered) {
            return;
        }

        User::resolveRelationUsing('options', function (User $user) {
            return $user->belongsToMany(Option::class, 'user_option', 'user_id', 'option_id')->withTimestamps();
        });

        User::resolveRelationUsing('disabilities', function (User $user) {
            return $user->options()->where('type', Option::TYPE_DISABILITY);
        });

        self::$relationsRegistered = true;
    }
}
