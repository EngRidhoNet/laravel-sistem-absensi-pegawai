<?php
namespace App\Livewire;
use Carbon\Carbon;
use Livewire\Component;
use App\Models\Schedule;
use App\Models\Attendance;

class Presensi extends Component
{
    public $latitude;
    public $longitude;
    public $insideRadiuse = false;
    public function render()
    {
        $schedule = Schedule::where('user_id', auth()->user()->id)->first();
        $attendance = Attendance::where('user_id', auth()->user()->id)
            ->whereDate('created_at', date('Y-m-d'))->first();
        // dd($schedule);
        return view('livewire.presensi',[
            'schedule' => $schedule,
            'insideRadius' => $this->insideRadiuse,
            'attendance' => $attendance
        ]);
    }

    public function store()
    {
        $this->validate([
            'latitude' => 'required',
            'longitude' => 'required'
        ]);
        $schedule = Schedule::where('user_id', auth()->user()->id)->first();
        // dd($schedule);
        if ($schedule){
            $attendance = Attendance::where('user_id', auth()->user()->id)
                ->whereDate('created_at', date('Y-m-d'))->first();
            if (!$attendance){
            $attendance = Attendance::create([
                'user_id' => auth()->user()->id,
                'schedule_latitude' => $schedule->office->latitude,
                'schedule_longitude' => $schedule->office->longitude,
                'schedule_start_time' => $schedule->shift->start_time,
                'schedule_end_time' => $schedule->shift->end_time,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'start_time' => Carbon::now()->toTimeString(),
                'end_time' => Carbon::now()->toTimeString(),
            ]);
            } else {
                $attendance->update([
                    'latitude' => $this->latitude,
                    'longitude' => $this->longitude,
                    'end_time' => Carbon::now()->toTimeString(),
                ]);
            }

            return redirect()->route('presensi',[
                'schedule' => $schedule,
                'insideRadius' => false
            ]);
        }
    }
}
