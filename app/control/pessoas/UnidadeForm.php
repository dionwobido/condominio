<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Wrapper\TDBUniqueSearch;
use Adianti\Wrapper\BootstrapFormBuilder;
use Adianti\Widget\Form\TDate;

class UnidadeForm extends TPage
{
    protected $form;

    use \Adianti\Base\AdiantiStandardFormTrait;

    function __construct()
    {
        parent::__construct();

        parent::setTargetContainer('adianti_right_panel');
        $this->setAfterSaveAction(new TAction(['UnidadeList', 'onReload'], ['register_state' => 'true']) );

        $this->setDatabase('db_condominio');
        $this->setActiveRecord('Unidade');

        $this->form = new BootstrapFormBuilder('Unidade');
        $this->form->setFormTitle('Unidade');
        $this->form->setClientValidation(true);
        $this->form->setColumnClasses(2, ['col-sm-5 col-lg-4', 'col-sm-7 col-lg-8']);

        $id = new TEntry('id');
        $descricao = new TEntry('descricao');
        $grupo_id = new TDBUniqueSearch('grupo_id', 'db_condominio', 'Grupo', 'id', 'nome');
        $grupo_id->setMinLength(0);
        $grupo_id->setMask('{nome}');
        $bloco = new TEntry('bloco');
        $pessoa_id = new TDBUniqueSearch('pessoa_id', 'db_condominio', 'Pessoa', 'id', 'nome');
        $papel_id = new TDBUniqueSearch('papel_id', 'db_condominio', 'Papel', 'id', 'nome');
        $papel_id->setMinLength(0);
        $papel_id->setMask('{nome}');
        $fracao = new TEntry('fracao');
        $area_util = new TEntry('area_util');
        $area_total = new TEntry('area_total');
        $observacao = new TEntry('observacao');

        //$fracao->setNumericMask(2, ',', '.');
        //$area_util->setNumericMask(2, ',', '.');
        //$area_total->setNumericMask(2, ',', '.');

        $this->form->addFields([ new TLabel('Id')], [$id]);
        $this->form->addFields([ new TLabel('Descricao')], [$descricao]);
        $this->form->addFields([ new TLabel('Grupo')], [$grupo_id]);
        $this->form->addFields([ new TLabel('Bloco')], [$bloco]);
        $this->form->addFields([ new TLabel('Pessoa')], [$pessoa_id]);
        $this->form->addFields([ new TLabel('Papel')], [$papel_id]);
        $this->form->addFields([ new TLabel('Fra????o')], [$fracao]);
        $this->form->addFields([ new TLabel('??rea ??til')], [$area_util]);
        $this->form->addFields([ new TLabel('??rea Total')], [$area_total]);
        $this->form->addFields([ new TLabel('Observa????o')], [$observacao]);

        $pessoa_id->addValidation('Pessoa', new TRequiredValidator);
        $papel_id->addValidation('Papel', new TRequiredValidator);
        $grupo_id->addValidation('Grupo', new TRequiredValidator);
        $descricao->addValidation('Descri????o', new TRequiredValidator);

        $descricao->forceUpperCase();
        $fracao->setNumericMask(8, ',', '.', true);
        $area_util->setNumericMask(2, ',', '.', true);
        $area_total->setNumericMask(2, ',', '.', true);

        $id->setSize('100%');
        $descricao->setSize('100%');
        $grupo_id->setSize('100%');
        $bloco->setSize('100%');
        $pessoa_id->setSize('100%');
        $papel_id->setSize('100%');
        $fracao->setSize('100%');
        $area_util->setSize('100%');
        $area_total->setSize('100%');
        $observacao->setSize('100%');

        $id->setEditable(FALSE);

        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save' );
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction([$this, 'onEdit']), 'fa:eraser red');

        $this->form->addHeaderActionLink(_t('Close'), new TAction([$this, 'onClose']), 'fa:times red');

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);

        parent::add($container);

    }

    public static function onClose($param)
    {
        TScript::create("Template.closeRightPanel()");
    }
}