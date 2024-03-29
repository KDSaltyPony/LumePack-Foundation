<?php
/**
 * CRUD class file
 *
 * PHP Version 7.2.0
 *
 * @category Repository
 * @package  LumePack\Foundation\Data\Repositories
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
namespace LumePack\Foundation\Data\Repositories;

use Exception;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use LumePack\Foundation\Data\Models\InheritanceModel;

/**
 * CRUD
 *
 * @category Repository
 * @package  LumePack\Foundation\Data\Repositories
 * @author   KDSaltyPony <kallofdragon@gmail.com>
 * @license  https://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link     none
 */
abstract class CRUD
{
    /**
     * Association set relation => query method prefix
     *
     * @var array
     */
    // $bitwise = [ 'n' => 'AND', 'u' => 'OR'/*, '\\' => 'XOR'*/ ];
    private const PREFIXES = [ 'n' => 'where', 'u' => 'orWhere' ];

    /**
     * Association consise operator => comparator
     *
     * @var array
     */
    private const OPERATORS = [
        'eq' => '=', 'neq' => '<>', 'gt' => '>', 'lt' => '<',
        'gte' => '>=', 'lte' => '<=', 'lk' => 'LIKE', 'nlk' => 'NOT LIKE',
        'ilk' => 'LIKE', 'nilk' => 'NOT LIKE'
    ];

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
     * The status of the Model on register
     *
     * @var boolean
     */
    protected $is_saved = false;

    /**
     * The Table associated to the model
     *
     * @var string
     */
    protected $talbe = null;

    /**
     * The Collection retrived by "all" method
     *
     * @var Collection
     */
    protected $collection = null;

    /**
     * The Paginator retrived by "all" method if limit <> 0
     *
     * @var Paginator
     */
    protected $paginator = null;

    /**
     * The query used by "all" method
     *
     * @var Builder
     */
    protected $query = null;

    /**
     * The many to many relations with there key
     *
     * @var array
     */
    protected $nn_relations = [];

    /**
     * The relations to reload after the register
     *
     * @var array
     */
    protected $reloads = [];

    /**
     * The rows available as filters in the query
     *
     * @var array
     */
    protected $filters = []; // 'n' => 'name' TODO => [ 'n' => [ 'row' => 'name', 'forbiden' => [ 'eq', 'lk' ], 'join', 'control' => 'is mail, is string, is int...' ] ]

    /**
     * The joins already done
     *
     * @var array
     */
    protected $joins = [];

    /**
     * The orders available in the query
     *
     * @var array
     */
    protected $orders = [];

    /**
     * The reflection of the model
     *
     * @var \ReflectionClass
     */
    protected $reflect = null;

    /**
     * Set the Model we need for CRUD methods.
     *
     * @param string $model_class The Model full namespace
     */
    public function __construct(?string $model_class = null)
    {
        if (is_null($model_class)) {
            $model_class = get_class($this);
            $model_class = str_replace(
                'App\\Data\\Repositories\\', 'App\\Data\\Models\\', $model_class
            );
            $model_class = preg_replace('/Repository$/', '', $model_class);
        }

        $this->model_class = $model_class;
        $this->model = new $model_class();
        $this->table = $this->model->getTable();
        $this->query = $this->model_class::selectRaw(
            "{$this->table}.*"
        )->whereRaw('1=1');
        $this->reflect = new \ReflectionClass($this->model_class);
    }

    /**
     * Retrive all items.
     *
     * @param bool $limited Limit to the authenticateded user (user_id field)
     *
     * @return Collection|Model[]|Paginator
     */
    public function all(bool $limited = true)
    {
        $this->setQuery();

        if ($limited) {
            $this->setQueryLimiter();
        }

        if (config('paginator.limit') !== 0) {
            $this->paginator = $this->query->paginate(
                config('paginator.limit'), '*', 'page', config('paginator.page')
            );
            $this->collection = $this->paginator->items();
            $this->collection = (
                $this->collection instanceof Collection?
                    $this->collection: Collection::make($this->collection)
            );

            return $this->paginator;
        }

        return $this->collection = (clone $this->query)->get();
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
            $this->setQueryLimiter();
        }

