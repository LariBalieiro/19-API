<?php

namespace APP2\Models;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model {
    protected $table = 'produtos';
    protected $fillable = ['titulo', 'descricao', 'preco',
    'fabricante', 'created_at', 'updated_at'];

    public $timestamps = true;

    // Você pode adicionar métodos adicionais aqui, se necessário
}

?>