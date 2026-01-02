@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="bi bi-people me-2"></i> Students Management</h3>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addStudentModal" onclick="resetModal()">
            <i class="bi bi-plus-circle"></i> Add Student
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="row mb-3">
        <div class="col-md-3">
            <select id="filter_program" class="form-select" onchange="loadStudents()">
                <option value="">All Programs</option>
                <!-- Add your programs dynamically if needed -->
                <option value="1">B.Com</option>
            </select>
        </div>
        <div class="col-md-3">
            <select id="filter_academic_year" class="form-select" onchange="loadStudents()">
                <option value="">All Years</option>
                <option value="FY">FY</option>
                <option value="SY">SY</option>
                <option value="TY">TY</option>
            </select>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Roll No</th>
                            <th>Program</th>
                            <th>Division</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="studentsTableBody">
                        <tr><td colspan="8" class="text-center text-muted">Loading students...</td></tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="card-footer d-flex justify-content-center">
                <nav aria-label="Students pagination">
                    <ul class="pagination mb-0" id="pagination"></ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Student Modal -->
<!-- Add/Edit Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="studentForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add New Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="studentId">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" required>
                        </div>
                    </div>

                    <!-- NEW: Date of Birth & Gender -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="date_of_birth" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                            <select class="form-control" id="gender" required>
                                <option value="">Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mobile</label>
                        <input type="text" class="form-control" id="mobile_number">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Program <span class="text-danger">*</span></label>
                            <select class="form-control" id="program_id" required>
                                <option value="1">B.Com</option>
                                <!-- Add more as needed -->
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                            <select class="form-control" id="academic_year" required>
                                <option value="FY">FY</option>
                                <option value="SY">SY</option>
                                <option value="TY">TY</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Division <span class="text-danger">*</span></label>
                            <select class="form-control" id="division_id" required>
                                <option value="1">A</option>
                                <!-- Add more as needed -->
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Admission Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="admission_date" required>
                        </div>
                    </div>

                    <!-- NEW: Academic Session ID (use hidden or dropdown) -->
                    <div class="mb-3">
                        <label class="form-label">Academic Session <span class="text-danger">*</span></label>
                        <select class="form-control" id="academic_session_id" required>
                            <option value="2">2025-26 (Current)</option>
                            <!-- Add other sessions if needed -->
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-control" id="category" required>
                            <option value="general">General</option>
                            <option value="obc">OBC</option>
                            <option value="sc">SC</option>
                            <option value="st">ST</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">Save Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Student Modal -->
<div class="modal fade" id="viewStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background: linear-gradient(135deg, #007bff 0%, #1a1a1a 100%); color: white;">
                <h5 class="modal-title"><i class="bi bi-person-circle me-2"></i>Student Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" style="background-color: white;">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card border-0 h-100" style="background-color: white; box-shadow: 0 4px 6px rgba(0,123,255,0.1);">
                            <div class="card-body">
                                <h6 class="card-title text-primary mb-3"><i class="bi bi-person me-2"></i>Personal Information</h6>
                                <div class="mb-3">
                                    <strong style="color: #1a1a1a;">Full Name:</strong>
                                    <p class="mb-1 fw-semibold text-dark" id="view_name"></p>
                                </div>
                                <div class="mb-3">
                                    <strong style="color: #1a1a1a;">Date of Birth:</strong>
                                    <p class="mb-1 text-dark" id="view_date_of_birth"></p>
                                </div>
                                <div class="mb-3">
                                    <strong style="color: #1a1a1a;">Gender:</strong>
                                    <p class="mb-1 text-dark" id="view_gender"></p>
                                </div>
                                <div class="mb-3">
                                    <strong style="color: #1a1a1a;">Category:</strong>
                                    <p class="mb-1 text-dark" id="view_category"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 h-100" style="background-color: white; color: #1a1a1a; box-shadow: 0 4px 6px rgba(26,26,26,0.1);">
                            <div class="card-body">
                                <h6 class="card-title text-primary mb-3"><i class="bi bi-telephone me-2"></i>Contact Information</h6>
                                <div class="mb-3">
                                    <strong style="color: #1a1a1a;">Email:</strong>
                                    <p class="mb-1 text-dark" id="view_email"></p>
                                </div>
                                <div class="mb-3">
                                    <strong style="color: #1a1a1a;">Mobile:</strong>
                                    <p class="mb-1 text-dark" id="view_mobile"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 h-100" style="background-color: white; color: #1a1a1a; box-shadow: 0 4px 6px rgba(26,26,26,0.1);">
                            <div class="card-body">
                                <h6 class="card-title text-primary mb-3"><i class="bi bi-book me-2"></i>Academic Information</h6>
                                <div class="mb-3">
                                    <strong style="color: #1a1a1a;">Roll Number:</strong>
                                    <p class="mb-1 text-dark" id="view_roll_number"></p>
                                </div>
                                <div class="mb-3">
                                    <strong style="color: #1a1a1a;">Program:</strong>
                                    <p class="mb-1 text-dark" id="view_program"></p>
                                </div>
                                <div class="mb-3">
                                    <strong style="color: #1a1a1a;">Academic Year:</strong>
                                    <p class="mb-1 text-dark" id="view_academic_year"></p>
                                </div>
                                <div class="mb-3">
                                    <strong style="color: #1a1a1a;">Division:</strong>
                                    <p class="mb-1 text-dark" id="view_division"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 h-100" style="background-color: white; box-shadow: 0 4px 6px rgba(0,123,255,0.1);">
                            <div class="card-body">
                                <h6 class="card-title text-primary mb-3"><i class="bi bi-calendar-check me-2"></i>Admission Details</h6>
                                <div class="mb-3">
                                    <strong style="color: #1a1a1a;">Admission Date:</strong>
                                    <p class="mb-1 text-dark" id="view_admission_date"></p>
                                </div>
                                <div class="mb-3">
                                    <strong style="color: #1a1a1a;">Status:</strong>
                                    <p class="mb-1" id="view_status"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background-color: white;">
                <button type="button" class="btn" style="background-color: #1a1a1a; color: white; border-color: #1a1a1a;" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentPage = 1;

