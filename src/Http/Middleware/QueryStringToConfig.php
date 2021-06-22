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
use LumePack\Foundation\Services\ResponseService;
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

        return $next($request);
    }

    /**
     * Extract the order by raw string and add it to global config
     *
     * @param string $sort The sort query param
     *
     * @return void
     */
    private function _formatOrderBy(string $sort): void
    {
        $orders = explode(',', $sort);

        foreach ($orders as $key => $order) {
            if ($order !== '') {
                $order = explode('.', $order);

                config(
                    [
                        "query.order_by.{$key}" => [
                            'attribute' => strtolower($order[0]),
                            'order'     => (array_key_exists(
                                1, $order
                            )? strtoupper($order[1]): 'ASC')
                        ]
                    ]
                );
            }
        }

        if (count(config('query.order_by')) > 0) {
            config([ 'query.sort' => $sort ]);
        }
    }

    /**
     * Extract the filters raw string and add it to global config
     *
     * @param string $filters The filters query param
     *
     * @return void
     */
    private function _formatFilters(string $filters): void
    {
        config(
            [ 'query.conditions' => $this->_filtersParser($filters, []) ]
        );

        if (count(config('query.conditions')) > 0) {
            config([ 'query.sort' => $filters ]);
        }
    }

    /**
     * Change a filters string into nested arrays of conditions
     *
     * @param string $filters The filters query to parse
     *
     * @return void
     */
    private function _filtersParser(string $filters): array
    {
        // TODO : refactoring this shit
        $conditions = [];
        $brakets = $this->_getBraketsPositions($filters);

        if (count($brakets) === 0) {
            $conditions = array_merge(
                $conditions, $this->_conditionsParser($filters)
            );
        }

        foreach ($brakets as $key => $br) {
            $start = ($key === 0)? 0: $brakets[$key - 1]['closing'] + 1;
            $close = $br['opening'] - 3;

            if ($close - $start > 0) {
                $conditions = array_merge(
                    $conditions,
                    $this->_conditionsParser(
                        substr($filters, $start, $close)
                    )
                );
            }

            $start = $br['opening'] + 1;
            $close = $br['closing'] - $br['opening'] - 1;

            $conditions[count($conditions)] = [
                'bitwise'    => (
                    $br['opening'] === 0
                )? 'n': $filters[$br['opening'] - 2],
                'conditions' => $this->_filtersParser(
                    substr($filters, $start, $close)
                )
            ];

            if (
                $key === count($brakets) - 1 &&
                $br['closing'] + 1 !== strlen($filters)
            ) {
                $conditions = array_merge(
                    $conditions,
                    $this->_conditionsParser(
                        substr($filters, $br['closing'] + 1)
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
     * @param string $filters The filters query to parse
     *
     * @return array
     */
    private function _conditionsParser(string $filters): array
    {
        $filters = explode(
            ' ', preg_replace('/\|(u|n|\\\)\|/', ' $1:', $filters)
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
}
