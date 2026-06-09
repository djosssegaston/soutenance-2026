<?php
$pageConfig = $pageConfig ?? [];
$pageTitle = $pageConfig['title'] ?? 'Tableau de bord';
$pageSubtitle = $pageConfig['subtitle'] ?? '';
$pageParentLabel = $pageConfig['parent_label'] ?? 'Tableau de bord';
$pageParentUrl = $pageConfig['parent_url'] ?? 'index.php';
$stats = $pageConfig['stats'] ?? [];
$sections = $pageConfig['sections'] ?? [];
?>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <div>
                    <h1 class="page-title text-uppercase fw-bold"><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
                    <?php if ($pageSubtitle !== ''): ?>
                        <p class="text-muted mb-0"><?php echo htmlspecialchars($pageSubtitle, ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo htmlspecialchars($pageParentUrl, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($pageParentLabel, ENT_QUOTES, 'UTF-8'); ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></li>
                    </ol>
                </div>
            </div>

            <?php if ($stats !== []): ?>
                <div class="row">
                    <?php foreach ($stats as $stat): ?>
                        <?php
                        $variant = $stat['variant'] ?? 'primary';
                        $variantClass = match ($variant) {
                            'success' => 'success',
                            'warning' => 'warning',
                            'danger' => 'danger',
                            'info' => 'info',
                            default => 'primary',
                        };
                        ?>
                        <div class="col-md-6 col-xl-3">
                            <div class="card cockpit-card shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-start justify-content-between gap-3">
                                        <div>
                                            <p class="text-muted mb-1 text-uppercase fs-12 fw-semibold"><?php echo htmlspecialchars($stat['title'], ENT_QUOTES, 'UTF-8'); ?></p>
                                            <h3 class="mb-0 number-font fw-bold"><?php echo htmlspecialchars($stat['value'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                        </div>
                                        <div class="card-icon bg-<?php echo $variantClass; ?>-transparent text-<?php echo $variantClass; ?>">
                                            <i class="<?php echo htmlspecialchars($stat['icon'] ?? 'bi bi-grid', ENT_QUOTES, 'UTF-8'); ?> fs-20"></i>
                                        </div>
                                    </div>
                                    <?php if (!empty($stat['hint'])): ?>
                                        <div class="mt-3 fs-11 text-muted">
                                            <span class="fw-semibold"><?php echo htmlspecialchars($stat['hint'], ENT_QUOTES, 'UTF-8'); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <?php foreach ($sections as $section): ?>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header justify-content-between">
                                <h4 class="card-title"><?php echo htmlspecialchars($section['title'], ENT_QUOTES, 'UTF-8'); ?></h4>
                                <?php if (!empty($section['badge'])): ?>
                                    <span class="badge bg-primary-transparent text-primary"><?php echo htmlspecialchars($section['badge'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <?php foreach (($section['items'] ?? []) as $index => $item): ?>
                                        <div class="d-flex align-items-start justify-content-between gap-3 py-3 <?php echo $index < count($section['items']) - 1 ? 'border-bottom' : ''; ?>">
                                            <div class="d-flex align-items-start gap-3">
                                                <?php if (!empty($item['icon'])): ?>
                                                    <span class="avatar avatar-md bg-light text-primary rounded-circle">
                                                        <i class="<?php echo htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8'); ?>"></i>
                                                    </span>
                                                <?php endif; ?>
                                                <div>
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?></h6>
                                                    <?php if (!empty($item['meta'])): ?>
                                                        <p class="text-muted mb-0"><?php echo htmlspecialchars($item['meta'], ENT_QUOTES, 'UTF-8'); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <?php if (!empty($item['status'])): ?>
                                                <span class="badge <?php echo htmlspecialchars($item['status_class'] ?? 'bg-primary-transparent text-primary', ENT_QUOTES, 'UTF-8'); ?> rounded-pill px-3 py-2">
                                                    <?php echo htmlspecialchars($item['status'], ENT_QUOTES, 'UTF-8'); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
