<?php
require_once '/xampp/htdocs/Blog/db/session_manager.php'; // Include session handling file
?>
<nav class="navbar">
  <div class="logo">Pinoy Blogs</div>
  <button class="hamburger" onclick="toggleMenu()">☰</button>
  <ul class="nav-links" id="nav-links">
    <li class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
      <a href="/Blog/index.php">Home</a>
    </li>
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
      <li class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
        <a href="/Blog/pages/admin/dashboard.php">Admin Dashboard</a>
      </li>
    <?php endif; ?>
    <?php if (isset($_SESSION['user_id'])): ?>
      <li><a href="javascript:void(0)" onclick="showProfileModal()">Profile</a></li>
      <li class="<?= basename($_SERVER['PHP_SELF']) == 'logout.php' ? 'active' : ''; ?>">
        <a href="/Blog/pages/auth/logout.php">Logout</a>
      </li>
    <?php else: ?>
      <li class="<?= basename($_SERVER['PHP_SELF']) == 'choose_login.php' ? 'active' : ''; ?>">
        <a href="/Blog/pages/auth/choose_login.php">Login</a>
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
<?php include '/xampp/htdocs/Blog/pages/profile_modal.php'; ?>