        return $this->model = (clone $this->query)->find($uid);
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
        $this->model = $this->reflect->newInstanceArgs();

        if ($limited) {
            $this->setQueryLimiter($fields);
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

        foreach ($items as $fields) {
            $this->create($fields, $limited);
            $this->collection->add($this->model);
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
        if ($limited) {
            $this->setQueryLimiter($fields);
        }

        $this->model = (clone $this->query)->find($uid);

        return $this->register($fields);
    }

    /**
     * Modify existing database items.
     *
     * @param array $items   A matrix of the fields to register
     * @param bool  $limited Limit to the authenticateded user (user_id field)
     *
     * @return bool
     */
    public function massUpdate(array $items, bool $limited = true): Collection
    {
        $this->collection = new Collection();

        foreach ($items as $fields) {
            $uid = $fields['id'];

            unset($fields['id']);
            $this->update($fields, $uid, $limited);
            $this->collection->add($this->model);
        }

        return $this->collection;
    }

    /**
     * Delete a database item.
     *
     * @param array $fields  The fields to register
     * @param int   $uid     The unique id of the model to retrieve
     * @param bool  $limited Limit to the authenticateded user (user_id field)
     *
     * @return bool
     */
    public function delete(array $fields, int $uid, bool $limited = true): bool
    {
        if ($limited) {
            $this->setQueryLimiter();
        }

        $this->model = (clone $this->query)->find($uid);

        return $this->model->delete() === true;
    }

    /**
     * Delete database items.
     *
     * @param array $items   A matrix of the fields to register
     * @param bool  $limited Limit to the authenticateded user (user_id field)
     *
     * @return bool
     */
    public function massDelete(
        array $items = null, bool $limited = true
    ): Collection
    {
        if (is_null($items) || empty($items)) {
            config( [ 'paginator.limit' => 0 ] );
            $this->all($limited);

            $items = $this->collection->map->only('id')->toArray();
        }

        $this->collection = new Collection();

        foreach ($items as $fields) {
            $uid = $fields['id'];

            unset($fields['id']);
            $this->delete($fields, $uid, $limited);
            $this->collection->add($this->model);
        }

        return $this->collection;
    }

    // TODO: trigger to call setQueryLimiter, $limited based on crud
    // public function __call($name, $arguments)
    // {
    //     // Note: value of $name is case sensitive.
    //     dd("Calling object method '$name' "
    //          . implode(', ', $arguments). "\n");
    // }

    /**
     * The Model class string.
     *
     * @return string
     */
    public function getModelClass(): ?string
    {
        return ($this->model_class === '')? null: $this->model_class;
    }

    /**
     * The Model class string.
     *
     * @return string
     */
    public function getModelClassName(): ?string
    {
        return ($this->model_class === '')? null: explode(
            '\\', $this->model_class
        )[
            count(explode('\\', $this->model_class)) - 1
        ];
    }

    /**
     * The Table.
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * The Model.
     *
     * @return Model
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
     * The Paginator.
     *
     * @return Paginator|null
     */
    public function getPaginator(): ?Paginator
    {
        return $this->paginator;
    }

    /**
     * The Many To Many relations ans the keys used in register.
     *
     * @return array
     */
    public function addNNRelation($relation, $register_key): void
    {
        $this->nn_relations[$relation] = $register_key;
    }

    /**
     * The Many To Many relations ans the keys used in register.
     *
     * @return array
     */
    public function getNNRelations(): array
    {
        return $this->nn_relations;
    }

    /**
     * The Filters.
     *
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Format Query.
     *
     * @return Builder
     */
    protected function setQuery(): Builder
    {
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
            $this->_setQueryOrders(
                $this->query, config('query.order_by')
            );
        }
        // $this->query->dd();

        return $this->query;
    }

