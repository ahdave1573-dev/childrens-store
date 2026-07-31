<?php
// cart.php
$page_title = "My Shopping Cart | Childrens-Store";
$page_desc = "Review the cute items in your cart. Proceed to checkout to secure the most adorable clothing and toys for your children.";
$page_keywords = "kids cart, baby clothing checkout, childrens store shopping cart";

require_once 'includes/header.php';
require_once __DIR__ . '/config/database.php';

$cart = $_SESSION['cart'] ?? [];
$total = 0;
?>

<div class="container" style="padding: 3rem 5%; min-height: 70vh;">
    <h1 style="margin-bottom: 0.5rem; font-family:'Quicksand', sans-serif; font-weight:700;">My Cart</h1>
    <p style="color:var(--light-grey); margin-bottom: 2.5rem; font-weight:500;">Review your selected toys and clothing before checking out</p>

    <?php if (empty($cart)): ?>
        <div class="premium-card" style="text-align: center; padding: 4rem 2rem;">
            <i class="fas fa-shopping-bag" style="font-size: 4rem; color:var(--soft-bg); margin-bottom: 1.5rem; display:block;"></i>
            <h2 style="font-family:'Quicksand', sans-serif; margin-bottom: 1rem;">Your cart is empty!</h2>
            <p style="color:var(--light-grey); margin-bottom: 2rem;">Add some lovely products to your cart and make your little ones smile.</p>
            <a href="index.php" class="btn btn-primary">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="cart-layout">
            <!-- Left: Cart Items -->
            <div class="premium-card" style="padding: 1.5rem; overflow-x: auto;">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Size</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Remove</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart as $index => $item): 
                            $stmt = $conn->prepare("SELECT name, price FROM products WHERE id = ?");
                            $stmt->bind_param("i", $item['id']);
                            $stmt->execute();
                            $prod = $stmt->get_result()->fetch_assoc();
                            
                            if (!$prod) {
                                $prod = ['name' => 'Product Unavailable', 'price' => 0];
                            }

                            $item_total = $prod['price'] * $item['qty'];
                            $total += $item_total;
                        ?>
                            <tr>
                                <td class="cart-item-title"><?php echo htmlspecialchars($prod['name']); ?></td>
                                <td style="font-weight: 600; color: var(--light-grey);"><?php echo htmlspecialchars($item['size']); ?></td>
                                <td style="font-weight: 600;">₹<?php echo number_format($prod['price']); ?></td>
                                <td>
                                    <form action="cart_action.php" method="POST" class="qty-control">
                                        <input type="hidden" name="action" value="update_qty">
                                        <input type="hidden" name="index" value="<?php echo $index; ?>">
                                        <button type="button" class="qty-btn" onclick="this.form.qty.value=parseInt(this.form.qty.value)-1; this.form.submit();">-</button>
                                        <input type="number" name="qty" class="qty-input" value="<?php echo $item['qty']; ?>" min="0" onchange="this.form.submit();">
                                        <button type="button" class="qty-btn" onclick="this.form.qty.value=parseInt(this.form.qty.value)+1; this.form.submit();">+</button>
                                    </form>
                                </td>
                                <td class="cart-total-price">₹<?php echo number_format($item_total); ?></td>
                                <td>
                                    <a href="cart_action.php?action=remove&index=<?php echo $index; ?>" class="cart-remove-btn"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="margin-top: 2rem; display: flex; justify-content: space-between; align-items: center;">
                    <a href="index.php" style="color:var(--kids-blue); font-weight:700; font-family:'Quicksand', sans-serif; display:flex; align-items:center; gap:0.5rem;"><i class="fas fa-arrow-left"></i> Continue Shopping</a>
                    
                    <form action="cart_action.php" method="POST">
                        <input type="hidden" name="action" value="clear">
                        <button type="submit" style="background:none; border:none; color:var(--kids-pink); font-weight:700; font-family:'Quicksand', sans-serif; cursor:pointer; display:flex; align-items:center; gap:0.5rem; transition:color 0.2s;" onmouseover="this.style.color='var(--kids-pink-dark)';" onmouseout="this.style.color='var(--kids-pink)';"><i class="fas fa-trash"></i> Clear All Items</button>
                    </form>
                </div>
            </div>

            <!-- Right: Order Summary Panel -->
            <div class="premium-card" style="height: fit-content;">
                <h2 class="summary-title">Summary</h2>
                <div class="summary-row">
                    <span style="color:var(--light-grey); font-weight:500;">Subtotal</span>
                    <span style="font-weight:600; color:var(--dark);">₹<?php echo number_format($total); ?></span>
                </div>
                <div class="summary-row">
                    <span style="color:var(--light-grey); font-weight:500;">Shipping</span>
                    <span style="color:var(--kids-green); font-weight:700;">FREE</span>
                </div>
                <div class="summary-row total-row">
                    <span>Total</span>
                    <span>₹<?php echo number_format($total); ?></span>
                </div>

                <div style="margin-top: 2.5rem;">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="checkout.php" class="btn-buy" style="display: block; text-align: center; text-decoration: none; padding: 15px 0;">Proceed to Checkout</a>
                    <?php else: ?>
                        <a href="login.php" class="btn-buy" style="display: block; text-align: center; text-decoration: none; padding: 15px 0;">Login to Checkout</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
