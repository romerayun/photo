<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        $faqs = Faq::orderBy('sort_order')->orderBy('id')->get();
        return view('admin.faqs.index', compact('faqs'));
    }

    public function create(): View
    {
        return view('admin.faqs.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'question_ru' => ['required', 'string', 'max:255'],
            'question_en' => ['nullable', 'string', 'max:255'],
            'answer_ru'   => ['nullable', 'string'],
            'answer_en'   => ['nullable', 'string'],
            'sort_order'  => ['nullable', 'integer'],
            'is_draft'    => ['nullable', 'boolean'],
        ]);

        $validated['is_draft'] = $request->boolean('is_draft');
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        $faq = Faq::create($validated);

        return redirect()->route('admin.faqs.index')
            ->with('success', "Вопрос «{$faq->question_ru}» успешно добавлен.");
    }

    public function edit(Faq $faq): View
    {
        return view('admin.faqs.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq): RedirectResponse
    {
        $validated = $request->validate([
            'question_ru' => ['required', 'string', 'max:255'],
            'question_en' => ['nullable', 'string', 'max:255'],
            'answer_ru'   => ['nullable', 'string'],
            'answer_en'   => ['nullable', 'string'],
            'sort_order'  => ['nullable', 'integer'],
            'is_draft'    => ['nullable', 'boolean'],
        ]);

        $validated['is_draft'] = $request->boolean('is_draft');
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        $faq->update($validated);

        return redirect()->route('admin.faqs.index')
            ->with('success', "Вопрос «{$faq->question_ru}» успешно обновлён.");
    }

    public function toggle(Faq $faq): RedirectResponse
    {
        $faq->update(['is_draft' => !$faq->is_draft]);
        $status = $faq->is_draft ? 'переведён в черновики' : 'опубликован';

        return redirect()->back()
            ->with('success', "Вопрос «{$faq->question_ru}» {$status}.");
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $title = $faq->question_ru;
        $faq->delete();

        return redirect()->route('admin.faqs.index')
            ->with('success', "Вопрос «{$title}» успешно удалён.");
    }
}
