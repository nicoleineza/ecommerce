document.getElementById("loginForm").addEventListener("submit", function(event) {
    event.preventDefault(); 

    let formData = new FormData(this);
    const loadingSpinner = document.getElementById("loading-spinner");
    const messageDiv = document.getElementById("message");
    
    loadingSpinner.style.display = "block"; 
    messageDiv.style.display = "none"; 

    fetch("../Controllers/customercontroller.php", { 
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        loadingSpinner.style.display = "none"; 
        messageDiv.className = ''; // Reset classes
        messageDiv.style.display = 'block'; 
        
        
        if (data.status === "success") {
            messageDiv.classList.add('success');
            messageDiv.textContent = "Login successful! Redirecting..."; 
            // Immediate redirect after successful login
            window.location.href = "../views/shop.php";
            
        } else {
            messageDiv.classList.add('error');
            messageDiv.textContent = "Invalid login credentials."; 
        }
    })
    .catch(error => {
        loadingSpinner.style.display = "none"; 
        messageDiv.classList.add('error');
        messageDiv.textContent = "An unexpected error occurred."; 
        messageDiv.style.display = "block"; 
        console.error("Error:", error);
    });
});
