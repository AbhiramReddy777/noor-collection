<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add to cart with duplicate prevention (same item increases qty)
if (isset($_POST['add_to_cart'])) {
    $name  = htmlspecialchars($_POST['name']);
    $price = floatval($_POST['price']);
    $image = htmlspecialchars($_POST['image']);

    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['name'] === $name) {
            $item['qty']++;
            $found = true;
            break;
        }
    }
    unset($item);

    if (!$found) {
        $_SESSION['cart'][] = ['name' => $name, 'price' => $price, 'image' => $image, 'qty' => 1];
    }

    // Redirect to avoid form resubmission on refresh
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?') . '?added=1');
    exit;
}

// Update quantity
if (isset($_POST['update_qty'])) {
    $index = intval($_POST['index']);
    $qty   = intval($_POST['qty']);
    if ($qty <= 0) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    } else {
        $_SESSION['cart'][$index]['qty'] = $qty;
    }
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// Remove item
if (isset($_GET['remove'])) {
    $index = intval($_GET['remove']);
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// Clear cart
if (isset($_GET['clear_cart'])) {
    $_SESSION['cart'] = [];
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// Normalize legacy cart items that may lack 'qty'
foreach ($_SESSION['cart'] as &$_ci) {
    if (!isset($_ci['qty']) || intval($_ci['qty']) < 1) $_ci['qty'] = 1;
}
unset($_ci);

$total     = 0;
$cartCount = 0;
foreach ($_SESSION['cart'] as $item) {
    $cartCount += intval($item['qty']);
    $total     += floatval($item['price']) * intval($item['qty']);
}

$products = [
    [
        'name'  => 'Red Floor Length Chiffon Prom Dress',
        'desc'  => 'Sleeveless silhouette adorned with hand-set crystals along the neckline. Floats effortlessly with every step.',
        'price' => 12499,
        'badge' => 'Bestseller',
        'image' => 'http://cdn.shopify.com/s/files/1/1233/6964/products/red_sleeveless_chiffon_prom_dresses_1200x1200.jpg?v=1540889604',
    ],
    [
        'name'  => 'Gradient One Shoulder Ombre Evening Dress',
        'desc'  => 'Sweeping ombré chiffon transitions from blush to deep rose, finished with delicate beadwork at the shoulder.',
        'price' => 14999,
        'badge' => 'New Arrival',
        'image' => 'https://www.simibridaldresses.com/cdn/shop/products/Gradient_One_Shoulder_Chiffon_Evening_Dress_Ombre_Prom_Dresses_with_Beads.jpg?v=1522230094&width=600',
    ],
    [
        'name'  => 'Modest Red Mermaid Short Sleeve Gown',
        'desc'  => 'Body-hugging mermaid silhouette in rich crimson with structured short sleeves for an effortlessly regal look.',
        'price' => 10999,
        'badge' => 'Limited',
        'image' => 'https://www.simibridaldresses.com/cdn/shop/products/Y0375_1.jpg?v=1636614970&width=800',
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>NOOR Collections — Luxury Evening Wear</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
/* ── RESET & BASE ── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --gold:        #c9a96e;
  --gold-light:  #e2c895;
  --gold-dark:   #8c6d3f;
  --crimson:     #8b1a2a;
  --crimson-mid: #c0293e;
  --ink:         #0d0b09;
  --ink-mid:     #1a1612;
  --ink-light:   #2a231d;
  --parchment:   #f5ede0;
  --parchment-2: #ede0cc;
  --text-light:  #d4c4a8;
  --text-muted:  #8a7560;
  --radius:      4px;
  --transition:  0.35s cubic-bezier(0.4, 0, 0.2, 1);
}

html { scroll-behavior: smooth; }

body {
  font-family: 'Jost', sans-serif;
  font-weight: 300;
  background-color: var(--ink);
  color: var(--text-light);
  min-height: 100vh;
  overflow-x: hidden;
}

/* ── NOISE OVERLAY ── */
body::before {
  content: '';
  position: fixed;
  inset: 0;
  background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
  pointer-events: none;
  z-index: 0;
  opacity: 0.4;
}

/* ── HERO BACKGROUND ── */
.site-bg {
  position: fixed;
  inset: 0;
  background:
    radial-gradient(ellipse 80% 60% at 50% 0%, rgba(139,26,42,0.25) 0%, transparent 70%),
    radial-gradient(ellipse 60% 40% at 100% 100%, rgba(201,169,110,0.08) 0%, transparent 60%),
    var(--ink);
  z-index: -1;
}

/* ── LAYOUT ── */
.site-wrapper {
  position: relative;
  z-index: 1;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
}

/* ── NAVBAR ── */
.navbar {
  position: sticky;
  top: 0;
  z-index: 100;
  background: rgba(13,11,9,0.85);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border-bottom: 1px solid rgba(201,169,110,0.15);
  padding: 0 24px;
}

.navbar-inner {
  max-width: 1200px;
  margin: 0 auto;
  height: 72px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.brand {
  display: flex;
  flex-direction: column;
  line-height: 1;
  text-decoration: none;
}

.brand-name {
  font-family: 'Cormorant Garamond', serif;
  font-size: 26px;
  font-weight: 600;
  letter-spacing: 0.18em;
  color: var(--gold);
}

.brand-tagline {
  font-size: 10px;
  letter-spacing: 0.3em;
  text-transform: uppercase;
  color: var(--text-muted);
  margin-top: 2px;
}

.nav-actions {
  display: flex;
  align-items: center;
  gap: 24px;
}

.nav-link {
  font-size: 12px;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: var(--text-muted);
  text-decoration: none;
  transition: color var(--transition);
}
.nav-link:hover { color: var(--gold); }

.cart-btn {
  position: relative;
  display: flex;
  align-items: center;
  gap: 8px;
  background: none;
  border: 1px solid rgba(201,169,110,0.35);
  color: var(--gold);
  padding: 8px 16px;
  border-radius: 2px;
  font-family: 'Jost', sans-serif;
  font-size: 12px;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  cursor: pointer;
  transition: all var(--transition);
  text-decoration: none;
}
.cart-btn:hover {
  background: rgba(201,169,110,0.12);
  border-color: var(--gold);
  color: var(--gold-light);
}

.cart-badge {
  position: absolute;
  top: -8px;
  right: -8px;
  background: var(--crimson-mid);
  color: white;
  font-size: 10px;
  font-weight: 500;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid var(--ink);
}

/* ── HERO SECTION ── */
.hero {
  padding: 80px 0 60px;
  text-align: center;
}

.hero-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  font-size: 11px;
  letter-spacing: 0.35em;
  text-transform: uppercase;
  color: var(--gold);
  margin-bottom: 20px;
}
.hero-eyebrow::before,
.hero-eyebrow::after {
  content: '';
  width: 32px;
  height: 1px;
  background: var(--gold);
  opacity: 0.5;
}

.hero-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(42px, 7vw, 80px);
  font-weight: 300;
  line-height: 1.05;
  color: var(--parchment);
  margin-bottom: 16px;
  letter-spacing: 0.02em;
}

.hero-title em {
  font-style: italic;
  color: var(--gold-light);
}

.hero-sub {
  font-size: 14px;
  letter-spacing: 0.08em;
  color: var(--text-muted);
  max-width: 420px;
  margin: 0 auto;
  line-height: 1.7;
}

/* ── DIVIDER ── */
.divider {
  display: flex;
  align-items: center;
  gap: 16px;
  margin: 40px 0;
}
.divider::before,
.divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: linear-gradient(to right, transparent, rgba(201,169,110,0.3), transparent);
}
.divider-icon {
  color: var(--gold);
  font-size: 14px;
  opacity: 0.6;
}

/* ── PRODUCT GRID ── */
.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 28px;
  margin-bottom: 80px;
}

/* ── PRODUCT CARD ── */
.product-card {
  background: var(--ink-mid);
  border: 1px solid rgba(201,169,110,0.12);
  border-radius: var(--radius);
  overflow: hidden;
  transition: transform var(--transition), box-shadow var(--transition), border-color var(--transition);
  animation: fadeUp 0.6s ease both;
}

.product-card:nth-child(1) { animation-delay: 0.1s; }
.product-card:nth-child(2) { animation-delay: 0.2s; }
.product-card:nth-child(3) { animation-delay: 0.3s; }

.product-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 24px 60px rgba(0,0,0,0.5), 0 0 0 1px rgba(201,169,110,0.2);
  border-color: rgba(201,169,110,0.3);
}

