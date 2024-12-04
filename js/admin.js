// Select all sidebar links and the content area
const sidebarLinks = document.querySelectorAll('.sidebar a');
const contentArea = document.getElementById('content-area');

// Add click event listeners to sidebar links
sidebarLinks.forEach(link => {
    link.addEventListener('click', function (e) {
        e.preventDefault(); n

       
        const targetUrl = this.getAttribute('data-target');

       
        contentArea.classList.add('loading');

        // Fetch the content via AJAX
        fetch(targetUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            })
            .then(data => {
                
                contentArea.classList.remove('loading');
                contentArea.innerHTML = data;
            })
            .catch(error => {
                
                contentArea.classList.remove('loading');
                contentArea.innerHTML = `<p>Error loading content: ${error.message}</p>`;
            });
    });
});
