// scripts/product.js

document.addEventListener('DOMContentLoaded', function() {
    
  // 1. Handle Color and Size selection (Pill Buttons)
  const pillButtons = document.querySelectorAll('label .pill-btn');
  
  pillButtons.forEach(btn => {
      btn.addEventListener('click', e => {
          // Find the label wrapper
          const label = e.target.closest('label');
          const input = label.querySelector('input');

          // Find all other inputs with the same name (e.g., all 'color' inputs)
          // and remove the 'selected' class from their buttons
          document.querySelectorAll(`input[name="${input.name}"]`).forEach(r => {
              const parentBtn = r.parentElement.querySelector('.pill-btn');
              if(parentBtn) parentBtn.classList.remove('selected');
          });

          // Select the clicked one
          input.checked = true;
          btn.classList.add('selected');
      });
  });

  // 2. Handle Quantity and Price Calculation
  const quantityInput = document.getElementById('quantity-input');
  const totalPriceSpan = document.getElementById('total-price');
  
  // Check if these elements exist (to prevent errors if product not found)
  if (quantityInput && totalPriceSpan) {
      
      // Get the price from the global variable defined in PHP
      // Default to 0 if not set
      const unitPrice = (typeof window.productPrice !== 'undefined') 
                        ? parseFloat(window.productPrice) 
                        : 0.00;

      quantityInput.addEventListener('input', () => {
          let qty = parseInt(quantityInput.value);
          
          // Validate input
          if(isNaN(qty) || qty < 1) {
              qty = 1;
              // Optional: reset the input value visually if you want
              // quantityInput.value = 1; 
          }
          
          // Calculate and display with 2 decimals
          const total = (unitPrice * qty).toFixed(2);
          totalPriceSpan.textContent = total;
      });
  }
});