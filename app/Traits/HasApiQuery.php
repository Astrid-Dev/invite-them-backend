<?php
namespace App\Traits;

trait HasApiQuery
{
    use HasApiRequest;

    public static function apiQuery(
        $selects = null,
        $includes = null,
        $counts = null,
        $has = null
    ) {
        $newIncludes = empty($includes) ? self::getIncludes() : $includes;
        $newCounts = empty($counts) ? self::getCountsParams() : $counts;
        $newHas = empty($has) ? self::getHasClause() : $has;
        $newSelects = empty($selects) ? self::getSelects() : $selects;

        return self::query()
            ->when(!in_array('*', $newSelects), function ($query) use ($newSelects) {
              $query->select($newSelects);
            })
            ->when(!empty($newIncludes), function ($query) use ($newIncludes) {
                $query->with($newIncludes);
            })
            ->when(!empty($newCounts), function ($query) use ($newCounts) {
                $query->withCount($newCounts);
            })
            ->when((sizeof($newHas) > 0), function ($query) use ($newHas) {
                foreach ($newHas as $hasClause) {
                    $query->whereHas($hasClause);
                }
            });
    }
}
