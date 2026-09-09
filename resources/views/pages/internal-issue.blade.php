<x-app-layout>
    @push('styles')
        <style>
            .internal-issue-shell {
                width: 100%;
                max-width: 1240px;
                margin: 0 auto;
                padding: 14px 12px 28px;
            }

            .internal-issue-header {
                display: flex;
                align-items: center;
                gap: 14px;
                padding: 8px 8px 18px;
                color: #1f2a37;
            }

            .back-icon-button {
                width: 28px;
                height: 28px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border: none;
                background: transparent;
                color: #2d3748;
                font-size: 28px;
                line-height: 1;
                cursor: pointer;
                font-weight: 400;
            }

            .internal-issue-title {
                margin: 0;
                font-size: 21px;
                line-height: 1.2;
                font-weight: 700;
                color: #1f2937;
            }

            .internal-issue-subtitle {
                margin: 3px 0 0;
                color: #4b5563;
                font-size: 14px;
                line-height: 1.5;
            }

            .internal-issue-panel {
                background: rgba(255, 255, 255, 0.55);
                border: 1px solid #dfe7f2;
                border-radius: 10px;
                box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
                overflow: hidden;
            }

            .internal-issue-panel-header {
                padding: 16px 18px 14px;
                border-bottom: 1px solid #dfe7f2;
                background: #f8fbff;
            }

            .internal-issue-panel-header h2 {
                margin: 0;
                font-size: 15px;
                font-weight: 700;
                color: #0f172a;
            }

            .internal-issue-panel-body {
                padding: 18px 18px 12px;
            }

            .internal-form-grid {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 18px 18px;
            }

            .internal-form-grid.compact {
                grid-template-columns: minmax(0, 1.2fr) minmax(0, 1.2fr);
            }

            .description-attachment-grid {
                display: grid;
                grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
                gap: 18px;
                align-items: start;
                margin-top: 18px;
            }

            .internal-field {
                display: flex;
                flex-direction: column;
                gap: 7px;
            }

            .internal-field label {
                display: block;
                font-size: 13px;
                font-weight: 700;
                color: #1f2937;
                letter-spacing: -0.01em;
            }

            .required-star {
                color: #d72944;
                margin-left: 2px;
            }

            .internal-input,
            .internal-select,
            .internal-textarea {
                width: 100%;
                border: 1px solid #cfe0ef;
                border-radius: 8px;
                background: #fff;
                color: #1f2937;
                padding: 11px 12px;
                font-size: 14px;
                line-height: 1.4;
                outline: none;
                transition: border-color 0.2s ease, box-shadow 0.2s ease;
            }

            .internal-input::placeholder,
            .internal-textarea::placeholder {
                color: #8aa0b3;
            }

            .internal-input:focus,
            .internal-select:focus,
            .internal-textarea:focus {
                border-color: #84afd6;
                box-shadow: 0 0 0 3px rgba(56, 130, 197, 0.08);
            }

            .internal-select {
                appearance: none;
                -webkit-appearance: none;
                -moz-appearance: none;
                background-image: linear-gradient(45deg, transparent 50%, #64748b 50%), linear-gradient(135deg, #64748b 50%, transparent 50%);
                background-position: calc(100% - 18px) calc(50% - 2px), calc(100% - 12px) calc(50% - 2px);
                background-size: 6px 6px, 6px 6px;
                background-repeat: no-repeat;
                padding-right: 34px;
            }

            .internal-textarea-wrap {
                position: relative;
            }

            .internal-textarea {
                min-height: 118px;
                resize: vertical;
                padding-right: 58px;
            }

            .char-counter {
                position: absolute;
                right: 12px;
                bottom: 9px;
                font-size: 12px;
                color: #6b7280;
                letter-spacing: 0.01em;
            }

            .internal-info-box {
                margin-top: 18px;
                display: flex;
                align-items: center;
                gap: 10px;
                background: #dfeef9;
                border: 1px solid #b7d9ef;
                border-radius: 8px;
                padding: 12px 14px;
                color: #07457d;
                font-size: 14px;
                font-weight: 700;
            }

            .internal-info-box .info-badge {
                width: 18px;
                height: 18px;
                border-radius: 50%;
                background: #1878d3;
                color: #fff;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                font-weight: 800;
                line-height: 1;
            }

            .internal-upload-box {
                margin-top: 18px;
                border: 1px dashed #b7cfe4;
                border-radius: 10px;
                background: #fff;
                min-height: 150px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: border-color 0.2s ease, background 0.2s ease;
            }

            .internal-upload-box:hover {
                border-color: #7caed6;
                background: rgba(230, 242, 252, 0.45);
            }

            .internal-upload-box.is-dragging {
                border-color: #1878d3;
                background: rgba(214, 235, 252, 0.7);
            }

            .internal-upload-inner {
                text-align: center;
                color: #5d7890;
                padding: 18px 20px;
            }

            .upload-icon {
                width: 42px;
                height: 42px;
                margin: 0 auto 10px;
                display: block;
                color: #6f88a2;
            }

            .upload-copy {
                font-size: 14px;
                font-weight: 600;
                color: #456180;
            }

            .upload-meta {
                margin-top: 8px;
                font-size: 12px;
                color: #6a7f96;
                line-height: 1.5;
            }

            .upload-limit-status {
                margin-top: 8px;
                font-size: 12px;
                font-weight: 700;
                color: #456180;
            }

            .upload-limit-error {
                margin-top: 6px;
                color: #c52243;
                font-size: 12px;
                font-weight: 700;
            }

            .selected-files {
                width: min(100%, 620px);
                margin: 12px auto 0;
                display: grid;
                gap: 6px;
                text-align: left;
            }

            .selected-file {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                padding: 7px 10px;
                border: 1px solid #d5e3ef;
                border-radius: 7px;
                background: #fff;
                color: #304a63;
                font-size: 12px;
            }

            .selected-file-name {
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .selected-file-remove {
                flex: 0 0 auto;
                border: 0;
                background: transparent;
                color: #c52243;
                cursor: pointer;
                font-size: 12px;
                font-weight: 700;
            }

            .internal-actions {
                display: flex;
                justify-content: flex-end;
                align-items: center;
                gap: 12px;
                margin-top: 26px;
                padding: 0 2px 8px;
            }

            .btn-cancel,
            .btn-submit {
                border-radius: 8px;
                border: 1px solid #c9d6e3;
                background: #f5f8fb;
                color: #1f2937;
                font-size: 15px;
                font-weight: 600;
                padding: 10px 20px;
                cursor: pointer;
                transition: transform 0.15s ease, box-shadow 0.15s ease;
            }

            .btn-cancel:hover,
            .btn-submit:hover {
                transform: translateY(-1px);
            }

            .btn-submit {
                background: linear-gradient(180deg, #1a80e7 0%, #0b6ec4 100%);
                border-color: #0b6ec4;
                color: #fff;
                box-shadow: 0 8px 18px rgba(20, 110, 190, 0.16);
            }

            .btn-submit:disabled {
                background: #a9b8c7;
                border-color: #a9b8c7;
                box-shadow: none;
                cursor: not-allowed;
                opacity: 0.75;
                transform: none;
            }

            .send-icon {
                display: inline-block;
                margin-left: 8px;
                transform: translateY(-1px);
            }

            @media (max-width: 1024px) {
                .internal-form-grid,
                .internal-form-grid.compact {
                    grid-template-columns: 1fr;
                }

                .description-attachment-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    @endpush

    <div class="internal-issue-shell">
        <div class="internal-issue-header">
            <button type="button" class="back-icon-button" aria-label="Back">←</button>
            <div>
                <h1 class="internal-issue-title">Raise Internal IT Support Ticket</h1>
                <p class="internal-issue-subtitle">Report your IT related issue and get quick support from the local IT Support Desk.</p>
            </div>
        </div>

        <div class="internal-issue-panel">
            <div class="internal-issue-panel-header">
                <h2>Issue Information</h2>
            </div>

            <div class="internal-issue-panel-body">
                <form>
                    <div class="internal-form-grid">
                        <div class="internal-field">
                            <label for="issueCategory">Issue Category <span class="required-star">*</span></label>
                            <select id="issueCategory" class="internal-select" name="issue_category">
                                <option selected disabled>Select Issue Category</option>
                                @foreach ($categoryOptions as $option)
                                    <option value="{{ $option['id'] }}">{{ $option['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="internal-field">
                            <label for="deviceType">Device Type <span class="required-star">*</span></label>
                            <select id="deviceType" class="internal-select" name="device_type">
                                <option selected disabled>Select Device Type</option>
                                @foreach ($deviceOptions as $option)
                                    <option value="{{ $option['id'] }}">{{ $option['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="internal-field">
                            <label for="issueType">Issue Type <span class="required-star">*</span></label>
                            <select id="issueType" class="internal-select" name="issue_type" disabled>
                                <option selected disabled>Select Issue Category First</option>
                                @foreach ($issueTypeOptions as $option)
                                    <option value="{{ $option['id'] }}" data-category-id="{{ $option['category_id'] }}" hidden>{{ $option['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="internal-form-grid compact" style="margin-top: 18px;">
                        <div class="internal-field">
                            <label for="issueSubject">Issue Subject <span class="required-star">*</span></label>
                            <input id="issueSubject" class="internal-input" type="text" name="subject" placeholder="Enter issue subject (e.g. Laptop not connecting to Wi-Fi)" />
                        </div>

                        <div class="internal-field">
                            <label for="issueImpact">Issue Impact <span class="required-star">*</span></label>
                            <select id="issueImpact" class="internal-select" name="impact">
                                <option selected disabled>Select Impact</option>
                                @foreach ($impactOptions as $option)
                                    <option value="{{ $option['id'] }}">{{ $option['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="description-attachment-grid">
                        <div class="internal-field">
                            <label for="issueDescription">Issue Description <span class="required-star">*</span></label>
                            <div class="internal-textarea-wrap">
                                <textarea id="issueDescription" class="internal-textarea" name="description" maxlength="2000" placeholder="Please provide a detailed description of the issue, including when it started, what you were trying to do, any error messages and troubleshooting steps you have already tried."></textarea>
                                <span class="char-counter">0/2000</span>
                            </div>
                        </div>

                        <div>
                            <div class="internal-info-box" style="margin-top: 0;">
                                <span class="info-badge">i</span>
                                <span>Additional Information <span style="font-weight: 500; color: #2a4a67;">(Optional)</span></span>
                            </div>

                            <label id="uploadDropZone" class="internal-upload-box" for="issueAttachment" aria-label="Upload attachments">
                                <div class="internal-upload-inner">
                                    <svg class="upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                        <path d="M17 8l-5-5-5 5"/>
                                        <path d="M12 3v12"/>
                                    </svg>
                                    <div class="upload-copy">Click to upload or drag and drop files here</div>
                                    <div class="upload-meta">Supported formats: JPG, JPEG, PNG, PDF, DOC, DOCX, XLS, XLSX<br>Multiple files allowed. Maximum total upload size: 10 MB</div>
                                    <div id="uploadLimitStatus" class="upload-limit-status">Remaining upload size: 10.00 MB</div>
                                    <div id="uploadLimitError" class="upload-limit-error" role="alert" hidden></div>
                                    <div id="selectedFiles" class="selected-files" aria-live="polite"></div>
                                </div>
                                <input id="issueAttachment" type="file" class="hidden" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx" />
                            </label>
                        </div>
                    </div>

                    <div class="internal-actions">
                        <button id="resetTicketButton" type="reset" class="btn-cancel">Reset</button>
                        <button type="button" class="btn-cancel">Cancel</button>
                        <button id="submitTicketButton" type="submit" class="btn-submit">Submit Ticket <span class="send-icon">✈</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const textarea = document.getElementById('issueDescription');
                const counter = document.querySelector('.char-counter');

                if (textarea && counter) {
                    const updateCounter = () => {
                        counter.textContent = textarea.value.length + '/2000';
                    };
                    textarea.addEventListener('input', updateCounter);
                    updateCounter();
                }

                const categorySelect = document.getElementById('issueCategory');
                const issueTypeSelect = document.getElementById('issueType');
                const attachmentInput = document.getElementById('issueAttachment');
                const uploadDropZone = document.getElementById('uploadDropZone');
                const selectedFiles = document.getElementById('selectedFiles');
                const uploadLimitStatus = document.getElementById('uploadLimitStatus');
                const uploadLimitError = document.getElementById('uploadLimitError');
                const submitTicketButton = document.getElementById('submitTicketButton');
                const resetTicketButton = document.getElementById('resetTicketButton');
                const maxTotalFileSize = 10 * 1024 * 1024;
                const allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx'];
                let selectedFileList = [];

                function formatMegabytes(bytes) {
                    return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
                }

                function updateUploadLimitStatus() {
                    const totalBytes = selectedFileList.reduce(function (total, file) {
                        return total + file.size;
                    }, 0);
                    const remainingBytes = maxTotalFileSize - totalBytes;
                    const overLimit = remainingBytes < 0;

                    uploadLimitStatus.textContent = overLimit
                        ? 'Upload limit exceeded by ' + formatMegabytes(Math.abs(remainingBytes))
                        : 'Remaining upload size: ' + formatMegabytes(remainingBytes);
                    uploadLimitStatus.classList.toggle('upload-limit-error', overLimit);
                    uploadLimitError.hidden = !overLimit;
                    uploadLimitError.textContent = overLimit
                        ? 'The combined file size exceeds the 10 MB limit. Remove file(s) before submitting.'
                        : '';
                    submitTicketButton.disabled = overLimit;
                }

                function resetTicketForm() {
                    selectedFileList = [];
                    attachmentInput.value = '';
                    syncAttachmentInput();
                    renderSelectedFiles();
                    updateUploadLimitStatus();

                    issueTypeSelect.value = '';
                    issueTypeSelect.disabled = true;
                    issueTypeSelect.options[0].textContent = 'Select Issue Category First';
                    Array.from(issueTypeSelect.options).forEach(function (option, index) {
                        if (index > 0) {
                            option.hidden = true;
                        }
                    });
                }

                function syncAttachmentInput() {
                    const transfer = new DataTransfer();
                    selectedFileList.forEach(function (file) {
                        transfer.items.add(file);
                    });
                    attachmentInput.files = transfer.files;
                }

                function renderSelectedFiles() {
                    selectedFiles.innerHTML = '';

                    selectedFileList.forEach(function (file, index) {
                        const row = document.createElement('div');
                        row.className = 'selected-file';

                        const name = document.createElement('span');
                        name.className = 'selected-file-name';
                        name.textContent = file.name + ' (' + Math.ceil(file.size / 1024) + ' KB)';

                        const remove = document.createElement('button');
                        remove.type = 'button';
                        remove.className = 'selected-file-remove';
                        remove.textContent = 'Remove';
                        remove.addEventListener('click', function (event) {
                            event.preventDefault();
                            event.stopPropagation();
                            selectedFileList.splice(index, 1);
                            syncAttachmentInput();
                            renderSelectedFiles();
                            updateUploadLimitStatus();
                        });

                        row.appendChild(name);
                        row.appendChild(remove);
                        selectedFiles.appendChild(row);
                    });
                }

                function addFiles(fileList) {
                    Array.from(fileList).forEach(function (file) {
                        const extension = file.name.split('.').pop().toLowerCase();
                        const alreadySelected = selectedFileList.some(function (selectedFile) {
                            return selectedFile.name === file.name && selectedFile.size === file.size;
                        });

                        if (!allowedExtensions.includes(extension)) {
                            window.alert(file.name + ' is not a supported file type.');
                            return;
                        }

                        if (!alreadySelected) {
                            selectedFileList.push(file);
                        }
                    });

                    syncAttachmentInput();
                    renderSelectedFiles();
                    updateUploadLimitStatus();
                }

                if (attachmentInput && uploadDropZone && selectedFiles) {
                    attachmentInput.addEventListener('change', function () {
                        addFiles(this.files);
                    });

                    ['dragenter', 'dragover'].forEach(function (eventName) {
                        uploadDropZone.addEventListener(eventName, function (event) {
                            event.preventDefault();
                            uploadDropZone.classList.add('is-dragging');
                        });
                    });

                    ['dragleave', 'drop'].forEach(function (eventName) {
                        uploadDropZone.addEventListener(eventName, function (event) {
                            event.preventDefault();
                            uploadDropZone.classList.remove('is-dragging');
                        });
                    });

                    uploadDropZone.addEventListener('drop', function (event) {
                        addFiles(event.dataTransfer.files);
                    });
                }

                if (resetTicketButton) {
                    resetTicketButton.addEventListener('click', function () {
                        window.setTimeout(resetTicketForm, 0);
                    });
                }

                if (categorySelect && issueTypeSelect) {
                    categorySelect.addEventListener('change', function () {
                        const categoryId = this.value;
                        let matchingTypes = 0;

                        issueTypeSelect.value = '';
                        Array.from(issueTypeSelect.options).forEach(function (option, index) {
                            if (index === 0) {
                                option.textContent = 'Select Issue Type';
                                option.hidden = false;
                                return;
                            }

                            const matches = option.dataset.categoryId === categoryId;
                            option.hidden = !matches;
                            if (matches) {
                                matchingTypes += 1;
                            }
                        });

                        issueTypeSelect.disabled = matchingTypes === 0;
                        if (matchingTypes === 0) {
                            issueTypeSelect.options[0].textContent = 'No Issue Type Available';
                        }
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>
