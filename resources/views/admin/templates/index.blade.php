@extends('layouts.dashboard')

@section('title', 'Document Templates')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Document Templates</h2>
            <p class="text-muted">Manage document templates for BAK and RKB reports</p>
        </div>
        <div class="col-md-6 text-end">
            <div class="btn-group">
                <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-plus-circle"></i> Create New Template
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.templates.create.type', 'bak') }}">BAK Template</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.templates.create.type', 'rkb') }}">RKB Template</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- BAK Templates Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-file-alt me-1"></i>
                    BAK Templates
                </div>
                <div class="card-body">
                    @if($bakTemplates->count() > 0)
                        <div class="row">
                            @foreach($bakTemplates as $template)
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100 {{ $template->is_default ? 'border-primary' : '' }}">
                                        <div class="card-img-top bg-light" style="height: 180px;">
                                            @if($template->thumbnail)
                                                <img src="{{ Storage::url($template->thumbnail) }}" alt="{{ $template->name }}"
                                                     class="img-fluid" style="width: 100%; height: 180px; object-fit: cover;">
                                            @else
                                                <div class="d-flex align-items-center justify-content-center h-100">
                                                    <i class="fas fa-file-alt fa-4x text-secondary"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $template->name }}</h5>
                                            <p class="card-text small text-muted">
                                                Created by: {{ $template->creator ? $template->creator->name : 'Unknown' }}<br>
                                                Last updated: {{ $template->updated_at->format('M d, Y H:i') }}
                                            </p>
                                            @if($template->is_default)
                                                <div class="badge bg-primary mb-2">Default Template</div>
                                            @endif
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <div class="d-flex justify-content-between">
                                                <a href="{{ route('admin.templates.editor', $template) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('admin.templates.destroy', $template) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Are you sure you want to delete this template?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i>
                            No BAK templates found. Create one to get started.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- RKB Templates Section -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-file-invoice-dollar me-1"></i>
                    RKB Templates
                </div>
                <div class="card-body">
                    @if($rkbTemplates->count() > 0)
                        <div class="row">
                            @foreach($rkbTemplates as $template)
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100 {{ $template->is_default ? 'border-primary' : '' }}">
                                        <div class="card-img-top bg-light" style="height: 180px;">
                                            @if($template->thumbnail)
                                                <img src="{{ Storage::url($template->thumbnail) }}" alt="{{ $template->name }}"
                                                     class="img-fluid" style="width: 100%; height: 180px; object-fit: cover;">
                                            @else
                                                <div class="d-flex align-items-center justify-content-center h-100">
                                                    <i class="fas fa-file-invoice-dollar fa-4x text-secondary"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $template->name }}</h5>
                                            <p class="card-text small text-muted">
                                                Created by: {{ $template->creator ? $template->creator->name : 'Unknown' }}<br>
                                                Last updated: {{ $template->updated_at->format('M d, Y H:i') }}
                                            </p>
                                            @if($template->is_default)
                                                <div class="badge bg-primary mb-2">Default Template</div>
                                            @endif
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <div class="d-flex justify-content-between">
                                                <a href="{{ route('admin.templates.editor', $template) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('admin.templates.destroy', $template) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Are you sure you want to delete this template?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i>
                            No RKB templates found. Create one to get started.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
