<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocTemplateController extends Controller
{
    public function index()
    {
        $bakTemplates = DocTemplate::where('type', 'bak')->get();
        $rkbTemplates = DocTemplate::where('type', 'rkb')->get();

        return view('admin.templates.index', compact('bakTemplates', 'rkbTemplates'));
    }

    public function create()
    {
        return view('admin.templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:bak,rkb',
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|max:1024',
            'is_default' => 'boolean'
        ]);

        // If this is set as default, remove default status from other templates of same type
        if ($request->is_default) {
            DocTemplate::where('type', $request->type)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        // Handle thumbnail
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('templates/thumbnails', 'public');
        }

        DocTemplate::create([
            'name' => $request->name,
            'type' => $request->type,
            'content' => $request->content,
            'thumbnail' => $thumbnailPath,
            'is_default' => $request->is_default ?? false,
            'created_by' => Auth::id()
        ]);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template created successfully.');
    }

    public function edit(DocTemplate $template)
    {
        return view('admin.templates.edit', compact('template'));
    }

    public function update(Request $request, DocTemplate $template)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|max:1024',
            'is_default' => 'boolean'
        ]);

        // If this is set as default, remove default status from other templates of same type
        if ($request->is_default) {
            DocTemplate::where('type', $template->type)
                ->where('id', '!=', $template->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        // Handle thumbnail
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($template->thumbnail) {
                Storage::disk('public')->delete($template->thumbnail);
            }

            $thumbnailPath = $request->file('thumbnail')->store('templates/thumbnails', 'public');
            $template->thumbnail = $thumbnailPath;
        }

        $template->name = $request->name;
        $template->content = $request->content;
        $template->is_default = $request->is_default ?? false;
        $template->save();

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template updated successfully.');
    }

    public function destroy(DocTemplate $template)
    {
        // Delete thumbnail
        if ($template->thumbnail) {
            Storage::disk('public')->delete($template->thumbnail);
        }

        $template->delete();

        return redirect()->route('admin.templates.index')
            ->with('success', 'Template deleted successfully.');
    }

    public function editor(DocTemplate $template = null, $type = null)
    {
        // If template is not provided, load default template of given type
        if (!$template && $type) {
            $template = DocTemplate::where('type', $type)
                ->where('is_default', true)
                ->first();
        }

        return view('admin.templates.editor', compact('template', 'type'));
    }

    public function preview(Request $request)
    {
        $content = $request->content;
        return view('admin.templates.preview', compact('content'));
    }
}
