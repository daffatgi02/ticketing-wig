@extends('layouts.dashboard')

@section('title', 'External Support Report Form')

@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2>External Support Report Form</h2>
                <p class="text-muted">Create report details for ticket #{{ $ticket->ticket_id }}</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Ticket
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <i class="fas fa-file-alt me-1"></i>
                        Report Information
                    </div>
                    <div class="card-body">
                        <form action="{{ route('tickets.submit-external-support', $ticket) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="incident_date" class="form-label">Date of Incident</label>
                                    <input type="date" class="form-control @error('incident_date') is-invalid @enderror"
                                        id="incident_date" name="incident_date"
                                        value="{{ old('incident_date', $ticket->incident_date ? $ticket->incident_date->format('Y-m-d') : now()->format('Y-m-d')) }}"
                                        required>
                                    @error('incident_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="incident_time" class="form-label">Time of Incident</label>
                                    <input type="time" class="form-control @error('incident_time') is-invalid @enderror"
                                        id="incident_time" name="incident_time"
                                        value="{{ old('incident_time', $ticket->incident_time ? $ticket->incident_time->format('H:i') : now()->format('H:i')) }}"
                                        required>
                                    @error('incident_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="report_recipient" class="form-label">Report Recipient</label>
                                <input type="text" class="form-control @error('report_recipient') is-invalid @enderror"
                                    id="report_recipient" name="report_recipient"
                                    value="{{ old('report_recipient', $ticket->report_recipient ?? 'Bpk Agus') }}"
                                    placeholder="e.g., Bpk Agus" required>
                                @error('report_recipient')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="report_recipient_position" class="form-label">Recipient Position</label>
                                <input type="text"
                                    class="form-control @error('report_recipient_position') is-invalid @enderror"
                                    id="report_recipient_position" name="report_recipient_position"
                                    value="{{ old('report_recipient_position', $ticket->report_recipient_position ?? 'General Affair') }}"
                                    placeholder="e.g., General Affair" required>
                                @error('report_recipient_position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="issue_detail" class="form-label">Detailed Issue Description</label>
                                <textarea class="form-control @error('issue_detail') is-invalid @enderror" id="issue_detail" name="issue_detail"
                                    rows="4" required>{{ old('issue_detail', $ticket->issue_detail ?? $ticket->description) }}</textarea>
                                <div class="form-text">Provide a detailed description of the issue, including any specific
                                    symptoms, errors, or problems observed.</div>
                                @error('issue_detail')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="actions_taken" class="form-label">Actions Taken</label>
                                <textarea class="form-control @error('actions_taken') is-invalid @enderror" id="actions_taken" name="actions_taken"
                                    rows="4" required>{{ old('actions_taken', $ticket->actions_taken ?? '- membuat berita acara dari IT ke dept GA (General Affair)') }}</textarea>
                                <div class="form-text">List all troubleshooting steps or actions that have been taken so
                                    far.</div>
                                @error('actions_taken')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="external_support_reason" class="form-label">Reason for External Support</label>
                                <textarea class="form-control @error('external_support_reason') is-invalid @enderror" id="external_support_reason"
                                    name="external_support_reason" rows="3" required>{{ old('external_support_reason', $ticket->external_support_reason) }}</textarea>
                                <div class="form-text">Explain why this issue requires external support, vendor assistance,
                                    or part replacement.</div>
                                @error('external_support_reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="additional_notes" class="form-label">Additional Notes (Optional)</label>
                                <textarea class="form-control @error('additional_notes') is-invalid @enderror" id="additional_notes"
                                    name="additional_notes" rows="3">{{ old('additional_notes', $ticket->additional_notes) }}</textarea>
                                @error('additional_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="document_format" class="form-label">Document Format</label>
                                <select class="form-select @error('document_format') is-invalid @enderror"
                                    id="document_format" name="document_format" required>
                                    <option value="pdf" {{ old('document_format') == 'pdf' ? 'selected' : '' }}>PDF
                                    </option>
                                    <option value="docx" {{ old('document_format') == 'docx' ? 'selected' : '' }}>Word
                                        (DOCX)</option>
                                </select>
                                @error('document_format')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- NEW: Multiple Image Upload Section -->
                            <div class="mb-3">
                                <label class="form-label">Image Attachments for Report</label>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-1"></i>
                                            You can upload up to 10 images that will be included in the report. These images
                                            will be displayed in the attachment page.
                                        </div>
                                    </div>
                                </div>

                                <!-- Current Images (if any) -->
                                @if ($ticket->attachments->where('use_in_report', true)->count() > 0)
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <p class="fw-bold">Current Report Images:</p>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        @foreach ($ticket->attachments->where('use_in_report', true)->sortBy('report_order') as $attachment)
                                            <div class="col-md-3 mb-3">
                                                <div class="card h-100">
                                                    @if ($attachment->isImage())
                                                        <img src="{{ Storage::url($attachment->filepath) }}"
                                                            class="card-img-top" alt="{{ $attachment->filename }}"
                                                            style="height: 150px; object-fit: cover;">
                                                    @else
                                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                                            style="height: 150px;">
                                                            <i class="fas fa-file fa-3x text-secondary"></i>
                                                        </div>
                                                    @endif
                                                    <div class="card-body">
                                                        <p class="card-text small">
                                                            {{ Str::limit($attachment->filename, 20) }}</p>

                                                        <div class="input-group mb-2">
                                                            <span class="input-group-text">Order</span>
                                                            <input type="number"
                                                                name="attachment_order[{{ $attachment->id }}]"
                                                                class="form-control" min="1" max="10"
                                                                value="{{ $attachment->report_order ?? $loop->iteration }}">
                                                        </div>

                                                        <div class="d-flex justify-content-between">
                                                            <a href="{{ Storage::url($attachment->filepath) }}"
                                                                class="btn btn-sm btn-primary" target="_blank">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <button type="button"
                                                                class="btn btn-sm btn-danger remove-report-image"
                                                                data-attachment-id="{{ $attachment->id }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                        <!-- Hidden inputs for removing attachments -->
                                        <div id="removed-attachments-container"></div>
                                    </div>
                                @endif

                                <!-- New Image Upload -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="report_images" class="form-label">Add New Images</label>
                                            <input type="file"
                                                class="form-control @error('report_images.*') is-invalid @enderror"
                                                id="report_images" name="report_images[]" multiple accept="image/*">
                                            <div class="form-text">
                                                Upload up to
                                                {{ 10 - $ticket->attachments->where('use_in_report', true)->count() }} more
                                                images
                                                (JPG, PNG, max 2MB each).
                                            </div>
                                            @error('report_images.*')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Other Attachments section -->
                            <div class="mb-3">
                                <label for="attachments" class="form-label">Other Attachments (Optional)</label>
                                <input type="file" class="form-control @error('attachments.*') is-invalid @enderror"
                                    id="attachments" name="attachments[]" multiple>
                                <div class="form-text">Upload other supporting documents (PDF, DOC, DOCX, max 2MB each).
                                </div>
                                @error('attachments.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('tickets.show', $ticket) }}"
                                    class="btn btn-secondary me-md-2">Cancel</a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-file-export me-1"></i> Generate Reports
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle removing report images
        const removeButtons = document.querySelectorAll('.remove-report-image');
        const removedContainer = document.getElementById('removed-attachments-container');

        removeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const attachmentId = this.getAttribute('data-attachment-id');

                // Create hidden input to mark this attachment for removal
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'remove_attachments[]';
                hiddenInput.value = attachmentId;
                removedContainer.appendChild(hiddenInput);

                // Hide the card
                this.closest('.col-md-3').style.display = 'none';
            });
        });

        // Limit file selection to remaining slots
        const reportImagesInput = document.getElementById('report_images');
        const maxImages = {{ 10 - $ticket->attachments->where('use_in_report', true)->count() }};

        reportImagesInput.addEventListener('change', function() {
            if (this.files.length > maxImages) {
                alert(`You can only upload up to ${maxImages} more images.`);
                this.value = '';
            }
        });
    });
</script>
@endpush
@endsection
