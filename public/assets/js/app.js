// Main App JavaScript
console.log('MCX Admin App Loaded');

document.addEventListener('DOMContentLoaded', () => {
    // Sidebar Users Dropdown Toggle
    const usersDropdownBtn = document.getElementById('usersDropdownBtn');
    const usersSubmenu = document.getElementById('usersSubmenu');

    if (usersDropdownBtn && usersSubmenu) {
        usersDropdownBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Toggle visibility
            const isExpanded = usersSubmenu.style.display === 'flex';
            
            if (isExpanded) {
                usersSubmenu.style.display = 'none';
                this.querySelector('.dropdown-arrow').style.transform = 'rotate(0deg)';
                this.classList.remove('active');
            } else {
                usersSubmenu.style.display = 'flex';
                this.querySelector('.dropdown-arrow').style.transform = 'rotate(180deg)';
                this.classList.add('active');
            }
        });
    }
});
