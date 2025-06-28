/**
 * Change Request Show Page JavaScript
 * Handles incorporation status updates, verification, and modal interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all functionality when DOM is ready
    initializeIncorporationHandlers();
    initializeVerificationHandlers();
    initializeModalHandlers();
    initializeFormHandlers();
});

/**
 * Initialize incorporation status handlers
 */
function initializeIncorporationHandlers() {
    // Mark as Incorporated functionality
    const markIncorporatedBtn = document.getElementById('mark-incorporated-btn');
    if (markIncorporatedBtn) {
        markIncorporatedBtn.addEventListener('click', function() {
            openModal('mark-incorporated-modal');
        });
    }

    // Re-incorporate functionality
    const reIncorporateBtn = document.getElementById('re-incorporate-btn');
    if (reIncorporateBtn) {
        reIncorporateBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to mark this change request for re-incorporation? This will reset its status to pending.')) {
                markAsIncorporated(this.dataset.requestId, 'pending', 'Reset for re-incorporation');
            }
        });
    }
}

/**
 * Initialize verification handlers
 */
function initializeVerificationHandlers() {
    // Verify Changes functionality
    const verifyChangesBtn = document.getElementById('verify-changes-btn');
    if (verifyChangesBtn) {
        verifyChangesBtn.addEventListener('click', function() {
            openModal('verify-changes-modal');
        });
    }

    // Start verification button
    const startVerificationBtn = document.getElementById('start-verification');
    if (startVerificationBtn) {
        startVerificationBtn.addEventListener('click', function() {
            const verifyChangesBtn = document.getElementById('verify-changes-btn');
            if (verifyChangesBtn) {
                const requestId = verifyChangesBtn.dataset.requestId;
                verifyChanges(requestId);
            }
        });
    }
}

/**
 * Initialize modal handlers
 */
function initializeModalHandlers() {
    // Listen for custom modal events
    window.addEventListener('open-modal', function(event) {
        const modalName = event.detail;
        const modal = document.querySelector(`[x-data*="${modalName}"]`);
        if (modal) {
            // Trigger Alpine.js modal opening
            modal.dispatchEvent(new CustomEvent('open-modal', { detail: modalName }));
        }
    });

    window.addEventListener('close-modal', function(event) {
        const modalName = event.detail;
        const modal = document.querySelector(`[x-data*="${modalName}"]`);
        if (modal) {
            // Trigger Alpine.js modal closing
            modal.dispatchEvent(new CustomEvent('close'));
        }
    });
}

/**
 * Initialize form handlers
 */
function initializeFormHandlers() {
    // Handle Mark as Incorporated form submission
    const markIncorporatedForm = document.getElementById('mark-incorporated-form');
    if (markIncorporatedForm) {
        markIncorporatedForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const markIncorporatedBtn = document.getElementById('mark-incorporated-btn');
            if (!markIncorporatedBtn) return;

            const requestId = markIncorporatedBtn.dataset.requestId;
            const incorporatedBy = document.getElementById('incorporated-by').value;
            const notes = document.getElementById('incorporation-notes').value;

            markAsIncorporated(requestId, 'incorporated', incorporatedBy, notes);
        });
    }
}

/**
 * Mark a change request as incorporated
 */
function markAsIncorporated(requestId, status = 'incorporated', incorporatedBy = '', notes = '') {
    const submitButton = document.querySelector('#mark-incorporated-form button[type="submit"]');
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.textContent = 'Processing...';
    }

    fetch(`/change-requests/${requestId}/mark-incorporated`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCSRFToken()
        },
        body: JSON.stringify({
            incorporation_status: status,
            incorporated_by: incorporatedBy,
            incorporation_notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessMessage(data.message || 'Status updated successfully');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showErrorMessage(data.message || 'Failed to update status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorMessage('An error occurred while updating the status');
    })
    .finally(() => {
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.textContent = 'Mark as Incorporated';
        }
        closeModal('mark-incorporated-modal');
    });
}

/**
 * Verify incorporated changes
 */
function verifyChanges(requestId) {
    const progressDiv = document.getElementById('verification-progress');
    const resultsDiv = document.getElementById('verification-results');
    const startBtn = document.getElementById('start-verification');

    if (!progressDiv || !resultsDiv || !startBtn) return;

    progressDiv.classList.remove('hidden');
    resultsDiv.classList.add('hidden');
    startBtn.disabled = true;

    fetch(`/change-requests/${requestId}/verify`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCSRFToken()
        }
    })
    .then(response => response.json())
    .then(data => {
        progressDiv.classList.add('hidden');
        resultsDiv.classList.remove('hidden');

        if (data.success) {
            const result = data.verification_result;
            let statusClass = 'bg-green-100 text-green-800';
            let statusIcon = '✅';

            if (result.status === 'missing') {
                statusClass = 'bg-orange-100 text-orange-800';
                statusIcon = '⚠️';
            } else if (result.status === 'conflicted') {
                statusClass = 'bg-red-100 text-red-800';
                statusIcon = '❌';
            }

            resultsDiv.innerHTML = `
                <div class="p-4 rounded-lg ${statusClass}">
                    <div class="flex items-center mb-2">
                        <span class="text-lg mr-2">${statusIcon}</span>
                        <span class="font-medium">Status: ${result.status}</span>
                    </div>
                    <p class="text-sm">${result.message || 'Verification completed'}</p>
                    ${result.details ? `<div class="mt-2 text-xs"><strong>Details:</strong> ${JSON.stringify(result.details, null, 2)}</div>` : ''}
                </div>
            `;

            // Reload page after a delay to show updated status
            if (result.status !== 'incorporated') {
                setTimeout(() => {
                    window.location.reload();
                }, 3000);
            }
        } else {
            resultsDiv.innerHTML = `
                <div class="p-4 rounded-lg bg-red-100 text-red-800">
                    <span class="font-medium">❌ Verification Failed</span>
                    <p class="text-sm mt-1">${data.message || 'Unknown error occurred'}</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        progressDiv.classList.add('hidden');
        resultsDiv.classList.remove('hidden');
        resultsDiv.innerHTML = `
            <div class="p-4 rounded-lg bg-red-100 text-red-800">
                <span class="font-medium">❌ Error</span>
                <p class="text-sm mt-1">An error occurred during verification</p>
            </div>
        `;
    })
    .finally(() => {
        startBtn.disabled = false;
    });
}

/**
 * Utility Functions
 */

function openModal(modalName) {
    window.dispatchEvent(new CustomEvent('open-modal', {
        detail: modalName
    }));
}

function closeModal(modalName) {
    window.dispatchEvent(new CustomEvent('close-modal', {
        detail: modalName
    }));
}

function getCSRFToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.getAttribute('content') : '';
}

function showSuccessMessage(message) {
    showMessage(message, 'success');
}

function showErrorMessage(message) {
    showMessage(message, 'error');
}

function showMessage(message, type) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${
        type === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'
    }`;
    messageDiv.textContent = message;

    document.body.appendChild(messageDiv);

    setTimeout(() => {
        messageDiv.remove();
    }, 5000);
}

// Export functions for potential external use
window.ChangeRequestShow = {
    markAsIncorporated,
    verifyChanges,
    showSuccessMessage,
    showErrorMessage
};
