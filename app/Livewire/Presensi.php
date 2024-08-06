<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Schedule;

class Presensi extends Component
{
    public function render()
    {
        $schedule = Schedule::where('user_id', auth()->user()->id)->first();
        // dd($schedule);

        return view('livewire.presensi',[

            'schedule' => $schedule
        ]);
    }
}
