<?php

namespace App\Livewire\SuperAdmin;

use App\Livewire\Crud\BaseCrudComponent;
use App\Models\InterestField;
use App\Models\Language;
use Illuminate\Database\Eloquent\Builder;

class InterestFieldManager extends BaseCrudComponent
{
    public string $newTranslationLanguage = '';

    public array $availableLanguages = [];
    public bool $showInactivated = false;

    protected function rules(): array
    {
        return [
            'form.name' => 'required|string|max:255',
            'form.description' => 'required|string|max:1000',
            'form.active' => 'boolean',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.name' => 'name',
            'form.description' => 'description',
            'form.active' => 'active',
        ];
    }

    public function save(): void
    {
        $this->validate();
        $wasActive = null;

        if ($this->editingId) {
            // Update existing interest field
            $interestField = InterestField::where('interest_field_id', $this->editingId)->firstOrFail();
            $wasActive = (bool) $interestField->active;

            $interestField->update([
                'name' => $this->form['name'],
                'description' => $this->form['description'],
                'active' => $this->form['active'] ?? true,
            ]);

            // Update translations
            foreach ($this->form['translations'] as $languageCode => $translation) {
                $interestFieldTranslation = $interestField->interestFieldTranslations()
                    ->whereHas('language', function ($query) use ($languageCode) {
                        $query->where('language_code', $languageCode);
                    })
                    ->first();

                if ($interestFieldTranslation) {
                    $interestFieldTranslation->update($translation);
                } else {
                    $interestField->interestFieldTranslations()->create([
                        'language_code' => $languageCode,
                        'name' => $translation['name'],
                        'description' => $translation['description'],
                    ]);
                }
            }
        } else {
            // Create new interest field
            $interestField = InterestField::create([
                'name' => $this->form['name'],
                'description' => $this->form['description'],
                'active' => $this->form['active'] ?? true,
            ]);

            $wasActive = null; // newly created
        }

        // If the active state changed (or was created inactive), ensure the UI reflects it immediately.
        $isActiveNow = (bool) ($interestField->active ?? ($this->form['active'] ?? true));

        // If we edited and it changed from active->inactive, show inactive list.
        if ($wasActive !== null && $wasActive !== $isActiveNow) {
            $this->showInactivated = ! $isActiveNow; // if now inactive, show inactive table
            // reset the paginator for the visible table
            if ($this->showInactivated) {
                $this->resetPage('inactivePage');
            } else {
                $this->resetPage('activePage');
            }
        }

        // If we created a new inactive record, ensure inactive list is visible so user sees it.
        if ($wasActive === null && ! $isActiveNow) {
            $this->showInactivated = true;
            $this->resetPage('inactivePage');
        }

        $this->resetFormState();
        $this->dispatch('modal-close', name: 'create-interest-field-form');
    }

    protected function view(): string
    {
        return 'livewire.superadmin.interest-field-manager';
    }

    protected function defaultFormState(): array
    {
        $languages = Language::all();

        $form = [
            'name' => '',
            'description' => '',
            'active' => true,
            'translations' => [],
        ];

        return $form;
    }

    protected function baseQuery(): Builder
    {
        return InterestField::query()->where('active', true);
    }

    protected function inactivatedQuery(): Builder
    {
        return InterestField::query()->where('active', false);
    }

    protected function findRecord(int $id)
    {
        return InterestField::where('interest_field_id', $id)->firstOrFail();
    }

    public function transformRecordToForm($record): array
    {
        $form = [
            'name' => $record->name,
            'description' => $record->description,
            'active' => $record->active ?? true,
            'translations' => [],
        ];

        foreach ($record->interestFieldTranslations as $translation) {
            $form['translations'][$translation->language->language_code] = [
                'name' => $translation->name,
                'description' => $translation->description,
            ];
        }

        return $form;
    }