.product-img-wrap {
  position: relative;
  overflow: hidden;
  aspect-ratio: 3/4;
  background: var(--ink-light);
}

.product-img-wrap img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
  display: block;
}

.product-card:hover .product-img-wrap img {
  transform: scale(1.05);
}

.product-badge {
  position: absolute;
  top: 14px;
  left: 14px;
  background: var(--crimson);
  color: var(--parchment);
  font-size: 9px;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  padding: 4px 10px;
  border-radius: 1px;
}

.product-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(13,11,9,0.7) 0%, transparent 50%);
  opacity: 0;
  transition: opacity var(--transition);
}
.product-card:hover .product-overlay { opacity: 1; }

.product-body {
  padding: 22px 20px 20px;
}

.product-name {
  font-family: 'Cormorant Garamond', serif;
  font-size: 20px;
  font-weight: 400;
  color: var(--parchment);
  line-height: 1.3;
  margin-bottom: 8px;
  letter-spacing: 0.01em;
}

.product-desc {
  font-size: 13px;
  color: var(--text-muted);
  line-height: 1.65;
  margin-bottom: 18px;
}

.product-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.product-price {
  font-family: 'Cormorant Garamond', serif;
  font-size: 26px;
  font-weight: 300;
  color: var(--gold-light);
  letter-spacing: 0.02em;
}

