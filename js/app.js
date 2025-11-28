/**
 * app.js
 * Unified JavaScript file for the Utility Management System
 * Contains all functionality for customer and meter management
 * Version: 1.0.0
 */

// ============================================================================
// CONFIGURATION
// ============================================================================

/**
 * API Base URL - Change this to match your backend API URL and connect it to the database
 */
const API_BASE_URL = 'http://localhost:8080';

/**
 * API Endpoints - Centralized API endpoints for easy maintenance
 */
const API_ENDPOINTS = {
    customers: {
        getAll: '/api/customers',
        getById: (id) => `/api/customers/${id}`,
        create: '/api/customers',
        update: (id) => `/api/customers/${id}`,
        delete: (id) => `/api/customers/${id}`
    },
    meters: {
        getAll: '/api/meters',
        getById: (id) => `/api/meters/${id}`,
        getByCustomer: (customerId) => `/api/meters/by-customer/${customerId}`,
        create: '/api/meters',
        update: (id) => `/api/meters/${id}`,
        delete: (id) => `/api/meters/${id}`
    }
};

/**
 * Utility Types - Mapping of utility type IDs to names
 */
const UTILITY_TYPES = {
    1: 'Electricity',
    2: 'Water',
    3: 'Gas'
};

/**
 * Customer Types - Available customer types in the system
 */
const CUSTOMER_TYPES = ['Residential', 'Commercial', 'Industrial'];

/**
 * Application Settings
 */
const APP_SETTINGS = {
    appName: 'Utility Management System',
    version: '1.0.0',
    dateFormat: 'en-US',
    currency: 'USD'
};

console.log(`%c${APP_SETTINGS.appName} v${APP_SETTINGS.version}`, 
    'color: #0066cc; font-weight: bold; font-size: 14px;');
console.log(`API Base URL: ${API_BASE_URL}`);

// ============================================================================
// UTILITY FUNCTIONS
// ============================================================================


 
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

/**
 * Get utility type name from utility type ID
 */
function getUtilityTypeName(utilityTypeID) {
    switch(utilityTypeID) {
        case 1: return 'Electricity';
        case 2: return 'Water';
        case 3: return 'Gas';
        default: return 'Unknown';
    }
}

/**
 * Get utility type icon class
 */
function getUtilityTypeIcon(utilityTypeID) {
    switch(utilityTypeID) {
        case 1: return 'bi-lightning-charge-fill text-warning';
        case 2: return 'bi-droplet-fill text-primary';
        case 3: return 'bi-fire text-danger';
        default: return 'bi-speedometer text-secondary';
    }
}

/**
 * Format date to a readable string
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

/**
 * Get status badge HTML
 */
function getStatusBadge(status) {
    if (status === 'Active') {
        return '<span class="badge bg-success">Active</span>';
    } else if (status === 'Inactive') {
        return '<span class="badge bg-secondary">Inactive</span>';
    } else if (status === 'Maintenance') {
        return '<span class="badge bg-warning">Maintenance</span>';
    } else {
        return '<span class="badge bg-dark">Unknown</span>';
    }
}

/**
 * Validate meter form data
 */
function validateMeterForm(formData) {
    const errors = [];
    if (!formData.customerID || isNaN(formData.customerID)) {
        errors.push('Invalid customer ID');
    }
    if (!formData.utilityTypeID || ![1, 2, 3].includes(parseInt(formData.utilityTypeID))) {
        errors.push('Please select a valid utility type');
    }
    if (!formData.installationDate) {
        errors.push('Installation date is required');
    } else {
        const date = new Date(formData.installationDate);
        if (isNaN(date.getTime())) {
            errors.push('Invalid installation date');
        }
    }
    return {
        isValid: errors.length === 0,
        errors: errors
    };
}

// ============================================================================
// ADMIN PANEL FUNCTIONS
// ============================================================================

/**
 * Initialize admin dashboard
 */
function initAdminPanel() {
    const currentDateElem = document.getElementById('currentDate');
    if (currentDateElem) {
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const currentDate = new Date().toLocaleDateString('en-US', options);
        currentDateElem.textContent = currentDate;
        loadStatistics();
    }
}

/**
 * Load statistics from API
 */
