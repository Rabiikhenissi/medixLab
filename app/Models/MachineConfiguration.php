<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use App\Models\Traits\ActiveScoped;

#[Fillable(['labo_id', 'name', 'host', 'port', 'protocol', 'mllp_port', 'api_key', 'timeout', 'enabled', 'is_archive'])]
class MachineConfiguration extends Model
{
    use ActiveScoped;

    protected $table = 'machine_configurations';

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'is_archive' => 'boolean',
            'timeout' => 'integer',
            'port' => 'integer',
            'mllp_port' => 'integer',
        ];
    }

    public function laboratory()
    {
        return $this->belongsTo(Labo::class, 'labo_id');
    }

    public function getBaseUrl(): string
    {
        return 'http://' . $this->host . ':' . $this->port;
    }
}
