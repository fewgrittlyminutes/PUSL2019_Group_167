# Project Structure - Customer & Meter Management Module

## 📁 File Hierarchy

```
PUSL2021_Group_67/
│
├── index.html                          # Main admin dashboard (homepage)
├── README.md                           # Project documentation
├── ums_1.sql                          # Database schema file
│
├── Admin pages/                       # All admin page files
│   ├── customer-list.html            # View all customers
│   ├── add-customer.html             # Add new customer form
│   ├── edit-customer.html            # Edit customer form
│   ├── customer-profile.html         # Customer details & meters
│   └── assign-meter.html             # Assign meter to customer
│
├── css/                               # Stylesheets
│   └── styles.css                    # Custom styles (gradients, cards, animations)
│
└── js/                                # JavaScript files
    └── app.js                        # Single unified JS file (all functionality)
```

## 📄 File Descriptions

### Root Files

| File | Purpose |
|------|---------|
| `index.html` | Main admin dashboard with statistics and navigation cards |
| `README.md` | Complete project documentation and setup instructions |
| `ums_1.sql` | MySQL database schema with Customer, Meter, and UtilityType tables |

### Admin Pages Folder

| File | Purpose | Key Features |
|------|---------|--------------|
| `customer-list.html` | Displays all customers in a table | View, Edit buttons for each customer |
| `add-customer.html` | Form to add new customers | Name, Address, Type, Contact, Email fields |
| `edit-customer.html` | Form to edit existing customer | Pre-populated with customer data |
| `customer-profile.html` | Shows customer details | Customer info + assigned meters table |
| `assign-meter.html` | Assign utility meters to customers | Electricity, Water, Gas meter assignment |

### CSS Folder

| File | Purpose |
|------|---------|
| `styles.css` | Custom styles including gradient backgrounds, card designs, hover effects, and animations |

### JS Folder

| File | Purpose |
|------|---------|
| `app.js` | **All JavaScript functionality in ONE file** - includes configuration, API calls, form handling, and page initialization |

## 🔗 How Pages Link Together

```
index.html (Dashboard)
    │
    ├──> Admin pages/customer-list.html
    │         │
    │         ├──> Admin pages/customer-profile.html?id=X
    │         │         │
    │         │         ├──> Admin pages/edit-customer.html?id=X
    │         │         └──> Admin pages/assign-meter.html?customerId=X
    │         │
    │         └──> Admin pages/edit-customer.html?id=X
    │
    └──> Admin pages/add-customer.html
```

## 🎯 Navigation Flow

1. **Dashboard (index.html)** → Click "View All Customers" → `customer-list.html`
2. **Customer List** → Click "View" → `customer-profile.html`
3. **Customer Profile** → Click "Edit Customer" → `edit-customer.html`
4. **Customer Profile** → Click "Assign Meter" → `assign-meter.html`
5. **Dashboard** → Click "Add New Customer" → `add-customer.html`

## 🔧 JavaScript Structure (app.js)

The `app.js` file contains all functionality organized in sections:

1. **Configuration** - API URL and endpoints
2. **Utility Functions** - Date formatting, HTML escaping, type converters
3. **Admin Panel Functions** - Dashboard statistics
4. **Customer List Functions** - Load and display customers
5. **Add Customer Functions** - Form submission
6. **Edit Customer Functions** - Load and update customer
7. **Customer Profile Functions** - Display customer + meters
8. **Assign Meter Functions** - Meter assignment form

## 📡 API Integration

All pages use REST API endpoints through the backend server:

- **Base URL**: `http://localhost:8080` (configurable in `app.js`)
- **Customer Endpoints**:
  - GET `/api/customers` - Get all customers
  - GET `/api/customers/{id}` - Get customer by ID
  - POST `/api/customers` - Create customer
  - PUT `/api/customers/{id}` - Update customer
  - DELETE `/api/customers/{id}` - Delete customer

- **Meter Endpoints**:
  - GET `/api/meters/by-customer/{customerId}` - Get customer's meters
  - POST `/api/meters` - Assign new meter

## 🎨 Styling

- **Framework**: Bootstrap 5.3
- **Icons**: Bootstrap Icons
- **Custom CSS**: `styles.css` for gradients, cards, and animations
- **Color Scheme**: 
  - Primary: Blue (#667eea)
  - Success: Green (for actions)
  - Warning: Orange (for electricity)
  - Danger: Red (for critical actions)

## 🗄️ Database

The `ums_1.sql` file contains:
- `Customer` table - Stores customer information
- `Meter` table - Stores meter assignments
- `UtilityType` table - Electricity (1), Water (2), Gas (3)
- Foreign key relationships between tables

## 📝 Quick Start for Team Members

1. **To modify a page**: Edit the HTML file in `Admin pages/` folder
2. **To change styles**: Edit `css/styles.css`
3. **To update functionality**: Edit `js/app.js` (all JavaScript is here)
4. **To change API URL**: Update `API_BASE_URL` in `js/app.js` line 15
5. **Database setup**: Import `ums_1.sql` into MySQL

## ⚠️ Important Notes

- All HTML pages in `Admin pages/` use `../js/app.js` (go up one folder)
- `index.html` uses `js/app.js` (same level)
- Backend API must be running on port 8080 for functionality to work
- Frontend does NOT connect directly to database - it goes through backend API
