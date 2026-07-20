<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use App\Models\Traits\ActiveScoped;

#[Fillable(['name', 'address', 'city', 'phone', 'email', 'latitude', 'longitude', 'is_archive'])]
class Labo extends Model
{
    use ActiveScoped;
    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
        ];
    }
    protected $table = 'labos';

    public function staff()
    {
        return $this->hasMany(Staff::class, 'laboratory_id');
    }

    public function workingHours()
    {
        return $this->hasMany(WorkingHours::class);
    }

    public function consumables()
    {
        return $this->hasMany(Consumable::class);
    }

    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }

    public function availableExams()
    {
        return $this->hasMany(AvailableExam::class);
    }

    public function examRequests()
    {
        return $this->hasMany(ExamRequest::class);
    }
}
