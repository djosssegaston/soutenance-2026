<?php
$page_inline_scripts = isset($page_inline_scripts) ? trim((string) $page_inline_scripts) : '';
?>
      </div>
    </main>
  </div>
  <?php include __DIR__ . '/mobile_bottom_nav.html'; ?>
  <?php include __DIR__ . '/dashboard_logout_modal.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="<?php echo htmlspecialchars(dashboard_asset('js/dashboard.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
  <script src="<?php echo htmlspecialchars(dashboard_asset('js/dashboard-enhancements.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
<?php if ($page_inline_scripts !== ''): ?>
  <script>
<?php echo $page_inline_scripts . PHP_EOL; ?>
  </script>
<?php endif; ?>
</body>
</html>
