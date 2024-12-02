// Select all sidebar links and the content area
const sidebarLinks = document.querySelectorAll('.sidebar a');
const contentArea = document.getElementById('content-area');

// Add click event listeners to sidebar links
sidebarLinks.forEach(link => {
    link.addEventListener('click', function (e) {
        e.preventDefault(); // Prevent default redirection

        // Get the target URL from data-target attribute
        const targetUrl = this.getAttribute('data-target');

        // Add the "loading" class to show the loading indicator
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
                // Remove the "loading" class and display the fetched content
                contentArea.classList.remove('loading');
                contentArea.innerHTML = data;
            })
            .catch(error => {
                // Remove the "loading" class and show an error message
                contentArea.classList.remove('loading');
                contentArea.innerHTML = `<p>Error loading content: ${error.message}</p>`;
            });
    });
});
