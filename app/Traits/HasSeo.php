<?php

namespace App\Traits;

use App\Models\SeoMeta;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasSeo
{
    /**
     * Get the model's SEO meta.
     */
    public function seoMeta(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'metasable');
    }

    /**
     * Update or create SEO meta for the model.
     *
     * @param array $data
     * @return \App\Models\SeoMeta
     */
    public function updateSeo(array $data)
    {
        return $this->seoMeta()->updateOrCreate(
            ['metasable_id' => $this->id, 'metasable_type' => get_class($this)],
            $data
        );
    }
}
