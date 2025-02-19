document.querySelectorAll('.add-to-cart').forEach((button) => {
    button.addEventListener('click', () => {
        const productId = button.getAttribute('data-id');
        const productTitle = button.getAttribute('data-title');
        const productPrice = parseFloat(button.getAttribute('data-price'));
        const productImage = button.getAttribute('data-image');

        fetch('../addToCart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${productId}&product_title=${productTitle}&product_price=${productPrice}&product_image=${encodeURIComponent(productImage)}`
        })
        .then((response) => response.json())
        .then((data) => {
            if (data.error) {
                // Handle stock errors
                if (data.error === 'Not enough stock available') {
                    alert('Sorry, there is not enough stock available for this product.');
                } else if (data.error === 'Out of stock') {
                    alert('This product is currently out of stock.');
                } else {
                    console.error('Error:', data.error);
                }
                return;
            }

            // Update cart count
            const cartCountSpan = document.querySelector('.cart-item-count');
            if (data.cartCount == 0 || data.cartCount == null ) {
                cartCountSpan.textContent = 0;
            } else {
                cartCountSpan.textContent = data.cartCount; // Update the cart count dynamically
            }

            // Update total price dynamically
            let total = document.querySelector('.cart-total');
            let oldTotal = parseFloat(total.textContent.replace('Total : $', '')) || 0;
            total.textContent = `Total : $${(productPrice + oldTotal).toFixed(2)}`;  // Update with new total

            // Check if the product already exists in the cart
            const existingItem = document.querySelector(`.cart-item[data-id='${productId}']`);
            if (existingItem) {
                // Increase the quantity by 1
                const quantitySpan = existingItem.querySelector('.cart-item-quantity');
                let currentQuantity = parseInt(quantitySpan.textContent.replace('Qty: ', ''));
                quantitySpan.textContent = `Qty: ${currentQuantity + 1}`;
            } else {
                // Add the newly added item to the cart HTML
                const cartItemsContainer = document.querySelector('.cart-items');
                const cartItem = document.createElement('article');
                cartItem.className = 'cart-item';
                cartItem.setAttribute('data-id', productId);
                cartItem.innerHTML = `
                    <img src="${productImage}" class="cart-item-img" alt="product" />
                    <div class="cart-item-info">
                        <h5 class="cart-item-name">${productTitle}</h5>
                        <span class="cart-item-price">$${productPrice.toFixed(2)}</span>
                        <span class="cart-item-quantity">Qty: 1</span>
                        <button class="cart-item-remove-btn" data-id="${productId}">Remove</button>
                    </div>
                `;
                cartItemsContainer.appendChild(cartItem);

                // Adding removal functionality
                cartItem.querySelector('.cart-item-remove-btn').addEventListener('click', () => {
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

                        // Update cart count dynamically after removal
                        const cartCountSpan = document.querySelector('.cart-item-count');
                        cartCountSpan.textContent = data.cartCount;

                        // Update total dynamically
                        let total = document.querySelector('.cart-total');
                        const cartTotal = data.cartTotal;
                        total.textContent = `Total : $${(cartTotal && !isNaN(cartTotal)) ? cartTotal : '0.00'}`;

                        // Remove the item from the cart HTML
                        const cartItemToRemove = document.querySelector(`.cart-item[data-id='${productId}']`);
                        if (cartItemToRemove) {
                            cartItemToRemove.remove(); // Ensure the item is removed from the DOM
                        }
                    })
                    .catch((error) => {
                        console.error('Error during item removal:', error);
                    });
                });
            }
        })
        .catch((error) => {
            console.error('Error adding to cart:', error);
        });
    });
});
