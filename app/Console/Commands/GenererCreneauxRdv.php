<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\DatabaseHelper;
use Illuminate\Console\Command;
use App\Models\CreneauDisponibleRdv;


class GenererCreneauxRdv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'GenererCreneauxRdv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command pour generer creneau disponible every day';

    /**
     * Execute the console command.
     */
   // Dans app/Console/Commands/GenererCreneaux.php

public function handle()
{
    $joursEnAvance = 30; // Générer pour 30 jours
    $heureDebut = 8; // 8h
    $heureFin = 18; // 18h
    $dureeCreneau = 30; // minutes

    for ($i = 0; $i < $joursEnAvance; $i++) {
        $date = now()->addDays($i)->startOfDay();

        // Passer les weekends si nécessaire
        if ($date->isWeekend()) continue;

        $debut = $date->copy()->addHours($heureDebut);
        $fin = $date->copy()->addHours($heureFin);

        while ($debut->lt($fin)) {
            CreneauDisponibleRdv::on('temp')->firstOrCreate([
                'debut' => $debut->format('Y-m-d H:i:s'),
                'fin' => $debut->copy()->addMinutes($dureeCreneau)->format('Y-m-d H:i:s')
            ], [
                'disponible' => true
            ]);

            $debut->addMinutes($dureeCreneau);
        }
    }

    $this->info('Créneaux générés avec succès');
}
}
