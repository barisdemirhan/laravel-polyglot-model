<?php

declare(strict_types=1);

namespace PolyglotModel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use PolyglotModel\Models\Translation;

class TranslationUpdated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Translation $translation
    ) {}
}
