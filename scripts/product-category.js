// scripts/categories.js

document.addEventListener('DOMContentLoaded', function() {
    
  // 1. Initialize Cart Count from the global variable defined in PHP
  // If the variable isn't set, default to 0
  let currentCartCount = (typeof window.initialCartCount !== 'undefined') 
                         ? window.initialCartCount 
                         : 0;

  console.log('Page loaded, cart count:', currentCartCount);
  
  // Update badge immediately if count > 0
  if (currentCartCount > 0) {
      updateCartBadge(currentCartCount);
  }

  // 2. Setup Modal and Quantity Logic
  const modalElement = document.getElementById('addToCartModal');
  
  // Safety check: Only run this logic if the modal actually exists on the page
  if (modalElement) {
      const modal = new bootstrap.Modal(modalElement);
      const quantityInput = document.getElementById('quantityInput');
      const decreaseBtn = document.getElementById('decreaseBtn');
      const increaseBtn = document.getElementById('increaseBtn');
      const quantityFormInput = document.getElementById('quantityFormInput');

      if(decreaseBtn && increaseBtn && quantityInput) {
          decreaseBtn.addEventListener('click', function() {
              let value = parseInt(quantityInput.value);
              if (value > 1) {
                  quantityInput.value = value - 1;
                  quantityFormInput.value = value - 1;
              }
          });

          increaseBtn.addEventListener('click', function() {
              let value = parseInt(quantityInput.value);
              quantityInput.value = value + 1;
              quantityFormInput.value = value + 1;
          });

          quantityInput.addEventListener('change', function() {
              if (this.value < 1) this.value = 1;
              quantityFormInput.value = this.value;
          });
          
          // Hover effects
          [decreaseBtn, increaseBtn].forEach(btn => {
              btn.addEventListener('mouseover', function() {
                  this.style.transform = 'scale(1.1)';
                  this.style.boxShadow = '0 4px 15px rgba(102, 126, 234, 0.4)';
              });
              btn.addEventListener('mouseout', function() {
                  this.style.transform = 'scale(1)';
                  this.style.boxShadow = 'none';
              });
          });
      }

      // 3. Handle Form Submission
      const cartForm = document.getElementById('addToCartForm');
      if (cartForm) {
          cartForm.addEventListener('submit', function(e) {
              e.preventDefault();
              
              const formData = new FormData(this);
              
              fetch('add-to-cart.php', {
                  method: 'POST',
                  body: formData
              })
              .then(response => {
                  if (!response.ok) throw new Error('Network response was not ok');
                  return response.json();
              })
              .then(data => {
                  if (data.success) {
                      // Update cart count
                      if (data.cart_count !== undefined) {
                          currentCartCount = parseInt(data.cart_count);
                          updateCartBadge(currentCartCount);
                      }
                      
                      showNotification(data.message, 'success');
                      modal.hide();
                      
                      // Reset form
                      if(quantityInput) quantityInput.value = 1;
                      if(quantityFormInput) quantityFormInput.value = 1;
                  } else {
                      showNotification(data.message || 'Error adding to cart', 'error');
                  }
              })
              .catch(error => {
                  console.error('Fetch error:', error);
                  showNotification('Error adding to cart. Please try again.', 'error');
              });
          });
      }
      
      // Expose openCartModal to global scope so inline onclick="..." works
      window.openCartModal = function(productId, productName, productPrice) {
          if (!isUserLoggedIn()) {
              window.location.href = 'login.php';
              return;
          }

          document.getElementById('productIdInput').value = productId;
          document.getElementById('productName').textContent = productName;
          document.getElementById('productPrice').textContent = '$' + parseFloat(productPrice).toFixed(2);
          document.getElementById('quantityInput').value = 1;
          document.getElementById('quantityFormInput').value = 1;

          modal.show();
      };
  }
});

// Helper Functions

function showNotification(message, type = 'success') {
  const notification = document.createElement('div');
  notification.className = `toast-notification ${type === 'error' ? 'error' : ''}`;
  notification.innerHTML = `
  <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
  <span>${message}</span>
  `;
  document.body.appendChild(notification);

  setTimeout(() => {
      notification.style.animation = 'slideIn 0.4s reverse';
      setTimeout(() => {
          notification.remove();
      }, 400);
  }, 3500);
}

function updateCartBadge(count) {
  // Try multiple ways to find the cart icon
  let cartIcon = document.querySelector('a[href*="cart"]');
  
  if (!cartIcon) cartIcon = document.querySelector('.cart-link');
  if (!cartIcon) cartIcon = document.querySelector('[class*="cart"]');
  
  if (cartIcon) {
      if (cartIcon.style.position !== 'relative') {
          cartIcon.style.position = 'relative';
      }
      
      const oldBadge = cartIcon.querySelector('.cart-badge');
      if (oldBadge) oldBadge.remove();
      
      if (count > 0) {
          const badge = document.createElement('span');
          badge.className = 'cart-badge';
          badge.textContent = count;
          badge.style.cssText = `
              position: absolute;
              top: -8px;
              right: -8px;
              background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
              color: white;
              width: 28px;
              height: 28px;
              border-radius: 50%;
              display: flex;
              align-items: center;
              justify-content: center;
              font-weight: 800;
              font-size: 0.9rem;
              animation: pop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
              box-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);
              z-index: 99;
          `;
          cartIcon.appendChild(badge);
      }
  }
}

function isUserLoggedIn() {
  // Check the global variable set by PHP
  if (typeof window.isUserLoggedIn !== 'undefined') {
      return window.isUserLoggedIn;
  }
  return false;
}