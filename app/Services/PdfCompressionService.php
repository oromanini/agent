<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class PdfCompressionService
{
    /**
     * Recomprime um PDF usando o Ghostscript.
     *
     * As propostas usam imagens de fundo em alta resolução que o Dompdf embute
     * a 200 DPI, gerando arquivos de ~20 MB. O Ghostscript reamostra e recomprime
     * essas imagens, derrubando o tamanho para ~1-2 MB sem perda visível.
     *
     * Em qualquer falha o PDF original é devolvido, para nunca quebrar o download.
     *
     * @param string $rawPdf  Conteúdo binário do PDF já gerado.
     * @param string $quality Preset do Ghostscript: screen | ebook | printer | prepress.
     */
    public function compress(string $rawPdf, string $quality = 'ebook'): string
    {
        if (! config('services.ghostscript.enabled', true)) {
            return $rawPdf;
        }

        $tmpIn  = tempnam(sys_get_temp_dir(), 'pdf_in_');
        $tmpOut = tempnam(sys_get_temp_dir(), 'pdf_out_');
        file_put_contents($tmpIn, $rawPdf);

        try {
            $resolution = (int) config('services.ghostscript.image_resolution', 150);

            $process = new Process([
                config('services.ghostscript.bin', 'gs'),
                '-sDEVICE=pdfwrite',
                '-dCompatibilityLevel=1.4',
                "-dPDFSETTINGS=/{$quality}",
                '-dNOPAUSE',
                '-dQUIET',
                '-dBATCH',
                '-dSAFER',
                '-dAutoRotatePages=/None',
                '-dDownsampleColorImages=true',
                '-dColorImageDownsampleType=/Bicubic',
                "-dColorImageResolution={$resolution}",
                '-dDownsampleGrayImages=true',
                '-dGrayImageDownsampleType=/Bicubic',
                "-dGrayImageResolution={$resolution}",
                '-dDownsampleMonoImages=true',
                "-dMonoImageResolution={$resolution}",
                "-sOutputFile={$tmpOut}",
                $tmpIn,
            ]);
            $process->setTimeout((float) config('services.ghostscript.timeout', 120));
            $process->run();

            if (! $process->isSuccessful() || ! filesize($tmpOut)) {
                Log::warning('PdfCompressionService: Ghostscript falhou, devolvendo PDF original.', [
                    'exit_code' => $process->getExitCode(),
                    'stderr'    => $process->getErrorOutput(),
                ]);

                return $rawPdf;
            }

            $compressed = file_get_contents($tmpOut);

            // Se a compressão não ajudou (ou piorou), mantém o original.
            return ($compressed !== false && strlen($compressed) > 0 && strlen($compressed) < strlen($rawPdf))
                ? $compressed
                : $rawPdf;
        } catch (ProcessFailedException $e) {
            Log::warning('PdfCompressionService: exceção ao executar o Ghostscript.', [
                'message' => $e->getMessage(),
            ]);

            return $rawPdf;
        } finally {
            @unlink($tmpIn);
            @unlink($tmpOut);
        }
    }
}
