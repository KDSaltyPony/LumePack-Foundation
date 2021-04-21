<?php
/**
 * CRUD class file
 * 
 * PHP Version 7.2.0
 * 
 * @category Repositorie
 * @package  LumePack\Foundation\Data\Repositories
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

/**
 * CRUD
 * 
 * @category Repositorie
 * @package  LumePack\Foundation\Data\Repositories
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
abstract class CRUD
{
    /**
     * The namespace of the Model to use
     * 
     * @var string
     */
    protected $model_class = '';

    /**
     * The Model retrived by "CRU" methods
     * 
     * @var Model
     */
    protected $model = null;

    /**
     * The Collection retrived by "all" method
     * 
     * @var Collection
     */
    protected $collection = null;

    /**
     * The query used by "all" method
     * 
     * @var Builder
     */
    protected $query = null;

    /**
     * The rows available as filters in the query
     * 
     * @var array
     */
    protected $filters = [ 'n' => 'name' ]; // TODO => [ 'n' => [ 'row' => 'name', 'forbiden' => [ 'eq', 'lk' ], 'join', 'control' => 'is mail, is string, is int...' ] ]

    /**
     * The orders available in the query
     * 
     * @var array
     */
    protected $orders = [];

    /**
     * Set the Model we need for CRUD methods.
     * 
     * @param string $model_class The Model full namespace
     */
    public function __construct(string $model_class)
    {
        $this->model_class = $model_class;
        $this->_setQuery();
    }

    /**
     * Retrive all items.
     * 
     * @param bool $limited Limit to the authenticateded user (user_id field)
     * 
     * @return Collection|Model[]
     */
    public function all(bool $limited = true): Collection
    {
        if ($limited) {
            $this->_setQueryLimiter();
        }

        return $this->collection = $this->query->get();
    }

    /**
     * Retrive one item by id.
     * 
     * @param int  $uid     The unique id of the model to retrieve
     * @param bool $limited Limit to the authenticateded user (user_id field)
     * 
     * @return Model|null
     */
    public function read(int $uid, bool $limited = true): ?Model
    {
        if ($limited) {
            $this->_setQueryLimiter();
        }

        return $this->model = $this->model_class::find($uid);
    }

    /**
     * Register a new database item.
     * 
     * @param array $fields  The fields to register
     * @param bool  $limited Limit to the authenticateded user (user_id field)
     * 
     * @return bool
     */
    public function create(array $fields, bool $limited = true): bool
    {
        $this->model = new $this->model_class();

        if ($limited) {
            $this->_setQueryLimiter($fields);
        }

        return $this->register($fields);
    }

    /**
     * Register new database items.
     * 
     * @param array $items   A matrix of the fields to register
     * @param bool  $limited Limit to the authenticateded user (user_id field)
     * 
     * @return Collection|Model[]
     */
    public function massCreate(array $items, bool $limited = true): ?Collection
    {
        $this->collection = new Collection();
        $this->model = null;

        foreach ($items as $fields) {
            $this->collection->add($this->create($fields, $limited));
        }

        return $this->collection;
    }

    /**
     * Modify an existing database item.
     * 
     * @param array $fields  The fields to register
     * @param int   $uid     The unique id of the model to modify
     * @param bool  $limited Limit to the authenticateded user (user_id field)
     * 
     * @return bool
     */
    public function update(array $fields, int $uid, bool $limited = true): bool
    {
        $this->model = $this->model_class::find($uid);

        if ($limited) {
            $this->_setQueryLimiter($fields);
        }

        return $this->register($fields);
    }

    /**
     * Delete a database item.
     * 
     * @param int $uid The unique id of the model to retrieve
     * 
     * @return bool
     */
    public function delete(int $uid, bool $limited = true): bool
    {
        $this->model = $this->model_class::find($uid);

        if ($limited) {
            $this->_setQueryLimiter();
        }

        return $this->model_class::destroy($uid) === 1;
    }

    /**
     * The Model class string.
     * 
     * @return string
     */
    public function getModelClass(): string
    {
        return ($this->model_class === '')? null: $this->model_class;
    }

    /**
     * The Model class string.
     * 
     * @return string
     */
    public function getModelClassName(): string
    {
        return ($this->model_class === '')? null: explode(
            '\\', $this->model_class
        )[
            count(explode('\\', $this->model_class)) - 1
        ];
    }

    /**
     * The Model.
     * 
     * @return Model|null
     */
    public function getModel(): ?Model
    {
        return $this->model;
    }

    /**
     * The Collection.
     * 
     * @return Collection|null
     */
    public function getCollection(): ?Collection
    {
        return $this->collection;
    }

    /**
     * Format Query.
     * 
     * @return Builder
     */
    private function _setQuery(): Builder
    {
        $this->query = $this->model_class::whereRaw('1=1');

        if (config('query.conditions')) {
            $this->query->where(
                function ($q) {
                    $this->_setQueryConditions(
                        $q, config('query.conditions')
                    );
                }
            );
        }

        if (config('query.order_by')) {
            foreach (config('query.order_by') as $order) {
                $this->query->orderBy($order['attribute'], $order['order']);
            }
        }
        // $this->query->dd();

        return $this->query;
    }

    /**
     * Format Query conditions recursively.
     * 
     * @param Builder &$query     The quer to edit (by reference)
     * @param array   $conditions An nested array of conditions
     * 
     * @return void
     */
    protected function _setQueryConditions(Builder &$query, array $conditions): void
    {
        // $bitwise = [ 'n' => 'AND', 'u' => 'OR'/*, '\\' => 'XOR'*/ ];
        $prefixes = [ 'n' => 'where', 'u' => 'orWhere' ];
        $operators = [
            'eq' => '=', 'neq' => '<>', 'gt' => '>', 'lt' => '<',
            'gte' => '>=', 'lte' => '<=', 'lk' => 'LIKE', 'nlk' => 'NOT LIKE'
        ];

        foreach ($conditions as $cond) {
            if (!array_key_exists($cond['bitwise'], $prefixes)) {
                # TODO => uknown code ! => throw error
            }

            $method = $prefixes[$cond['bitwise']];
            $params = [];

            if (array_key_exists('conditions', $cond)) {
                $query->$method(
                    function ($q) use ($cond) {
                        $this->_setQueryConditions($q, $cond['conditions']);
                    }
                );
            } else {
                // $params[0] = $this->_getFilter(
                //     $cond['target'], $cond['operator']
                // );
                $params[0] = $cond['target'];

                switch ($cond['operator']) {
                    case 'nbtw': case 'nin': case 'nn':
                        $method .= 'Not';
                        $cond['operator'] = substr($cond['operator'], 1);
                        break;

                    case 'ceq': case 'cneq': case 'cgt':
                    case 'clt': case 'cgte': case 'clte':
                        $method .= 'Column';
                        $cond['operator'] = substr($cond['operator'], 1);
                        // $cond['value'] = $this->_getFilter(
                        //     $cond['value'], $cond['operator']
                        // );
                        $cond['value'] = $cond['value'];
                        break;
                }

                switch ($cond['operator']) {
                    case 'btw':
                        $method .= 'Between';
                        $params[1] = explode(',', $cond['value']);

                        if (count($params[1]) !== 2) {
                            // TODO => throw error
                        }
                        break;

                    case 'in':
                        $method .= 'In';
                        $params[1] = explode(',', $cond['value']);

                        if (count($params[1]) < 2) {
                            // TODO => throw error
                        }
                        break;

                    case 'n':
                        $method .= 'Null';
                        break;

                    default:
                        if (!array_key_exists($cond['operator'], $operators)) {
                            # TODO => uknown code ! => throw error
                        }

                        $params[1] = $operators[$cond['operator']];
                        $params[2] = $cond['value']; // TODO => Value controls ?
                        break;
                }

                call_user_func_array([ $query, $method ], $params);
            }
        }
    }

    /**
     * Check a filter validity. Return the corresponding column.
     * 
     * @param string $key      The filter key in filters array
     * @param string $operator The operator
     * 
     * @return string
     */
    private function _getFilter(string $key, string $operator): string
    {
        if (!array_key_exists($key, $this->filters)) {
            # TODO throw error
        }

        if (
            is_array($this->filters[$key]) &&
            in_array($operator, $this->filters[$key]['forbiden'])
        ) {
            # TODO throw error
        }

        return is_array(
            $this->filters[$key]
        )? $this->filters[$key]['row']: $this->filters[$key];
    }

    /**
     * Format Query filter by user to limit resource access.
     * Only work if the resource is link to the user via a user_id row.
     * 
     * @param array $fields Add user_id = Auth::id to the included fields
     * 
     * @return void
     */
    private function _setQueryLimiter(?array &$fields = null): void
    {
        $model = $this->model_class;
 
        if (
            Schema::hasColumn((new $model())->getTable(), 'user_id') &&
            !is_null(Auth::user())
        ) {
            if (is_null($fields)) {
                $this->query->where(
                    function ($q) {
                        $q->where('user_id', Auth::user()->id)
                            ->orWhereNull('user_id');
                    }
                );
            } else {
                if (!array_key_exists('user_id', $fields)) {
                    $fields['user_id'] = Auth::user()->id;
                }
            }
        }
    }

    /**
     * Register the fields in database and edit the model.
     *
     * @param array $fields The fields to save
     * 
     * @return bool TRUE if success or FALSE if failed
     */
    abstract protected function register(array $fields): bool;
}
