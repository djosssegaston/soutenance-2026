(function ($) {
  "use strict";

  if (!$) {
    return;
  }

  var targetCounter = 0;

  $(function () {
    initResponsiveTables();
    initTableActionButtons();
    initSearchInterfaces();
    initSkeletonLoading();
    initTooltips();
    initLogoutConfirmation();
  });

  function initResponsiveTables() {
    $(".dashboard-table, .data-table").each(function (tableIndex) {
      var $table = $(this);
      var tableKey = $table.closest(".dashboard-table-wrapper").attr("id") || ("dashboard-table-" + (tableIndex + 1));
      var headers = [];

      $table.find("thead th").each(function () {
        headers.push($.trim($(this).text()));
      });

      $table.find("tbody tr").not(".dashboard-table-empty-row").each(function (rowIndex) {
        var $row = $(this);
        ensureSearchTargetId($row, tableKey + "-row-" + (rowIndex + 1));
        $row.attr("data-page-match", "1");
        $row.attr("data-table-match", "1");

        $row.children("td").each(function (cellIndex) {
          var $cell = $(this);
          if (!$cell.attr("data-label")) {
            $cell.attr("data-label", headers[cellIndex] || "");
          }
        });
      });
    });
  }

  function initTableActionButtons() {
    var selector = [
      ".dashboard-table tbody a.btn-dashboard",
      ".dashboard-table tbody button.btn-dashboard",
      ".dashboard-table tbody a.dashboard-btn-primary",
      ".dashboard-table tbody button.dashboard-btn-primary",
      ".data-table tbody a.btn-dashboard",
      ".data-table tbody button.btn-dashboard",
      ".data-table tbody a.dashboard-btn-primary",
      ".data-table tbody button.dashboard-btn-primary"
    ].join(", ");

    $(selector).each(function () {
      var $action = $(this);
      var actionLabel = inferActionLabel($action);
      var iconClass = resolveActionIcon(actionLabel);
      var variantClass = resolveActionVariant(actionLabel);
      var $cell = $action.closest("td");

      $action.addClass("dashboard-table-action");
      $cell.addClass("dashboard-table-actions-cell");
      if (variantClass) {
        $action.addClass(variantClass);
      }

      if (!$action.find("i, svg").length) {
        $action.prepend('<i class="bi ' + iconClass + '"></i>');
      }

      if (!$action.find("span").length) {
        $action.contents().filter(function () {
          return this.nodeType === 3;
        }).remove();
        $action.append($("<span></span>").text(actionLabel));
      }

      $action.attr("title", actionLabel);
      $action.attr("aria-label", actionLabel);
      $action.attr("data-bs-toggle", "tooltip");
      $action.attr("data-bs-placement", "top");
      $action.find("span").addClass("dashboard-table-action__label");
    });
  }

  function initSearchInterfaces() {
    $(".js-dashboard-search").each(function () {
      var $form = $(this);
      var $input = $form.find('input[type="search"]');
      var $suggestions = $form.siblings(".dashboard-search-suggestions");
      var scope = String($form.data("searchScope") || "page");

      if (!$input.length) {
        return;
      }

      if (!$suggestions.length) {
        $suggestions = $('<div class="dashboard-search-suggestions" hidden></div>');
        $form.after($suggestions);
      }

      var runSearch = debounce(function () {
        var query = $input.val();
        if (scope === "table") {
          applyTableSearch($form, query, $suggestions);
        } else {
          applyPageSearch(query, $suggestions);
        }
      }, 60);

      $input.on("input", runSearch);

      $input.on("focus", function () {
        if ($input.val()) {
          runSearch();
        }
      });

      $input.on("keydown", function (event) {
        if (event.key === "Escape") {
          $input.val("");
          if (scope === "table") {
            applyTableSearch($form, "", $suggestions);
          } else {
            applyPageSearch("", $suggestions);
          }
          hideSuggestions($suggestions);
        }

        if (event.key === "Enter") {
          var $firstSuggestion = $suggestions.find(".dashboard-search-suggestion").first();
          if ($firstSuggestion.length) {
            event.preventDefault();
            $firstSuggestion.trigger("click");
          }
        }
      });

      $form.on("submit", function (event) {
        event.preventDefault();
        runSearch();
      });
    });

    $(document).on("click", ".dashboard-search-suggestion", function () {
      var $suggestion = $(this);
      var targetId = $suggestion.data("targetId");
      var $shell = $suggestion.closest(".dashboard-search-shell");
      var $input = $shell.find('input[type="search"]').first();
      var $target = $("#" + targetId);

      if ($input.length && $suggestion.data("fill")) {
        $input.val(String($suggestion.data("fill")));
      }

      if ($target.length) {
        revealTargetContext($target);
        scrollToTarget($target);
        highlightTarget($target);
      }

      hideSuggestions($shell.find(".dashboard-search-suggestions"));
    });

    $(document).on("click", function (event) {
      if (!$(event.target).closest(".dashboard-search-shell").length) {
        $(".dashboard-search-suggestions").attr("hidden", true);
      }
    });
  }

  function applyTableSearch($form, rawQuery, $suggestions) {
    var query = normalizeText(rawQuery);
    var targetId = String($form.data("searchTarget") || "");
    var $wrapper = $("#" + targetId);
    var $table = $wrapper.find("table").first();
    var matches = [];

    if (!$table.length) {
      hideSuggestions($suggestions);
      return;
    }

    getTableRows($table).each(function () {
      var $row = $(this);
      var isMatch = !query || readSearchText($row).indexOf(query) !== -1;
      setRowMatch($row, "table", isMatch);

      if (query && isMatch) {
        matches.push(buildSuggestionItem($row, "Ligne du tableau"));
      }
    });

    syncTableEmptyState($table, rawQuery);

    if (!query) {
      hideSuggestions($suggestions);
      return;
    }

    renderSuggestions($suggestions, matches, rawQuery, "Aucun résultat dans ce tableau.");
  }

  function applyPageSearch(rawQuery, $suggestions) {
    var query = normalizeText(rawQuery);
    var matches = [];

    getPageSearchCandidates().each(function () {
      var $candidate = $(this);
      var isMatch = !query || readSearchText($candidate).indexOf(query) !== -1;

      if ($candidate.is("tr")) {
        setRowMatch($candidate, "page", isMatch);
      } else {
        $candidate.toggleClass("dashboard-search-dimmed", !!query && !isMatch);
        $candidate.toggleClass("dashboard-search-match", !!query && isMatch);
      }

      if (query && isMatch) {
        matches.push(buildSuggestionItem($candidate, getSuggestionMeta($candidate)));
      }
    });

    $(".dashboard-table, .data-table").each(function () {
      syncTableEmptyState($(this), rawQuery);
    });

    if (!query) {
      resetPageSearchState();
      hideSuggestions($suggestions);
      return;
    }

    renderSuggestions($suggestions, matches, rawQuery, "Aucun élément correspondant sur cette page.");
  }

  function resetPageSearchState() {
    $(".dashboard-card, .content-card, .stat-card, .dashboard-timeline li")
      .removeClass("dashboard-search-dimmed dashboard-search-match");

    $(".dashboard-table tbody tr, .data-table tbody tr").not(".dashboard-table-empty-row").each(function () {
      setRowMatch($(this), "page", true);
    });
  }

  function getPageSearchCandidates() {
    return $([
      ".dashboard-content .stat-card",
      ".dashboard-content .dashboard-card:not(.dashboard-table-card)",
      ".dashboard-content .dashboard-timeline li",
      ".dashboard-content .dashboard-table tbody tr"
    ].join(", ")).filter(function () {
      var $element = $(this);
      return !$element.hasClass("dashboard-table-empty-row") && $.trim($element.text()).length > 0;
    });
  }

  function buildSuggestionItem($element, metaLabel) {
    ensureSearchTargetId($element, "dashboard-search-target");

    return {
      targetId: $element.attr("id"),
      fill: extractSuggestionFill($element),
      title: extractSuggestionTitle($element),
      meta: metaLabel || ""
    };
  }

  function renderSuggestions($container, matches, rawQuery, emptyMessage) {
    var query = $.trim(String(rawQuery || ""));

    if (!query) {
      hideSuggestions($container);
      return;
    }

    if (!matches.length) {
      $container.html('<div class="dashboard-search-no-results">' + escapeHtml(emptyMessage || "Aucun résultat.") + "</div>");
      $container.attr("hidden", false);
      return;
    }

    var html = matches.slice(0, 6).map(function (item) {
      return '' +
        '<button type="button" class="dashboard-search-suggestion" data-target-id="' + escapeHtml(item.targetId) + '" data-fill="' + escapeHtml(item.fill) + '">' +
          '<span class="dashboard-search-suggestion__title">' + escapeHtml(item.title) + "</span>" +
          '<span class="dashboard-search-suggestion__meta">' + escapeHtml(item.meta) + "</span>" +
        "</button>";
    }).join("");

    $container.html(html);
    $container.attr("hidden", false);
  }

  function hideSuggestions($container) {
    if ($container && $container.length) {
      $container.attr("hidden", true);
      $container.empty();
    }
  }

  function getTableRows($table) {
    return $table.find("tbody tr").not(".dashboard-table-empty-row");
  }

  function setRowMatch($row, type, isMatch) {
    if (type === "page") {
      $row.attr("data-page-match", isMatch ? "1" : "0");
    } else {
      $row.attr("data-table-match", isMatch ? "1" : "0");
    }

    applyRowVisibility($row);
  }

  function applyRowVisibility($row) {
    var pageMatch = $row.attr("data-page-match") !== "0";
    var tableMatch = $row.attr("data-table-match") !== "0";
    $row.toggle(pageMatch && tableMatch);
  }

  function syncTableEmptyState($table, rawQuery) {
    var query = resolveActiveTableQuery($table, rawQuery);
    var $tbody = $table.find("tbody");
    var visibleRows = getTableRows($table).filter(function () {
      return $(this).css("display") !== "none";
    }).length;
    var columnCount = $table.find("thead th").length || 1;
    var $emptyRow = $tbody.children(".dashboard-table-empty-row");

    if (!query || visibleRows > 0) {
      $emptyRow.remove();
      return;
    }

    if (!$emptyRow.length) {
      $emptyRow = $('<tr class="dashboard-table-empty-row"><td colspan="' + columnCount + '"></td></tr>');
      $tbody.append($emptyRow);
    }

    $emptyRow.find("td").attr("colspan", columnCount).text('Aucun résultat pour "' + query + '".');
  }

  function resolveActiveTableQuery($table, rawQuery) {
    var query = $.trim(String(rawQuery || ""));
    if (query) {
      return query;
    }

    var tableId = $table.closest(".dashboard-table-wrapper").attr("id");
    if (tableId) {
      var $linkedInput = $('.js-dashboard-search[data-search-scope="table"][data-search-target="' + tableId + '"]').find('input[type="search"]').first();
      if ($linkedInput.length) {
        query = $.trim(String($linkedInput.val() || ""));
        if (query) {
          return query;
        }
      }
    }

    var $pageInput = $('.js-dashboard-search[data-search-scope="page"]').find('input[type="search"]').first();
    if ($pageInput.length) {
      query = $.trim(String($pageInput.val() || ""));
    }

    return query;
  }

  function revealTargetContext($target) {
    if ($target.is("tr")) {
      $target.attr("data-page-match", "1");
      $target.attr("data-table-match", "1");
      applyRowVisibility($target);

      var $table = $target.closest("table");
      if ($table.length) {
        syncTableEmptyState($table, "");
      }
    } else {
      $target.removeClass("dashboard-search-dimmed").addClass("dashboard-search-match");
    }
  }

  function scrollToTarget($target) {
    var offsetTop = $target.offset() ? $target.offset().top : 0;
    var topbarHeight = $(".dashboard-topbar").outerHeight() || 72;

    $("html, body").stop(true).animate({
      scrollTop: Math.max(offsetTop - topbarHeight - 24, 0)
    }, 350);
  }

  function highlightTarget($target) {
    $target.addClass("dashboard-search-hit");

    window.setTimeout(function () {
      $target.removeClass("dashboard-search-hit");
    }, 2600);
  }

  function initSkeletonLoading() {
    var $targets = $(".dashboard-content .stat-card, .dashboard-content .dashboard-card").filter(function () {
      return !$(this).closest(".dashboard-search-suggestions").length && !$(this).find("> .dashboard-skeleton-overlay").length;
    });

    $targets.each(function (index) {
      var $target = $(this);
      var skeletonType = "card";

      if ($target.is(".stat-card")) {
        skeletonType = "stat";
      } else if ($target.find("table").length) {
        skeletonType = "table";
      } else if ($target.find("canvas").length) {
        skeletonType = "chart";
      }

      var $overlay = $(buildSkeletonMarkup(skeletonType));
      $target.addClass("dashboard-skeletonable").append($overlay);

      window.setTimeout(function () {
        $overlay.addClass("is-hidden");
        window.setTimeout(function () {
          $overlay.remove();
          $target.removeClass("dashboard-skeletonable");
        }, 360);
      }, 220 + Math.min(index * 45, 560));
    });
  }

  function buildSkeletonMarkup(type) {
    if (type === "stat") {
      return '' +
        '<div class="dashboard-skeleton-overlay">' +
          '<div class="dashboard-skeleton-chip"></div>' +
          '<div class="dashboard-skeleton-line is-short"></div>' +
          '<div class="dashboard-skeleton-line is-title"></div>' +
          '<div class="dashboard-skeleton-line is-short"></div>' +
        "</div>";
    }

    if (type === "table") {
      return '' +
        '<div class="dashboard-skeleton-overlay">' +
          '<div class="dashboard-skeleton-line is-title"></div>' +
          '<div class="dashboard-skeleton-grid">' +
            '<div class="dashboard-skeleton-row">' +
              '<div class="dashboard-skeleton-line"></div>' +
              '<div class="dashboard-skeleton-line"></div>' +
              '<div class="dashboard-skeleton-line"></div>' +
              '<div class="dashboard-skeleton-line"></div>' +
            "</div>" +
            '<div class="dashboard-skeleton-row">' +
              '<div class="dashboard-skeleton-line"></div>' +
              '<div class="dashboard-skeleton-line"></div>' +
              '<div class="dashboard-skeleton-line"></div>' +
              '<div class="dashboard-skeleton-line"></div>' +
            "</div>" +
            '<div class="dashboard-skeleton-row">' +
              '<div class="dashboard-skeleton-line"></div>' +
              '<div class="dashboard-skeleton-line"></div>' +
              '<div class="dashboard-skeleton-line"></div>' +
              '<div class="dashboard-skeleton-line"></div>' +
            "</div>" +
          "</div>" +
        "</div>";
    }

    if (type === "chart") {
      return '' +
        '<div class="dashboard-skeleton-overlay">' +
          '<div class="dashboard-skeleton-line is-title"></div>' +
          '<div class="dashboard-skeleton-line is-medium"></div>' +
          '<div class="dashboard-skeleton-block"></div>' +
        "</div>";
    }

    return '' +
      '<div class="dashboard-skeleton-overlay">' +
        '<div class="dashboard-skeleton-line is-title"></div>' +
        '<div class="dashboard-skeleton-line is-medium"></div>' +
        '<div class="dashboard-skeleton-line"></div>' +
        '<div class="dashboard-skeleton-line"></div>' +
        '<div class="dashboard-skeleton-line is-short"></div>' +
      "</div>";
  }

  function initTooltips() {
    if (typeof bootstrap === "undefined" || !bootstrap.Tooltip) {
      return;
    }

    $('[data-bs-toggle="tooltip"]').each(function () {
      bootstrap.Tooltip.getOrCreateInstance(this);
    });
  }

  function initLogoutConfirmation() {
    if (typeof bootstrap === "undefined" || !bootstrap.Modal) {
      return;
    }

    var modalElement = document.getElementById("dashboardLogoutModal");
    if (!modalElement) {
      return;
    }

    var logoutModal = bootstrap.Modal.getOrCreateInstance(modalElement);
    var confirmButton = modalElement.querySelector("[data-logout-confirm]");
    var triggers = document.querySelectorAll(".js-dashboard-logout");

    triggers.forEach(function (trigger) {
      trigger.addEventListener("click", function (event) {
        event.preventDefault();
        logoutModal.show();
      });
    });

    if (confirmButton) {
      confirmButton.addEventListener("click", function () {
        var logoutUrl = confirmButton.getAttribute("data-logout-url") || "logout.php";
        logoutModal.hide();
        window.location.href = logoutUrl;
      });
    }
  }

  function inferActionLabel($action) {
    var label = $.trim(String($action.attr("aria-label") || $action.attr("title") || ""));

    if (label) {
      return label;
    }

    var $clone = $action.clone();
    $clone.find("i, svg").remove();
    label = $.trim($clone.text()).replace(/\s+/g, " ");

    return label || "Action";
  }

  function resolveActionIcon(label) {
    var normalized = normalizeText(label);

    if (/supprimer|effacer|retirer/.test(normalized)) {
      return "bi-trash";
    }

    if (/suspendre|bloquer/.test(normalized)) {
      return "bi-slash-circle";
    }

    if (/refuser|rejeter|fermer/.test(normalized)) {
      return "bi-x-circle";
    }

    if (/valider|approuver|activer/.test(normalized)) {
      return "bi-check-circle";
    }

    if (/modifier|editer|éditer|mettre a jour|mettre à jour/.test(normalized)) {
      return "bi-pencil-square";
    }

    if (/voir|ouvrir|consulter|detail|détail/.test(normalized)) {
      return "bi-eye";
    }

    return "bi-three-dots";
  }

  function resolveActionVariant(label) {
    var normalized = normalizeText(label);

    if (/supprimer|suspendre|refuser|rejeter|fermer/.test(normalized)) {
      return "dashboard-table-action--danger";
    }

    if (/valider|approuver|activer/.test(normalized)) {
      return "dashboard-table-action--success";
    }

    if (/modifier|editer|éditer|mettre a jour|mettre à jour/.test(normalized)) {
      return "dashboard-table-action--warning";
    }

    if (/voir|ouvrir|consulter|detail|détail/.test(normalized)) {
      return "dashboard-table-action--info";
    }

    return "";
  }

  function extractSuggestionTitle($element) {
    var label = "";

    if ($element.is("tr")) {
      var $cells = $element.children("td");
      label = $.trim($cells.eq(1).text()) || $.trim($cells.eq(0).text());
    } else {
      label = $.trim($element.find("h1, h2, h3, h4, h5, h6, strong").first().text());
    }

    if (!label) {
      label = truncateText($.trim($element.text()).replace(/\s+/g, " "), 64);
    }

    return label || "Résultat";
  }

  function extractSuggestionFill($element) {
    return extractSuggestionTitle($element);
  }

  function getSuggestionMeta($element) {
    if ($element.is("tr")) {
      return "Résultat de tableau";
    }

    if ($element.is(".stat-card")) {
      return "Carte KPI";
    }

    if ($element.is("li")) {
      return "Élément de suivi";
    }

    return "Bloc du dashboard";
  }

  function readSearchText($element) {
    var cached = $element.attr("data-search-text");

    if (cached) {
      return cached;
    }

    var text = normalizeText($.trim($element.text()).replace(/\s+/g, " "));
    $element.attr("data-search-text", text);
    return text;
  }

  function ensureSearchTargetId($element, fallbackPrefix) {
    if (!$element.attr("id")) {
      targetCounter += 1;
      $element.attr("id", fallbackPrefix + "-" + targetCounter);
    }
  }

  function normalizeText(value) {
    var text = String(value || "").toLowerCase();

    if (typeof text.normalize === "function") {
      text = text.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    }

    return text.replace(/\s+/g, " ").trim();
  }

  function truncateText(value, maxLength) {
    var text = String(value || "");
    if (text.length <= maxLength) {
      return text;
    }

    return text.slice(0, maxLength - 1) + "…";
  }

  function escapeHtml(value) {
    return String(value || "")
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;");
  }

  function debounce(callback, delay) {
    var timeoutId;

    return function () {
      var context = this;
      var args = arguments;

      window.clearTimeout(timeoutId);
      timeoutId = window.setTimeout(function () {
        callback.apply(context, args);
      }, delay);
    };
  }
})(window.jQuery);
