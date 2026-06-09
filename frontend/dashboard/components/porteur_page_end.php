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
  <script>
    // Global feedback modal helper
    (function () {
      var modalEl = document.getElementById('actionFeedbackModal');
      if (!modalEl) {
        var wrapper = document.createElement('div');
        wrapper.innerHTML = '<div class="modal fade" id="actionFeedbackModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content text-center p-4"><div id="feedbackIcon" class="mb-2" style="font-size:42px;"></div><h6 id="feedbackMessage" class="mb-0"></h6><button type="button" class="btn-dashboard primary mt-3" data-bs-dismiss="modal">OK</button></div></div></div>';
        document.body.appendChild(wrapper.firstChild);
        modalEl = document.getElementById('actionFeedbackModal');
      }
      window.showFeedback = function(type, message, onClose) {
        var icon = document.getElementById('feedbackIcon');
        var text = document.getElementById('feedbackMessage');
        if (!icon || !text) return;
        if (type === 'success') {
          icon.innerHTML = '<i class="bi bi-check-circle-fill text-success"></i>';
        } else {
          icon.innerHTML = '<i class="bi bi-x-circle-fill text-danger"></i>';
        }
        text.textContent = message || '';
        var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
        modalEl.addEventListener('hidden.bs.modal', function handler() {
          modalEl.removeEventListener('hidden.bs.modal', handler);
          if (typeof onClose === 'function') { onClose(); }
        });
      };
    })();

    // Simple dropdown toggles (notifications / user) for topbar
    (function() {
      var triggers = document.querySelectorAll('[data-dropdown-target]');
      var style = document.createElement('style');
      style.innerHTML = '.dropdown-dashboard{display:none;} .dropdown-dashboard.is-open{display:block;}';
      document.head.appendChild(style);
      function closeAll() {
        document.querySelectorAll('.dropdown-dashboard').forEach(function(el){ el.classList.remove('is-open'); el.setAttribute('hidden','hidden'); });
      }
      triggers.forEach(function(btn) {
        btn.addEventListener('click', function(e){
          e.preventDefault();
          e.stopPropagation();
          var id = btn.getAttribute('data-dropdown-target');
          var target = document.getElementById(id);
          if (!target) return;
          var isOpen = target.classList.contains('is-open');
          closeAll();
          if (!isOpen) {
            target.classList.add('is-open');
            target.removeAttribute('hidden');
          }
        });
      });
      document.addEventListener('click', function(e){
        if (!e.target.closest('.dashboard-dropdown-wrap')) {
          closeAll();
        }
      });
    })();
  </script>
</body>
</html>
