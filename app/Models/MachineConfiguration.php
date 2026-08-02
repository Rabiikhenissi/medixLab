<?php

namespace App\Models;

use App\Models\Traits\ActiveScoped;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['labo_id', 'name', 'host', 'port', 'protocol', 'mllp_port', 'serial_port', 'baud_rate', 'data_bits', 'stop_bits', 'parity', 'api_key', 'timeout', 'enabled', 'is_archive'])]
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
            'baud_rate' => 'integer',
            'data_bits' => 'integer',
            'stop_bits' => 'integer',
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

    /**
     * Human-friendly label for the transport protocol.
     */
    public function getProtocolLabelAttribute(): string
    {
        return match ($this->protocol) {
            'serial_hl7' => 'HL7 / Série (RS-232 / USB)',
            'http_json' => 'HTTP / JSON',
            default => 'HL7 / MLLP (TCP)',
        };
    }

    /**
     * Short technical summary of the connection target.
     */
    public function getConnectionSummaryAttribute(): string
    {
        if ($this->protocol === 'serial_hl7') {
            return $this->serial_port.' @ '.$this->baud_rate.' baud';
        }

        return $this->host.':'.($this->protocol === 'hl7_mllp' && $this->mllp_port ? $this->mllp_port : $this->port);
    }
}
