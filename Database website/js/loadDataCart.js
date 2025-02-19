document.addEventListener('DOMContentLoaded', () => {
    fetch('../getCart.php')
        .then((response) => {
            return response.text(); // Get raw response as text first
        })
        .then((text) => {
            try {
                const data = JSON.parse(text); // Try to parse the text as JSON
                if (data.error) {
                    console.error('Error:', data.error);
                    return; // Stop further execution if error
                }

                console.log('Cart Data:', data); // Check if cart data is returned correctly

                // Update cart count
                const cartCountSpan = document.querySelector('.cart-item-count');
                if (data.cartCount == 0) {
                    cartCountSpan.textContent = 0;
                }
                cartCountSpan.textContent = data.cartCount; // Update the cart count dynamically

                // Populate cart items
                const cartItemsContainer = document.querySelector('.cart-items');
                cartItemsContainer.innerHTML = ''; // Clear existing items before re-rendering
                let total = 0;

                data.cartItems.forEach((item) => {
                    const cartItem = document.createElement('article');
                    cartItem.className = 'cart-item';
                    cartItem.setAttribute('data-id', item.product_id); // Store product_id in the item container
                    cartItem.innerHTML = `
                        <img src="${item.product_image}" class="cart-item-img" alt="product" />
                        <div class="cart-item-info">
                            <h5 class="cart-item-name">${item.product_title}</h5>
                            <span class="cart-item-price">$${parseFloat(item.product_price).toFixed(2)}</span>
                            <span class="cart-item-quantity">Qty: ${item.quantity}</span>
                            <button class="cart-item-remove-btn" data-id="${item.product_id}">Remove</button>
                        </div>
                    `;
                    cartItemsContainer.appendChild(cartItem);
                    total += item.product_price * item.quantity;
                });

                // Update total
                const totalElement = document.getElementById('total');
                totalElement.textContent = `total : $${total.toFixed(2)}`;

                // Add remove functionality to each remove button
                document.querySelectorAll('.cart-item-remove-btn').forEach((button) => {
                    button.addEventListener('click', () => {
                        const productId = button.getAttribute('data-id');

                        // Call remove product from cart
                        fetch('../rmFromCart.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `product_id=${productId}`
                        })
                        .then((response) => response.json())
                        .then((data) => {
                            if (data.error) {
                                console.error('Error removing item:', data.error);
                                return;
                            }

                            // Update cart count
                            const cartCountSpan = document.querySelector('.cart-item-count');
                            cartCountSpan.textContent = data.cartCount;

                            // Remove the item from the DOM
                            const cartItemToRemove = document.querySelector(`.cart-item[data-id="${productId}"]`);
                            cartItemToRemove.remove();

                            // Update total
                            let total = 0;
                            data.cartItems.forEach((item) => {
                                total += item.product_price * item.quantity;
                            });
                            const totalElement = document.getElementById('total');
                            totalElement.textContent = `total : $${total.toFixed(2)}`;
                        })
                        .catch((error) => {
                            console.error('Error during remove operation:', error);
                        });
                    });
                });

            } catch (error) {
                console.error('Error parsing JSON:', error);
                console.error('Raw response:', text);
            }
        })
        .catch((error) => {
            console.error('Error3:', error);
        });
});
