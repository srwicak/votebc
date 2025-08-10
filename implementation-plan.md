# Candidate Management Integration Plan

## Overview
This plan outlines the changes needed to integrate candidate management directly into the election editing process. Currently, candidates are added through a separate menu, but the new implementation will allow administrators to manage candidates within the context of each election.

## Current vs. Proposed Flow

### Current Flow:
1. Admin creates an election
2. Admin navigates to a separate candidate management section
3. Admin selects an election and adds candidates one by one
4. System validates if candidates are eligible based on department/faculty/university

### Proposed Flow:
1. Admin creates an election
2. When editing the election, admin can directly manage candidates
3. Admin can search for eligible candidates using an API
4. System validates candidates based on department/faculty/university in real-time
5. All candidate management happens within the election edit page

## Implementation Steps

### 1. Create an API Endpoint for Searching Eligible Users

#### API Design:
- **Endpoint**: `/api/admin/users/search`
- **Method**: GET
- **Parameters**:
  - `query`: Search term (name, NIM)
  - `election_id`: ID of the election (to filter eligible users)
  - `page`: Pagination page number
  - `limit`: Number of results per page

#### Implementation Tasks:
- Add a search method to the UserModel that filters users based on eligibility criteria
- Create a controller method to handle the search requests
- Implement proper validation and error handling

### 2. Modify the Edit Election View

#### UI Components to Add:
- Candidate management section with tabs:
  - Current Candidates: List of candidates already added to the election
  - Add Candidates: Interface for searching and adding new candidates

#### Candidate Search UI:
- Search input field with auto-suggest functionality
- Results display with user details (name, NIM, department, faculty)
- Add button for each search result
- Option to add as primary candidate or running mate

#### Current Candidates UI:
- List of current candidates with details
- Remove button for each candidate
- Edit button to modify candidate details

### 3. Update the Admin Controller

#### Controller Changes:
- Modify the `updateElection` method to handle candidate management
- Add methods for adding/removing candidates within the election context
- Implement validation to ensure candidates are eligible for the election

#### New Methods:
- `searchEligibleUsers`: Search for eligible users based on election criteria
- `addCandidateToElection`: Add a candidate to an election
- `removeCandidateFromElection`: Remove a candidate from an election

### 4. JavaScript Implementation

#### Client-Side Functionality:
- AJAX for real-time user search
- Handling candidate addition/removal without page reload
- Validation to prevent adding ineligible candidates
- UI updates to reflect changes

## Technical Details

### User Search API Implementation

```php
// In UserModel.php
public function searchEligibleUsers($query, $electionId, $page = 1, $limit = 10)
{
    // Get election details to determine eligibility criteria
    $electionModel = new ElectionModel();
    $election = $electionModel->find($electionId);
    
    if (!$election) {
        return [];
    }
    
    // Start building the query
    $builder = $this->builder();
    $builder->select('users.*, departments.name as department_name, faculties.name as faculty_name');
    $builder->join('departments', 'departments.id = users.department_id', 'left');
    $builder->join('faculties', 'faculties.id = departments.faculty_id', 'left');
    
    // Add search condition
    $builder->groupStart();
    $builder->like('users.name', $query);
    $builder->orLike('users.nim', $query);
    $builder->groupEnd();
    
    // Filter based on election level
    if ($election['level'] === 'fakultas') {
        $builder->where('faculties.id', $election['target_id']);
    } else if ($election['level'] === 'jurusan') {
        $builder->where('departments.id', $election['target_id']);
    }
    
    // Exclude users who are already candidates in this election
    $candidateModel = new CandidateModel();
    $existingCandidates = $candidateModel->select('user_id')
                                        ->where('election_id', $electionId)
                                        ->findAll();
    
    $existingUserIds = array_column($existingCandidates, 'user_id');
    if (!empty($existingUserIds)) {
        $builder->whereNotIn('users.id', $existingUserIds);
    }
    
    // Pagination
    $offset = ($page - 1) * $limit;
    $builder->limit($limit, $offset);
    
    return $builder->get()->getResultArray();
}
```

### Edit Election View Changes

