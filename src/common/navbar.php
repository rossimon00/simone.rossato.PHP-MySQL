<nav class="navbar navbar-expand-lg bg-white d-flex flex-row align-items-center justify-content-between" style="box-shadow: 0px 2px 8px 1px #072944;">
    <!-- Logo a sinistra -->
    <a href="dashboard.php" class="d-flex align-items-center">
        <img src="../assets/images/logo.png" alt="Logo" class="navbar-logo">
    </a>
    <!-- Pulsante toggle -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <!-- Contenitore Collassabile -->
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">DASHBOARD</a>
            </li>
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_users.php' ? 'active' : ''; ?>" href="manage_users.php">UTENTI</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_products.php' ? 'active' : ''; ?>" href="manage_products.php">PRODOTTI</a>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'logout.php' ? 'active' : ''; ?>" href="logout.php">LOGOUT</a>
            </li>
        </ul>
    </div>
</nav>
