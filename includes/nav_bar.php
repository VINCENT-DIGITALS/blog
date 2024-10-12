<?php
require_once __DIR__.  '../../db/session_manager.php'; // Include session handling file
?>
<!-- Navbar -->
<nav class="navbar">
  <div class="logo">Pinoy Blogs</div>

  <!-- Hamburger Menu for Mobile -->
  <button class="hamburger" onclick="toggleMenu()">
    ☰
  </button>

  <!-- Navigation Links -->
  <ul class="nav-links" id="nav-links">
    <li class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
      <a href="/index.php">Home</a>
    </li>
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
      <!-- If user is an admin, show Admin Dashboard -->
      <li class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
        <a href="/pages/admin/dashboard.php">Admin Dashboard</a>
      </li>
    <?php endif; ?>

    <?php if (isset($_SESSION['user_id'])): ?>
      <!-- If user is logged in, show Logout -->
      <li class="<?= basename($_SERVER['PHP_SELF']) == 'logout.php' ? 'active' : ''; ?>">
        <a href="/pages/auth/logout.php">Logout</a>
      </li>


    <?php else: ?>
      <!-- If user is not logged in, show Login -->
      <li class="<?= basename($_SERVER['PHP_SELF']) == 'choose_login.php' ? 'active' : ''; ?>">
        <a href="/pages/auth/choose_login.php">Login</a>
      </li>
    <?php endif; ?>
  </ul>
</nav>

<script>
  function toggleMenu() {
    const navLinks = document.getElementById('nav-links');
    navLinks.classList.toggle('open');
  }
</script>