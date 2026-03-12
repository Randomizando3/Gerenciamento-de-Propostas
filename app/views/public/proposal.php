<?php
declare(strict_types=1);

$previewMode = (bool) ($previewMode ?? false);
$settings = is_array($settings ?? null) ? $settings : [];

echo render_proposal_template_html($proposal, $payload, $settings, $previewMode);

