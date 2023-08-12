<?php
/**
 * QueryStringToConfig class file
 *
 * PHP Version 7.2.19
 *
 * @category Middleware
 * @package  LumePack\Foundation\Http\Middleware
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * QueryStringToConfig
 *
 * @category Middleware
 * @package  LumePack\Foundation\Http\Middleware
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
class QueryStringToConfig
{
    /**
     * Handle an incoming request.
     *
     * @param Request  $request The request to validate
     * @param \Closure $next    The controller method passed in routes
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!is_null($request->query('page'))) {
            config(
                [ 'paginator.page' => intval($request->query('page')) ]
            );
        }

        if (!is_null($request->query('limit'))) {
            config(
                [ 'paginator.limit' => intval($request->query('limit')) ]
            );
        }

        if (!is_null($request->query('sort'))) {
            $this->_formatOrderBy($request->query('sort'));
        }

        if (!is_null($request->query('filters'))) {
            $this->_formatFilters($request->query('filters'));
        }

        if (!is_null($request->query('distinct'))) {
            config(
                [ 'query.distinct' => intval($request->query('distinct')) ]
            );
        }

        if (!is_null($request->query('with'))) {
            $this->_formatRelations($request->query('with'));
        }

        return $next($request);
    }

    /**
     * Extract the order by raw string and add it to global config
     *
     * @param string $qstring The sort query param
     *
     * @return void
     */
    private function _formatOrderBy(string $qstring): void
    {
        $orders = explode(',', $qstring);

        foreach ($orders as $key => $order) {
            if ($order !== '') {
                $order = explode('.', strtolower($order));
                $suffix = 'asc';

                if (in_array($order[count($order) - 1], [ 'asc', 'desc' ])) {
                    $suffix = $order[count($order) - 1];
                    unset($order[count($order) - 1]);
                }

                config(
                    [
                        "query.order_by.{$key}" => [
                            'attribute' => $order, 'order' => $suffix
                        ]
                    ]
                );
            }
        }

        if (count(config('query.order_by')) > 0) {
            config([ 'query.sort' => $qstring ]);
        }
    }

    /**
     * Extract the filters raw string and add it to global config
     *
     * @param string $qstring The filters query param
     *
     * @return void
     */
    private function _formatFilters(string $qstring): void
    {
        config(
            [ 'query.conditions' => $this->_filtersParser($qstring) ]
        );

        if (count(config('query.conditions')) > 0) {
            config([ 'query.sort' => $qstring ]);
        }
    }

    /**
     * Change a filters string into nested arrays of conditions
     *
     * @param string $qstring The filters query to parse
     *
     * @return void
     */
    private function _filtersParser(string $qstring): array
    {
        // TODO : refactoring this shit
        $conditions = [];
        $brakets = $this->_getBraketsPositions($qstring);

        if (count($brakets) === 0) {
            $conditions = array_merge(
                $conditions, $this->_conditionsParser($qstring)
            );
        }

        foreach ($brakets as $key => $br) {
            $start = ($key === 0)? 0: $brakets[$key - 1]['closing'] + 1;
            $close = $br['opening'] - 3;

            if ($close - $start > 0) {
                $conditions = array_merge(
                    $conditions,
                    $this->_conditionsParser(
                        substr($qstring, $start, $close)
                    )
                );
            }

            $start = $br['opening'] + 1;
            $close = $br['closing'] - $br['opening'] - 1;

            $conditions[count($conditions)] = [
                'bitwise'    => (
                    $br['opening'] === 0
                )? 'n': $qstring[$br['opening'] - 2],
                'conditions' => $this->_filtersParser(
                    substr($qstring, $start, $close)
                )
            ];

            if (
                $key === count($brakets) - 1 &&
                $br['closing'] + 1 !== strlen($qstring)
            ) {
                $conditions = array_merge(
                    $conditions,
                    $this->_conditionsParser(
                        substr($qstring, $br['closing'] + 1)
                    )
                );
            }
        }

        return $conditions;
    }

    /**
     * Retrive the opening and corresponding closing brakets in a string
     * without considering nested ones.
     *
     * @param string $str The string to analyze
     *
     * @return array
     */
    private function _getBraketsPositions(string $str): array
    {
        $opened = 0;
        $brakets = [];

        foreach (str_split($str) as $key => $char) {
            if ($char === '[') {
                $opened++;

                if ($opened === 1) {
                    $brakets[] = [ 'opening' => $key ];
                }
            }

            if ($char === ']' && $opened >= 1) {
                if ($opened === 1) {
                    $brakets[count($brakets) - 1]['closing'] = $key;
                }

                $opened--;
            }
        }

        return $brakets;
    }

    /**
     * Change a filters string into an array of conditions
     *
     * @param string $qstring The filters query to parse
     *
     * @return array
     */
    private function _conditionsParser(string $qstring): array
    {
        $filters = explode(
            ' ', preg_replace('/\|(u|n|\\\)\|/', ' $1:', $qstring)
        );

        if ($filters[0] === '') {
            array_shift($filters);
        }

        foreach ($filters as $key => $filter) {
            $filters[$key] = explode(':', $filter);

            $k = (count($filters[$key]) < 3)? 1: 0;
            $filters[$key][2 - $k] = explode(
                '(', $filters[$key][2 - $k], 2
            );

            $filters[$key] = [
                'bitwise'  => ($k === 0)? $filters[$key][0]: 'n',
                'target'   => $filters[$key][1 - $k],
                'operator' => $filters[$key][2 - $k][0],
                'value'    => substr($filters[$key][2 - $k][1], 0, -1)
            ];
        }

        return $filters;
    }

    /**
     * Extract the relations raw string and add it to global config
     *
     * @param string $qstring The relations to load
     *
     * @return void
     */
    private function _formatRelations(string $qstring): void
    {
        config(
            [ 'query.relations' => $this->_relationsParser($qstring) ]
        );

        if (count(config('query.relations')) > 0) {
            config([ 'query.with' => $qstring ]);
        }
    }

    /**
     * Change a with string into array of relations
     *
     * @param string $qstring The with query to parse
     *
     * @return void
     */
    private function _relationsParser(string $qstring): array
    {
        return explode(';', $qstring);
    }
}
