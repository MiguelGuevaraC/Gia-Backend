<?php
namespace App\Traits;

use App\Utils\Constants;
use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    protected function applyFilters($query, $request, $filters)
    {
        // Obtener el valor del campo 'search' de la solicitud
        $search = $request->query('search');

        foreach ($filters as $filter => $operator) {
            $paramName = str_replace('.', '$', $filter);
            $value     = $request->query($paramName);

            // Aplicar lógica especial para el campo 'search'
            if ($search !== null && $operator === 'like') {
                // Aplicar un filtro LIKE con el valor de 'search'
                if (strpos($filter, '.') !== false) {
                    [$relation, $relationFilter] = explode('.', $filter);
                    $query->orWhereHas($relation, function ($q) use ($relationFilter, $search) {
                        $this->applyFilterCondition($q, $relationFilter, 'like', '%' . $search . '%');
                    });
                } else {
                    $query->orWhere($filter, 'like', '%' . $search . '%');
                }
                continue; // Saltamos al siguiente filtro ya que ya hemos aplicado el filtro para 'search'
            }

            // Si el filtro usa 'between', verificamos la existencia de 'from' y 'to'
            if ($operator === 'between') {
                $from = $request->query('from');
                $to   = $request->query('to');

                if ($from || $to) {
                    $this->applyFilterCondition($query, $filter, $operator, compact('from', 'to'));
                    continue; // Saltamos al siguiente filtro ya que se ha aplicado el between
                }
            }

            if ($value !== null) {
                if (strpos($filter, '.') !== false) {
                    [$relation, $relationFilter] = explode('.', $filter);
                    $query->whereHas($relation, function ($q) use ($relationFilter, $operator, $value) {
                        $this->applyFilterCondition($q, $relationFilter, $operator, $value);
                    });
                } else {
                    $this->applyFilterCondition($query, $filter, $operator, $value);
                }
            }
        }

        return $query;
    }

    protected function applyFilterCondition($query, $filter, $operator, $value)
    {
        if ($operator === 'between' && is_array($value)) {
            $from = $value['from'] ?? null;
            $to   = $value['to'] ?? null;

            if ($from && $to) {
                $query->whereBetween($filter, [$from, $to]);
            } elseif ($from) {
                $query->where($filter, '>=', $from);
            } elseif ($to) {
                $query->where($filter, '<=', $to);
            }
            return;
        }

        switch ($operator) {
            case 'like':
                $query->where($filter, 'like', '%' . $value . '%');
                break;
            case '>':
                $query->where($filter, '>', $value);
                break;
            case '<':
                $query->where($filter, '<', $value);
                break;
            case '>=':
                $query->where($filter, '>=', $value);
                break;
            case '<=':
                $query->where($filter, '<=', $value);
                break;
            case '=':
                $query->where($filter, '=', $value);
                break;
            case 'date': // Nuevo operador para filtrar por una fecha exacta
                $query->whereDate($filter, '=', $value);
                break;
            default:
                break;
        }
    }

    protected function applySorting($query, $request, $sorts)
    {
        $sortField = $request->query('sort');
        $sortOrder = $request->query('direction', 'desc');

        if ($sortField !== null && array_key_exists($sortField, $sorts)) {
            if ($sortField === 'sort') {
                // Ordena numéricamente si el campo es "sort"
                $query->orderByRaw("CAST(sort AS UNSIGNED) $sortOrder");
            } else {
                $query->orderBy($sortField, $sortOrder);
            }
        } else {
            $query->orderBy('id', $sortOrder); // Valor por defecto
        }

        return $query;
    }

    protected function getFilteredResults($modelOrQuery, $request, $filters, $sorts, $resource)
    {

        if ($modelOrQuery instanceof Builder) {
            $query = $modelOrQuery;
        } else {
            $query = $modelOrQuery::query();
        }

        $query = $this->applyFilters($query, $request, $filters);
        $query = $this->applySorting($query, $request, $sorts);

        $all     = $request->query('all', false) === 'true';
        $results = $all ? $query->get() : $query->paginate($request->query('per_page', Constants::DEFAULT_PER_PAGE));

        return $all ? response()->json($resource::collection($results)) : $resource::collection($results);
    }
}
