document.addEventListener('DOMContentLoaded', function() {
    // Load header
    fetch('headerStaff-dashboard.html')
        .then(response => response.text())
        .then(data => {
            document.getElementById('headerContainer').innerHTML = data;

            // Fetch and update the unassigned appointment count
            updateUnassignedCount();

            // Attach logout event listener
            document.getElementById('logoutBtn').addEventListener('click', function() {
                localStorage.clear();
            });
        })
        .catch(error => console.error('Error loading header:', error));

    function updateUnassignedCount() {
        fetch('php/get-unassigned-appointments-count.php')
            .then(response => response.json())
            .then(data => {
                if (data.count !== undefined) {
                    document.getElementById('unassignedCount').innerText = `(${data.count})`;
                } else {
                    console.error('Invalid response format:', data);
                }
            })
            .catch(error => console.error('Error fetching unassigned appointments count:', error));
    }
});
