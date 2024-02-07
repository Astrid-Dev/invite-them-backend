<?php
namespace App\Traits;

trait HasApiRequest
{
    protected static function DEFAULT_PER_PAGE() {return 15;}
    protected static function PAGINATE_KEY() {return '_paginate';}
    protected static function INCLUDES_KEY() {return '_includes';}
    protected static function HAS_CLAUSE_KEY() {return '_has';}
    protected static function COUNTS_KEY() {return '_counts';}
    protected static function ORDER_BY_KEY() {return '_order_by';}
    protected static function ORDER_DIRECTION_KEY() {return '_order_direction';}
    protected static function SELECT_KEY() {return '_selects';}

    public static function handleRetrieve($results, $orders = [])
    {
        $shouldPaginate = (request(self::PAGINATE_KEY(), self::DEFAULT_PER_PAGE()) !== 'none');
        $perPage = intval(request(self::PAGINATE_KEY())) < 1 ?
            self::DEFAULT_PER_PAGE() : intval(request(self::PAGINATE_KEY()));
        $orderData = self::getOrder($orders);
        if (!empty($orderData)) {
            $results->orderBy($orderData['by'], $orderData['direction']);
        }

        return $shouldPaginate ? $results->paginate($perPage)->withQueryString() : $results->get();
    }

    public static function getIncludes(): array
    {
        return request(self::INCLUDES_KEY()) ? explode('|', request(self::INCLUDES_KEY())) : [];
    }

    public static function getSelects(): array
    {
        return request(self::SELECT_KEY()) ? explode(',', request(self::SELECT_KEY())) : ['*'];
    }

    public static function getCountsParams(): array
    {
        return request(self::COUNTS_KEY()) ? explode(',', request(self::COUNTS_KEY())) : [];
    }

    public static function getHasClause(): array
    {
        return request(self::HAS_CLAUSE_KEY()) ? explode(',', request(self::HAS_CLAUSE_KEY())) : [];
    }

    public static function getOrder($orders, $directions = ['asc', 'desc']): ?array
    {
        $orderBy = request(self::ORDER_BY_KEY());
        $orderDirection = request(self::ORDER_DIRECTION_KEY());

        if (empty($orderBy) || !in_array($orderBy, $orders)) {
            return null;
        } elseif (empty($orderDirection) || !in_array($orderDirection, $directions)) {
            $orderDirection = 'desc';
        }

        return [
            'by' => $orderBy,
            'direction' => $orderDirection
        ];
    }
}
