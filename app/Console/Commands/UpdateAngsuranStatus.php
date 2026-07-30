<?php

namespace App\Console\Commands;
use App\Models\Angsuran;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateAngsuranStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'app:update-angsuran-status';
    protected $signature = 'angsuran:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        $updated = Angsuran::where('status', '!=', 'dibayar')
            ->whereDate('tanggal_jatuh_tempo', '<=', $today)
            ->update([
                'status' => 'jatuh_tempo'
            ]);
    
        $this->info("{$updated} angsuran berhasil diperbarui.");
    
        return self::SUCCESS;
    }
}
