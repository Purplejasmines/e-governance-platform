// JavaScript code to handle the sidebar active state and service request actions

// ==========================
// Sidebar Active State Logic
// ==========================
document.addEventListener("DOMContentLoaded", () => {
    const currentUrl = window.location.href;
    const navLinks = document.querySelectorAll('.admin-sidebar a, .user-sidebar a');
    navLinks.forEach(link => {
        link.parentElement.classList.remove('active');
        if (link.href === currentUrl) {
            link.parentElement.classList.add('active');
        }
    });

    const logoutLink = document.querySelector('.admin-sidebar li.logout a');
    if (logoutLink && currentUrl.includes('logout')) {
        logoutLink.parentElement.classList.add('active');
    }

    // ==========================
    // Fetch and Display Requests
    // ==========================
    fetch("service-request.php?fetch=requests")
        .then(res => {
            if (!res.ok) {
                throw new Error(`HTTP error! Status: ${res.status}`);
            }
            return res.json();
        })
        .then(data => {
            const tableBody = document.getElementById("request-body");

            if (!Array.isArray(data) || data.length === 0) {
                console.warn("No service requests found.");
                tableBody.innerHTML = "<tr><td colspan='8'>No service requests available.</td></tr>";
                return;
            }

            data.forEach(request => {
                const row = document.createElement("tr");
                row.setAttribute("data-id", request.id);

                row.innerHTML = `
                    <td>#${request.id}</td>
                    <td>${request.email}</td>
                    <td>${request.type}</td>
                    <td>${request.category}</td>
                    <td class="status ${request.status.toLowerCase()}">${request.status}</td>
                    <td>${new Date(request.submitted_at).toLocaleString()}</td>
                    <td>${request.details || "No details provided"}</td>
                `;

                // Add action buttons
                const approveBtn = document.createElement("button");
                approveBtn.className = "approve-btn";
                approveBtn.textContent = "Approve";
                approveBtn.addEventListener("click", () => handleAction(request.id, "Approved"));

                const rejectBtn = document.createElement("button");
                rejectBtn.className = "reject-btn";
                rejectBtn.textContent = "Reject";
                rejectBtn.addEventListener("click", () => handleAction(request.id, "Rejected"));

                const feedbackBtn = document.createElement("button");
                feedbackBtn.className = "feedback-btn";
                feedbackBtn.textContent = "Feedback";
                feedbackBtn.addEventListener("click", () => showFeedbackForm(request.id));

                const actionCell = document.createElement("td");
                actionCell.appendChild(approveBtn);
                actionCell.appendChild(rejectBtn);
                actionCell.appendChild(feedbackBtn);

                row.appendChild(actionCell);
                tableBody.appendChild(row);
            });
        })
        .catch(err => {
            console.error("Error loading service requests:", err);
            const tableBody = document.getElementById("request-body");
            tableBody.innerHTML = "<tr><td colspan='8'>Failed to load service requests.</td></tr>";
        });

    // ==========================
    // Constants for Status
    // ==========================
    const STATUS_APPROVED = "Approved";
    const STATUS_REJECTED = "Rejected";

    // ==========================
    // Handle Approve/Reject Actions
    // ==========================
    function handleAction(id, newStatus) {
        fetch('update_status.php', {
            method: 'POST',
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id, status: newStatus })
        })
        .then(res => {
            if (!res.ok) {
                throw new Error(`HTTP error! Status: ${res.status}`);
            }
            return res.json();
        })
        .then(response => {
            alert(response.message);
            const row = document.querySelector(`tr[data-id="${id}"]`);
            if (row) {
                const statusCell = row.querySelector(".status");
                statusCell.textContent = newStatus;
                statusCell.className = `status ${newStatus.toLowerCase()}`;
            }
        })
        .catch(error => {
            console.error("Error updating status:", error);
            alert("Failed to update status. Please try again.");
        });
    }

    // ==========================
    // Dynamic Dropdown Logic
    // ==========================
    const serviceType = document.getElementById("type");
    const category = document.getElementById("category");

    if (serviceType && category) {
        console.log("Service type and category dropdowns found.");

        const categories = {
            Certificate: ["Birth", "Marriage", "Police Clearance"],
            Permit: ["Business", "Construction", "Event"]
        };

        serviceType.addEventListener("change", function () {
            console.log("Service type selected:", this.value);
            category.innerHTML = '<option value="">-- Select Category --</option>';
            if (this.value) {
                categories[this.value].forEach(cat => {
                    console.log("Adding category:", cat);
                    let option = document.createElement("option");
                    option.value = cat;
                    option.textContent = cat;
                    category.appendChild(option);
                });
            }
        });
    } else {
        console.warn("Service type and category dropdowns are not present on this page.");
    }

    // ==========================
    // Submit Request Logic
    // ==========================
    const form = document.getElementById("requestForm"); // Target by ID

    form.addEventListener("submit", function (e) {
        e.preventDefault(); // Prevent the default form submission behavior

        const selectedType = serviceType.value;
        const selectedCategory = category.value;
        const requestDetails = document.getElementById("details").value;
        const email = document.getElementById("email").value;

        if (!selectedType || !selectedCategory) {
            alert("Please select a service type and category.");
            return;
        }

        const requestData = {
            type: selectedType,
            category: selectedCategory,
            details: requestDetails,
            email: email
        };

        console.log("Submitting request data:", requestData); // Debugging log

        fetch("/egovernance/Users/submit-request.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            credentials: "include", // Include cookies for session authentication
            body: JSON.stringify(requestData)
        })
            .then(res => {
                if (!res.ok) {
                    throw new Error(`HTTP error! Status: ${res.status}`);
                }
                return res.json();
            })
            .then(response => {
                alert(response.message);
            })
            .catch(error => {
                console.error("Error submitting request:", error);
            });
    });
});

// ==========================
// Feedback Form Logic
// ==========================
function showFeedbackForm(requestId) {
    // Set the request ID in the hidden input field
    document.getElementById('feedbackRequestId').value = requestId;

    // Show the feedback modal
    document.getElementById('feedbackModal').style.display = 'flex';
}

function closeFeedbackModal() {
    // Hide the feedback modal
    document.getElementById('feedbackModal').style.display = 'none';
}

// Handle feedback form submission
document.getElementById('feedbackForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const requestId = document.getElementById('feedbackRequestId').value;
    const rating = document.getElementById('rating').value;
    const comments = document.getElementById('comments').value;

    fetch('submit-feedback.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ request_id: requestId, rating: rating, comments: comments })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Feedback submitted successfully!');
            closeFeedbackModal();
        } else {
            alert('Failed to submit feedback. Please try again.');
        }
    })
    .catch(err => {
        console.error('Error submitting feedback:', err);
        alert('An error occurred. Please try again.');
    });
});