The edit_election.php view will be updated to include a new section for candidate management:

```html
<!-- Candidate Management Section -->
<div class="border-t border-gray-200 pt-4 mt-6">
    <h5 class="text-lg font-semibold mb-4">Manajemen Kandidat</h5>
    
    <div class="mb-4">
        <ul class="flex border-b">
            <li class="mr-1">
                <a href="#current-candidates" class="tab-link bg-white inline-block py-2 px-4 text-blue-500 hover:text-blue-800 font-semibold" data-tab="current-candidates">
                    Kandidat Saat Ini
                </a>
            </li>
            <li class="mr-1">
                <a href="#add-candidates" class="tab-link bg-white inline-block py-2 px-4 text-blue-500 hover:text-blue-800 font-semibold" data-tab="add-candidates">
                    Tambah Kandidat
                </a>
            </li>
        </ul>
    </div>
    
    <!-- Current Candidates Tab -->
    <div id="current-candidates" class="tab-content">
        <div id="candidates-list" class="space-y-4">
            <!-- Candidates will be loaded here -->
        </div>
    </div>
    
    <!-- Add Candidates Tab -->
    <div id="add-candidates" class="tab-content hidden">
        <div class="mb-4">
            <label for="candidate-search" class="block text-sm font-medium text-gray-700 mb-1">Cari Mahasiswa</label>
            <div class="flex">
                <input type="text" id="candidate-search" class="flex-1 px-4 py-2 border rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nama atau NIM...">
                <button id="search-button" class="px-4 py-2 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700 transition">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
        
        <div id="search-results" class="mt-4">
            <!-- Search results will be displayed here -->
        </div>
    </div>
</div>
```

### JavaScript for Candidate Management

```javascript
// Candidate management functionality
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching
    document.querySelectorAll('.tab-link').forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            const tabId = this.getAttribute('data-tab');
            
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Show selected tab content
            document.getElementById(tabId).classList.remove('hidden');
            
            // Update active tab
            document.querySelectorAll('.tab-link').forEach(t => {
                t.classList.remove('bg-blue-100', 'border-blue-500');
            });
            this.classList.add('bg-blue-100', 'border-blue-500');
        });
    });
    
    // Load current candidates
    loadCurrentCandidates();
    
    // Search functionality
    document.getElementById('search-button').addEventListener('click', function() {
        searchCandidates();
    });
    
    document.getElementById('candidate-search').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchCandidates();
        }
    });
});

// Load current candidates for this election
function loadCurrentCandidates() {
    const electionId = document.getElementById('election_id').value;
    
    fetch(`${BASE_URL}/api/election/${electionId}/candidates`, {
        headers: {
            'Authorization': `Bearer ${AUTH_TOKEN}`
        }
    })
    .then(response => response.json())
    .then(data => {
        const candidatesList = document.getElementById('candidates-list');
        candidatesList.innerHTML = '';
        
        if (data.length === 0) {
            candidatesList.innerHTML = '<p class="text-gray-500">Belum ada kandidat untuk pemilihan ini.</p>';
            return;
        }
        
        data.forEach(candidate => {
            const candidateItem = document.createElement('div');
            candidateItem.className = 'bg-white p-4 rounded-lg shadow';
            candidateItem.innerHTML = `
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-semibold">${candidate.user_name} (${candidate.nim})</h4>
                        <p class="text-sm text-gray-600">${candidate.department_name || ''}</p>
                        ${candidate.running_mate_name ? `
                            <div class="mt-2">
                                <p class="text-sm font-medium">Running Mate:</p>
                                <p>${candidate.running_mate_name} (${candidate.running_mate_nim})</p>
                                <p class="text-sm text-gray-600">${candidate.running_mate_department_name || ''}</p>
                            </div>
                        ` : ''}
                    </div>
                    <button class="remove-candidate text-red-500 hover:text-red-700" data-id="${candidate.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            
            candidatesList.appendChild(candidateItem);
            
            // Add event listener to remove button
            candidateItem.querySelector('.remove-candidate').addEventListener('click', function() {
                removeCandidate(this.getAttribute('data-id'));
            });
        });
    })
    .catch(error => {
        console.error('Error loading candidates:', error);
    });
}

