<?php

namespace App\Livewire\SuperAdmin;

use App\Livewire\Crud\BaseCrudComponent;
use App\Models\InterestField;
use Illuminate\Database\Eloquent\Builder;

class InterestFieldManager extends BaseCrudComponent
{
    protected function rules(): array
    {
        return [
            'form.name' => 'required|string|max:255',
            'form.description' => 'required|string|max:1000',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.name' => 'name',
            'form.description' => 'description',
        ];
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editingId) {
            // Update existing interest field
            $interestField = InterestField::where('interest_field_id', $this->editingId)->firstOrFail();
            $interestField->update([
                'name' => $this->form['name'],
                'description' => $this->form['description'],
            ]);

            session()->flash('status', __('Interest field updated successfully.'));
        } else {
            // Create new interest field
            InterestField::create([
                'name' => $this->form['name'],
                'description' => $this->form['description'],
            ]);

            session()->flash('status', __('Interest field created successfully.'));
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
        // $this->ensureMentorContext();
        return [
            'name' => '',
            'description' => '',
        ];
    }

    protected function baseQuery(): Builder
    {
        return InterestField::query();
    }

    protected function findRecord(int $id)
    {
        return InterestField::where('interest_field_id', $id)->firstOrFail();
    }

    protected function transformRecordToForm($record): array
    {
        return [
            'name' => $record->name,
            'description' => $record->description,
        ];
    }

    protected function applySearch(Builder $query): Builder
    {
        if (empty($this->search)) {
            return $query;
        }

        return $query->where(function ($q) {
            $q->where('name', 'like', '%' . $this->search . '%')
              ->orWhere('description', 'like', '%' . $this->search . '%');
        });
    }

    public function closeFormModal(): void
    {
        $this->resetFormState();
    }

}
