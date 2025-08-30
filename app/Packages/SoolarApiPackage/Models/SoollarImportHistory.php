<?php

namespace App\Packages\SoolarApiPackage\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class SoollarImportHistory extends Model
{
    use HasFactory;

    const STATUS_PROCESSING = 'PROCESSING';
    const STATUS_SUCCESS = 'SUCCESS';
    const STATUS_ERROR = 'ERROR';

    protected $connection = 'soollar';

    protected $guarded = [];
    public $timestamps = false;

    public static function initProcess(): void
    {
        if (self::isAnotherProcessRunning()) {
            Log::error('Há outro processo de atualização de equipamentos em execução');
            return;
        }

        $process = new self();

        $process->date = now()->toDateTimeString();
        $process->status = self::STATUS_PROCESSING;
        $process->created_products = 0;
        $process->updated_products = 0;
        $process->created_kits = 0;
        $process->updated_kits = 0;
        $process->elapsed_time = now()->secondsSinceMidnight();

        $process->save();
    }

    protected static function isAnotherProcessRunning(): bool
    {
        return self::getProcessing()->exists();
    }

    public static function getProcessing(): Builder
    {
        return self::query()
            ->where('status', self::STATUS_PROCESSING);
    }

    public static function updateProcess(
        ?int $createdProducts = null,
        ?int $updatedProducts = null,
        ?int $createdKits = null,
        ?int $updatedKits = null,
        ?string $status = null
    ): void {

        if (!self::isAnotherProcessRunning()) {
            Log::error('nenhum processo existente para atualizar');
        }

        $process = self::getProcessing()->first();

        !is_null($createdProducts) && $process->created_products += $createdProducts;
        !is_null($updatedProducts) && $process->updated_products += $updatedProducts;
        !is_null($createdKits) && $process->created_kits += $createdKits;
        !is_null($updatedKits) && $process->updated_kits += $updatedKits;
        !is_null($status) && $process->status = $status;

        $process->update();
    }

    public static function finishProcess(): void
    {
        $process = self::getProcessing()->first();

        $process->status = self::STATUS_SUCCESS;
        $process->elapsed_time = now()->diffInSeconds($process->date);

        $process->update();
    }
}
