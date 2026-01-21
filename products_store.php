<?php
require_once __DIR__ . '/includes/db_connection.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/product_manager.php';

$base = BASE_PATH;
$pm = new ProductManager($db);

$search = $_GET['search'] ?? '';
$category_id = $_GET['category'] ?? '';

$products = $pm->get_products(
    $category_id ? (int)$category_id : null,
    $search ? $search : null
);
$categories = $pm->get_categories();

// Get cart data for sidebar
$cart = $_SESSION['cart'] ?? [];
$cart_items = [];
$subtotal = 0;

foreach ($cart as $product_id => $quantity) {
    $product = $pm->get_product($product_id);
    if ($product) {
        $item_total = $product['price'] * $quantity;
        $cart_items[] = [
            'product_id' => $product_id,
            'product_name' => $product['product_name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'item_total' => $item_total,
            'image' => $product['image'],
            'type' => $product['product_type']
        ];
        $subtotal += $item_total;
    }
}

// Calculate shipping
$shipping = 0;
if (count($cart_items) > 0) {
    $has_physical = false;
    foreach ($cart_items as $item) {
        if ($item['type'] === 'physical') {
            $has_physical = true;
            break;
        }
    }
    if ($has_physical && $subtotal > 0) {
        $shipping = 10; // Flat rate
    }
}

$total = $subtotal + $shipping;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Melody Masters</title>
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/store.css">
</head>
<body>
    <header>
        <nav class="container">
            <a href="<?php echo $base; ?>/index.php" class="logo">Melody Masters</a>
            <ul class="nav-links">
                <li><a href="<?php echo $base; ?>/products_store.php">Shop</a></li>
                <li><a href="<?php echo $base; ?>/products.php">Browse All</a></li>
                <?php if (is_logged_in()): ?>
                    <?php if (has_role('admin')): ?>
                        <li><a href="<?php echo $base; ?>/admin/dashboard.php">Admin</a></li>
                    <?php elseif (has_role('staff')): ?>
                        <li><a href="<?php echo $base; ?>/staff/dashboard.php">Staff</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo $base; ?>/customer/dashboard.php">My Account</a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo $base; ?>/public/logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="<?php echo $base; ?>/login.php">Login</a></li>
                    <li><a href="<?php echo $base; ?>/signup.php">Sign Up</a></li>
                <?php endif; ?>
                <li>
                    <button class="cart-icon-btn" onclick="toggleCartSidebar()" title="View Cart">
                        🛒 <span class="cart-count-header"><?php echo count($cart_items); ?></span>
                    </button>
                </li>
            </ul>
        </nav>
    </header>

    <div class="store-wrapper">
        <!-- Products Section -->
        <main class="store-main">
            <div class="container">
                <h1>🎵 Shop Instruments</h1>
                <p class="store-subtitle">Browse our collection and add items to your cart</p>
                
                <!-- Search & Filter -->
                <div class="card filter-card">
                    <form method="GET" action="" class="filter-form">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="search">Search Products</label>
                            <input type="text" id="search" name="search" placeholder="Guitar, Piano, Drums..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="category">Category</label>
                            <select id="category" name="category">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['category_id']; ?>" <?php echo ($category_id == $cat['category_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['category_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Search</button>
                    </form>
                </div>

                <!-- Products Grid -->
                <?php if (empty($products)): ?>
                    <div class="card empty-state">
                        <h3>No Products Found</h3>
                        <p>Try adjusting your search or filters to find what you're looking for.</p>
                    </div>
                <?php else: ?>
                    <div class="products-grid-store">
                        <?php foreach ($products as $product): ?>
                            <div class="product-card-store">
                                <div class="product-image-store">
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="<?php echo $base; ?>/assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                                    <?php else: ?>
                                        <div class="placeholder-image">
                                            <span>🎸</span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="product-type-badge">
                                        <?php echo ($product['product_type'] === 'digital') ? '📱 Digital' : '📦 Physical'; ?>
                                    </div>
                                </div>
                                <div class="product-info-store">
                                    <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                                    <?php if (!empty($product['brand'])): ?>
                                        <p class="product-brand">by <?php echo htmlspecialchars($product['brand']); ?></p>
                                    <?php endif; ?>
                                    <p class="product-desc"><?php echo htmlspecialchars(substr($product['description'] ?? '', 0, 80)); ?>...</p>
                                    
                                    <div class="product-footer">
                                        <span class="product-price-store">$<?php echo number_format($product['price'], 2); ?></span>
                                        <span class="product-stock-store">
                                            <?php if ($product['product_type'] === 'physical'): ?>
                                                <?php echo ((int)$product['stock'] > 0) ? '✓ In Stock' : '⨯ Out'; ?>
                                            <?php else: ?>
                                                ✓ Available
                                            <?php endif; ?>
                                        </span>
                                    </div>

                                    <?php if ($product['product_type'] === 'physical' && (int)$product['stock'] <= 0): ?>
                                        <button class="btn btn-disabled" disabled>Out of Stock</button>
                                    <?php else: ?>
                                        <form class="add-to-cart-form" data-product-id="<?php echo $product['product_id']; ?>">
                                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                            <div style="display: flex; gap: 0.5rem; align-items: center;">
                                                <input type="number" name="quantity" value="1" min="1" max="10" class="qty-input">
                                                <button type="button" class="btn btn-primary btn-add-cart" onclick="addToCartAjax(this)">Add to Cart</button>
                                            </div>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>

        <!-- Cart Sidebar -->
        <aside class="cart-sidebar" id="cartSidebar">
            <div class="cart-header">
                <h2>🛒 Your Cart</h2>
                <button class="cart-close" onclick="toggleCartSidebar()">✕</button>
            </div>

            <?php if (empty($cart_items)): ?>
                <div class="cart-empty">
                    <div class="empty-icon">🛍️</div>
                    <p>Your cart is empty</p>
                    <small>Add items to get started!</small>
                </div>
            <?php else: ?>
                <div class="cart-items-list">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item">
                            <div class="cart-item-image">
                                <?php if (!empty($item['image'])): ?>
                                    <img src="<?php echo $base; ?>/assets/images/products/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                                <?php else: ?>
                                    <div class="placeholder-image-small">🎵</div>
                                <?php endif; ?>
                            </div>
                            <div class="cart-item-details">
                                <h4><?php echo htmlspecialchars($item['product_name']); ?></h4>
                                <p class="cart-item-price">$<?php echo number_format($item['price'], 2); ?> × <span class="item-qty-display"><?php echo $item['quantity']; ?></span></p>
                                <p class="cart-item-total">Total: $<span class="item-total-display"><?php echo number_format($item['item_total'], 2); ?></span></p>
                            </div>
                            <button type="button" class="cart-item-remove" onclick="removeFromCartAjax(<?php echo $item['product_id']; ?>)" title="Remove">🗑️</button>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="cart-summary">
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span>$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <?php if ($shipping > 0): ?>
                        <div class="summary-row">
                            <span>Shipping:</span>
                            <span>$<?php echo number_format($shipping, 2); ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span>$<?php echo number_format($total, 2); ?></span>
                    </div>
                </div>

                <div class="cart-actions">
                    <a href="<?php echo $base; ?>/cart.php" class="btn btn-primary btn-block">Proceed to Order</a>
                </div>
            <?php endif; ?>
        </aside>
    </div>

    <footer>
        <p>&copy; 2026 Melody Masters. All rights reserved.</p>
        <p>Your source for quality music instruments and accessories.</p>
    </footer>

    <script>
        function toggleCartSidebar() {
            const sidebar = document.getElementById('cartSidebar');
            sidebar.classList.toggle('open');
            document.body.classList.toggle('cart-open');
        }

        // Close cart when clicking outside on mobile
        // Close cart when clicking outside
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('cartSidebar');
            const toggleBtn = document.querySelector('.cart-icon-btn');
            
            if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
                sidebar.classList.remove('open');
                document.body.classList.remove('cart-open');
            }
        });

        // AJAX Add to Cart
        function addToCartAjax(button) {
            const form = button.closest('form');
            const productId = form.querySelector('input[name="product_id"]').value;
            const quantity = form.querySelector('input[name="quantity"]').value;

            fetch('<?php echo $base; ?>/add_to_cart_ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'product_id=' + productId + '&quantity=' + quantity
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP error! status: ' + response.status);
                }
                return response.text();
            })
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        updateCartSidebar(data.cart_items, data.subtotal, data.shipping, data.total);
                        updateCartCount(data.cart_items.length);
                        showNotification('Item added to cart!');
                    } else {
                        showNotification('Error: ' + data.message, 'error');
                    }
                } catch (e) {
                    console.error('JSON parse error:', e);
                    console.error('Response text:', text);
                    showNotification('Error: Invalid server response', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error adding item to cart: ' + error.message, 'error');
            });
        }

        // AJAX Remove from Cart
        function removeFromCartAjax(productId) {
            fetch('<?php echo $base; ?>/remove_from_cart_ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'product_id=' + productId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateCartSidebar(data.cart_items, data.subtotal, data.shipping, data.total);
                    updateCartCount(data.cart_items.length);
                    showNotification('Item removed from cart');
                } else {
                    showNotification('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error removing item', 'error');
            });
        }

        // Update Cart Sidebar
        function updateCartSidebar(cartItems, subtotal, shipping, total) {
            const cartList = document.querySelector('.cart-items-list');
            
            if (cartItems.length === 0) {
                document.querySelector('.cart-empty').style.display = 'flex';
                document.querySelector('.cart-items-list').style.display = 'none';
                document.querySelector('.cart-summary').style.display = 'none';
                document.querySelector('.cart-actions').innerHTML = '';
                return;
            }

            document.querySelector('.cart-empty').style.display = 'none';
            document.querySelector('.cart-items-list').style.display = 'flex';
            document.querySelector('.cart-summary').style.display = 'flex';

            // Rebuild cart items
            cartList.innerHTML = '';
            cartItems.forEach(item => {
                const itemHTML = `
                    <div class="cart-item">
                        <div class="cart-item-image">
                            ${item.image ? `<img src="<?php echo $base; ?>/assets/images/products/${item.image}" alt="${item.product_name}">` : '<div class="placeholder-image-small">🎵</div>'}
                        </div>
                        <div class="cart-item-details">
                            <h4>${item.product_name}</h4>
                            <p class="cart-item-price">$${parseFloat(item.price).toFixed(2)} × ${item.quantity}</p>
                            <p class="cart-item-total">Total: $${parseFloat(item.item_total).toFixed(2)}</p>
                        </div>
                        <button type="button" class="cart-item-remove" onclick="removeFromCartAjax(${item.product_id})" title="Remove">🗑️</button>
                    </div>
                `;
                cartList.innerHTML += itemHTML;
            });

            // Update summary
            const summaryHTML = `
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>$${parseFloat(subtotal).toFixed(2)}</span>
                </div>
                ${shipping > 0 ? `<div class="summary-row">
                    <span>Shipping:</span>
                    <span>$${parseFloat(shipping).toFixed(2)}</span>
                </div>` : ''}
                <div class="summary-row total">
                    <span>Total:</span>
                    <span>$${parseFloat(total).toFixed(2)}</span>
                </div>
            `;
            document.querySelector('.cart-summary').innerHTML = summaryHTML;

            // Update actions
            const actionsHTML = `<a href="<?php echo $base; ?>/cart.php" class="btn btn-primary btn-block">Proceed to Order</a>`;
            document.querySelector('.cart-actions').innerHTML = actionsHTML;
        }

        // Update Cart Count Badge
        function updateCartCount(count) {
            const cartCount = document.querySelector('.cart-count');
            const cartCountHeader = document.querySelector('.cart-count-header');
            if (cartCount) cartCount.textContent = count;
            if (cartCountHeader) cartCountHeader.textContent = count;
        }

        // Show Notification
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? '#27AE60' : '#C0392B'};
                color: white;
                padding: 1rem 1.5rem;
                border-radius: 6px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 9999;
                animation: slideIn 0.3s ease-out;
            `;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(() => notification.remove(), 300);
            }, 2500);
        }

        // Add animation styles
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(400px); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(400px); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
