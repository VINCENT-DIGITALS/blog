<link rel="stylesheet" href="/css/main.css">
<header>
  <nav class="navbar">
    <div class="container">
      <!-- Logo/Brand -->
      <a class="logo" href="/">MyWebsite</a>
      
      <!-- Hamburger Menu for Mobile -->
      <button aria-label="Toggle Navigation" class="menu-toggle" id="mobile-menu">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
      </button>
      
      <!-- Navigation Links -->
      <ul class="nav-links" id="nav-links">
        <li><a href="/">Home</a></li>
        <li><a href="/about">About</a></li>
        <li><a href="/services">Services</a></li>
        <li><a href="/contact">Contact</a></li>
      </ul>
    </div>
  </nav>
</header>


<!-- Responsive Navbar JS -->
<script>
  document.getElementById('mobile-menu').addEventListener('click', function() {
    document.getElementById('nav-links').classList.toggle('show');
  });
</script>