    /**
     * Format Query conditions recursively.
     *
     * @param Builder &$query     The query to edit (by reference)
     * @param array   $conditions An nested array of conditions
     *
     * @return void
     */
    private function _setQueryConditions(
        Builder &$query, array $conditions
    ): void
    {
        foreach ($conditions as $cond) {
            if (!array_key_exists($cond['bitwise'], self::PREFIXES)) {
                # TODO => uknown code ! => throw error
            }

            $method = self::PREFIXES[$cond['bitwise']];

            if (array_key_exists('conditions', $cond)) {
                $query->$method(
                    function ($q) use ($cond) {
                        $this->_setQueryConditions($q, $cond['conditions']);
                    }
                );
            } else {
                $this->_setQueryCondition(
                    $query,
                    $cond['bitwise'],
                    $cond['target'],
                    $cond['operator'],
                    $cond['value']
                );
            }
        }
    }

    /**
     * Format Query condition.
     *
     * @param Builder &$query   The query to edit (by reference)
     * @param string  $bitwise  The bitwise operator
     * @param string  $target   The targeted field
     * @param string  $operator The condition operator
     * @param string  $value    The value to compare
     * @param CRUD    $repo     The repo (default this)
     *
     * @return void
     */
    private function _setQueryCondition(
        Builder &$query,
        string $bitwise,
        string $target,
        string $operator,
        string $value,
        CRUD  $repo = null
    ): void
    {
        $repo = (is_null($repo))? $this: $repo;
        $table = $repo->getTable();
        $target = explode('.', $target);
        $params = $repo->_getFilterRaw(
            $target[0], $operator, $repo->getFilters()
        );

        if (is_array($params)) {
            array_shift($target);

            $repo = $this->_setQueryJoin($params, $table);

            $this->_setQueryCondition(
                $query, $bitwise, join('.', $target),
                $operator, $value, $repo
            );
        } else {
            $params = Schema::hasColumn($table, $params)? "{$table}.{$params}": $params;
            $params = [ $params ];

            call_user_func_array(
                [
                    $query,
                    $this->_getMethod($bitwise, $operator, $value, $params)
                ], $params
            );
        }
    }

    /**
     * Format Query Orders.
     *
     * @param Builder &$query The query to edit (by reference)
     * @param array   $orders An array of orders
     *
     * @return void
     */
    private function _setQueryOrders(
        Builder &$query, array $orders
    ): void
    {
        foreach ($orders as $order) {
            $this->_setQueryOrder(
                $query, $order['attribute'], $order['order']
            );
        }
        // $this->query->dd();
    }

    /**
     * Format Query Order.
     *
     * @param Builder &$query The query to edit (by reference)
     * @param array   $target The targeted field
     * @param string  $order  The order (asc|desc)
     * @param CRUD    $repo   The repo (default this)
     *
     * @return void
     */
    private function _setQueryOrder(
        Builder &$query,
        array $target,
        string $order,
        CRUD  $repo = null
    ): void
    {
        $repo = (is_null($repo))? $this: $repo;
        $table = $repo->getTable();

        if (count($target) > 1) {
            $repo = $this->_setQueryJoin($this->_getRelation(
                Str::camel(array_shift($target))
            ), $table);

            $this->_setQueryOrder($query, $target, $order, $repo);
        } else {
            $query->orderBy((
                Schema::hasColumn($table, $target[0])?
                "{$table}.{$target[0]}": $target[0]
            ), $order);
        }
    }

    /**
     * Format Query join.
     *
     * @param array  $join  The join details (repo, pk, fk, pivot)
     * @param string $table The table table join
     *
     * @return CRUD
     */
    private function _setQueryJoin(array $join, string $table): CRUD
    {
        $repo = new $join['repo']();
        $target = $repo->getTable();

        if (!in_array($join['repo'], $this->joins)) {
            if (array_key_exists('pivot', $join) && !is_null($join['pivot'])) {
                $this->query->join(
                    $join['pivot']['table'],
                    "{$table}.{$join['pivot']['target_key']}",
                    "{$join['pivot']['table']}.{$join['pivot']['owner_key']}"
                );

                $table = $join['pivot']['table'];
            }

            $this->joins[] = $join['repo'];
            $this->query->leftJoin(
                $target, "{$target}.{$join['owner_key']}",
                "{$table}.{$join['target_key']}"
            );
        }

        return $repo;
    }