// Search for eligible candidates
function searchCandidates() {
    const query = document.getElementById('candidate-search').value;
    const electionId = document.getElementById('election_id').value;
    
    if (!query) {
        return;
    }
    
    fetch(`${BASE_URL}/api/admin/users/search?query=${encodeURIComponent(query)}&election_id=${electionId}`, {
        headers: {
            'Authorization': `Bearer ${AUTH_TOKEN}`
        }
    })
    .then(response => response.json())
    .then(data => {
        const searchResults = document.getElementById('search-results');
        searchResults.innerHTML = '';
        
        if (data.length === 0) {
            searchResults.innerHTML = '<p class="text-gray-500">Tidak ada hasil yang ditemukan.</p>';
            return;
        }
        
        data.forEach(user => {
            const userItem = document.createElement('div');
            userItem.className = 'bg-white p-4 rounded-lg shadow mb-2';
            userItem.innerHTML = `
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="font-semibold">${user.name} (${user.nim})</h4>
                        <p class="text-sm text-gray-600">${user.department_name || ''} - ${user.faculty_name || ''}</p>
                    </div>
                    <div class="space-x-2">
                        <button class="add-as-primary px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm" data-id="${user.id}">
                            Tambah sebagai Kandidat Utama
                        </button>
                        <button class="add-as-running-mate px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-sm" data-id="${user.id}">
                            Tambah sebagai Running Mate
                        </button>
                    </div>
                </div>
            `;
            
            searchResults.appendChild(userItem);
            
            // Add event listeners to buttons
            userItem.querySelector('.add-as-primary').addEventListener('click', function() {
                addCandidate(this.getAttribute('data-id'), null);
            });
            
            userItem.querySelector('.add-as-running-mate').addEventListener('click', function() {
                // Show modal to select primary candidate
                showPrimaryCandidateModal(this.getAttribute('data-id'));
            });
        });
    })
    .catch(error => {
        console.error('Error searching users:', error);
    });
}

// Add a candidate to the election
function addCandidate(userId, runningMateId = null) {
    const electionId = document.getElementById('election_id').value;
    
    fetch(`${BASE_URL}/api/admin/election/${electionId}/candidates`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${AUTH_TOKEN}`
        },
        body: JSON.stringify({
            user_id: userId,
            running_mate_id: runningMateId,
            use_blockchain: document.getElementById('useBlockchain').checked
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.error) {
            alert('Error: ' + result.error);
        } else {
            alert('Kandidat berhasil ditambahkan');
            loadCurrentCandidates();
            document.getElementById('candidate-search').value = '';
            document.getElementById('search-results').innerHTML = '';
        }
    })
    .catch(error => {
        console.error('Error adding candidate:', error);
        alert('Terjadi kesalahan saat menambahkan kandidat');
    });
}

// Remove a candidate from the election
function removeCandidate(candidateId) {
    if (!confirm('Apakah Anda yakin ingin menghapus kandidat ini?')) {
        return;
    }
    
    fetch(`${BASE_URL}/api/admin/candidates/${candidateId}`, {
        method: 'DELETE',
        headers: {
            'Authorization': `Bearer ${AUTH_TOKEN}`
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.error) {
            alert('Error: ' + result.error);
        } else {
            alert('Kandidat berhasil dihapus');
            loadCurrentCandidates();
        }
    })
    .catch(error => {
        console.error('Error removing candidate:', error);
        alert('Terjadi kesalahan saat menghapus kandidat');
    });
}

// Show modal to select primary candidate for a running mate
function showPrimaryCandidateModal(runningMateId) {
    // Implementation for selecting a primary candidate for the running mate
    // This would show a modal with existing candidates or allow creating a new pair
}
```

## Conclusion

This implementation plan provides a comprehensive approach to integrating candidate management directly into the election editing process. By following these steps, the system will allow administrators to manage candidates more efficiently and ensure that only eligible candidates are added to elections.

The changes maintain the existing validation logic while improving the user experience by centralizing election management in one place. This approach also makes it easier to ensure that candidates meet the eligibility requirements for each election.