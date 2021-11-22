<?php

use Adianti\Database\TRecord;

class PessoaPapel extends TRecord
{
    const TABLENAME = 'pessoa_papel';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'max';

    const CREATEDAT = 'create_at';
    const UPDATEDAT = 'updated_at';

    public function  __construct($id = Null, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('pessoa_id');
        parent::addAttribute('grupo_id');
        
    }
}