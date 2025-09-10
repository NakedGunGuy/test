// Quantity selector functionality
function changeQuantity(productId, change) {
    const input = document.getElementById('qty-' + productId);
    if (!input) return;
    
    const currentValue = parseInt(input.value) || 1;
    const min = parseInt(input.getAttribute('min')) || 1;
    const max = parseInt(input.getAttribute('max')) || 999;
    
    let newValue = currentValue + change;
    
    // Enforce min/max constraints
    if (newValue < min) newValue = min;
    if (newValue > max) newValue = max;
    
    // Update the input value
    input.value = newValue;
}

// Function to initialize quantity inputs with validation
function initializeQuantityInputs(container = document) {
    container.querySelectorAll('.qty-input').forEach(function(input) {
        // Remove existing listeners to prevent duplicates
        input.removeEventListener('input', handleQuantityInput);
        // Add event listener to prevent manual editing beyond limits
        input.addEventListener('input', handleQuantityInput);
    });
}

// Input validation handler
function handleQuantityInput() {
    const min = parseInt(this.getAttribute('min')) || 1;
    const max = parseInt(this.getAttribute('max')) || 999;
    let value = parseInt(this.value) || min;
    
    if (value < min) this.value = min;
    if (value > max) this.value = max;
}

// Initialize quantity selectors on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeQuantityInputs();
});

// Re-initialize quantity selectors after HTMX content updates
document.body.addEventListener('htmx:afterSettle', function(evt) {
    initializeQuantityInputs(evt.target);
});