<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use App\Models\Faq;
use App\Models\FrequentlyAskedQuestionTranslation;
use App\Models\Language;
use Livewire\WithPagination;

class FaqManager extends Component
{
    use WithPagination;

    public $search = '';
    public $editingId = null;
    public $form = [
        'question' => '',
        'answer' => '',
        'translations' => [], // language_id => ['question' => '', 'answer' => '']
    ];
    public $availableLanguages = [];
    public $newTranslationLanguage = '';
    public function addTranslation()
    {
        $langId = $this->newTranslationLanguage;
        if ($langId && !isset($this->form['translations'][$langId]) && isset($this->availableLanguages[$langId])) {
            $this->form['translations'][$langId] = [
                'question' => '',
                'answer' => '',
            ];
            // If editing an existing FAQ, persist the translation row immediately
            if ($this->editingId) {
                $languageId = Language::where('language_code', $langId)->value('language_id');
                if ($languageId) {
                    FrequentlyAskedQuestionTranslation::create([
                        'frequently_asked_question_id' => $this->editingId,
                        'language_id' => $languageId,
                        'language_code' => $langId,
                        'question' => '',
                        'answer' => '',
                    ]);
                    // store language_id so save() can skip lookup
                    $this->form['translations'][$langId]['language_id'] = $languageId;
                }
            }
        }
        $this->newTranslationLanguage = '';
    }

    protected function rules(): array
    {
        $rules = [
            'form.question' => 'required|string|max:255',
            'form.answer' => 'required|string|max:2000',
        ];

        foreach (array_keys($this->form['translations'] ?? []) as $langCode) {
            $rules["form.translations.{$langCode}.question"] = 'required|string|max:255';
            $rules["form.translations.{$langCode}.answer"] = 'required|string|max:2000';
        }

        return $rules;
    }

    public function startCreate()
    {
        $this->editingId = null;
        $this->resetFormState();
        $this->loadLanguages();
        $this->dispatch('modal-open', name: 'create-faq-form');
    }

    public function startEdit($id)
    {
        $faq = Faq::where('frequently_asked_question_id', $id)->firstOrFail();
        $this->editingId = $id;
        $this->form['question'] = $faq->question;
        $this->form['answer'] = $faq->answer;
        $this->form['translations'] = [];
        $this->loadLanguages();
        foreach ($faq->translations as $translation) {
            $this->form['translations'][$translation->language->language_code ?? $translation->language_id] = [
                'language_id' => $translation->language_id,
                'question' => $translation->question,
                'answer' => $translation->answer,
            ];
        }
        $this->dispatch('modal-open', name: 'create-faq-form');
    }

    public function save()
    {
        $this->validate();
        if ($this->editingId) {
            $faq = Faq::where('frequently_asked_question_id', $this->editingId)->firstOrFail();
            $faq->update([
                'question' => $this->form['question'],
                'answer' => $this->form['answer'],
            ]);
        } else {
            $faq = Faq::create([
                'question' => $this->form['question'],
                'answer' => $this->form['answer'],
            ]);
        }

        //Save translations 
        foreach ($this->form['translations'] as $languageCode => $data) {
            $languageId = $data['language_id'] ?? Language::where('language_code', $languageCode)->value('language_id');

            FrequentlyAskedQuestionTranslation::updateOrCreate(
                [
                    'frequently_asked_question_id' => $faq->frequently_asked_question_id,
                    'language_id' => $languageId,
                ],
                [
                    'question' => $data['question'],
                    'answer' => $data['answer'],
                ]
            );
        }
        $this->dispatch('modal-close', name: 'create-faq-form');
        $this->resetFormState();

    }
    public function loadLanguages()
    {
        $this->availableLanguages = Language::where('language_code', '!=', 'nl')
            ->pluck('language_name', 'language_code')
            ->toArray();    }

    public function confirmDelete($id)
    {
        $this->editingId = $id;
        $this->dispatch('modal-open', name: 'delete-faq-confirmation');
    }

    public function removeTranslation(string $languageCode): void
    {
        if (isset($this->form['translations'][$languageCode])) {
            unset($this->form['translations'][$languageCode]);

            if ($this->editingId) {
                $languageId = Language::where('language_code', $languageCode)->value('language_id');
                if ($languageId) {
                    FrequentlyAskedQuestionTranslation::where('frequently_asked_question_id', $this->editingId)
                        ->where('language_id', $languageId)
                        ->delete();
                }
            }

            session()->flash('status', [
                'message' => __('faq.translation_removed_success'),
                'type' => 'success',
            ]);
        }
    }

    public function updatedNewTranslationLanguage($value): void
    {
        $this->newTranslationLanguage = is_string($value) && array_key_exists($value, $this->availableLanguages)
            ? $value
            : '';
    }

    public function deleteFaq()
    {
        Faq::where('frequently_asked_question_id', $this->editingId)->firstOrFail()->delete();
        $this->resetFormState();
        $this->dispatch('modal-close', name: 'delete-faq-confirmation');
    }
    public function resetFormState()
    {
        $this->form = [
            'question' => '',
            'answer' => '',
            'translations' => [],
        ];
        $this->editingId = null;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function getRecordsProperty()
    {
        $query = Faq::query();
        if (trim($this->search) !== '') {
            $query->where('question', 'like', '%'.$this->search.'%')
                  ->orWhere('answer', 'like', '%'.$this->search.'%');
        }
        return $query->orderBy('frequently_asked_question_id', 'desc')->paginate(10);
    }

    public function render()
    {
        return view('livewire.superadmin.faq-manager', [
            'records' => $this->records,
        ]);
    }
}