    /**
     * Transform an operator into a query method.
     *
     * @param string $bitwise The join details (repo, owner_key, target_key)
     *
     * @return string
     */
    private function _getMethod(
        string $bitwise, string $operator, string $value, array &$params
    ): string
    {
        $method = self::PREFIXES[$bitwise];
        $method .= $this->_methodNegate($operator);
        $method .= $this->_methodSuffix($operator, $value, $params);

        return $method;
    }

    /**
     * Transform an operator and extract a suffix if negate (whereNot).
     *
     * @param string $operator The join details (repo, pk, fk)
     *
     * @return string
     */
    private function _methodNegate(string &$operator): string
    {
        $suffix = '';

        if (in_array($operator, [ 'nbtw', 'nin', 'nn' ])) {
            $suffix .= 'Not';
            $operator = substr($operator, 1);
        }

        return $suffix;
    }

    /**
     * Transform an operator into a query method suffix.
     *
     * @param string $operator The join details (repo, pk, fk)
     *
     * @return string
     */
    private function _methodSuffix(
        string $operator, string $value, array &$params
    ): string
    {
        $suffix = '';

        switch ($operator) {
            case 'btw':
                $suffix .= 'Between';
                $params[1] = explode(',', $value);

                if (count($params[1]) !== 2) {
                    // TODO => throw error
                }
                break;

            case 'in':
                $suffix .= 'In';
                $params[1] = explode(',', $value);

                if (count($params[1]) < 1) {
                    // TODO => throw error
                }
                break;

            case 'n':
                $suffix .= 'Null';
                break;

            case 'ist':
            case 'isf':
                $params[1] = '=';
                $params[2] = ($operator === 'ist');
                break;

            default:
                if (!array_key_exists($operator, self::OPERATORS)) {
                    // TODO => uknown code ! => throw error
                }

                $params[1] = self::OPERATORS[$operator];
                $params[2] = $value; // TODO => Value controls ?

                if ($operator === 'ilk') {
                    // ILIKE ???
                    $params[0] = DB::raw("LOWER({$params[0]})");
                    $params[2] = strtolower($params[2]);
                }
                break;
        }

        return $suffix;
    }

    /**
     * Check a filter validity. Return the corresponding column.
     *
     * @param string $key      The filter key in filters array
     * @param string $operator The operator
     * @param array  $filters  The filters
     *
     * @return mixed
     */
    private function _getFilterRaw(
        string $key, string $operator, array $filters = null
    ) {
        $filters = is_null($filters)? $this->filters: $filters;
        $filter = null;

        if (!array_key_exists($key, $filters)) {
            # TODO throw error
        }

        if (
            is_array($filters[$key]) &&
            array_key_exists('forbiden', $filters[$key]) &&
            in_array($operator, $filters[$key]['forbiden'])
        ) {
            # TODO throw error
        }

        if (is_array($filters[$key])) {
            $filter = $filters[$key]['row'];
        } else {
            $filter = $filters[$key];

            if (explode('.', $filters[$key])[0] === 'relation') {
                $filter = $this->_getRelation(Str::camel(
                    explode('.', $filters[$key])[1]
                ));
            }
        }

        return $filter;
    }

    /**
     * Format an attribute to get relation informations.
     *
     * @param string $attribute An attribute that is a relation
     *
     * @return array
     */
    private function _getRelation(string $attribute): array
    {
        $relation = [];

        if ($this->reflect->hasMethod($attribute)) {
            $method = $this->reflect->getMethod($attribute);
            $method_name = $method->getName();
            $type = explode('\\', $method->getReturnType()->getName());
            $type = $type[count($type) - 1];
            $relation = [];
            $method_r = (
                $this->reflect->newInstance()
            )->$method_name();
            $repo = get_class($method_r->getRelated());
            $repo = str_replace('Models', 'Repositories', $repo);
            $repo .= 'Repository';

            if (!class_exists($repo)) {
                throw new Exception("{$repo} not found!");
            }

            switch (Str::lower($type)) {
                case 'belongsto':
                    $relation = [
                        'repo'       => $repo,
                        'owner_key'  => $method_r->getOwnerKeyName(),
                        'target_key' => $method_r->getForeignKeyName()
                    ];
                    break;

                case 'hasmany':
                    $relation = [
                        'repo'       => $repo,
                        'owner_key'  => $method_r->getForeignKeyName(),
                        'target_key' => $method_r->getLocalKeyName()
                    ];
                    break;

                case 'belongstomany':
                    $relation = [
                        'pivot'      => [
                            'table'      => $method_r->getTable(),
                            'owner_key'  => $method_r->getForeignPivotKeyName(),
                            'target_key' => $method_r->getParentKeyName()
                        ],
                        'repo'       => $repo,
                        'owner_key'  => $method_r->getRelatedKeyName(),
                        'target_key' => $method_r->getRelatedPivotKeyName()
                    ];
                    break;
            }
        }

        return $relation;
    }

