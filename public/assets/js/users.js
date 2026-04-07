// User Management Interactivity
document.addEventListener('DOMContentLoaded', () => {
    console.log('Users Management initialized');

    // Handle Toggle Switch Change
    document.querySelectorAll('.status-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const row = this.closest('tr');
            const statusBadge = row.querySelector('.status-badge');
            
            if (this.checked) {
                // Set to Active
                statusBadge.innerText = 'Active';
                statusBadge.className = 'status-badge active';
                row.className = row.className.replace('inactive', 'active').replace('pending', 'active');
                console.log('User toggled to Active');
            } else {
                // Set to Inactive
                statusBadge.innerText = 'Inactive';
                statusBadge.className = 'status-badge inactive';
                row.className = row.className.replace('active', 'inactive').replace('pending', 'inactive');
                console.log('User toggled to Inactive');
            }
            
            // Re-apply filter to handle immediate hiding
            applyTableFilter(new URLSearchParams(window.location.search).get('filter') || 'all');
        });
    });

    // Handle initial filtering automatically based on URL query parameter
    const urlParams = new URLSearchParams(window.location.search);
    const currentFilter = urlParams.get('filter') || 'all'; // Default 'all'
    
    // Function to apply filter to table rows
    function applyTableFilter(filterValue) {
        const rows = document.querySelectorAll('tr.status-row');
        let visibleCount = 0;
        
        rows.forEach(row => {
            if (filterValue === 'all') {
                row.style.display = '';
                visibleCount++;
            } else if (row.classList.contains(filterValue)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Update pagination info (mocking)
        const infoEl = document.querySelector('.pagination-info');
        if (infoEl) {
            infoEl.innerText = `Showing 1 to ${visibleCount} of ${visibleCount} entries`;
        }
    }
    
    // Apply on load
    applyTableFilter(currentFilter);
});
