<?php

namespace App\Models;

use App\Models\Traits\ActiveScoped;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'address', 'city', 'phone', 'email', 'latitude', 'longitude', 'is_archive'])]
/**
 * Medical laboratory (center) that runs exams, keeps stock and machines.
 */
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

    /** Staff members working at this laboratory. */
    public function staff()
    {
        return $this->hasMany(Staff::class, 'laboratory_id');
    }

    /** Weekly opening hours configured for this laboratory. */
    public function workingHours()
    {
        return $this->hasMany(WorkingHours::class);
    }

    /** Consumable stock owned by this laboratory. */
    public function consumables()
    {
        return $this->hasMany(Consumable::class);
    }

    /** Equipment owned by this laboratory. */
    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }

    /** Exams this laboratory offers, with their prices. */
    public function availableExams()
    {
        return $this->hasMany(AvailableExam::class);
    }

    /** Exam requests claimed by this laboratory. */
    public function examRequests()
    {
        return $this->hasMany(ExamRequest::class);
    }

    /** HL7 machine integrations configured for this laboratory. */
    public function machineConfigurations()
    {
        return $this->hasMany(MachineConfiguration::class);
    }

    /** Invoices issued by this laboratory. */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /** Samples handled by this laboratory. */
    public function samples()
    {
        return $this->hasMany(Sample::class);
    }
}
