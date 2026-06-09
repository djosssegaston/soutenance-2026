<?php
/*
 * Composant carte statistique.
 * Les variables attendues sont definies avant include.
 */
$stat_icon = isset($stat_icon) ? $stat_icon : "bi-bar-chart";
$stat_title = isset($stat_title) ? $stat_title : "Statistique";
$stat_value = isset($stat_value) ? $stat_value : "0";
$stat_trend = isset($stat_trend) ? $stat_trend : "";
$stat_trend_class = isset($stat_trend_class) ? $stat_trend_class : "is-up";
$stat_trend_icon = isset($stat_trend_icon) ? $stat_trend_icon : "bi-arrow-up-right";
$stat_icon_bg = isset($stat_icon_bg) ? $stat_icon_bg : "rgba(252, 160, 40, 0.15)";
$stat_icon_color = isset($stat_icon_color) ? $stat_icon_color : "#FCA028";
$stat_compact_header = isset($stat_compact_header) ? (bool) $stat_compact_header : false;

$trendVariant = "up";
if (strpos($stat_trend_class, "down") !== false) {
  $trendVariant = "down";
}
?>

<div class="dashboard-card content-card stat-card hover-lift">
  <?php if ($stat_compact_header): ?>
    <div class="d-flex align-items-center gap-2">
      <span class="stat-card__icon icon-box" style="background: <?php echo htmlspecialchars($stat_icon_bg, ENT_QUOTES, 'UTF-8'); ?>; color: <?php echo htmlspecialchars($stat_icon_color, ENT_QUOTES, 'UTF-8'); ?>;">
        <i class="bi <?php echo htmlspecialchars($stat_icon, ENT_QUOTES, 'UTF-8'); ?>"></i>
      </span>
      <p class="stat-card__label stat-label mb-0"><?php echo htmlspecialchars($stat_title, ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
  <?php else: ?>
    <span class="stat-card__icon icon-box" style="background: <?php echo htmlspecialchars($stat_icon_bg, ENT_QUOTES, 'UTF-8'); ?>; color: <?php echo htmlspecialchars($stat_icon_color, ENT_QUOTES, 'UTF-8'); ?>;">
      <i class="bi <?php echo htmlspecialchars($stat_icon, ENT_QUOTES, 'UTF-8'); ?>"></i>
    </span>
    <p class="stat-card__label stat-label"><?php echo htmlspecialchars($stat_title, ENT_QUOTES, 'UTF-8'); ?></p>
  <?php endif; ?>
  <h4 class="stat-card__value stat-value"><?php echo htmlspecialchars($stat_value, ENT_QUOTES, 'UTF-8'); ?></h4>

  <?php if ($stat_trend !== ""): ?>
    <p class="stat-card__trend stat-trend <?php echo htmlspecialchars($trendVariant, ENT_QUOTES, 'UTF-8'); ?> <?php echo htmlspecialchars($stat_trend_class, ENT_QUOTES, 'UTF-8'); ?>">
      <i class="bi <?php echo htmlspecialchars($stat_trend_icon, ENT_QUOTES, 'UTF-8'); ?>"></i>
      <span><?php echo htmlspecialchars($stat_trend, ENT_QUOTES, 'UTF-8'); ?></span>
    </p>
  <?php endif; ?>
</div>
