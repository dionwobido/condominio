<?php

    use Adianti\Database\TRecord;

    class Cidade extends TRecord
    {
        const TABLENAME = 'cidade';
        const PRIMARYKEY = 'id';
        const IDPOLUCY = 'max';

        public function __construct($id = NULL, $callObjectLoad = TRUE)
        {
          parent::__construct($id, $callObjectLoad);
          parent::addAttribute('placa');
          parent::addAttribute('marca');
          parent::addAttribute('modelo');
          parent::addAttribute('cor');
          parent::addAttribute('ano_modelo');
          parent::addAttribute('pessoa_id');
        }

        public function get_estado()
        {
            return Pessoa::find($this->pessoa_id);
        }
    } 