.add-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: linear-gradient(135deg, var(--gold-dark), var(--gold));
  color: var(--ink);
  border: none;
  padding: 10px 20px;
  border-radius: 1px;
  font-family: 'Jost', sans-serif;
  font-size: 11px;
  font-weight: 500;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  cursor: pointer;
  transition: all var(--transition);
}
.add-btn:hover {
  background: linear-gradient(135deg, var(--gold), var(--gold-light));
  transform: translateY(-1px);
  box-shadow: 0 8px 20px rgba(201,169,110,0.3);
}
.add-btn:active { transform: translateY(0); }

/* ── TOAST NOTIFICATION ── */
.toast {
  position: fixed;
  bottom: 32px;
  right: 32px;
  background: var(--ink-light);
  border: 1px solid rgba(201,169,110,0.4);
  color: var(--gold-light);
  padding: 14px 20px;
  border-radius: var(--radius);
  font-size: 13px;
  letter-spacing: 0.06em;
  display: flex;
  align-items: center;
  gap: 10px;
  box-shadow: 0 8px 32px rgba(0,0,0,0.4);
  z-index: 999;
  animation: toastIn 0.4s ease, toastOut 0.4s ease 2.5s both;
  pointer-events: none;
}

/* ── CART SECTION ── */
.cart-section {
  margin-bottom: 80px;
}

.section-header {
  display: flex;
  align-items: baseline;
  justify-content: space-between;
  margin-bottom: 28px;
}

.section-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 32px;
  font-weight: 300;
  color: var(--parchment);
  letter-spacing: 0.04em;
}

.section-title span {
  font-size: 14px;
  color: var(--text-muted);
  font-family: 'Jost', sans-serif;
  font-weight: 300;
  letter-spacing: 0.1em;
  margin-left: 12px;
}

.clear-link {
  font-size: 11px;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: var(--text-muted);
  text-decoration: none;
  transition: color var(--transition);
  border-bottom: 1px solid transparent;
}
.clear-link:hover { color: var(--crimson-mid); border-color: var(--crimson-mid); }