async function loadStatistics() {
    try {
        const customersResponse = await fetch(`${API_BASE_URL}/api/customers`);
        if (customersResponse.ok) {
            const customers = await customersResponse.json();
            const totalCustomersElem = document.getElementById('totalCustomers');
            if (totalCustomersElem) {
                totalCustomersElem.textContent = customers.length;
            }
        }

        const totalMetersElem = document.getElementById('totalMeters');
        const electricityMetersElem = document.getElementById('electricityMeters');
        const waterMetersElem = document.getElementById('waterMeters');
        
        if (totalMetersElem) totalMetersElem.textContent = '--';
        if (electricityMetersElem) electricityMetersElem.textContent = '--';
        if (waterMetersElem) waterMetersElem.textContent = '--';
        
    } catch (error) {
        console.error('Error loading statistics:', error);
    }
}

/**
 * Search customer function
 */
function searchCustomer() {
    const customerId = prompt('Enter Customer ID to search:');
    if (customerId) {
        window.location.href = `Admin pages/customer-profile.html?id=${customerId}`;
    }
}

/**
 * Assign meter prompt
 */
function assignMeterPrompt() {
    const customerId = prompt('Enter Customer ID to assign a meter:');
    if (customerId) {
        window.location.href = `Admin pages/assign-meter.html?customerId=${customerId}`;
    }
}

/**
 * View profile function
 */
function viewProfile() {
    const customerId = prompt('Enter Customer ID to view profile:');
    if (customerId) {
        window.location.href = `Admin pages/customer-profile.html?id=${customerId}`;
    }
}

/**
 * Edit customer function
 */
function editCustomer() {
    const customerId = prompt('Enter Customer ID to edit:');
    if (customerId) {
        window.location.href = `Admin pages/edit-customer.html?id=${customerId}`;
    }
}

// ============================================================================
// CUSTOMER LIST FUNCTIONS
// ============================================================================

/**
 * Load and display all customers
 */
async function loadCustomers() {
    const loadingSpinner = document.getElementById('loadingSpinner');
    const errorAlert = document.getElementById('errorAlert');
    const errorMessage = document.getElementById('errorMessage');
    const customerTableContainer = document.getElementById('customerTableContainer');
    const emptyState = document.getElementById('emptyState');

    try {
        loadingSpinner?.classList.remove('d-none');
        errorAlert?.classList.add('d-none');
        customerTableContainer?.classList.add('d-none');
        emptyState?.classList.add('d-none');

        const response = await fetch(`${API_BASE_URL}/api/customers`);

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const customers = await response.json();
        loadingSpinner?.classList.add('d-none');

        if (customers.length === 0) {
            emptyState?.classList.remove('d-none');
        } else {
            displayCustomers(customers);
            customerTableContainer?.classList.remove('d-none');
        }

    } catch (error) {
        console.error('Error fetching customers:', error);
        loadingSpinner?.classList.add('d-none');
        if (errorMessage) errorMessage.textContent = 'Failed to load customers. Please try again later.';
        errorAlert?.classList.remove('d-none');
    }
}

/**
 * Display customers in the table
 */
function displayCustomers(customers) {
    const tableBody = document.getElementById('customerTableBody');
    if (!tableBody) return;
    
    tableBody.innerHTML = '';

    customers.forEach(customer => {
        const row = document.createElement('tr');
        
        let badgeColor = 'bg-primary';
        if (customer.customerType === 'Commercial') {
            badgeColor = 'bg-success';
        } else if (customer.customerType === 'Industrial') {
            badgeColor = 'bg-warning';
        }

        row.innerHTML = `
            <td>${customer.customerID}</td>
            <td>${escapeHtml(customer.name)}</td>
            <td><span class="badge ${badgeColor}">${escapeHtml(customer.customerType)}</span></td>
            <td>${escapeHtml(customer.contactNumber)}</td>
            <td>${escapeHtml(customer.email)}</td>
            <td>
                <div class="btn-group btn-group-sm" role="group">
                    <a href="customer-profile.html?id=${customer.customerID}" 
                       class="btn btn-info" title="View Profile">
                        <i class="bi bi-eye-fill"></i> View
                    </a>
                    <a href="edit-customer.html?id=${customer.customerID}" 
                       class="btn btn-warning" title="Edit Customer">
                        <i class="bi bi-pencil-fill"></i> Edit
                    </a>
                </div>
            </td>
        `;
        
        tableBody.appendChild(row);
    });
}