    /**
     * Format Query filter by user to limit resource access.
     * Only work if the resource is link to the user via a user_id row.
     *
     * @param array $fields Add user_id = Auth::id to the included fields
     *
     * @return void
     */
    protected function setQueryLimiter(?array &$fields = null): void
    {
        $uentity = config('crud.user_entity');
        $urelation = config('crud.user_relation');
        $ufk = config('crud.user_fk');

        if (
            Schema::hasColumn($this->getTable(), $ufk) && Auth::check()
        ) {
            if (is_null($fields)) {
                $this->query->where(
                    function ($q) use ($ufk) {
                        $q->where($ufk, Auth::user()->id)->orWhereNull($ufk);
                    }
                );
            } elseif (!is_null($this->model)) {
                $this->model->$urelation()->associate(Auth::user());
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

    /**
     * Register the fields in database and edit the model.
     *
     * @param array $fields The fields to save
     *
     * @return bool TRUE if success or FALSE if failed
     */
    protected function defaultRegister(array $fields)
    {
        // $nn_relations = [ 'sous_thematiques' => 'uid', 'cultures' => 'uid' ];
        $this->reloads = [];

        foreach ($fields as $field => $value) {
            if (preg_match('/^(?:.*?)_u?id$/', $field)) {
                $association = explode('_', $field);
                $key = array_pop($association);
                $association = Str::camel(implode('_', $association));

                if (method_exists($this->model_class, $association)) {
                    // array_push($this->reloads, $association);

                    if (is_null($value)) {
                        $this->model->$association()->dissociate();
                    } else {
                        $target = get_class(
                            $this->model->$association()->getQuery()->getModel()
                        );

                        $this->model->$association()->associate(
                            $target::firstWhere($key, $value)
                        );
                    }
                }
            } elseif (!in_array($field, array_keys($this->nn_relations))) {
                if (Schema::hasColumn($this->getTable(), $field)) {
                    //DateTimes ?
                    $this->model->$field = $value;
                }
            }
        }
        // if put and not patch => foreach attributes that are not in $fields = null ???

        $this->is_saved = $this->model->save();
        $this->_sync($fields);

        if (in_array(
            InheritanceModel::class, array_keys($this->reflect->getTraits())
        )) {
            $this->model->syncInherit();
        }

        // $this->model->load($this->reloads);
        $this->model = (clone $this->query)->find($this->model->id);

        return $this->is_saved;
    }

    /**
     * Register Many to Many fields associated with the model in database.
     *
     * @param array $fields The fields to save
     *
     * @return bool TRUE if success or FALSE if failed
     */
    private function _sync(array $fields)
    {
        foreach ($this->nn_relations as $nnr => $field) {
            if (array_key_exists($nnr, $fields)) {
                array_push($this->reloads, $association = Str::camel($nnr));
                $target = get_class(
                    $this->model->$association()->getQuery()->getModel()
                );
                $values = [];

                foreach ($fields[$nnr] as $realtion) {
                    $id = ($target::firstWhere($field, $realtion[$field]))->id;
                    unset($realtion[$field]);

                    if (empty($realtion)) {
                        array_push($values, $id);
                    } else {
                        $values[$id] = $realtion;
                    }
                }

                //pivot fields
                // TODO : patch syncWithoutDetaching
                $this->model->$association()->sync($values);
            }
        }

        // $this->model->load($this->reloads);
    }
}