function getFilters() {
    return {
        program_id: document.getElementById('filter_program').value,
        academic_year: document.getElementById('filter_academic_year').value,
        page: currentPage
    };
}

async function loadStudents() {
    const tbody = document.getElementById('studentsTableBody');
    const pagination = document.getElementById('pagination');
    tbody.innerHTML = '<tr><td colspan="8" class="text-center">Loading...</td></tr>';
    pagination.innerHTML = '';

    const params = new URLSearchParams(getFilters()).toString();
    const url = `/api/students?${params}`;

    try {
        const res = await fetch(url, {
            headers: { 'Accept': 'application/json' }
        });

        if (!res.ok) throw new Error('Failed to fetch');

        const result = await res.json();
        if (!result.success) throw new Error(result.message || 'Unknown error');

        const data = result.data;
        renderStudents(data.data);
        renderPagination(data);
    } catch (err) {
        tbody.innerHTML = `<tr><td colspan="8" class="text-center text-danger">Error: ${err.message}</td></tr>`;
        console.error(err);
    }
}

function renderStudents(students) {
    const tbody = document.getElementById('studentsTableBody');
    if (!students.length) {
        tbody.innerHTML = `<tr><td colspan="8" class="text-center text-muted">No students found</td></tr>`;
        return;
    }

    tbody.innerHTML = students.map(s => `
        <tr>
            <td>${s.id}</td>
            <td>${s.first_name} ${s.last_name || ''}</td>
            <td>${s.email || '—'}</td>
            <td>${s.roll_number || '—'}</td>
            <td>${s.program?.short_name || '—'}</td>
            <td>${s.division?.division_name || '—'}</td>
            <td><span class="badge bg-${s.student_status === 'active' ? 'success' : 'secondary'}">${s.student_status}</span></td>
            <td class="text-end">
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-primary" onclick="viewStudent(${s.id})" title="View Details">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm" style="background-color: #1a1a1a; color: white; border-color: #1a1a1a;" onclick="editStudent(${s.id})" title="Edit Student">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-dark" onclick="deleteStudent(${s.id})" title="Delete Student">
                        <i class="bi bi-trash3"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function renderPagination(data) {
    const pagination = document.getElementById('pagination');
    if (data.last_page <= 1) return;

    let html = '';
    if (data.prev_page_url) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${data.current_page - 1}); return false;">«</a></li>`;
    }

    for (let i = 1; i <= data.last_page; i++) {
        html += `
            <li class="page-item ${i === data.current_page ? 'active' : ''}">
                <a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>
            </li>`;
    }

    if (data.next_page_url) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${data.current_page + 1}); return false;">»</a></li>`;
    }

    pagination.innerHTML = html;
}

function changePage(page) {
    currentPage = page;
    loadStudents();
}

// Reset modal for adding new student
function resetModal() {
    document.getElementById('studentForm').reset();
    document.getElementById('studentId').value = '';
    document.getElementById('modalTitle').textContent = 'Add New Student';
    document.getElementById('saveBtn').textContent = 'Save Student';
}

