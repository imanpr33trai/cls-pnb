<!-- /admin/pages/users/view_users.php -->
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Manage Users</h1>

    <!-- Table to display users -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h6 class="font-semibold text-gray-700">All Registered Users</h6>
        </div>
        <div class="p-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="usersTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Provider</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="users-table-body" class="bg-white divide-y divide-gray-200">
                    <tr><td colspan="7" class="text-center py-4">Loading users...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal for Viewing User Details -->
<div id="userDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
  <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
    <div class="flex justify-between items-center border-b pb-3">
      <h5 class="text-lg font-bold">User Details</h5>
      <button id="closeModalBtn" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
      </button>
    </div>
    <div class="modal-body mt-4" id="userDetailsContent">
      <!-- JS will populate this -->
    </div>
    <div class="modal-footer mt-4 pt-4 border-t text-right">
      <button id="closeModalFooterBtn" type="button" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">Close</button>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById('users-table-body');
    const userDetailsContent = document.getElementById('userDetailsContent');
    const userDetailsModal = document.getElementById('userDetailsModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const closeModalFooterBtn = document.getElementById('closeModalFooterBtn');

    function showModal() {
        userDetailsModal.classList.remove('hidden');
    }

    function hideModal() {
        userDetailsModal.classList.add('hidden');
    }

    closeModalBtn.addEventListener('click', hideModal);
    closeModalFooterBtn.addEventListener('click', hideModal);

    async function fetchUsers() {
        try {
            const response = await fetch('../../api/v1/users.php');
            if (!response.ok) throw new Error(`HTTP Error: ${response.status}`);
            const users = await response.json();

            tableBody.innerHTML = '';
            if (users.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-4">No users found.</td></tr>';
                return;
            }

            users.forEach(user => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${user.id}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${escapeHtml(user.first_name)} ${escapeHtml(user.last_name)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${escapeHtml(user.email)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${user.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
                            ${user.status}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${escapeHtml(user.auth_provider)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${new Date(user.created_at).toLocaleDateString()}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button class="text-indigo-600 hover:text-indigo-900 view-btn" data-userid="${user.id}">View</button>
                        <button class="text-red-600 hover:text-red-900 ml-4 delete-btn" data-userid="${user.id}">Delete</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        } catch (error) {
            console.error("Failed to fetch users:", error);
            tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-red-500">Error loading users.</td></tr>`;
        }
    }

    tableBody.addEventListener('click', async function(event) {
        const target = event.target;
        const userId = target.dataset.userid;

        if (!userId) return;

        if (target.classList.contains('view-btn')) {
            userDetailsContent.innerHTML = '<p>Loading details...</p>';
            showModal();
            try {
                const response = await fetch(`../../api/v1/users.php?id=${userId}`);
                if (!response.ok) throw new Error('User not found.');
                const user = await response.json();
                
                userDetailsContent.innerHTML = `
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                        <dt class="font-semibold text-gray-600">User ID</dt><dd class="text-gray-800">${user.id}</dd>
                        <dt class="font-semibold text-gray-600">Full Name</dt><dd class="text-gray-800">${escapeHtml(user.first_name)} ${escapeHtml(user.last_name)}</dd>
                        <dt class="font-semibold text-gray-600">Email</dt><dd class="text-gray-800">${escapeHtml(user.email)}</dd>
                        <dt class="font-semibold text-gray-600">Phone</dt><dd class="text-gray-800">${user.phone || 'N/A'}</dd>
                        <dt class="font-semibold text-gray-600">Country</dt><dd class="text-gray-800">${user.country || 'N/A'}</dd>
                        <dt class="font-semibold text-gray-600">Status</dt><dd class="text-gray-800">${user.status}</dd>
                        <dt class="font-semibold text-gray-600">Auth Provider</dt><dd class="text-gray-800">${user.auth_provider}</dd>
                        <dt class="font-semibold text-gray-600">Google ID</dt><dd class="text-gray-800">${user.google_id || 'N/A'}</dd>
                        <dt class="font-semibold text-gray-600">GitHub ID</dt><dd class="text-gray-800">${user.github_id || 'N/A'}</dd>
                        <dt class="font-semibold text-gray-600">Joined On</dt><dd class="text-gray-800">${new Date(user.created_at).toLocaleString()}</dd>
                    </dl>
                `;
            } catch (error) {
                userDetailsContent.innerHTML = `<p class="text-red-500">${error.message}</p>`;
            }
        }

        if (target.classList.contains('delete-btn')) {
            if (confirm(`Are you sure you want to delete user #${userId}? This action cannot be undone.`)) {
                try {
                    const response = await fetch(`../../api/v1/users.php?id=${userId}`, { method: 'DELETE' });
                    const result = await response.json();

                    if (response.ok) {
                        alert(result.message);
                        target.closest('tr').remove();
                    } else {
                        throw new Error(result.error || 'Deletion failed.');
                    }
                } catch (error) {
                    alert(`Error: ${error.message}`);
                }
            }
        }
    });

    function escapeHtml(text) {
        if (text === null || typeof text === 'undefined') return '';
        return text.toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
    
    fetchUsers();
});
</script>