// ============================================================================
// ADD CUSTOMER FUNCTIONS
// ============================================================================

/**
 * Initialize add customer form
 */
function initAddCustomerForm() {
    const form = document.getElementById('addCustomerForm');
    if (!form) return;

    const submitBtn = document.getElementById('submitBtn');
    const successAlert = document.getElementById('successAlert');
    const errorAlert = document.getElementById('errorAlert');
    const errorMessage = document.getElementById('errorMessage');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = {
            name: document.getElementById('name').value.trim(),
            address: document.getElementById('address').value.trim(),
            customerType: document.getElementById('customerType').value,
            contactNumber: document.getElementById('contactNumber').value.trim(),
            email: document.getElementById('email').value.trim()
        };

        if (!formData.name || !formData.address || !formData.customerType || 
            !formData.contactNumber || !formData.email) {
            showFormError(errorAlert, errorMessage, 'Please fill in all required fields');
            return;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(formData.email)) {
            showFormError(errorAlert, errorMessage, 'Please enter a valid email address');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

        try {
            const response = await fetch(`${API_BASE_URL}/api/customers`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            hideFormError(errorAlert);
            showFormSuccess(successAlert);
            form.reset();

            setTimeout(() => {
                window.location.href = 'customer-list.html';
            }, 2000);

        } catch (error) {
            console.error('Error adding customer:', error);
            showFormError(errorAlert, errorMessage, 'Failed to add customer. Please try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-save"></i> Save Customer';
        }
    });
}

// ============================================================================
// EDIT CUSTOMER FUNCTIONS
// ============================================================================

/**
 * Initialize edit customer form
 */
function initEditCustomerForm() {
    const form = document.getElementById('editCustomerForm');
    if (!form) return;

    const loadingSpinner = document.getElementById('loadingSpinner');
    const editCustomerCard = document.getElementById('editCustomerCard');
    const submitBtn = document.getElementById('submitBtn');
    const successAlert = document.getElementById('successAlert');
    const errorAlert = document.getElementById('errorAlert');
    const errorMessage = document.getElementById('errorMessage');

    const urlParams = new URLSearchParams(window.location.search);
    const customerId = urlParams.get('id');

    if (!customerId) {
        showFormError(errorAlert, errorMessage, 'No customer ID provided');
        loadingSpinner?.classList.add('d-none');
        return;
    }

    loadCustomerDataForEdit(customerId, loadingSpinner, editCustomerCard, errorAlert, errorMessage);

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = {
            name: document.getElementById('name').value.trim(),
            address: document.getElementById('address').value.trim(),
            customerType: document.getElementById('customerType').value,
            contactNumber: document.getElementById('contactNumber').value.trim(),
            email: document.getElementById('email').value.trim()
        };

        if (!formData.name || !formData.address || !formData.customerType || 
            !formData.contactNumber || !formData.email) {
            showFormError(errorAlert, errorMessage, 'Please fill in all required fields');
            return;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(formData.email)) {
            showFormError(errorAlert, errorMessage, 'Please enter a valid email address');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';

        try {
            const response = await fetch(`${API_BASE_URL}/api/customers/${customerId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            hideFormError(errorAlert);
            showFormSuccess(successAlert);

            setTimeout(() => {
                window.location.href = 'customer-list.html';
            }, 2000);

        } catch (error) {
            console.error('Error updating customer:', error);
            showFormError(errorAlert, errorMessage, 'Failed to update customer. Please try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-save"></i> Update Customer';
        }
    });
}

/**
 * Load customer data for editing
 */
async function loadCustomerDataForEdit(customerId, loadingSpinner, editCustomerCard, errorAlert, errorMessage) {
    try {
        const response = await fetch(`${API_BASE_URL}/api/customers/${customerId}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const customer = await response.json();

        document.getElementById('customerId').value = customer.customerID;
        document.getElementById('name').value = customer.name;
        document.getElementById('address').value = customer.address;
        document.getElementById('customerType').value = customer.customerType;
        document.getElementById('contactNumber').value = customer.contactNumber;
        document.getElementById('email').value = customer.email;

        loadingSpinner?.classList.add('d-none');
        editCustomerCard?.classList.remove('d-none');

    } catch (error) {
        console.error('Error loading customer:', error);
        showFormError(errorAlert, errorMessage, 'Failed to load customer data. Please try again.');
        loadingSpinner?.classList.add('d-none');
    }
}

// ============================================================================
// CUSTOMER PROFILE FUNCTIONS
// ============================================================================

/**
 * Initialize customer profile page
 */
function initCustomerProfile() {
    const profileContent = document.getElementById('profileContent');
    if (!profileContent) return;

    const loadingSpinner = document.getElementById('loadingSpinner');
    const errorAlert = document.getElementById('errorAlert');
    const errorMessage = document.getElementById('errorMessage');

    const urlParams = new URLSearchParams(window.location.search);
    const customerId = urlParams.get('id');

    if (!customerId) {
        showFormError(errorAlert, errorMessage, 'No customer ID provided');
        loadingSpinner?.classList.add('d-none');
        return;
    }

    loadCustomerProfile(customerId, loadingSpinner, profileContent, errorAlert, errorMessage);
    loadCustomerMeters(customerId);
}

/**
 * Load customer profile
 */
async function loadCustomerProfile(customerId, loadingSpinner, profileContent, errorAlert, errorMessage) {
    try {
        const response = await fetch(`${API_BASE_URL}/api/customers/${customerId}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const customer = await response.json();

        document.getElementById('customerId').textContent = customer.customerID;
        document.getElementById('customerName').textContent = customer.name;
        document.getElementById('customerType').textContent = customer.customerType;
        document.getElementById('customerContact').textContent = customer.contactNumber;
        document.getElementById('customerEmail').textContent = customer.email;
        document.getElementById('customerAddress').textContent = customer.address;

        document.getElementById('editCustomerBtn').href = `edit-customer.html?id=${customer.customerID}`;
        document.getElementById('assignMeterBtn').href = `assign-meter.html?customerId=${customer.customerID}`;
        const assignMeterBtn2 = document.getElementById('assignMeterBtn2');
        if (assignMeterBtn2) {
            assignMeterBtn2.href = `assign-meter.html?customerId=${customer.customerID}`;
        }

        loadingSpinner?.classList.add('d-none');
        profileContent?.classList.remove('d-none');

    } catch (error) {
        console.error('Error loading customer profile:', error);
        showFormError(errorAlert, errorMessage, 'Failed to load customer profile. Please try again.');
        loadingSpinner?.classList.add('d-none');
    }
}

/**
 * Load customer meters
 */
async function loadCustomerMeters(customerId) {
    const metersLoadingSpinner = document.getElementById('metersLoadingSpinner');
    const metersTableContainer = document.getElementById('metersTableContainer');
    const noMetersMessage = document.getElementById('noMetersMessage');

    try {
        const response = await fetch(`${API_BASE_URL}/api/meters/by-customer/${customerId}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const meters = await response.json();
        metersLoadingSpinner?.classList.add('d-none');

        if (meters.length === 0) {
            noMetersMessage?.classList.remove('d-none');
        } else {
            const tableBody = document.getElementById('metersTableBody');
            if (tableBody) {
                tableBody.innerHTML = '';

                meters.forEach(meter => {
                    const row = document.createElement('tr');
                    const utilityType = getUtilityTypeName(meter.utilityTypeID);
                    const statusBadge = meter.status === 'Active' 
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-secondary">Inactive</span>';
                    const installDate = new Date(meter.installationDate).toLocaleDateString();

                    row.innerHTML = `
                        <td>${meter.meterID}</td>
                        <td><i class="bi bi-lightning-charge-fill text-warning"></i> ${utilityType}</td>
                        <td>${installDate}</td>
                        <td>${statusBadge}</td>
                    `;
                    
                    tableBody.appendChild(row);
                });

                metersTableContainer?.classList.remove('d-none');
            }
        }

    } catch (error) {
        console.error('Error loading meters:', error);
        metersLoadingSpinner?.classList.add('d-none');
        noMetersMessage?.classList.remove('d-none');
    }
}

// ============================================================================
// ASSIGN METER FUNCTIONS
// ============================================================================

/**
 * Initialize assign meter form
 */
function initAssignMeterForm() {
    const form = document.getElementById('assignMeterForm');
    if (!form) return;

    const submitBtn = document.getElementById('submitBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const successAlert = document.getElementById('successAlert');
    const errorAlert = document.getElementById('errorAlert');
    const errorMessage = document.getElementById('errorMessage');

    const urlParams = new URLSearchParams(window.location.search);
    const customerId = urlParams.get('customerId');

    if (!customerId) {
        showFormError(errorAlert, errorMessage, 'No customer ID provided');
        return;
    }

    document.getElementById('customerId').value = customerId;
    document.getElementById('displayCustomerId').textContent = customerId;
    loadCustomerNameForMeter(customerId);

    const today = new Date().toISOString().split('T')[0];
    document.getElementById('installationDate').value = today;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const utilityTypeID = parseInt(document.getElementById('utilityType').value);
        const installationDate = document.getElementById('installationDate').value;
        const meterNumber = document.getElementById('meterNumber').value.trim();

        if (!utilityTypeID) {
            showFormError(errorAlert, errorMessage, 'Please select a utility type');
            return;
        }

        if (!installationDate) {
            showFormError(errorAlert, errorMessage, 'Please select an installation date');
            return;
        }

        const requestBody = {
            customerID: parseInt(customerId),
            utilityTypeID: utilityTypeID,
            installationDate: installationDate
        };

        if (meterNumber) {
            requestBody.meterNumber = meterNumber;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Assigning...';

        try {
            const response = await fetch(`${API_BASE_URL}/api/meters`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(requestBody)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            hideFormError(errorAlert);
            showFormSuccess(successAlert);
            form.reset();
            document.getElementById('installationDate').value = today;

            setTimeout(() => {
                window.location.href = `customer-profile.html?id=${customerId}`;
            }, 2000);

        } catch (error) {
            console.error('Error assigning meter:', error);
            showFormError(errorAlert, errorMessage, 'Failed to assign meter. Please try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Assign Meter';
        }
    });

    cancelBtn?.addEventListener('click', () => {
        window.location.href = `customer-profile.html?id=${customerId}`;
    });
}

/**
 * Load customer name for meter assignment
 */
async function loadCustomerNameForMeter(customerId) {
    try {
        const response = await fetch(`${API_BASE_URL}/api/customers/${customerId}`);
        
        if (response.ok) {
            const customer = await response.json();
            const displayNameElem = document.getElementById('displayCustomerName');
            if (displayNameElem) {
                displayNameElem.textContent = customer.name;
            }
        }
    } catch (error) {
        console.error('Error loading customer name:', error);
    }
}

// ============================================================================
// HELPER FUNCTIONS FOR FORMS
// ============================================================================

function showFormSuccess(successAlert) {
    successAlert?.classList.remove('d-none');
    window.scrollTo(0, 0);
}

function showFormError(errorAlert, errorMessage, message) {
    if (errorMessage) errorMessage.textContent = message;
    errorAlert?.classList.remove('d-none');
    window.scrollTo(0, 0);
}

function hideFormError(errorAlert) {
    errorAlert?.classList.add('d-none');
}

// ============================================================================
// PAGE INITIALIZATION
// ============================================================================

/**
 * Initialize the appropriate page based on current page
 */
document.addEventListener('DOMContentLoaded', () => {
    // Check which page we're on and initialize accordingly
    if (document.getElementById('customerTableBody')) {
        loadCustomers();
    }
    
    if (document.getElementById('addCustomerForm')) {
        initAddCustomerForm();
    }
    
    if (document.getElementById('editCustomerForm')) {
        initEditCustomerForm();
    }
    
    if (document.getElementById('profileContent')) {
        initCustomerProfile();
    }
    
    if (document.getElementById('assignMeterForm')) {
        initAssignMeterForm();
    }
    
    if (document.getElementById('currentDate')) {
        initAdminPanel();
    }
});