.cart-table {
  width: 100%;
  border-collapse: collapse;
}

.cart-table thead th {
  font-size: 10px;
  letter-spacing: 0.25em;
  text-transform: uppercase;
  color: var(--text-muted);
  padding: 0 16px 16px;
  text-align: left;
  border-bottom: 1px solid rgba(201,169,110,0.15);
}
.cart-table thead th:last-child { text-align: right; }

.cart-row {
  border-bottom: 1px solid rgba(255,255,255,0.05);
  transition: background var(--transition);
  animation: fadeUp 0.4s ease both;
}
.cart-row:hover { background: rgba(201,169,110,0.04); }

.cart-row td {
  padding: 18px 16px;
  vertical-align: middle;
}

.cart-thumb {
  width: 64px;
  height: 80px;
  object-fit: cover;
  border-radius: 2px;
  border: 1px solid rgba(201,169,110,0.15);
}

.cart-item-name {
  font-family: 'Cormorant Garamond', serif;
  font-size: 17px;
  color: var(--parchment);
  margin-bottom: 3px;
}

.cart-item-unit {
  font-size: 12px;
  color: var(--text-muted);
}

.qty-form {
  display: flex;
  align-items: center;
  gap: 0;
}

.qty-btn {
  width: 28px;
  height: 28px;
  background: var(--ink-light);
  border: 1px solid rgba(201,169,110,0.2);
  color: var(--gold);
  cursor: pointer;
  font-size: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background var(--transition);
}
.qty-btn:hover { background: rgba(201,169,110,0.15); }

.qty-input {
  width: 42px;
  height: 28px;
  background: var(--ink-mid);
  border: 1px solid rgba(201,169,110,0.2);
  border-left: none;
  border-right: none;
  color: var(--parchment);
  text-align: center;
  font-family: 'Jost', sans-serif;
  font-size: 13px;
}
.qty-input:focus { outline: none; border-color: var(--gold); }

.cart-subtotal {
  font-family: 'Cormorant Garamond', serif;
  font-size: 18px;
  color: var(--gold-light);
  text-align: right;
}

.remove-btn {
  background: none;
  border: none;
  color: var(--text-muted);
  cursor: pointer;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 2px;
  transition: all var(--transition);
  text-decoration: none;
  font-size: 13px;
}
.remove-btn:hover {
  background: rgba(192,41,62,0.2);
  color: #e07080;
}

/* ── CART EMPTY ── */
.cart-empty {
  text-align: center;
  padding: 60px 0;
  border: 1px dashed rgba(201,169,110,0.15);
  border-radius: var(--radius);
}
.cart-empty-icon {
  font-size: 40px;
  color: rgba(201,169,110,0.2);
  margin-bottom: 16px;
}
.cart-empty p {
  color: var(--text-muted);
  font-size: 14px;
  letter-spacing: 0.05em;
}

/* ── CART SUMMARY ── */
.cart-summary {
  display: flex;
  justify-content: flex-end;
  margin-top: 28px;
}

.summary-box {
  background: var(--ink-mid);
  border: 1px solid rgba(201,169,110,0.15);
  border-radius: var(--radius);
  padding: 28px 32px;
  min-width: 320px;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
  font-size: 13px;
  color: var(--text-muted);
  letter-spacing: 0.05em;
}

.summary-divider {
  height: 1px;
  background: rgba(201,169,110,0.15);
  margin: 12px 0;
}

.summary-total-label {
  font-family: 'Cormorant Garamond', serif;
  font-size: 16px;
  color: var(--parchment);
  letter-spacing: 0.1em;
  text-transform: uppercase;
}

.summary-total-amount {
  font-family: 'Cormorant Garamond', serif;
  font-size: 32px;
  font-weight: 300;
  color: var(--gold-light);
}

