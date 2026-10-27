<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Se lanza desde la capa de servicio cuando una regla del dominio
 * impide la operación. La capa de servicio NO conoce HTTP: la traducción
 * a un código de estado se hace en un único lugar (bootstrap/app.php).
 */
class ReglaNegocioException extends RuntimeException
{
    protected $codigo;

    public function __construct($message, $codigo = 409)
    {
        parent::__construct($message, (int) $codigo);
        $this->codigo = $codigo;
    }

    public function getCodigo()
    {
        return $this->codigo;
    }
}