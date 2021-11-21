<?php

    class Pessoa extends TRecord
    {
        const TABLENAME = 'pessoa';
        const PRIMARYKEY = 'id';
        const IDPOLUCY = 'max';

        public function __construct($id = NULL, $callObjectLoad = TRUE)
        {
          parent::__construct($id, $callObjectLoad);
          parent::addAttribute('nome');
          parent::addAttribute('nome_fantasia');
          parent::addAttribute('tipo');
          parent::addAttribute('codigo_nacional');
          parent::addAttribute('codigo_estadual');
          parent::addAttribute('codigo_municipal');
          parent::addAttribute('email');
          parent::addAttribute('observacao');
          parent::addAttribute('cep');
          parent::addAttribute('logradouro');
          parent::addAttribute('numero');
          parent::addAttribute('complemento');
          parent::addAttribute('bairro');
          parent::addAttribute('cidade_id');
          parent::addAttribute('grupo_id');
          parent::addAttribute('created_at');
          parent::addAttribute('updated_at');
        }

        public function get_estado()
        {
            return Estado::find($this->estado_id);
        }
    } 