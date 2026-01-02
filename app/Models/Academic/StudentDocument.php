<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class StudentDocument extends Model
{
    protected $fillable = [
        'admission_id',
        'document_type',
        'file_path',
        'original_filename',
        'mime_type',
        'file_size',
        'is_verified',
        'verified_by',
        'verified_at',
        'verification_notes'
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'file_size' => 'integer'
    ];

    // Relationships
    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    // Methods
    public function getFileUrl(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function getFileSizeFormatted(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public static function getDocumentTypes(): array
    {
        return [
            'tenth_marksheet' => '10th Marksheet',
            'twelfth_marksheet' => '12th Marksheet',
            'caste_certificate' => 'Caste Certificate',
            'income_certificate' => 'Income Certificate',
            'domicile_certificate' => 'Domicile Certificate',
            'migration_certificate' => 'Migration Certificate',
            'character_certificate' => 'Character Certificate',
            'passport_photo' => 'Passport Photo',
            'signature' => 'Signature',
            'aadhar_card' => 'Aadhar Card',
            'other' => 'Other'
        ];
    }

    public function getDocumentTypeNameAttribute(): string
    {
        return self::getDocumentTypes()[$this->document_type] ?? $this->document_type;
    }
}