// Form Submission
document.getElementById('studentForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('studentId').value;
    const url = id ? `/api/students/${id}` : '/api/students';
    const method = id ? 'PUT' : 'POST';

    const data = {
        first_name: document.getElementById('first_name').value,
        last_name: document.getElementById('last_name').value,
        // NEW FIELDS ↓
        date_of_birth: document.getElementById('date_of_birth').value,
        gender: document.getElementById('gender').value,
        // EXISTING FIELDS ↓
        email: document.getElementById('email').value || undefined,
        mobile_number: document.getElementById('mobile_number').value || undefined,
        program_id: parseInt(document.getElementById('program_id').value),
        academic_year: document.getElementById('academic_year').value,
        division_id: parseInt(document.getElementById('division_id').value),
        admission_date: document.getElementById('admission_date').value,
        academic_session_id: parseInt(document.getElementById('academic_session_id').value), // NEW
        category: document.getElementById('category').value
    };

    try {
        const res = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        });

        const result = await res.json();
        if (!result.success) {
            alert('Error: ' + (result.message || JSON.stringify(result.data?.errors || {})));
            return;
        }

        // Show success & reset
        if (!id) {
            const msg = document.createElement('div');
            msg.className = 'alert alert-success alert-dismissible fade show position-fixed';
            msg.style = 'top: 20px; right: 20px; z-index: 1050;';
            msg.innerHTML = `${result.message} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
            document.body.appendChild(msg);
            setTimeout(() => msg.remove(), 5000);
        }

        loadStudents();
        document.getElementById('studentForm').reset();
        document.getElementById('studentId').value = '';
        bootstrap.Modal.getInstance(document.getElementById('addStudentModal')).hide();
    } catch (err) {
        alert('Network error: ' + err.message);
    }
});

// Edit
async function editStudent(id) {
    try {
        const res = await fetch(`/api/students/${id}`);
        const result = await res.json();
        if (!result.success) throw new Error(result.message);

        const s = result.data;
        
        // Set student ID for edit mode
        document.getElementById('studentId').value = s.id;
        
        // Populate all form fields
        document.getElementById('first_name').value = s.first_name || '';
        document.getElementById('last_name').value = s.last_name || '';
        document.getElementById('date_of_birth').value = s.date_of_birth ? s.date_of_birth.split('T')[0] : '';
        document.getElementById('gender').value = s.gender || '';
        document.getElementById('email').value = s.email || '';
        document.getElementById('mobile_number').value = s.mobile_number || '';
        document.getElementById('program_id').value = s.program_id || '';
        document.getElementById('academic_year').value = s.academic_year || '';
        document.getElementById('division_id').value = s.division_id || '';
        document.getElementById('admission_date').value = s.admission_date ? s.admission_date.split('T')[0] : '';
        document.getElementById('academic_session_id').value = s.academic_session_id || 2;
        document.getElementById('category').value = s.category || '';
        
        // Update modal title and button
        document.getElementById('modalTitle').textContent = 'Edit Student';
        document.getElementById('saveBtn').textContent = 'Update Student';
        
        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('addStudentModal'));
        modal.show();
        
    } catch (err) {
        alert('Failed to load student: ' + err.message);
    }
}

// View Student
async function viewStudent(id) {
    try {
        const res = await fetch(`/api/students/${id}`);
        const result = await res.json();
        if (!result.success) throw new Error(result.message);

        const s = result.data;
        
        // Populate view modal fields
        document.getElementById('view_name').textContent = `${s.first_name} ${s.last_name || ''}`;
        document.getElementById('view_email').textContent = s.email || '—';
        document.getElementById('view_mobile').textContent = s.mobile_number || '—';
        document.getElementById('view_roll_number').textContent = s.roll_number || '—';
        document.getElementById('view_date_of_birth').textContent = s.date_of_birth ? new Date(s.date_of_birth).toLocaleDateString() : '—';
        document.getElementById('view_gender').textContent = s.gender ? s.gender.charAt(0).toUpperCase() + s.gender.slice(1) : '—';
        document.getElementById('view_program').textContent = s.program?.name || s.program?.short_name || '—';
        document.getElementById('view_academic_year').textContent = s.academic_year || '—';
        document.getElementById('view_division').textContent = s.division?.division_name || '—';
        document.getElementById('view_category').textContent = s.category ? s.category.toUpperCase() : '—';
        document.getElementById('view_admission_date').textContent = s.admission_date ? new Date(s.admission_date).toLocaleDateString() : '—';
        document.getElementById('view_status').innerHTML = `<span class="badge bg-${s.student_status === 'active' ? 'success' : 'secondary'}">${s.student_status}</span>`;
        
        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('viewStudentModal'));
        modal.show();
        
    } catch (err) {
        alert('Failed to load student details: ' + err.message);
    }
}

// Delete
async function deleteStudent(id) {
    if (!confirm('Are you sure you want to delete this student?')) return;

    try {
        const res = await fetch(`/api/students/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });

        const result = await res.json();
        if (result.success) {
            loadStudents();
        } else {
            alert('Delete failed: ' + result.message);
        }
    } catch (err) {
        alert('Error: ' + err.message);
    }
}

// Initial load
document.addEventListener('DOMContentLoaded', loadStudents);
</script>
@endpush
@endsection
