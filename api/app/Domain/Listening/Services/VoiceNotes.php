<?php

namespace App\Domain\Listening\Services;

use App\Domain\Listening\Data\VoiceNote;
use App\Domain\Listening\Models\Message;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Messages vocaux : chiffrés avec la clé de l'application et rangés hors du dossier public,
 * comme le texte des messages. Ils ne sont servis qu'aux participants autorisés.
 */
class VoiceNotes
{
    /** Durée maximale d'un message vocal, en secondes. */
    public const MAX_SECONDS = 120;

    private const DISK = 'local';

    public function store(UploadedFile $file, int $duration): VoiceNote
    {
        $path = 'voice/'.Str::uuid().'.enc';
        Storage::disk(self::DISK)->put($path, Crypt::encryptString(base64_encode((string) $file->get())));

        return new VoiceNote($path, $file->getMimeType() ?? 'audio/mp4', min($duration, self::MAX_SECONDS));
    }

    /** Contenu audio déchiffré. */
    public function read(Message $message): string
    {
        return base64_decode(Crypt::decryptString((string) Storage::disk(self::DISK)->get((string) $message->audio_path)));
    }

    /** @param  iterable<string>  $paths */
    public function delete(iterable $paths): void
    {
        foreach ($paths as $path) {
            Storage::disk(self::DISK)->delete($path);
        }
    }
}