    protected function applySearch(Builder $query): Builder
    {
        $term = trim((string) $this->search);

        if ($term === '') {
            return $query;
        }

        // Search in the base interest_field columns
        $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', '%'.$term.'%')
                ->orWhere('description', 'like', '%'.$term.'%');
        });

        // Also search in translations (any language)
        $query->orWhereHas('interestFieldTranslations', function (Builder $q) use ($term) {
            $q->where('name', 'like', '%'.$term.'%')
              ->orWhere('description', 'like', '%'.$term.'%');
        });

        return $query;
    }

    public function closeFormModal(): void
    {
        $this->resetFormState();
    }

    public function confirmDelete(int $id): void
    {
        $this->editingId = $id; // Store the ID of the interest field to be deleted
        $this->dispatch('modal-open', name: 'delete-interest-field-confirmation');
    }

    /**
     * Deactivate an active interest field immediately without confirmation modal.
     */
    public function deactivateInterestField(int $id): void
    {
        $interestField = InterestField::where('interest_field_id', $id)->first();

        if (! $interestField) {
            session()->flash('status', [
                'message' => __('interestfield.delete_error'),
                'type' => 'error',
            ]);

            return;
        }

        if ($interestField->active) {
            $interestField->active = false;
            $interestField->save();

            // Ensure UI shows inactive list so user sees it moved
            $this->showInactivated = true;
            $this->resetPage('inactivePage');

            session()->flash('status', [
                'message' => __('interestfield.deactivated_success'),
                'type' => 'success',
            ]);
        }
    }

    public function deleteInterestField(): void
    {
        $interestField = InterestField::where('interest_field_id', $this->editingId)->first();

        if (! $interestField) {
            session()->flash('status', [
                'message' => __('interestfield.delete_error'),
                'type' => 'error',
            ]);

            $this->resetFormState();
            $this->dispatch('modal-close', name: 'delete-interest-field-confirmation');

            return;
        }

        // If the interest field is active, mark it inactive instead of deleting
        if ($interestField->active) {
            $interestField->active = false;
            $interestField->save();

            // Ensure UI shows inactive list so user sees it moved
            $this->showInactivated = true;
            $this->resetPage('inactivePage');

            session()->flash('status', [
                'message' => __('interestfield.deactivated_success'),
                'type' => 'success',
            ]);
        } else {
            // If not active and not used in questions, delete permanently
            if (! $interestField->questions()->exists()) {
                $interestField->delete();
                session()->flash('status', [
                    'message' => __('interestfield.delete_success'),
                    'type' => 'success',
                ]);
            } else {
                session()->flash('status', [
                    'message' => __('interestfield.delete_error'),
                    'type' => 'error',
                ]);
            }
        }

        $this->resetFormState();
        $this->dispatch('modal-close', name: 'delete-interest-field-confirmation');
    }

    public function addTranslation(): void
    {
        // Ensure newTranslationLanguage is a valid string and not empty
        if (! is_string($this->newTranslationLanguage) || trim($this->newTranslationLanguage) === '') {
            session()->flash('status', [
                'message' => __('interestfield.select_valid_language'),
                'type' => 'error',
            ]);
            $this->newTranslationLanguage = ''; // Reset if invalid

            return;
        }

        $this->newTranslationLanguage = trim($this->newTranslationLanguage);

        // Fetch the language ID from the database
        $languageId = Language::where('language_code', $this->newTranslationLanguage)->value('language_id');

        if (! $languageId) {
            session()->flash('status', [
                'message' => __('interestfield.language_not_found'),
                'type' => 'error',
            ]);

            return;
        }

        // Add the new translation to the form
        $this->form['translations'][$this->newTranslationLanguage] = [
            'name' => '__',
            'description' => '__',
        ];

        // If editing an existing interest field, ensure the translation includes language_id
        if ($this->editingId) {
            $interestField = InterestField::where('interest_field_id', $this->editingId)->firstOrFail();
            $interestField->interestFieldTranslations()->create([
                'language_code' => $this->newTranslationLanguage,
                'language_id' => $languageId,
                'name' => '',
                'description' => '',
            ]);
        }

        $this->newTranslationLanguage = ''; // Reset the selected language
    }

    public function mount(): void
    {
        $this->form = [
            'name' => '',
            'description' => '',
            'translations' => [],
        ];

        // Fetch available languages from the database, excluding Dutch
        $this->availableLanguages = Language::where('language_code', '!=', 'nl')
            ->pluck('language_name', 'language_code')
            ->toArray();
    }

    /**
     * Reset both paginators when the search term updates so results reflect the
     * new filter immediately for both the active and inactive lists.
     */
    public function updatingSearch(): void
    {
        $this->resetPage('activePage');
        $this->resetPage('inactivePage');
    }

    public function updatedNewTranslationLanguage($value): void
    {
        // Ensure newTranslationLanguage is always a valid string
        $this->newTranslationLanguage = is_string($value) && array_key_exists($value, $this->availableLanguages)
            ? $value
            : '';
    }

    public function getRecordsProperty()
    {
        return $this->applySearch($this->baseQuery())->paginate(10, ['*'], 'activePage');
    }

    public function getInactiveRecordsProperty()
    {
        return $this->applySearch($this->inactivatedQuery())->paginate(10, ['*'], 'inactivePage');
    }

    public function toggleShowInactivated(): void
    {
        $this->showInactivated = ! $this->showInactivated;

        // Reset the paginator for the table that is now visible
        if ($this->showInactivated) {
            $this->resetPage('inactivePage');
        } else {
            $this->resetPage('activePage');
        }
    }

    public function removeTranslation(string $languageCode): void
    {
        if (isset($this->form['translations'][$languageCode])) {
            unset($this->form['translations'][$languageCode]);

            if ($this->editingId) {
                $interestField = InterestField::where('interest_field_id', $this->editingId)->firstOrFail();
                $interestField->interestFieldTranslations()
                    ->whereHas('language', function ($query) use ($languageCode) {
                        $query->where('language_code', $languageCode);
                    })
                    ->delete();
            }

            session()->flash('status', [
                'message' => __('interestfield.translation_removed_success'),
                'type' => 'success',
            ]);
        }
    }
}
