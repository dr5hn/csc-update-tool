/**
 * Change Request Form Component
 * Handles form validation, auto-save, and user experience improvements
 */
class ChangeRequestForm {
    constructor(formSelector) {
        this.form = document.querySelector(formSelector);
        this.autoSaveInterval = 30000; // 30 seconds
        this.autoSaveTimer = null;
        this.isDirty = false;
        this.lastSavedData = null;

        if (this.form) {
            this.init();
        }
    }

    init() {
        this.setupEventListeners();
        this.setupAutoSave();
        this.setupFormValidation();
        this.setupUnsavedChangesWarning();
        this.loadDraftData();
    }

    setupEventListeners() {
        // Track form changes
        this.form.addEventListener('input', (e) => {
            this.markAsDirty();
            this.validateField(e.target);
        });

        this.form.addEventListener('change', (e) => {
            this.markAsDirty();
            this.validateField(e.target);
        });

        // Handle form submission
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleSubmit();
        });

        // Save as draft button
        const saveDraftBtn = this.form.querySelector('[data-action="save-draft"]');
        if (saveDraftBtn) {
            saveDraftBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.saveDraft();
            });
        }

        // Preview button
        const previewBtn = this.form.querySelector('[data-action="preview"]');
        if (previewBtn) {
            previewBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.showPreview();
            });
        }
    }

    setupAutoSave() {
        this.autoSaveTimer = setInterval(() => {
            if (this.isDirty) {
                this.saveDraft(true); // Silent auto-save
            }
        }, this.autoSaveInterval);
    }

    setupFormValidation() {
        const titleField = this.form.querySelector('[name="title"]');
        const descriptionField = this.form.querySelector('[name="description"]');

        if (titleField) {
            titleField.addEventListener('blur', () => {
                this.validateTitle(titleField);
            });
        }

        if (descriptionField) {
            descriptionField.addEventListener('blur', () => {
                this.validateDescription(descriptionField);
            });
        }
    }

    setupUnsavedChangesWarning() {
        window.addEventListener('beforeunload', (e) => {
            if (this.isDirty) {
                e.preventDefault();
                e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                return e.returnValue;
            }
        });
    }

    markAsDirty() {
        this.isDirty = true;
        this.updateSaveStatus('unsaved');
    }

    markAsClean() {
        this.isDirty = false;
        this.updateSaveStatus('saved');
    }

    updateSaveStatus(status) {
        const statusIndicator = document.querySelector('.save-status');
        if (statusIndicator) {
            statusIndicator.textContent = status === 'saved' ? 'All changes saved' : 'Unsaved changes';
            statusIndicator.className = `save-status ${status === 'saved' ? 'text-green-600' : 'text-yellow-600'}`;
        }
    }

    validateField(field) {
        const fieldName = field.getAttribute('name');

        switch (fieldName) {
            case 'title':
                return this.validateTitle(field);
            case 'description':
                return this.validateDescription(field);
            default:
                return true;
        }
    }

    validateTitle(field) {
        const value = field.value.trim();
        const minLength = 3;
        const maxLength = 255;

        this.clearFieldError(field);

        if (value.length < minLength) {
            this.showFieldError(field, `Title must be at least ${minLength} characters long`);
            return false;
        }

        if (value.length > maxLength) {
            this.showFieldError(field, `Title cannot exceed ${maxLength} characters`);
            return false;
        }

        return true;
    }

    validateDescription(field) {
        const value = field.value.trim();
        const minLength = 10;
        const maxLength = 2000;

        this.clearFieldError(field);

        if (value.length < minLength) {
            this.showFieldError(field, `Description must be at least ${minLength} characters long`);
            return false;
        }

        if (value.length > maxLength) {
            this.showFieldError(field, `Description cannot exceed ${maxLength} characters`);
            return false;
        }

        return true;
    }

    showFieldError(field, message) {
        const errorContainer = field.parentNode.querySelector('.field-error') ||
                              this.createErrorContainer(field);
        errorContainer.textContent = message;
        errorContainer.style.display = 'block';
        field.classList.add('border-red-500');
    }

    clearFieldError(field) {
        const errorContainer = field.parentNode.querySelector('.field-error');
        if (errorContainer) {
            errorContainer.style.display = 'none';
        }
        field.classList.remove('border-red-500');
    }

    createErrorContainer(field) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error text-red-500 text-sm mt-1';
        errorDiv.style.display = 'none';
        field.parentNode.appendChild(errorDiv);
        return errorDiv;
    }

    async saveDraft(silent = false) {
        const formData = this.getFormData();

        if (!silent) {
            this.showLoadingState('Saving draft...');
        }

        try {
            const response = await fetch(this.form.getAttribute('data-draft-url') || '/change-requests/draft', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (result.success) {
                this.markAsClean();
                this.lastSavedData = formData;

                if (!silent) {
                    this.showSuccessMessage('Draft saved successfully');
                }

                // Update form with any returned data (like ID)
                if (result.data && result.data.id) {
                    this.updateFormWithDraftId(result.data.id);
                }
            } else {
                throw new Error(result.message || 'Failed to save draft');
            }
        } catch (error) {
            console.error('Draft save error:', error);
            if (!silent) {
                this.showErrorMessage('Failed to save draft: ' + error.message);
            }
        } finally {
            if (!silent) {
                this.hideLoadingState();
            }
        }
    }

    async handleSubmit() {
        if (!this.validateForm()) {
            this.showErrorMessage('Please fix the validation errors before submitting');
            return;
        }

        this.showLoadingState('Submitting change request...');

        const formData = this.getFormData();

        try {
            const response = await fetch(this.form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (result.success) {
                this.markAsClean();
                this.showSuccessMessage('Change request submitted successfully');

                // Redirect after a short delay
                setTimeout(() => {
                    window.location.href = result.redirect || '/change-requests';
                }, 1500);
            } else {
                throw new Error(result.message || 'Failed to submit change request');
            }
        } catch (error) {
            console.error('Submit error:', error);
            this.showErrorMessage('Failed to submit change request: ' + error.message);
        } finally {
            this.hideLoadingState();
        }
    }

    validateForm() {
        const fields = this.form.querySelectorAll('[name]');
        let isValid = true;

        fields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });

        return isValid;
    }

    getFormData() {
        const formData = new FormData(this.form);
        const data = {};

        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }

        // Get change data from the change tracking component
        if (window.changeTracker) {
            data.new_data = JSON.stringify(window.changeTracker.getChanges());
        }

        return data;
    }

    loadDraftData() {
        const draftId = this.form.getAttribute('data-draft-id');
        if (draftId) {
            // Load existing draft data if editing
            this.loadExistingDraft(draftId);
        }
    }

    async loadExistingDraft(draftId) {
        try {
            const response = await fetch(`/change-requests/${draftId}/edit`);
            if (response.ok) {
                // Draft data would be loaded by the server in the initial page load
                this.markAsClean();
            }
        } catch (error) {
            console.error('Failed to load draft data:', error);
        }
    }

    updateFormWithDraftId(draftId) {
        this.form.setAttribute('data-draft-id', draftId);

        // Update the form action to use the draft ID for updates
        const currentAction = this.form.action;
        if (!currentAction.includes(draftId)) {
            this.form.action = currentAction.replace('/draft', `/${draftId}`);
        }
    }

    showPreview() {
        const formData = this.getFormData();

        // Create preview modal or redirect to preview page
        const previewUrl = this.form.getAttribute('data-preview-url') || '/change-requests/preview';

        // For now, open in a new tab
        const previewForm = document.createElement('form');
        previewForm.method = 'POST';
        previewForm.action = previewUrl;
        previewForm.target = '_blank';

        Object.keys(formData).forEach(key => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = typeof formData[key] === 'object' ? JSON.stringify(formData[key]) : formData[key];
            previewForm.appendChild(input);
        });

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        previewForm.appendChild(csrfInput);

        document.body.appendChild(previewForm);
        previewForm.submit();
        document.body.removeChild(previewForm);
    }

    showLoadingState(message) {
        // Implement loading state UI
        const submitBtn = this.form.querySelector('[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = message;
        }
    }

    hideLoadingState() {
        const submitBtn = this.form.querySelector('[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = submitBtn.getAttribute('data-original-text') || 'Submit';
        }
    }

    showSuccessMessage(message) {
        this.showMessage(message, 'success');
    }

    showErrorMessage(message) {
        this.showMessage(message, 'error');
    }

    showMessage(message, type) {
        // Create or update message container
        let messageContainer = document.querySelector('.form-message');
        if (!messageContainer) {
            messageContainer = document.createElement('div');
            messageContainer.className = 'form-message';
            this.form.insertBefore(messageContainer, this.form.firstChild);
        }

        messageContainer.className = `form-message p-4 rounded-md mb-4 ${
            type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
        }`;
        messageContainer.textContent = message;

        // Auto-hide after 5 seconds
        setTimeout(() => {
            if (messageContainer.parentNode) {
                messageContainer.parentNode.removeChild(messageContainer);
            }
        }, 5000);
    }

    destroy() {
        if (this.autoSaveTimer) {
            clearInterval(this.autoSaveTimer);
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    const changeRequestForm = new ChangeRequestForm('#change-request-form');

    // Make it globally accessible for other components
    window.changeRequestForm = changeRequestForm;
});

export default ChangeRequestForm;
