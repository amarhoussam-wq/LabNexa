<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analyse extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'name_fr',
        'sample_type',
        'tube_color',
        'description',
        'normal_range',
        'code',
        'price',
        'duration',
        'preparation_instructions',
        'preparation_instructions_fr',
        'preparation_video',
        'image',
        'availability',
        'requires_fasting',
        'unit',
        'min_critical',
        'max_critical',
        'critical_instructions',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'availability' => 'boolean',
        'requires_fasting' => 'boolean',
    ];

    public function histories()
    {
        return $this->hasMany(History::class);
    }

    public function requestReservations()
    {
        return $this->belongsToMany(Request_reservation::class, 'request_reservation_analyses');
    }

    /**
     * Get the questions for the medical analysis.
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Alias for questions relationship to support legacy view naming.
     */
    public function eligibilityQuestions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Get the rules established for this analysis.
     */
    public function analysisRules()
    {
        return $this->hasMany(AnalysisRule::class, 'analysis_id');
    }

    public function reservationAnalyses()
    {
        return $this->hasMany(ReservationAnalysis::class, 'analysis_id');
    }

    /**
     * Get the preparation video URL.
     * Always returns a launchable YouTube link for mobile app compatibility.
     */
    public function getPreparationVideoAttribute($value)
    {
        if (!$value) return null;

        // If it's a YouTube link, ensure it's in a standard watch format
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?|shorts)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $value, $match)) {
            $videoId = $match[1];
            return "https://www.youtube.com/watch?v={$videoId}";
        }

        return $value;
    }

    /**
     * Get only the 11-character YouTube video ID.
     */
    public function getYoutubeVideoIdAttribute()
    {
        $video = $this->getRawOriginal('preparation_video');
        if (!$video) return null;

        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?|shorts)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video, $match)) {
            return $match[1];
        }

        return null;
    }

    /**
     * Get a standard YouTube watch URL.
     * This is redundant now due to preparation_video accessor, but kept for explicit API use.
     */
    public function getStandardVideoUrlAttribute()
    {
        $videoId = $this->youtube_video_id;
        if (!$videoId) return $this->getRawOriginal('preparation_video');
        return "https://www.youtube.com/watch?v={$videoId}";
    }

    /**
     * Get the preparation video URL normalized for web views (Embed).
     */
    public function getNormalizedVideoUrlAttribute()
    {
        $videoId = $this->youtube_video_id;
        if (!$videoId) return $this->preparation_video;
        return "https://www.youtube.com/embed/{$videoId}";
    }
}
