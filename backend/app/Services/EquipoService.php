<?php

namespace App\Services;

use App\Exceptions\ReglaNegocioException;
use App\Models\Equipo;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EquipoService
{
    public function listar(?string $q, ?string $estado, int $porPagina): LengthAwarePaginator
    {
        $porPagina = min(max($porPagina, 1), 50);

        return Equipo::with('categoria')
            ->when($q, function ($query, $q) {
                $query->where('codigo', 'like', "%{$q}%")
                      ->orWhere('nombre', 'like', "%{$q}%");
            })
            ->when($estado, function ($query, $estado) {
                $query->where('estado', $estado);
            })
            ->orderBy('codigo')
            ->paginate($porPagina);
    }

    public function crear(array $datos): Equipo
    {
        if (!isset($datos['estado'])) {
            $datos['estado'] = 'disponible';
        }

        $equipo = Equipo::create($datos);

        return $equipo->load('categoria');
    }

    public function actualizar(Equipo $equipo, array $datos): Equipo
    {
        $equipo->update($datos);

        return $equipo->load('categoria');
    }

    public function eliminar(Equipo $equipo): void
    {
        if ($equipo->estado === 'prestado') {
            throw new ReglaNegocioException('No se puede eliminar un equipo prestado.', 'REGLA_NEGOCIO');
        }

        $equipo->delete();
    }

    public function prestar(Equipo $equipo): Equipo
    {
        if ($equipo->estado === 'mantenimiento') {
            throw new ReglaNegocioException('No se puede prestar un equipo en mantenimiento.', 'REGLA_NEGOCIO');
        }

        if ($equipo->estado === 'prestado') {
            throw new ReglaNegocioException('El equipo ya se encuentra prestado.', 'REGLA_NEGOCIO');
        }

        $equipo->update(['estado' => 'prestado']);

        return $equipo->load('categoria');
    }
}