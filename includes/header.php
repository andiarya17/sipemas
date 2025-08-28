<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid">
       <a class="navbar-brand" href="#">
   <img src="/sipemas/assets/img/luwuutara.png" width="40" height="40" class="me-2">
    SIPEMAS Desa Palandan
</a>
        <button class="navbar-toggler" type="button" onclick="toggleSidebar()">
            <i class="fas fa-bars text-white"></i>
        </button>
        
        <div class="navbar-nav ms-auto">
            <?php if (isset($_SESSION['admin_logged_in'])): ?>
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" role="button" 
                       data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i> <?= $_SESSION['admin_nama'] ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="profil.php">
                            <i class="fas fa-user"></i> Profil
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a></li>
                    </ul>
                </div>
            <?php elseif (isset($_SESSION['user_logged_in'])): ?>
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" role="button" 
                       data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i> <?= $_SESSION['user_nama'] ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="profil.php">
                            <i class="fas fa-user"></i> Profil
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a></li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>