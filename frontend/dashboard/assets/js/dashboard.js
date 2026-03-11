(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    syncTopbarSpacing();
    initSidebar();
    initDropdowns();
    initActiveSidebarLink();
    initCharts();
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
    var red = readCssVar("--primary-color-4", "#F35120");

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
      new Chart(adminFundingCanvas, {
        type: "line",
        data: {
            labels: ["Jan", "Fév", "Mar", "Avr", "Mai", "Jun"],
          datasets: [
            {
              label: "Financements",
              data: [320, 410, 520, 610, 720, 840],
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
              data: [230, 295, 360, 430, 500, 610],
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
      new Chart(adminSectorCanvas, {
        type: "doughnut",
        data: {
          labels: ["Agriculture", "Technologie", "Santé", "Transport", "Autre"],
          datasets: [
            {
              data: [35, 25, 20, 15, 5],
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
      new Chart(porteurRepaymentCanvas, {
        type: "bar",
        data: {
          labels: ["Jan", "Fév", "Mar", "Avr", "Mai", "Jun"],
          datasets: [
            {
              label: "Remboursements",
              data: [850, 850, 850, 850, 850, 0],
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
      new Chart(institutionPerformanceCanvas, {
        type: "line",
        data: {
          labels: ["Jan", "Fév", "Mar", "Avr", "Mai", "Jun"],
          datasets: [
            {
              label: "ROI %",
              data: [8.2, 9.1, 8.7, 10.2, 11.5, 10.8],
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
              data: [3.1, 2.8, 3.4, 2.9, 2.5, 2.7],
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

    var institutionRiskReturnCanvas = document.getElementById("institutionRiskReturnChart");
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
                text: "Rentabilité estimée %"
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
          labels: ["Jan", "Fév", "Mar", "Avr", "Mai", "Jun"],
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
          labels: ["Jan", "Fév", "Mar", "Avr", "Mai", "Jun"],
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
          labels: ["Jan", "Fév", "Mar", "Avr", "Mai", "Jun"],
          datasets: [
            {
              label: "Qualité dossiers",
              data: [72, 76, 81, 84, 88, 91],
              borderColor: blue,
              borderWidth: 2,
              fill: false
            },
            {
              label: "Rentabilité projetée",
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
          labels: ["Jan", "Fév", "Mar", "Avr", "Mai", "Jun"],
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
              label: "Échéances sensibles",
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
          labels: ["Jan", "Fév", "Mar", "Avr", "Mai", "Jun"],
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
          labels: ["Jan", "Fév", "Mar", "Avr", "Mai", "Jun"],
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
          labels: ["Jan", "Fév", "Mar", "Avr", "Mai", "Jun"],
          datasets: [
            {
              label: "Progression operationnelle",
              data: [18, 29, 41, 55, 63, 68],
              borderColor: blue,
              borderWidth: 2,
              fill: false
            },
            {
              label: "Maturité financement",
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
})();