.checkout-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  width: 100%;
  background: linear-gradient(135deg, var(--crimson), var(--crimson-mid));
  color: var(--parchment);
  border: none;
  padding: 14px;
  border-radius: 1px;
  font-family: 'Jost', sans-serif;
  font-size: 12px;
  font-weight: 500;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  cursor: pointer;
  margin-top: 20px;
  transition: all var(--transition);
}
.checkout-btn:hover {
  background: linear-gradient(135deg, var(--crimson-mid), #d63050);
  box-shadow: 0 8px 24px rgba(192,41,62,0.35);
  transform: translateY(-1px);
}

/* ── FOOTER ── */
.site-footer {
  border-top: 1px solid rgba(201,169,110,0.1);
  padding: 32px 0;
  text-align: center;
}
.footer-brand {
  font-family: 'Cormorant Garamond', serif;
  font-size: 18px;
  color: var(--gold);
  letter-spacing: 0.2em;
  margin-bottom: 8px;
}
.footer-text {
  font-size: 11px;
  letter-spacing: 0.12em;
  color: var(--text-muted);
}

/* ── ANIMATIONS ── */
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(24px); }
  to   { opacity: 1; transform: translateY(0); }
}

@keyframes toastIn {
  from { opacity: 0; transform: translateY(16px); }
  to   { opacity: 1; transform: translateY(0); }
}

@keyframes toastOut {
  from { opacity: 1; transform: translateY(0); }
  to   { opacity: 0; transform: translateY(16px); }
}

/* ── RESPONSIVE ── */
@media (max-width: 768px) {
  .navbar-inner { padding: 0; }
  .hero { padding: 48px 0 40px; }
  .products-grid { grid-template-columns: 1fr; }
  .summary-box { min-width: 100%; }
  .cart-summary { justify-content: stretch; }
  .nav-actions .nav-link { display: none; }
  .cart-table thead { display: none; }
  .cart-row { display: block; padding: 16px 0; }
  .cart-row td { display: inline-block; padding: 4px 8px; }
}
</style>
</head>
<body>

<div class="site-bg"></div>

<?php if (isset($_GET['added'])): ?>
<div class="toast">
  <i class="fas fa-check-circle"></i> Item added to your cart
</div>
<?php endif; ?>

<!-- NAVBAR -->
<nav class="navbar">
  <div class="navbar-inner">
    <a href="#" class="brand">
      <span class="brand-name">NOOR</span>
      <span class="brand-tagline">Collections</span>
    </a>
    <div class="nav-actions">
      <a href="#products" class="nav-link">Collection</a>
      <a href="#cart" class="nav-link">Lookbook</a>
      <a href="#cart" class="cart-btn">
        <i class="fas fa-bag-shopping"></i> Cart
        <?php if ($cartCount > 0): ?>
          <span class="cart-badge"><?= $cartCount ?></span>
        <?php endif; ?>
      </a>
    </div>
  </div>
</nav>

