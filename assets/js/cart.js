document.addEventListener('DOMContentLoaded', function() {
    const orderForm = document.getElementById('order-form');
    const totalPriceElement = document.getElementById('total-price');
    const modal = document.getElementById('confirmation-modal');
    const closeModal = document.querySelector('.close-modal');
    const continueShoppingBtn = document.querySelector('.continue-shopping');
    
    // Calculate initial total price
    updateTotalPrice();
    
    // Listen for ingredient changes
    orderForm.addEventListener('change', updateTotalPrice);
    document.getElementById('quantity').addEventListener('input', updateTotalPrice);
    
    // Handle form submission
    orderForm.addEventListener('submit', function(e) {
        e.preventDefault();
        addToCart();
    });
    
    // Modal controls
    closeModal.addEventListener('click', () => modal.style.display = 'none');
    continueShoppingBtn.addEventListener('click', () => modal.style.display = 'none');
    window.addEventListener('click', (e) => {
        if (e.target === modal) modal.style.display = 'none';
    });
    
    function updateTotalPrice() {
        const breadPrice = parseFloat('<?php echo $currentBread["price"]; ?>');
        const quantity = parseInt(document.getElementById('quantity').value) || 1;
        let total = breadPrice * quantity;
        
        // Add standard ingredients
        document.querySelectorAll('input[name="standard_ingredients[]"]:checked').forEach(ingredient => {
            total += parseFloat(ingredient.nextElementSibling.textContent.replace('+SRD ', ''));
        });
        
        // Add extra ingredients
        document.querySelectorAll('input[name="extra_ingredients[]"]:checked').forEach(ingredient => {
            total += parseFloat(ingredient.nextElementSibling.textContent.replace('+SRD ', ''));
        });
        
        totalPriceElement.textContent = `SRD ${total.toFixed(2)}`;
    }
    
    function addToCart() {
        const formData = new FormData(orderForm);
        
        fetch('../cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show confirmation modal
                modal.style.display = 'block';
                
                // Update cart count in header
                if (data.cart_count) {
                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.cart_count;
                    }
                }
            } else {
                alert('Error: ' + (data.message || 'Could not add to cart'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while adding to cart');
        });
    }
});