<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
 ?><!-- Image and text -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#">Ternurines Web</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarText">
    <ul class="navbar-nav mr-auto">
      <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
      <!-- opciones solo para administradores -->
      <li class="nav-item active">
        <a class="nav-link" href="usuarios.php">Usuarios<span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item active">
        <a class="nav-link" href="home_admin.php">Ternurines<span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item active">
        <a class="nav-link" href="crear_ternurin.php">Crear un nuevo ternurin<span class="sr-only">(current)</span></a>
      </li>
      <?php else: ?>
      <!-- opciones para usuarios invitados -->
      <li class="nav-item active">
        <a class="nav-link" href="home.php">Inicio <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="baul.php">Mi baul</a>
      </li>
      <?php endif; ?>
      <li class="nav-item">
        <a class="nav-link" href="cerrar_sesion.php">Cerrar sesion</a>
      </li>
    </ul>
    <span class="navbar-text">
    </span>
  </div>
</nav>
 