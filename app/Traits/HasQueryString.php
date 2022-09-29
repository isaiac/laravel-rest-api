<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait HasQueryString
{
    /**
     * Check if given query param is true.
     *
     * @param  string  $param
     * @return bool
     */
    public function isQueryParamTrue(string $param): bool
    {
        return in_array(strtolower($param), ['true', '1', 'yes', 'on']);
    }

    /**
     * Check if given query param is false.
     *
     * @param  string  $param
     * @return bool
     */
    public function isQueryParamFalse(string $param): bool
    {
        return in_array(strtolower($param), ['false', '0', 'no', 'off']);
    }

    /**
     * Get a collection from query params
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $model
     * @return mixed
     */
    public function getCollectionFromQueryString(Request $request, string $model)
    {
        $params = $this->getQueryBuilderParams($request);

        $collection = call_user_func([$model, 'query']);

        $collection = $this->setCollectionSelect($collection, $params['fields']);
        $collection = $this->setCollectionWith($collection, $params['embed']);
        $collection = $this->setCollectionWhere($collection, $params['filters']);
        $collection = $this->setCollectionOrderBy($collection, $params['sort']);
        $collection = $this->setCollectionOffset($collection, $params['offset']);
        $collection = $this->setCollectionLimit($collection, $params['limit']);

        return empty($params['per_page'])
            ? $collection->get()
            : $collection->paginate($params['per_page']);
    }

    /**
     * Get the query builder params.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<int, string>
     */
    public function getQueryBuilderParams(Request $request): array
    {
        $params = [
            'fields' => ['*'],
            'filters' => [],
            'sort' => [],
            'offset' => $request->query('offset'),
            'limit' => $request->query('limit'),
            'embed' => [],
            'page' => $request->query('page'),
            'per_page' => $request->query('per_page')
        ];

        if ($fields = $request->query('fields')) {
            $params['fields'] = explode(',', $fields);
        }

        if ($filters = $request->query('filter')) {
            foreach ($filters as $field => $value) {
                $where_condition = $this->getWhereCondition(
                    ['field' => $field, 'operator' => '=', 'value' => $value]
                );

                $filter_where_conditions = $this->getFilterWhereConditions();

                foreach ($filter_where_conditions as $where_operator => $filter_where_condition) {
                    if (str_contains($field, $where_operator)) {
                        $where_condition = $filter_where_condition([
                            'field' => str_replace($where_operator, '', $field),
                            'value' => $value
                        ]);

                        break;
                    }
                }

                $params['filters'][] = $where_condition;
            }
        }

        if ($sort = $request->query('sort')) {
            $sort = explode(',', $sort);

            foreach ($sort as $field) {
                $order = 'asc';

                if (Str::startsWith($field, '+')) {
                    $field = Str::substr($field, 1);
                } elseif (Str::startsWith($field, '-')) {
                    $field = Str::substr($field, 1);
                    $order = 'desc';
                }

                $params['sort'][$field] = $order;
            }
        }

        if ($embed = $request->query('embed')) {
            $params['embed'] = explode(',', $embed);
        }

        return $params;
    }

    /**
     * Set the collection select method.
     *
     * @param  mixed  $collection
     * @param  array<int, mixed>  $fields
     * @return mixed
     */
    public function setCollectionSelect($collection, array $fields)
    {
        call_user_func_array([$collection, 'select'], $fields);

        return $collection;
    }

    /**
     * Set the collection with methods.
     *
     * @param  mixed  $collection
     * @param  array<int, mixed>  $embed
     * @return mixed
     */
    public function setCollectionWith($collection, array $embed)
    {
        foreach ($embed as $embed_collection) {
            call_user_func_array([$collection, 'with'], [$embed_collection]);
        }

        return $collection;
    }

    /**
     * Set the collection where methods.
     *
     * @param  mixed  $collection
     * @param  array<int, mixed>  $filters
     * @return mixed
     */
    public function setCollectionWhere($collection, array $filters)
    {
        foreach ($filters as $where) {
            call_user_func_array(
                [$collection, $where['method']],
                Arr::except($where, 'method')
            );
        }

        return $collection;
    }

    /**
     * Set the collection sort methods.
     *
     * @param  mixed  $collection
     * @param  array<int, mixed>  $sort
     * @return mixed
     */
    public function setCollectionOrderBy($collection, array $sort)
    {
        foreach ($sort as $field => $order) {
            call_user_func_array([$collection, 'orderBy'], [$field, $order]);
        }

        return $collection;
    }

    /**
     * Set the collection offset method.
     *
     * @param  mixed  $collection
     * @param  mixed  $offset
     * @return mixed
     */
    public function setCollectionOffset($collection, $offset)
    {
        if ($offset) {
            call_user_func_array([$collection, 'offset'], [$offset]);
        }

        return $collection;
    }

    /**
     * Set the collection limit method.
     *
     * @param  mixed  $collection
     * @param  mixed  $limit
     * @return mixed
     */
    public function setCollectionLimit($collection, $limit)
    {
        if ($limit) {
            call_user_func_array([$collection, 'limit'], [$limit]);
        }

        return $collection;
    }

    /**
     * Get the filter query operators.
     *
     * @return array<string, function>
     */
    protected function getFilterWhereConditions(): array
    {
        return [
            '%!~' => function ($where) {
                return $this->getWhereCondition([
                    ...$where,
                    'operator' => 'not like',
                    'value' => '%'.$where['value']
                ]);
            },
            '!~%' => function ($where) {
                return $this->getWhereCondition([
                    ...$where,
                    'operator' => 'not like',
                    'value' => $where['value'].'%'
                ]);
            },
            '!~' => function ($where) {
                return $this->getWhereCondition([
                    ...$where,
                    'operator' => 'not like',
                    'value' => '%'.$where['value'].'%'
                ]);
            },
            '%~' => function ($where) {
                return $this->getWhereCondition([
                    ...$where,
                    'operator' => 'like',
                    'value' => '%'.$where['value']
                ]);
            },
            '~%' => function ($where) {
                return $this->getWhereCondition([
                    ...$where,
                    'operator' => 'like',
                    'value' => $where['value'].'%'
                ]);
            },
            '!()' => function ($where) {
                return $this->getWhereNotInCondition($where);
            },
            '()' => function ($where) {
                return $this->getWhereInCondition($where);
            },
            '!*' => function ($where) {
                return $this->getWhereNotNullCondition($where);
            },
            '*' => function ($where) {
                return $this->getWhereNullCondition($where);
            },
            '<~' => function ($where) {
                return $this->getWhereCondition([...$where, 'operator' => '<=']);
            },
            '>~' => function ($where) {
                return $this->getWhereCondition([...$where, 'operator' => '>=']);
            },
            '!' => function ($where) {
                return $this->getWhereCondition([...$where, 'operator' => '!=']);
            },
            '~' => function ($where) {
                return $this->getWhereCondition([
                    ...$where,
                    'operator' => 'like',
                    'value' => '%'.$where['value'].'%'
                ]);
            },
            '<' => function ($where) {
                return $this->getWhereCondition([...$where, 'operator' => '<']);
            },
            '>' => function ($where) {
                return $this->getWhereCondition([...$where, 'operator' => '>']);
            }
        ];
    }

    /**
     * Get the where condition.
     *
     * @param  array  $where
     * @return array<int, mixed>
     */
    protected function getWhereCondition($where): array
    {
        return [
            $where['field'] ?? null,
            $where['operator'] ?? null,
            $where['value'] ?? null,
            'method' => 'where'
        ];
    }

    /**
     * Get the where in condition.
     *
     * @param  array  $where
     * @return array<int, mixed>
     */
    protected function getWhereInCondition($where): array
    {
        return [
            $where['field'] ?? null,
            $where['value'] ? explode(',', $where['value']) : [],
            'method' => 'whereIn'
        ];
    }

    /**
     * Get the where not in condition.
     *
     * @param  array  $where
     * @return array<int, mixed>
     */
    protected function getWhereNotInCondition($where): array
    {
        return [
            $where['field'] ?? null,
            $where['value'] ? explode(',', $where['value']) : [],
            'method' => 'whereNotIn'
        ];
    }

    /**
     * Get the where null condition.
     *
     * @param  array  $where
     * @return array<int, mixed>
     */
    protected function getWhereNullCondition($where): array
    {
        return [$where['field'] ?? null, 'method' => 'whereNull'];
    }

    /**
     * Get the where not null condition.
     *
     * @param  array  $where
     * @return array<int, mixed>
     */
    protected function getWhereNotNullCondition($where): array
    {
        return [$where['field'] ?? null, 'method' => 'whereNotNull'];
    }
}
