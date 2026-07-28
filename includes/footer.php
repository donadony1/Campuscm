<footer class="bg-dark text-light mt-5 py-4">
  <div class="container text-center">
    <p class="mb-1 fw-semibold"><i class="bi bi-mortarboard-fill"></i> <?= APP_NAME ?></p>
    <p class="mb-0 small text-secondary">La plateforme qui connecte les écoles de formation aux étudiants camerounais.</p>
    <p class="mb-0 small text-secondary">&copy; <?= date('Y') ?> <?= APP_NAME ?>. Tous droits réservés.</p>
  </div>
</footer>

<!-- Menu mobile façon application : barre fixe en bas, visible uniquement en dessous de md -->
<nav class="app-bottom-nav d-md-none">
  <a href="home" class="app-bottom-nav-item <?= ($route ?? '') === 'home' ? 'active' : '' ?>">
    <i class="bi bi-house-door-fill"></i>
    <span>Accueil</span>
  </a>
  <a href="recherche" class="app-bottom-nav-item <?= ($route ?? '') === 'recherche' ? 'active' : '' ?>">
    <i class="bi bi-building"></i>
    <span>Écoles</span>
  </a>
  <a href="formations" class="app-bottom-nav-item <?= ($route ?? '') === 'formations' ? 'active' : '' ?>">
    <i class="bi bi-journal-bookmark"></i>
    <span>Formations</span>
  </a>
  <?php if (is_logged_in()): ?>
    <?php $lienCompte = current_user()['role'] === 'super_admin' ? 'superadmin/dashboard.php' : 'admin/dashboard'; ?>
    <a href="<?= e($lienCompte) ?>" class="app-bottom-nav-item">
      <i class="bi bi-person-circle"></i>
      <span>Mon compte</span>
    </a>
  <?php else: ?>
    <a href="login" class="app-bottom-nav-item <?= ($route ?? '') === 'login' ? 'active' : '' ?>">
      <i class="bi bi-person-circle"></i>
      <span>Connexion</span>
    </a>
  <?php endif; ?>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
