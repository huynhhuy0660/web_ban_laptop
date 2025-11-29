<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include 'db_connect.php'; 
include 'auth.php';
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
$products_in_cart = [];
$grand_total = 0;
$cart_product_ids = array_keys($_SESSION['cart']);
if (!empty($cart_product_ids)) {
    $ids_string = implode(',', $cart_product_ids);
    $sql = "SELECT id, name, price, image FROM product WHERE id IN ($ids_string)";
    
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $products_in_cart[] = $row;
        }
    } else {
        echo "Lỗi SQL: " . $conn->error; 
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng - LAPTOPWORLD</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="images/LAPTOP-WORLD.png" type="image/png">
</head>
<body>

    <header>
        <div class="container header-content">
                <a href="index.php">
                    <img src="images/lt.png" alt="LAPTOPWORLD Logo" class="logo-image">
                </a>
            <form class="search-bar" action="search.php" method="GET">
                <input type="text" name="query" placeholder="Bạn tìm gì..." required>
                <button type="submit">Tìm</button>
            </form>
            <div class="header-icons">
                <a href="cart.php">Giỏ hàng <?php echo !empty($_SESSION['cart']) ? '('.array_sum($_SESSION['cart']).')' : ''; ?></a>
                <?php if (isAuthenticated()): ?>
                    <span>|</span>
                    <a href="#" class="welcome-user">Chào, <?php echo $_SESSION['full_name']; ?></a> 
                    <span>|</span>
                    <a href="logout.php">Đăng xuất</a>
                <?php else: ?>
                    <a href="login.php">Đăng nhập</a>
                    <span>|</span>
                    <a href="register.php">Đăng ký</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    
    <nav class="main-nav">
        <div class="container">
            <ul>
                <li><a href="index.php">TRANG CHỦ</a></li>
                    <li class="megamenu-trigger">
                    <a href="#">LAPTOP</a>
                    <div class="mega-menu">
                        <div class="megamenu-column">
                            <h3>Thương hiệu</h3>
                            <a href="category.php?category_id=1&brand_id=1">ASUS</a>
                            <a href="category.php?category_id=1&brand_id=2">ACER</a>
                            <a href="category.php?category_id=1&brand_id=3">LENOVO</a>
                            <a href="category.php?category_id=1&brand_id=4">MSI</a>
                            <a href="category.php?category_id=1&brand_id=5">HP</a>
                            <a href="category.php?category_id=1&brand_id=6">DELL</a>
                        </div>
                        <div class="megamenu-column">
                            <h3>Giá bán</h3>
                            <a href="category.php?category_id=1&max_price=20000000">Dưới 20 triệu</a>
                            <a href="category.php?category_id=1&min_price=20000000&max_price=30000000">Từ 20 - 30 triệu</a>
                            <a href="category.php?category_id=1&min_price=30000000">Trên 30 triệu</a>
                        </div>
                    </div>
                </li>
                <li class="megamenu-trigger">
                    <a href="category.php?category_id=2">LINH KIỆN</a>
                    <div class="mega-menu">
                        <div class="megamenu-column">
                            <h3>Loại Linh Kiện</h3>
                            <a href="category.php?category_id=2">RAM (Nâng cấp)</a>
                            <a href="category.php?category_id=3">SSD (Ổ cứng)</a>
                        </div>
                        <div class="megamenu-column">
                            <h3>Giá bán</h3>
                            <a href="category.php?max_price=1000000">Dưới 1 triệu</a>
                            <a href="category.php?min_price=1000000&max_price=3000000">Từ 1 đến 3 triệu</a>
                            <a href="category.php?min_price=3000000">Trên 3 triệu</a>
                        </div>
                    </div>
                </li>
                <li><a href="info.php">DỊCH VỤ</a></li>
                <li><a href="#">LIÊN HỆ</a></li>
            </ul>
        </div>
    </nav>
    <main class="container cart-section">
        <h2>GIỎ HÀNG CỦA BẠN</h2>
        <?php if (empty($products_in_cart)): ?>
            <p style="text-align: center; padding: 50px; font-size: 18px;">
                Giỏ hàng của bạn đang trống. <br><br>
                <a href="index.php" class="add-to-cart-btn" style="display: inline-block; width: auto;">Tiếp tục mua sắm</a>
            </p>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th colspan="2">Sản phẩm</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                        <th>Xóa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products_in_cart as $product): ?>
                        <?php
                        $quantity = $_SESSION['cart'][$product['id']];
                        $sub_total = $product['price'] * $quantity;
                        $grand_total += $sub_total;
                        ?>
                        <tr>
                            <td class="cart-image">
                                <a href="product_detail.php?id=<?php echo $product['id']; ?>">
                                    <img src="images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                                </a>
                            </td>
                            <td class="cart-name">
                                <a href="product_detail.php?id=<?php echo $product['id']; ?>">
                                    <?php echo $product['name']; ?>
                                </a>
                            </td>
                            <td class="cart-price">
                                <?php echo number_format($product['price'], 0, ',', '.'); ?>đ
                            </td>
                            <td class="cart-quantity">
                                <form action="cart_action.php" method="POST">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="number" name="quantity" value="<?php echo $quantity; ?>" min="1" class="quantity-input">
                                    <button type="submit" class="update-cart-btn">Lưu</button>
                                </form>
                            </td>
                            <td class="cart-subtotal">
                                <?php echo number_format($sub_total, 0, ',', '.'); ?>đ
                            </td>
                            <td class="cart-remove">
                                <form action="cart_action.php" method="POST">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" class="remove-cart-btn" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?');">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-summary">
                <div class="cart-total">
                    <strong>Tổng tiền:</strong>
                    <span><?php echo number_format($grand_total, 0, ',', '.'); ?>đ</span>
                </div>
                <div class="cart-checkout">
                    <a href="checkout.php" class="checkout-btn">TIẾN HÀNH THANH TOÁN</a>
                </div>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>