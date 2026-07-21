<?php

namespace App\View\Composers;

use App\Support\StructuredData;
use Illuminate\Contracts\View\View;

class PortalSeoComposer
{
    public function compose(View $view): void
    {
        $pageSchemas = $view->getData()['structuredData'] ?? [];

        $schemas = [
            StructuredData::organization(),
            StructuredData::localBusiness(),
        ];

        if (! empty($view->getData()['breadcrumbs'])) {
            $schemas[] = StructuredData::breadcrumbList($view->getData()['breadcrumbs']);
        }

        $view->with('structuredData', array_merge($schemas, $pageSchemas));
    }
}
