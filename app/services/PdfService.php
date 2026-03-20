<?php

declare(strict_types=1);

use Dompdf\Dompdf;
use Dompdf\Options;

function dompdf_available(): bool
{
    return class_exists(Dompdf::class);
}

function pdf_embedded_images_available(): bool
{
    return extension_loaded('gd');
}

function proposal_pdf_filename(array $proposal): string
{
    $code = (string) ($proposal['code'] ?? 'proposta');
    $code = preg_replace('/[^a-zA-Z0-9_-]+/', '-', $code) ?? 'proposta';
    $code = trim($code, '-');
    if ($code === '') {
        $code = 'proposta';
    }

    return 'resumo-' . mb_strtolower($code, 'UTF-8') . '.pdf';
}

function proposal_contract_pdf_filename(array $proposal): string
{
    $code = (string) ($proposal['code'] ?? 'proposta');
    $code = preg_replace('/[^a-zA-Z0-9_-]+/', '-', $code) ?? 'proposta';
    $code = trim($code, '-');
    if ($code === '') {
        $code = 'proposta';
    }

    return 'aceite-' . mb_strtolower($code, 'UTF-8') . '.pdf';
}

function render_html_pdf_binary(string $html): string
{
    if (!dompdf_available()) {
        throw new RuntimeException('Biblioteca Dompdf não disponível no projeto.');
    }

    $previousLevel = error_reporting();
    error_reporting($previousLevel & ~E_DEPRECATED & ~E_USER_DEPRECATED);

    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    $options->set('defaultFont', 'DejaVu Sans');

    $dompdf = new Dompdf($options);
    try {
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    } finally {
        error_reporting($previousLevel);
    }
}

function proposal_summary_pdf_binary(array $proposal, array $payload, array $settings): string
{
    return render_html_pdf_binary(render_proposal_summary_page($proposal, $payload, $settings));
}

function proposal_contract_pdf_binary(array $proposal, array $payload, array $settings): string
{
    return render_html_pdf_binary(render_proposal_contract_page($proposal, $payload, $settings));
}

function output_proposal_summary_pdf(array $proposal, array $payload, array $settings): never
{
    $pdf = proposal_summary_pdf_binary($proposal, $payload, $settings);

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . proposal_pdf_filename($proposal) . '"');
    header('Content-Length: ' . (string) strlen($pdf));
    echo $pdf;
    exit;
}

function output_proposal_contract_pdf(array $proposal, array $payload, array $settings): never
{
    $pdf = proposal_contract_pdf_binary($proposal, $payload, $settings);

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . proposal_contract_pdf_filename($proposal) . '"');
    header('Content-Length: ' . (string) strlen($pdf));
    echo $pdf;
    exit;
}
