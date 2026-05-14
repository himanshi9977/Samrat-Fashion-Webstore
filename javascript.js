//SIGN-IN
        function togglePopUp() {
         
            const overlay = document.getElementById('popup');
            overlay.classList.toggle('show');
        }

        function toggleOTP(){
            const overlay = document.getElementById('otp');
            overlay.classList.toggle('show');
        }

        window.onclick = function(event) {
            const overlay = document.getElementById('popup');
            const overlay2 = document.getElementById('popup');
            if (event.target == overlay) {
                overlay.classList.remove('show');
                overlay2.classList.remove('show');
            }
        }

        function toggleclose(){
            const overlay = document.getElementById('otp');
            const overlay2 = document.getElementById('popup');
            overlay.classList.remove('show');
            overlay2.classList.remove('show');
        }

    async function requestOTP(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    const response = await fetch('auth_request.php', {
        method: 'POST',
        body: formData
    });
    
    const result = await response.text();
    if (result === "otp_sent") {
        togglePopUp(); // Close Email Popup
        toggleOTP();   // Open OTP Input Popup (which you already have)
    } else {
        alert("Error: " + result);
    }
}

async function finalVerify(event) {
    event.preventDefault();
    const otp = document.getElementById('otp-input').value;
    
    const formData = new FormData();
    formData.append('otp', otp);

    const response = await fetch('verify_otp.php', {
        method: 'POST',
        body: formData
    });

    if (await response.text() === "success") {
        alert("Logged in successfully!");
        window.location.reload();
    } else {
        alert("Incorrect OTP. Please try again.");
    }
}

// This function should be triggered when the profile icon is clicked
async function handleProfileClick() {
    try {
        const response = await fetch('check_session.php');
        const status = await response.json();

        if (status.loggedIn) {
            // If logged in, show account details or a logout option
            alert("Logged in as: " + status.email);
            // Optional: Redirect to a profile page or show a 'Logout' button
            // window.location.href = 'profile.html'; 
        } else {
            // If not logged in, show the original login popup
            togglePopUp(); 
        }
    } catch (error) {
        console.error("Session check failed", error);
    }
}


window.addEventListener('DOMContentLoaded', async () => {
    const response = await fetch('check_session.php');
    const status = await response.json();
    
    const profileBtn = document.getElementById('profile-nav-btn'); // Use your actual ID
    
    if (status.loggedIn) {
        // Change the look of the icon or text to show active session
        profileBtn.innerHTML = '<i class="fa fa-user"></i> Logout'; 
        // Example: change click behavior to logout
        profileBtn.onclick = logoutUser;
    }
});

function logoutUser() {
    if(confirm("Do you want to logout?")) {
        window.location.href = 'logout.php';
    }
}

async function handleProfileClick() {
    const response = await fetch('check_session.php'); // We created this earlier
    const status = await response.json();

    if (status.loggedIn) {
        // Show a "Logged In" status or a simple dropdown
        if(confirm("Logged in as: " + status.email + "\nDo you want to logout?")) {
            window.location.href = 'logout.php';
        }
    } else {
        togglePopUp(); // Show the login/OTP popup
    }
}


async function addToCart(productId, productSize, qty) {
    try {
        const response = await fetch('add_to_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                pid: productId, 
                size: productSize,
                quantity: qty
            })
        });

        const result = await response.json();

        if (result.success) {
            alert("Success! Item added to your cart.");
        } else {
            if (result.message === "Please login first") {
                alert("Please login to start shopping!");
                togglePopUp(); // Opens your login modal
            } else {
                alert(result.message);
            }
        }
    } catch (error) {
        console.error("Cart Error:", error);
    }
}


async function liveSearch(query) {
    const searchResults = document.getElementById('search-results-dropdown');
    
    if (query.length < 2) {
        searchResults.style.display = 'none';
        return;
    }

    const response = await fetch(`search.php?q=${query}`);
    const products = await response.json();

    if (products.length > 0) {
        searchResults.style.display = 'block';
        searchResults.innerHTML = products.map(p => `
            <a href="products.html?id=${p.pid}" class="list-group-item list-group-item-action d-flex align-items-center">
                <img src="${p.pimg}" width="40" class="me-3 rounded">
                <div>
                    <div class="fw-bold" style="font-size: 14px;">${p.pname}</div>
                    <small class="text-muted">₹${p.price}</small>
                </div>
            </a>
        `).join('');
    } else {
        searchResults.innerHTML = '<div class="list-group-item text-muted">No products found</div>';
    }
}


async function postReview() {
    const params = new URLSearchParams(window.location.search);
    const pid = params.get("id");
    const rating = document.getElementById('star-rating').value;
    const comment = document.getElementById('review-text').value;

    const formData = new FormData();
    formData.append('pid', pid);
    formData.append('rating', rating);
    formData.append('comment', comment);

    const res = await fetch('submit_review.php', { method: 'POST', body: formData });
    const result = await res.json();
    if(result.status === "success") {
        location.reload();
    } else {
        alert(result.message);
    }
}

async function loadReviews(pid) {
    const response = await fetch(`fetch_reviews.php?pid=${pid}`);
    const data = await response.json(); // Expecting { reviews: [], avg: 4.5, count: 12 }
    
    const container = document.getElementById('reviews-display-container');
    
    if (data.reviews.length === 0) {
        container.innerHTML = "<p class='text-muted'>No reviews yet. Be the first to rate this!</p>";
        return;
    }

    // Update the Summary Box
    document.getElementById('avg-rating-big').innerText = data.avg;
    document.getElementById('total-reviews-count').innerText = data.count;

    // Build the list
    container.innerHTML = data.reviews.map(rev => {
        let stars = '';
        for(let i=1; i<=5; i++) {
            stars += `<i class="fa-solid fa-star ${i <= rev.rating ? 'checked' : 'text-light'}"></i>`;
        }

        return `
            <div class="review-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="review-stars">${stars}</div>
                    <span class="review-date">${rev.date}</span>
                </div>
                <h6 class="fw-bold mb-1">${rev.username || 'Verified Buyer'}</h6>
                <p class="text-secondary mb-0" style="font-size: 0.95rem;">${rev.comment}</p>
            </div>
        `;
    }).join("");
}

async function loadOrderHistory() {
    const container = document.getElementById('orders-history-container');
    if (!container) return;

    try {
        const response = await fetch('fetch_orders.php');
        const orders = await response.json();

        if (orders.length === 0) {
            container.innerHTML = `<p class="text-muted small">No recent orders.</p>`;
            return;
        }

        container.innerHTML = orders.map(order => `
            <div class="d-flex align-items-center justify-content-between py-3 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <img src="${order.pimg}" style="width: 50px; height: 65px; object-fit: cover; border: 1px solid #eee;">
                    <div>
                        <h6 class="mb-0 fw-bold text-uppercase" style="font-size: 13px;">${order.pname || 'Item Purchased'}</h6>
                        <small class="text-muted">Order ID: #${order.oid}</small>
                    </div>
                </div>
                <div class="text-end">
                    <div class="small fw-bold">${new Date(order.order_date).toLocaleDateString()}</div>
                    <div class="small text-success text-uppercase" style="font-size: 10px;">Paid</div>
                </div>
            </div>
        `).join('');

    } catch (error) {
        console.error("Error loading history:", error);
    }
}

document.addEventListener('DOMContentLoaded', loadOrderHistory);