<?php

namespace App\Models;

use App\Models\Traits\ActiveScoped;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['labo_id', 'name', 'host', 'port', 'protocol', 'mllp_port', 'api_key', 'timeout', 'enabled', 'is_archive'])]
/**
 * Connection settings for an external laboratory machine integrated via HL7.
 */
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

    /** The laboratory owning this machine configuration. */
    public function laboratory()
    {
        return $this->belongsTo(Labo::class, 'labo_id');
    }

    /** Base http url built from the configured host and port. */
    public function getBaseUrl(): string
    {
        return 'http://'.$this->host.':'.$this->port;
    }
}
