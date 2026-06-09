(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    syncTopbarSpacing();
    initSidebar();
    initDropdowns();
    initActiveSidebarLink();
    initCharts();
    initAjaxActions();
    initFakeDeleteActions();
    initAutoPagination();
  });

  function syncTopbarSpacing() {
    var topbar = document.querySelector(".dashboard-topbar");
    var main = document.querySelector(".dashboard-content.main-content, .dashboard-content, .main-content");

    if (!topbar || !main) {
      return;
    }

    function applySpacing() {
      var topbarHeight = Math.ceil(topbar.getBoundingClientRect().height || 0);
      var container = main.querySelector(":scope > .container-fluid");
      var firstBlock = container ? Array.prototype.find.call(container.children, function (child) {
        return child && child.nodeType === 1;
      }) : null;

      if (topbarHeight > 0) {
        document.body.style.setProperty("--dashboard-topbar-height", topbarHeight + "px");
        main.style.marginTop = topbarHeight + "px";
      }

      main.style.paddingTop = "0px";

      if (container) {
        container.style.paddingTop = "0px";
      }

      if (firstBlock) {
        firstBlock.style.marginTop = "0px";

        if (firstBlock.className && String(firstBlock.className).indexOf("page-stack") !== -1) {
          var firstStackChild = Array.prototype.find.call(firstBlock.children, function (child) {
            return child && child.nodeType === 1;
          });

          if (firstStackChild) {
            firstStackChild.style.marginTop = "0px";
          }
        }
      }
    }

    applySpacing();
    window.addEventListener("load", applySpacing);
    window.addEventListener("resize", applySpacing);
  }

  function initSidebar() {
    var body = document.body;
    var sidebar = document.querySelector(".sidebar") || document.getElementById("dashboardSidebar");
    var overlay = document.querySelector(".sidebar-overlay");
    var toggleButtons = document.querySelectorAll(".dashboard-menu-toggle, .sidebar-toggle");
    var closeButton = document.querySelector(".dashboard-sidebar-close");

    if (!sidebar) {
      return;
    }

    function openSidebar() {
      if (window.innerWidth > 992) {
        return;
      }
      sidebar.classList.add("is-open");
      sidebar.classList.add("open");
      if (overlay) {
        overlay.classList.add("show");
        overlay.classList.add("active");
      }
      body.classList.add("sidebar-open");
    }

    function closeSidebar() {
      sidebar.classList.remove("is-open");
      sidebar.classList.remove("open");
      if (overlay) {
        overlay.classList.remove("show");
        overlay.classList.remove("active");
      }
      body.classList.remove("sidebar-open");
    }

    toggleButtons.forEach(function (button) {
      button.addEventListener("click", function (event) {
        event.preventDefault();
        if (sidebar.classList.contains("is-open")) {
          closeSidebar();
        } else {
          openSidebar();
        }
      });
    });

    if (closeButton) {
      closeButton.addEventListener("click", function () {
        closeSidebar();
      });
    }

    if (overlay) {
      overlay.addEventListener("click", function () {
        closeSidebar();
      });
    }

    window.addEventListener("resize", function () {
      if (window.innerWidth > 992) {
        closeSidebar();
      }
    });
  }

  function initDropdowns() {
    var toggles = document.querySelectorAll("[data-dropdown-target]");

    function closeAllDropdowns() {
      document.querySelectorAll(".dashboard-dropdown-panel.show").forEach(function (panel) {
        panel.classList.remove("show");
      });
    }

    toggles.forEach(function (toggle) {
      toggle.addEventListener("click", function (event) {
        event.preventDefault();
        event.stopPropagation();

        var targetId = toggle.getAttribute("data-dropdown-target");
        var panel = document.getElementById(targetId);

        if (!panel) {
          return;
        }

        var isOpen = panel.classList.contains("show");
        closeAllDropdowns();

        if (!isOpen) {
          panel.classList.add("show");
        }
      });
    });

    document.addEventListener("click", function (event) {
      if (!event.target.closest(".dashboard-dropdown-wrap")) {
        closeAllDropdowns();
      }
    });
  }

  function initActiveSidebarLink() {
    var currentPath = window.location.pathname.split("/").pop();
    var links = document.querySelectorAll(".dashboard-nav__link");
    var hasExactMatch = false;

    links.forEach(function (link) {
      var href = (link.getAttribute("href") || "").split("/").pop();
      link.classList.remove("active");

      if (href && href === currentPath) {
        link.classList.add("active");
        hasExactMatch = true;
      }
    });

    if (!hasExactMatch) {
      var fallback = document.body.getAttribute("data-active-nav");
      if (!fallback) {
        return;
      }

      var fallbackLink = document.querySelector('.dashboard-nav__link[data-nav="' + fallback + '"]');
      if (fallbackLink) {
        fallbackLink.classList.add("active");
      }
    }
  }



  function getCookieValue(name) {
    var matches = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\$1') + '=([^;]*)'));
    return matches ? decodeURIComponent(matches[1]) : '';
  }

  function initFakeDeleteActions() {
    document.addEventListener('click', function (event) {
      var target = event.target;
      if (!target) {
        return;
      }
      var button = target.closest('button, a');
      if (!button) {
        return;
      }
      var label = (button.textContent || '').toLowerCase();
      if (label.indexOf('supprimer') === -1) {
        return;
      }
      event.preventDefault();
      var confirmed = window.confirm('Confirmer la suppression ?');
      if (!confirmed) {
        return;
      }

      var removable = button.closest('tr')
        || button.closest('.dashboard-card')
        || button.closest('.list-group-item')
        || button.closest('.project-card')
        || button.closest('.timeline-item')
        || button.closest('.card')
        || button.closest('.col-12')
        || button.closest('li');

      if (removable) {
        removable.remove();
      }

      showActionToast('Suppression reussie.');
    });
  }

  function initAjaxActions() {
    document.addEventListener('click', function (event) {
      var target = event.target;
      if (!target) {
        return;
      }
      var button = target.closest('[data-ajax-url]');
      if (!button) {
        return;
      }
      event.preventDefault();

      var url = button.getAttribute('data-ajax-url');
      button.classList.add('is-loading');
      button.disabled = true;
      if (!url) {
        return;
      }

      var csrfToken = window.csrfToken || (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
      fetch(url, {
        method: 'POST',
        credentials: 'include',
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
          ...(!csrfToken ? { 'X-CSRF-TOKEN': getCookieValue('XSRF-TOKEN') } : {})
        }
      }).then(function (response) {
        return response.json().then(function (data) {
          return { ok: response.ok, data: data };
        }).catch(function () {
          return { ok: response.ok, data: {} };
        });
      }).then(function (result) {
        var data = result.data || {};
        var targetSelector = button.getAttribute('data-ajax-target');
        if (targetSelector) {
          var el = document.querySelector(targetSelector);
          if (el) {
            if (data.status_label) {
              el.textContent = data.status_label;
            }
            if (data.status_class) {
              el.className = 'status-badge ' + data.status_class;
            }
          }
        }

        var feedbackSelector = button.getAttribute('data-ajax-feedback');
        if (feedbackSelector) {
          var feedback = document.querySelector(feedbackSelector);
          if (feedback) {
            feedback.hidden = false;
            feedback.textContent = data.message || (result.ok ? 'Action enregistree.' : 'Action impossible.');
          }
        }

        if (!result.ok) {
          showActionToast(data.message || 'Action impossible.');
          button.classList.remove('is-loading');
          button.disabled = false;
          return;
        }

        showActionToast(data.message || 'Action enregistree.');
        button.classList.remove('is-loading');
        button.disabled = false;
      }).catch(function () {
        showActionToast('Erreur reseau.');
        button.classList.remove('is-loading');
        button.disabled = false;
      });
    });
  }

  function showActionToast(message) {
    var toast = document.createElement('div');
    toast.textContent = message;
    toast.style.position = 'fixed';
    toast.style.right = '20px';
    toast.style.bottom = '20px';
    toast.style.background = '#1f915f';
    toast.style.color = '#fff';
    toast.style.padding = '10px 14px';
    toast.style.borderRadius = '10px';
    toast.style.fontSize = '13px';
    toast.style.fontWeight = '600';
    toast.style.boxShadow = '0 10px 30px rgba(6, 42, 38, 0.2)';
    toast.style.zIndex = '9999';
    toast.style.opacity = '0';
    toast.style.transition = 'opacity 0.2s ease';
    document.body.appendChild(toast);

    requestAnimationFrame(function () {
      toast.style.opacity = '1';
    });

    setTimeout(function () {
      toast.style.opacity = '0';
      setTimeout(function () {
        toast.remove();
      }, 250);
    }, 2000);
  }

  function initCharts() {
    if (typeof Chart === "undefined") {
      return;
    }

    var headingColor = readCssVar("--text-heading-color", "#062A26");
    var bodyColor = readCssVar("--p-color", "#6A726F");
    var borderColor = readCssVar("--border-color-2", "#E9E9E8");
    var green = readCssVar("--primary-color-1", "#00C486");
    var blue = readCssVar("--primary-color-2", "#0048DC");
    var orange = readCssVar("--primary-color-3", "#FCA028");
        var chartPayloads = window.dashboardCharts || {};

    function getChartPayload(key, fallback) {
      if (chartPayloads && chartPayloads[key]) {
        return chartPayloads[key];
      }
      return fallback;
    }
Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size = 11;
    Chart.defaults.color = "#6A726F";
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.legend.labels.pointStyleWidth = 8;
    Chart.defaults.plugins.legend.labels.padding = 16;
    Chart.defaults.plugins.legend.labels.font = { size: 11, weight: "500" };
    Chart.defaults.elements.line.tension = 0.35;
    Chart.defaults.elements.point.radius = 0;
    Chart.defaults.elements.point.hoverRadius = 5;
    Chart.defaults.scale.grid.color = "rgba(233,233,232,0.5)";
    Chart.defaults.scale.border.display = false;

    var tooltipOptions = {
      backgroundColor: "#FFFFFF",
      titleColor: headingColor,
      bodyColor: bodyColor,
      borderColor: borderColor,
      borderWidth: 1,
      padding: 12,
      cornerRadius: 12,
      displayColors: true
    };

        var adminFundingCanvas = document.getElementById("adminFundingChart");
    if (adminFundingCanvas) {
      var adminFundingPayload = getChartPayload("adminFunding", {
        labels: ["Jan", "F�v", "Mar", "Avr", "Mai", "Jun"],
        financements: [320, 410, 520, 610, 720, 840],
        remboursements: [230, 295, 360, 430, 500, 610]
      });
      new Chart(adminFundingCanvas, {
        type: "line",
        data: {
          labels: adminFundingPayload.labels,
          datasets: [
            {
              label: "Financements",
              data: adminFundingPayload.financements,
              borderColor: green,
              borderWidth: 2,
              fill: true,
              backgroundColor: function (context) {
                var chart = context.chart;
                var area = chart.chartArea;
                if (!area) {
                  return "rgba(0, 196, 134, 0.2)";
                }
                var gradient = chart.ctx.createLinearGradient(0, area.top, 0, area.bottom);
                gradient.addColorStop(0, "rgba(0, 196, 134, 0.35)");
                gradient.addColorStop(1, "rgba(0, 196, 134, 0.02)");
                return gradient;
              }
            },
            {
              label: "Remboursements",
              data: adminFundingPayload.remboursements,
              borderColor: blue,
              borderWidth: 2,
              fill: false,
              borderDash: [8, 6]
            }
          ]
        },
        options: {
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: "top"
            },
            tooltip: tooltipOptions
          },
          scales: {
            x: {
              grid: {
                display: false,
                drawBorder: false
              },
              ticks: {
                color: bodyColor,
                font: {
                  size: 11
                }
              }
            },
            y: {
              beginAtZero: true,
              ticks: {
                color: bodyColor,
                font: {
                  size: 11
                },
                callback: function (value) {
                  return value + " M";
                }
              },
              grid: {
                color: "rgba(106, 114, 111, 0.15)",
                borderDash: [4, 6],
                drawBorder: false
              }
            }
          }
        }
      });
    }

        var adminSectorCanvas = document.getElementById("adminSectorChart");
    if (adminSectorCanvas) {
      var adminSectorPayload = getChartPayload("adminSector", {
        labels: ["Agriculture", "Technologie", "Sant�", "Transport", "Autre"],
        values: [35, 25, 20, 15, 5]
      });
      new Chart(adminSectorCanvas, {
        type: "doughnut",
        data: {
          labels: adminSectorPayload.labels,
          datasets: [
            {
              data: adminSectorPayload.values,
              backgroundColor: [green, blue, orange, red, bodyColor],
              borderWidth: 0,
              hoverOffset: 6
            }
          ]
        },
        options: {
          maintainAspectRatio: false,
          cutout: "68%",
          plugins: {
            legend: {
              display: false
            },
            tooltip: tooltipOptions
          }
        }
      });
    }

        var porteurRepaymentCanvas = document.getElementById("porteurRepaymentChart");
    if (porteurRepaymentCanvas) {
      var porteurRepaymentPayload = getChartPayload("porteurRepayment", {
        labels: ["Jan", "F�v", "Mar", "Avr", "Mai", "Jun"],
        values: [850, 850, 850, 850, 850, 0]
      });
      window.porteurRepaymentChart = new Chart(porteurRepaymentCanvas, {
        type: "bar",
        data: {
          labels: porteurRepaymentPayload.labels,
          datasets: [
            {
              label: "Remboursements",
              data: porteurRepaymentPayload.values,
              borderRadius: 10,
              maxBarThickness: 44,
              backgroundColor: green
            }
          ]
        },
        options: {
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            },
            tooltip: tooltipOptions
          },
          scales: {
            x: {
              ticks: {
                font: {
                  size: 11
                }
              },
              grid: {
                display: false,
                drawBorder: false
              }
            },
            y: {
              beginAtZero: true,
              ticks: {
                font: {
                  size: 11
                },
                callback: function (value) {
                  return value + "K FCFA";
                }
              },
              grid: {
                color: "rgba(106, 114, 111, 0.15)",
                borderDash: [4, 6],
                drawBorder: false
              }
            }
          }
        }
      });
    }

        var institutionPerformanceCanvas = document.getElementById("institutionPerformanceChart");
    if (institutionPerformanceCanvas) {
      var institutionPerformancePayload = getChartPayload("institutionPerformance", {
        labels: ["Jan", "F�v", "Mar", "Avr", "Mai", "Jun"],
        roi: [8.2, 9.1, 8.7, 10.2, 11.5, 10.8],
        risk: [3.1, 2.8, 3.4, 2.9, 2.5, 2.7]
      });
      new Chart(institutionPerformanceCanvas, {
        type: "line",
        data: {
          labels: institutionPerformancePayload.labels,
          datasets: [
            {
              label: "ROI %",
              data: institutionPerformancePayload.roi,
              borderColor: green,
              borderWidth: 2,
              fill: true,
              backgroundColor: function (context) {
                var chart = context.chart;
                var area = chart.chartArea;
                if (!area) {
                  return "rgba(0, 196, 134, 0.2)";
                }
                var gradient = chart.ctx.createLinearGradient(0, area.top, 0, area.bottom);
                gradient.addColorStop(0, "rgba(0, 196, 134, 0.32)");
                gradient.addColorStop(1, "rgba(0, 196, 134, 0.02)");
                return gradient;
              }
            },
            {
              label: "Risque %",
              data: institutionPerformancePayload.risk,
              borderColor: red,
              borderWidth: 2,
              fill: false,
              borderDash: [8, 6]
            }
          ]
        },
        options: {
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: "top"
            },
            tooltip: tooltipOptions
          },
          scales: {
            x: {
              ticks: {
                font: {
                  size: 11
                }
              },
              grid: {
                display: false,
                drawBorder: false
              }
            },
            y: {
              beginAtZero: true,
              ticks: {
                font: {
                  size: 11
                },
                callback: function (value) {
                  return value + "%";
                }
              },
              grid: {
                color: "rgba(106, 114, 111, 0.15)",
                borderDash: [4, 6],
                drawBorder: false
              }
            }
          }
        }
      });
    }

    var institutionRiskReturnCanvas = document.getElementById("institutionRiskReturnChart"); = document.getElementById("institutionRiskReturnChart");
    if (institutionRiskReturnCanvas) {
      new Chart(institutionRiskReturnCanvas, {
        type: "scatter",
        data: {
          datasets: [
            {
              label: "Risque / rendement",
              data: [
                { x: 22, y: 13.4 },
                { x: 48, y: 11.1 },
                { x: 18, y: 16.8 },
                { x: 69, y: 9.2 }
              ],
              backgroundColor: [green, orange, blue, red],
              pointRadius: 7,
              pointHoverRadius: 8
            }
          ]
        },
        options: {
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            },
            tooltip: tooltipOptions
          },
          scales: {
            x: {
              title: {
                display: true,
                text: "Score de risque"
              },
              beginAtZero: true,
              max: 100,
              grid: {
                color: "rgba(106, 114, 111, 0.15)",
                borderDash: [4, 6],
                drawBorder: false
              }
            },
            y: {
              title: {
                display: true,
                text: "Rentabilit� estim�e %"
              },
              beginAtZero: true,
              grid: {
                color: "rgba(106, 114, 111, 0.15)",
                borderDash: [4, 6],
                drawBorder: false
              }
            }
          }
        }
      });
    }

    var institutionInvestmentsCanvas = document.getElementById("institutionInvestmentsChart");
    if (institutionInvestmentsCanvas) {
      new Chart(institutionInvestmentsCanvas, {
        type: "bar",
        data: {
          labels: ["Jan", "F�v", "Mar", "Avr", "Mai", "Jun"],
          datasets: [
            {
              label: "Investissements",
              data: [4.8, 7.2, 9.8, 6.4, 8.1, 5.6],
              borderRadius: 10,
              maxBarThickness: 40,
              backgroundColor: orange
            }
          ]
        },
        options: {
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            },
            tooltip: tooltipOptions
          },
          scales: {
            x: {
              grid: {
                display: false,
                drawBorder: false
              }
            },
            y: {
              beginAtZero: true,
              ticks: {
                callback: function (value) {
                  return value + " M";
                }
              },
              grid: {
                color: "rgba(106, 114, 111, 0.15)",
                borderDash: [4, 6],
                drawBorder: false
              }
            }
          }
        }
      });
    }

    var institutionRoiCanvas = document.getElementById("institutionRoiChart");
    if (institutionRoiCanvas) {
      new Chart(institutionRoiCanvas, {
        type: "line",
        data: {
          labels: ["Jan", "F�v", "Mar", "Avr", "Mai", "Jun"],
          datasets: [
            {
              label: "ROI",
              data: [8.2, 9.4, 10.1, 10.8, 11.2, 10.9],
              borderColor: green,
              borderWidth: 2,
              fill: true,
              backgroundColor: function (context) {
                var chart = context.chart;
                var area = chart.chartArea;
                if (!area) {
                  return "rgba(0, 196, 134, 0.2)";
                }
                var gradient = chart.ctx.createLinearGradient(0, area.top, 0, area.bottom);
                gradient.addColorStop(0, "rgba(0, 196, 134, 0.30)");
                gradient.addColorStop(1, "rgba(0, 196, 134, 0.02)");
                return gradient;
              }
            }
          ]
        },
        options: {
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            },
            tooltip: tooltipOptions
          },
          scales: {
            x: {
              grid: {
                display: false,
                drawBorder: false
              }
            },
            y: {
              beginAtZero: true,
              ticks: {
                callback: function (value) {
                  return value + "%";
                }
              },
              grid: {
                color: "rgba(106, 114, 111, 0.15)",
                borderDash: [4, 6],
                drawBorder: false
              }
            }
          }
        }
      });
    }

    var institutionProjectPerformanceCanvas = document.getElementById("institutionProjectPerformanceChart");
    if (institutionProjectPerformanceCanvas) {
      new Chart(institutionProjectPerformanceCanvas, {
        type: "line",
        data: {
          labels: ["Jan", "F�v", "Mar", "Avr", "Mai", "Jun"],
          datasets: [
            {
              label: "Qualit� dossiers",
              data: [72, 76, 81, 84, 88, 91],
              borderColor: blue,
              borderWidth: 2,
              fill: false
            },
            {
              label: "Rentabilit� projet�e",
              data: [9, 10, 11, 12, 13, 14],
              borderColor: orange,
              borderWidth: 2,
              fill: false,
              borderDash: [8, 6]
            }
          ]
        },
        options: {
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: "top"
            },
            tooltip: tooltipOptions
          },
          scales: {
            x: {
              grid: {
                display: false,
                drawBorder: false
              }
            },
            y: {
              beginAtZero: true,
              grid: {
                color: "rgba(106, 114, 111, 0.15)",
                borderDash: [4, 6],
                drawBorder: false
              }
            }
          }
        }
      });
    }

    var repaymentsEvolutionCanvas = document.getElementById("repaymentsEvolutionChart");
    if (repaymentsEvolutionCanvas) {
      new Chart(repaymentsEvolutionCanvas, {
        type: "line",
        data: {
          labels: ["Jan", "F�v", "Mar", "Avr", "Mai", "Jun"],
          datasets: [
            {
              label: "Paiements encaisses",
              data: [1500, 1500, 850, 1200, 980, 0],
              borderColor: blue,
              borderWidth: 2,
              fill: true,
              backgroundColor: function (context) {
                var chart = context.chart;
                var area = chart.chartArea;
                if (!area) {
                  return "rgba(0, 72, 220, 0.12)";
                }
                var gradient = chart.ctx.createLinearGradient(0, area.top, 0, area.bottom);
                gradient.addColorStop(0, "rgba(0, 72, 220, 0.24)");
                gradient.addColorStop(1, "rgba(0, 72, 220, 0.02)");
                return gradient;
              }
            },
            {
              label: "�ch�ances sensibles",
              data: [0, 0, 650, 320, 180, 90],
              borderColor: orange,
              borderWidth: 2,
              fill: false,
              borderDash: [8, 6]
            }
          ]
        },
        options: {
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: "top"
            },
            tooltip: tooltipOptions
          },
          scales: {
            x: {
              grid: {
                display: false,
                drawBorder: false
              }
            },
            y: {
              beginAtZero: true,
              ticks: {
                callback: function (value) {
                  return value + "K FCFA";
                }
              },
              grid: {
                color: "rgba(106, 114, 111, 0.15)",
                borderDash: [4, 6],
                drawBorder: false
              }
            }
          }
        }
      });
    }

    var statsFundingCanvas = document.getElementById("statsFundingChart");
    if (statsFundingCanvas) {
      new Chart(statsFundingCanvas, {
        type: "bar",
        data: {
          labels: ["Jan", "F�v", "Mar", "Avr", "Mai", "Jun"],
          datasets: [
            {
              label: "Financement",
              data: [5, 3.25, 8, 1.75, 2.5, 0.5],
              borderRadius: 10,
              maxBarThickness: 42,
              backgroundColor: orange
            }
          ]
        },
        options: {
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            },
            tooltip: tooltipOptions
          },
          scales: {
            x: {
              grid: {
                display: false,
                drawBorder: false
              }
            },
            y: {
              beginAtZero: true,
              ticks: {
                callback: function (value) {
                  return value + " M";
                }
              },
              grid: {
                color: "rgba(106, 114, 111, 0.15)",
                borderDash: [4, 6],
                drawBorder: false
              }
            }
          }
        }
      });
    }

    var statsRepaymentsCanvas = document.getElementById("statsRepaymentsChart");
    if (statsRepaymentsCanvas) {
      new Chart(statsRepaymentsCanvas, {
        type: "bar",
        data: {
          labels: ["Jan", "F�v", "Mar", "Avr", "Mai", "Jun"],
          datasets: [
            {
              label: "Remboursements",
              data: [1.5, 1.5, 0.85, 1.1, 0.98, 0.65],
              borderRadius: 10,
              maxBarThickness: 42,
              backgroundColor: green
            }
          ]
        },
        options: {
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            },
            tooltip: tooltipOptions
          },
          scales: {
            x: {
              grid: {
                display: false,
                drawBorder: false
              }
            },
            y: {
              beginAtZero: true,
              ticks: {
                callback: function (value) {
                  return value + " M";
                }
              },
              grid: {
                color: "rgba(106, 114, 111, 0.15)",
                borderDash: [4, 6],
                drawBorder: false
              }
            }
          }
        }
      });
    }

    var statsProjectEvolutionCanvas = document.getElementById("statsProjectEvolutionChart");
    if (statsProjectEvolutionCanvas) {
      new Chart(statsProjectEvolutionCanvas, {
        type: "line",
        data: {
          labels: ["Jan", "F�v", "Mar", "Avr", "Mai", "Jun"],
          datasets: [
            {
              label: "Progression operationnelle",
              data: [18, 29, 41, 55, 63, 68],
              borderColor: blue,
              borderWidth: 2,
              fill: false
            },
            {
              label: "Maturit� financement",
              data: [10, 20, 33, 47, 58, 70],
              borderColor: green,
              borderWidth: 2,
              fill: false,
              borderDash: [8, 6]
            }
          ]
        },
        options: {
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: "top"
            },
            tooltip: tooltipOptions
          },
          scales: {
            x: {
              grid: {
                display: false,
                drawBorder: false
              }
            },
            y: {
              beginAtZero: true,
              ticks: {
                callback: function (value) {
                  return value + "%";
                }
              },
              grid: {
                color: "rgba(106, 114, 111, 0.15)",
                borderDash: [4, 6],
                drawBorder: false
              }
            }
          }
        }
      });
    }
  }

  function readCssVar(name, fallback) {
    var value = getComputedStyle(document.documentElement).getPropertyValue(name);
    return value ? value.trim() : fallback;
  }

  function initAutoPagination() {
    var cards = document.querySelectorAll('.dashboard-table-card[data-auto-paginate="true"]');
    if (!cards.length) return;

    cards.forEach(function (card) {
      var table = card.querySelector('.dashboard-table');
      if (!table) return;
      var tbody = table.querySelector('tbody');
      if (!tbody) return;

      var perPage = parseInt(table.getAttribute('data-items-per-page') || '10', 10);
      if (perPage < 1) perPage = 10;
      var rows = Array.from(tbody.querySelectorAll('tr'));
      if (!rows.length) return;
      var totalPages = Math.ceil(rows.length / perPage);
      if (totalPages < 1) totalPages = 1;
      var paginationEl = card.querySelector('.dashboard-pagination');
      if (!paginationEl) return;

      var currentPage = 1;

      function renderPage(page) {
        if (page < 1) page = 1;
        if (page > totalPages) page = totalPages;
        if (page === currentPage) return;
        currentPage = page;

        rows.forEach(function (row, idx) {
          row.style.display = Math.floor(idx / perPage) + 1 === page ? '' : 'none';
        });

        var btns = paginationEl.querySelectorAll('.page-btn');
        btns.forEach(function (btn) {
          var p = btn.getAttribute('data-page');
          if (p === 'prev' || p === 'next') return;
          btn.classList.toggle('active', parseInt(p, 10) === page);
        });
      }

      if (totalPages > 1) {
        var h = '<button type="button" class="dashboard-page-btn page-btn page-prev" data-page="prev"><i class="bi bi-chevron-left"></i></button>';
        for (var i = 1; i <= totalPages; i++) {
          h += '<button type="button" class="dashboard-page-btn page-btn' + (i === 1 ? ' active' : '') + '" data-page="' + i + '">' + i + '</button>';
        }
        h += '<button type="button" class="dashboard-page-btn page-btn page-next" data-page="next"><i class="bi bi-chevron-right"></i></button>';
        paginationEl.innerHTML = h;

        paginationEl.addEventListener('click', function (e) {
          var btn = e.target.closest('.page-btn');
          if (!btn) return;
          e.preventDefault();
          var target = btn.getAttribute('data-page');
          if (target === 'prev') {
            renderPage(currentPage - 1);
          } else if (target === 'next') {
            renderPage(currentPage + 1);
          } else {
            renderPage(parseInt(target, 10));
          }
        });
      } else {
        paginationEl.innerHTML = '<button type="button" class="dashboard-page-btn page-btn active" data-page="1">1</button>';
      }

      rows.forEach(function (row, idx) {
        row.style.display = idx < perPage ? '' : 'none';
      });
    });
  }
})();





