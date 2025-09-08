<?php

namespace App\Console\Commands;

use App\Packages\SoolarApiPackage\Models\InverterBrand;
use App\Packages\SoolarApiPackage\Models\ModuleBrand;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RenameBrandImages extends Command
{
    protected $signature = 'images:rename-brands {--dry-run : Exibe as mudanças sem executar a ação.}';
    protected $description = 'Renomeia imagens de marcas para seus nomes de marca e atualiza o banco de dados.';

    public function handle()
    {
        $directories = [
            'inverter_brand_logos',
            'inverter_brand_pictures',
            'module_brand_logos',
            'module_brand_pictures',
        ];

        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('Modo Dry Run ativado. Nenhuma alteração será feita.');
        }

        foreach ($directories as $directory) {
            $this->comment("Processando o diretório: {$directory}");

            $files = Storage::disk('public')->allFiles($directory);

            $this->withProgressBar($files, function ($file) use ($directory, $isDryRun) {
                $oldFilename = basename($file);
                $brand = null;

                $column = Str::contains($directory, 'logos') ? 'logo' : 'picture';

                if (Str::contains($directory, 'module')) {
                    $brand = ModuleBrand::query()->where($column, $oldFilename)->first();
                } else {
                    $brand = InverterBrand::query()->where($column, $oldFilename)->first();
                }

                if ($brand) {
                    $brandName = Str::slug($brand->brand);
                    $extension = pathinfo($oldFilename, PATHINFO_EXTENSION);
                    $newFilename = "{$brandName}.{$extension}";

                    if ($oldFilename === $newFilename) {
                        $this->line("Arquivo '{$oldFilename}' já está com o nome correto. Ignorando.");
                        return;
                    }

                    $this->info("Encontrado: '{$oldFilename}' -> Marca: {$brand->brand}. Novo nome: '{$newFilename}'");

                    if (!$isDryRun) {
                        try {
                            Storage::disk('public')->move($file, "{$directory}/{$newFilename}");

                            $brand->update([$column => $newFilename]);

                            $this->info('Arquivo renomeado e banco de dados atualizado com sucesso.');
                        } catch (\Exception $e) {
                            $this->error("Erro ao processar '{$oldFilename}': " . $e->getMessage());
                        }
                    }
                }
            });

            $this->newLine(2);
        }

        $this->info('Processamento concluído.');
    }
}
