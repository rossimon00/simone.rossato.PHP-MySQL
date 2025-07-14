<?php
include('../common/header.php');
?>

<nav>
    <!-- Barra bianca con logo centrato -->
    <div class="top-navbar bg-white d-flex justify-content-center flex-column align-items-center"
        style="box-shadow: 0px 2px 8px 1px #072944; height: 105px; position: sticky; top: 0; z-index: 2; width: 100%;">
        <div class="d-flex align-items-center position-relative px-4 w-100" style="height: 80px;">
            <a href="restaurant.php" class="mx-auto d-flex align-items-center">
                <img src="../assets/images/logo.png" alt="Logo" class="navbar-logo" style="height:80px;">
            </a>

            <div class="nav-item position-absolute" style="right:50px" id="cartIconContainer" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Conferma l’ordine"
                style="top: 50%; transform: translateY(-50%);">
                <a href="carrello.php" class="position-relative" 
                    style="color: #072944;">
                    <i class="bi bi-cart4 fs-3"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                        id="cartCountBadge" style="font-size: 0.75rem;">
                        0
                    </span>
                </a>
            </div>
        </div>

        <div id="toggleMenuArrowContainer" data-bs-toggle="tooltip" data-bs-placement="bottom"
            title="Apri il pannello di navigazione"
            style="    box-shadow: 0px 2px 8px 1px #072944;background: #4d575e;width: 40px;height: 30px;border-radius: 200px;top: 90px;position: absolute;"
            class="d-flex p-2 justify-content-center align-items-center">
            <button id="toggleMenuArrow" class="btn btn-link p-0" style="color:white; user-select:none;">
                <i class="bi bi-chevron-down fs-3"></i>
        </div>
        </button>


    </div>

    <!-- Menu blu a comparsa -->
    <div id="dropdownMenu" class="dropdown-menu-blue"
        style="box-shadow: 0px 2px 8px 1px #072944;height:70px; background-color:#f4f2e4; padding: 1rem 0; position: absolute; top: 0px; z-index: 1; width: 100%;">
        <ul class="menu-list d-flex justify-content-center align-items-center flex-wrap"
            style="list-style:none; margin:0; padding:0; gap: 2rem; font-size: 1.2rem; color: white;">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'beach_establishment.php' ? 'active' : ''; ?>"
                    href="beach_establishment.php">STABILIMENTO</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'restaurant.php' ? 'active' : ''; ?>"
                    href="restaurant.php">RISTORANTE</a>
            </li>
            <li class="nav-item">
                <a href="#" data-bs-toggle="modal" data-bs-target="#prenotazioneModal" class="nav-link">PRENOTA IL TUO
                    TAVOLO</a>
            </li>

            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_users.php' ? 'active' : ''; ?>"
                        href="manage_users.php">UTENTI</a>
                </li>
            <?php endif; ?>

            <?php if (isset($_SESSION['user']) && ($_SESSION['user']['role'] == 'admin' || $_SESSION['user']['role'] == 'manager')): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reservations.php' ? 'active' : ''; ?>"
                        href="reservations.php">PRENOTAZIONI</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_products.php' ? 'active' : ''; ?>"
                        href="manage_products.php">PRODOTTI</a>
                </li>
            <?php endif; ?>

            <?php if (isset($_SESSION['user'])): ?>
                <li class="nav-item dropdown">
                    <button class="btn bg-transparent rounded-circle p-0 d-flex align-items-center justify-content-center"
                        type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person fs-3"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown"
                        style="min-width: 200px; font-size: 1.15rem; padding: 0.5rem 0;">
                        <li><button class="dropdown-item" type="button" style="padding: 12px 20px;"
                                onclick="window.location.href='profile.php'">Profilo</button></li>
                        <li><button class="dropdown-item" type="button" style="padding: 12px 20px;">Prenotazioni</button>
                        </li>
                        <li><button class="dropdown-item" type="button" style="padding: 12px 20px;">Something else
                                here</button></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><button class="dropdown-item" type="button" onclick="window.location.href='logout.php'"
                                style="padding: 12px 20px;">LOGOUT</button></li>
                    </ul>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a href="register.php" class="nav-link">REGISTRATI</a>
                </li>
            <?php endif; ?>


        </ul>
    </div>
</nav>

<?php include 'restaurant_booking.php'; ?>
