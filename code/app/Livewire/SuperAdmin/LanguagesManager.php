<?php

namespace App\Livewire\SuperAdmin;

use App\Livewire\Crud\BaseCrudComponent;
use App\Models\Language;
use Illuminate\Database\Eloquent\Builder;

class LanguagesManager extends BaseCrudComponent
{
    protected function view(): string
    {
        return 'livewire.superadmin.languages-manager';
    }

    protected function initializeCrud(): void
    {
        parent::initializeCrud();
    }

    protected function baseQuery(): Builder
    {
        return Language::query()->orderBy('language_name');
    }

    protected function applySearch(Builder $query): Builder
    {
        $term = trim($this->search);
        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('language_name', 'like', "%{$term}%")
              ->orWhere('language_code', 'like', "%{$term}%");
        });
    }

    protected function findRecord(int $id)
    {
        return Language::where('language_id', $id)->firstOrFail();
    }

    public function toggleEnable(int $id): void
    {
        $language = $this->findRecord($id);
        $language->enabled = ! (bool) $language->enabled;
        $language->save();

        session()->flash('status', ['message' => __('languages.language_updated'), 'type' => 'success']);
        $this->dispatch('crud-record-updated', id: $id);
        $this->resetPage();
    }

    protected function defaultFormState(): array
    {
        return [
            'language_name' => '',
            'language_code' => '',
            'enabled' => true,
        ];
    }

    public function transformRecordToForm($record): array
    {
        return [
            'language_name' => $record->language_name,
            'language_code' => $record->language_code,
            'enabled' => (bool) $record->enabled,
        ];
    }

    protected function pageTitle(): string
    {
        return __('languages.manager');
    }
}
