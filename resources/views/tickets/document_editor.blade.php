@extends('layouts.dashboard')

@section('title', ucfirst($type) . ' Document Editor')

@push('styles')
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@ckeditor/ckeditor5-build-decoupled-document@40.1.0/build/ckeditor.css">
    <style>
        .document-editor {
            display: flex;
            flex-direction: column;
            border: 1px solid #c4c4c4;
            border-radius: 3px;
            height: calc(100vh - 250px);
        }

        .document-editor__toolbar {
            padding: 0 10px;
            border-bottom: 1px solid #c4c4c4;
            background-color: #f5f5f5;
        }

        .document-editor__editable-container {
            padding: 20px;
            overflow-y: auto;
            flex-grow: 1;
            background-color: #fff;
        }

        .document-editor__editable {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 20mm;
            border: 1px solid #ddd;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Override CKEditor styles */
        .ck-content h1,
        .ck-content h2,
        .ck-content h3,
        .ck-content h4,
        .ck-content h5,
        .ck-content h6 {
            margin-top: 0.5em;
            margin-bottom: 0.5em;
        }

        .ck-content p {
            margin-top: 0.5em;
            margin-bottom: 0.5em;
        }

        .ck-content table {
            width: 100%;
            border-collapse: collapse;
        }

        .ck-content table td,
        .ck-content table th {
            padding: 5px;
            border: 1px solid #ddd;
        }

        .save-indicator {
            display: none;
            position: fixed;
            top: 80px;
            right: 20px;
            padding: 10px 15px;
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            border-radius: 4px;
            z-index: 1000;
        }

        #toolbar-container .ck-toolbar {
            border-radius: 0;
            border: none;
        }

        /* Available Images Section */
        .available-images {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
            background-color: #f9f9f9;
            margin-top: 20px;
        }

        .image-thumbnails {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            max-height: 400px;
            overflow-y: auto;
        }

        .image-thumbnail {
            width: 100px;
            height: 100px;
            border: 1px solid #ddd;
            border-radius: 3px;
            overflow: hidden;
            cursor: pointer;
        }

        .image-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .image-thumbnail.active {
            border: 2px solid #0d6efd;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2>{{ ucfirst($type) }} Document Editor - Ticket #{{ $ticket->ticket_id }}</h2>
                <p class="text-muted">Edit the document and publish when ready</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="btn-group">
                    <button type="button" id="save-btn" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save
                    </button>
                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="visually-hidden">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" id="publish-pdf-btn">Publish as PDF</a></li>
                        <li><a class="dropdown-item" href="#" id="publish-docx-btn">Publish as Word</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="#" id="preview-btn">Preview</a></li>
                    </ul>
                </div>
                <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-secondary ms-2">
                    <i class="fas fa-times me-1"></i> Cancel
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-9">
                <!-- Document Editor -->
                <div class="document-editor">
                    <div class="document-editor__toolbar" id="toolbar-container"></div>
                    <div class="document-editor__editable-container">
                        <div class="document-editor__editable" id="editor">
                            {!! $content !!}
                        </div>
                    </div>
                </div>

                <div class="save-indicator" id="save-indicator">
                    <i class="fas fa-check-circle me-1"></i> Saved
                </div>
            </div>

            <div class="col-md-3">
                <!-- Report Images Panel -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-images me-1"></i>
                        Available Report Images
                    </div>
                    <div class="card-body">
                        @if ($reportImages->count() > 0)
                            <p class="small text-muted mb-2">Click on an image to insert it into the document:</p>
                            <div class="image-thumbnails">
                                @foreach ($reportImages as $image)
                                    @if ($image->isImage())
                                        <div class="image-thumbnail" data-url="{{ Storage::url($image->filepath) }}"
                                            title="{{ $image->filename }}">
                                            <img src="{{ Storage::url($image->filepath) }}" alt="{{ $image->filename }}">
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-1"></i>
                                No report images available. Add images in the external support form.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Document Variables Panel -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-code me-1"></i>
                        Document Variables
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-2">Click to insert document variables:</p>
                        <div class="list-group">
                            <button type="button" class="list-group-item list-group-item-action variable-btn"
                                data-variable="{ticket_id}">
                                Ticket ID
                            </button>
                            <button type="button" class="list-group-item list-group-item-action variable-btn"
                                data-variable="{issue_detail}">
                                Issue Detail
                            </button>
                            <button type="button" class="list-group-item list-group-item-action variable-btn"
                                data-variable="{actions_taken}">
                                Actions Taken
                            </button>
                            <button type="button" class="list-group-item list-group-item-action variable-btn"
                                data-variable="{report_recipient}">
                                Report Recipient
                            </button>
                            <button type="button" class="list-group-item list-group-item-action variable-btn"
                                data-variable="{report_recipient_position}">
                                Recipient Position
                            </button>
                            <button type="button" class="list-group-item list-group-item-action variable-btn"
                                data-variable="{today_date}">
                                Today's Date
                            </button>
                            <button type="button" class="list-group-item list-group-item-action variable-btn"
                                data-variable="{incident_date}">
                                Incident Date
                            </button>
                            <button type="button" class="list-group-item list-group-item-action variable-btn"
                                data-variable="{incident_time}">
                                Incident Time
                            </button>
                            <button type="button" class="list-group-item list-group-item-action variable-btn"
                                data-variable="{created_by}">
                                Created By
                            </button>
                            <button type="button" class="list-group-item list-group-item-action variable-btn"
                                data-variable="{department}">
                                Department
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Document Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe id="preview-frame" style="width: 100%; height: 70vh; border: none;"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/@ckeditor/ckeditor5-build-decoupled-document@40.1.0/build/ckeditor.js">
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let editor;

                // Initialize CKEditor
                DecoupledEditor
                    .create(document.querySelector('#editor'), {
                        toolbar: [
                            'heading', '|',
                            'fontfamily', 'fontsize', 'fontColor', 'fontBackgroundColor', '|',
                            'bold', 'italic', 'underline', 'strikethrough', '|',
                            'alignment', '|',
                            'numberedList', 'bulletedList', '|',
                            'indent', 'outdent', '|',
                            'link', 'blockQuote', 'insertTable', 'mediaEmbed', '|',
                            'undo', 'redo'
                        ],
                        image: {
                            toolbar: [
                                'imageStyle:inline',
                                'imageStyle:block',
                                'imageStyle:side',
                                '|',
                                'toggleImageCaption',
                                'imageTextAlternative'
                            ]
                        },
                        table: {
                            contentToolbar: [
                                'tableColumn',
                                'tableRow',
                                'mergeTableCells',
                                'tableCellProperties',
                                'tableProperties'
                            ]
                        }
                    })
                    .then(newEditor => {
                        editor = newEditor;

                        // Sets the toolbar to the container
                        document.querySelector('#toolbar-container').appendChild(editor.ui.view.toolbar.element);

                        // Auto-save every 30 seconds
                        setInterval(function() {
                            saveDocument();
                        }, 30000);
                    })
                    .catch(error => {
                        console.error(error);
                    });

                // Save Button Click
                document.getElementById('save-btn').addEventListener('click', function() {
                    saveDocument();
                });

                // Preview Button Click
                document.getElementById('preview-btn').addEventListener('click', function() {
                    const content = editor.getData();

                    // Create form data for preview
                    const formData = new FormData();
                    formData.append('content', content);
                    formData.append('_token', '{{ csrf_token() }}');

                    // Send to preview endpoint
                    fetch('{{ route('tickets.document.preview') }}', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.text())
                        .then(html => {
                            const previewFrame = document.getElementById('preview-frame');
                            const modal = new bootstrap.Modal(document.getElementById('previewModal'));

                            // Write HTML to iframe
                            previewFrame.contentWindow.document.open();
                            previewFrame.contentWindow.document.write(html);
                            previewFrame.contentWindow.document.close();

                            // Show modal
                            modal.show();
                        })
                        .catch(error => {
                            console.error('Preview error:', error);
                            alert('Failed to generate preview.');
                        });
                });

                // Publish as PDF Button Click
                document.getElementById('publish-pdf-btn').addEventListener('click', function() {
                    publishDocument('pdf');
                });

                // Publish as Word Button Click
                document.getElementById('publish-docx-btn').addEventListener('click', function() {
                    publishDocument('docx');
                });

                // Image Thumbnail Click
                document.querySelectorAll('.image-thumbnail').forEach(thumbnail => {
                    thumbnail.addEventListener('click', function() {
                        const imageUrl = this.getAttribute('data-url');
                        const imageAlt = this.querySelector('img').getAttribute('alt');

                        // Insert image at current position
                        editor.model.change(writer => {
                            const imageElement = writer.createElement('image', {
                                src: imageUrl,
                                alt: imageAlt
                            });
                            editor.model.insertContent(imageElement, editor.model.document
                                .selection.getFirstPosition());
                        });
                    });
                });

                // Variable Button Click
                document.querySelectorAll('.variable-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const variable = this.getAttribute('data-variable');

                        // Insert variable at current position
                        editor.model.change(writer => {
                            const textNode = writer.createText(variable);
                            editor.model.insertContent(textNode, editor.model.document.selection
                                .getFirstPosition());
                        });
                    });
                });

                // Save Document Function
                function saveDocument() {
                    const content = editor.getData();

                    // Create form data
                    const formData = new FormData();
                    formData.append('type', '{{ $type }}');
                    formData.append('content', content);
                    formData.append('_token', '{{ csrf_token() }}');

                    // Send to save endpoint
                    fetch('{{ route('tickets.document.save', $ticket) }}', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Show saved indicator
                                const indicator = document.getElementById('save-indicator');
                                indicator.style.display = 'block';
                                setTimeout(() => {
                                    indicator.style.display = 'none';
                                }, 2000);
                            } else {
                                console.error('Save error:', data.message);
                                alert('Failed to save document.');
                            }
                        })
                        .catch(error => {
                            console.error('Save error:', error);
                            alert('Failed to save document.');
                        });
                }

                // Publish Document Function
                function publishDocument(format) {
                    if (!confirm(`Are you sure you want to publish this document as ${format.toUpperCase()}?`)) {
                        return;
                    }

                    const content = editor.getData();

                    // Create form data
                    const formData = new FormData();
                    formData.append('type', '{{ $type }}');
                    formData.append('content', content);
                    formData.append('format', format);
                    formData.append('_token', '{{ csrf_token() }}');

                    // Send to publish endpoint
                    fetch('{{ route('tickets.document.publish', $ticket) }}', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Document published successfully.');
                                // Redirect back to ticket
                                window.location.href = data.redirect;
                            } else {
                                console.error('Publish error:', data.message);
                                alert('Failed to publish document: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Publish error:', error);
                            alert('Failed to publish document.');
                        });
                }
            });
        </script>
    @endpush
@endsection