<!-- MAIN CONTENT -->
<div class="site-wrapper">

  <!-- HERO -->
  <section class="hero" style="animation: fadeUp 0.8s ease both;">
    <div class="hero-eyebrow">
      <span></span> New Season Collection <span></span>
    </div>
    <h1 class="hero-title">
      Dressed in <em>Crimson</em><br>Made for Moments
    </h1>
    <p class="hero-sub">Exquisitely crafted evening wear for those who believe luxury is a feeling, not just a label.</p>
  </section>

  <div class="divider">
    <span class="divider-icon"><i class="fas fa-gem"></i></span>
  </div>

  <!-- PRODUCTS -->
  <section id="products">
    <div class="products-grid">
      <?php foreach ($products as $p): ?>
      <article class="product-card">
        <div class="product-img-wrap">
          <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy">
          <div class="product-overlay"></div>
          <span class="product-badge"><?= htmlspecialchars($p['badge']) ?></span>
        </div>
        <div class="product-body">
          <h3 class="product-name"><?= htmlspecialchars($p['name']) ?></h3>
          <p class="product-desc"><?= htmlspecialchars($p['desc']) ?></p>
          <div class="product-footer">
            <span class="product-price">₹<?= number_format($p['price'], 0) ?></span>
            <form method="POST">
              <input type="hidden" name="name"  value="<?= htmlspecialchars($p['name'])  ?>">
              <input type="hidden" name="price" value="<?= $p['price'] ?>">
              <input type="hidden" name="image" value="<?= htmlspecialchars($p['image']) ?>">
              <button name="add_to_cart" class="add-btn">
                <i class="fas fa-plus"></i> Add to Cart
              </button>
            </form>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </section>

  <div class="divider">
    <span class="divider-icon"><i class="fas fa-diamond"></i></span>
  </div>

  <!-- CART -->
  <section class="cart-section" id="cart">
    <div class="section-header">
      <h2 class="section-title">
        Your Selection
        <?php if ($cartCount > 0): ?>
          <span><?= $cartCount ?> <?= $cartCount === 1 ? 'piece' : 'pieces' ?></span>
        <?php endif; ?>
      </h2>
      <?php if (!empty($_SESSION['cart'])): ?>
        <a href="?clear_cart=1" class="clear-link" onclick="return confirm('Clear your entire cart?')">
          <i class="fas fa-trash-can"></i> Clear All
        </a>
      <?php endif; ?>
    </div>

    <?php if (empty($_SESSION['cart'])): ?>
      <div class="cart-empty">
        <div class="cart-empty-icon"><i class="fas fa-bag-shopping"></i></div>
        <p>Your cart is empty — discover the collection above.</p>
      </div>
    <?php else: ?>
      <table class="cart-table">
        <thead>
          <tr>
            <th>Item</th>
            <th></th>
            <th>Quantity</th>
            <th style="text-align:right">Subtotal</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($_SESSION['cart'] as $i => $item): ?>
          <tr class="cart-row">
            <td>
              <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="cart-thumb">
            </td>
            <td>
              <div class="cart-item-name"><?= htmlspecialchars($item['name']) ?></div>
              <div class="cart-item-unit">₹<?= number_format($item['price'], 0) ?> each</div>
            </td>
            <td>
              <form method="POST" class="qty-form" id="qty-form-<?= $i ?>">
                <input type="hidden" name="index" value="<?= $i ?>">
                <button type="button" class="qty-btn" onclick="changeQty(<?= $i ?>, -1)">−</button>
                <input type="number" name="qty" value="<?= intval($item['qty']) ?>" min="0" class="qty-input" id="qty-<?= $i ?>"
                       onchange="this.form.submit()">
                <button type="button" class="qty-btn" onclick="changeQty(<?= $i ?>, 1)">+</button>
                <input type="hidden" name="update_qty" value="1">
              </form>
            </td>
            <td class="cart-subtotal">
              ₹<?= number_format(floatval($item['price']) * intval($item['qty']), 0) ?>
            </td>
            <td>
              <a href="?remove=<?= $i ?>" class="remove-btn" title="Remove">
                <i class="fas fa-xmark"></i>
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <div class="cart-summary">
        <div class="summary-box">
          <div class="summary-row">
            <span>Subtotal</span>
            <span>₹<?= number_format($total, 0) ?></span>
          </div>
          <div class="summary-row">
            <span>Shipping</span>
            <span>Calculated at checkout</span>
          </div>
          <div class="summary-divider"></div>
          <div class="summary-row">
            <span class="summary-total-label">Total</span>
            <span class="summary-total-amount">₹<?= number_format($total, 0) ?></span>
          </div>
          <button class="checkout-btn">
            <i class="fas fa-lock"></i> Proceed to Checkout
          </button>
        </div>
      </div>
    <?php endif; ?>
  </section>

</div>

<!-- FOOTER -->
<footer class="site-footer">
  <div class="footer-brand">NOOR</div>
  <p class="footer-text">© <?= date('Y') ?> NOOR Collections. All rights reserved.</p>
</footer>

<script>
function changeQty(index, delta) {
  const input = document.getElementById('qty-' + index);
  const newVal = parseInt(input.value) + delta;
  input.value = Math.max(0, newVal);
  document.getElementById('qty-form-' + index).submit();
}
</script>

</body>
</html>