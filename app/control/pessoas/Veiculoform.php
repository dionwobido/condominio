<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Wrapper\BootstrapFormBuilder;

class VeiculoForm extends TPage
{
    protected $form;

    use \Adianti\Base\AdiantiStandardFormTrait;

    function __construct()
    {
        parent::__construct();

        parent::setTargetContainer('adianti_right_panel');
        $this->setAfterSaveAction(new TAction(['VeiculoList', 'onReload'], ['register_state' => 'true']) );

        $this->setDatabase('db_condominio');
        $this->setActiveRecord('Veiculo');

        $this->form = new BootstrapFormBuilder('form_Veiculo');
        $this->form->setFormTitle('Veiculo');
        $this->form->setClientValidation(true);
        $this->form->setColumnClasses(2, ['col-sm-5 col-lg-4', 'col-sm-7 col-lg-8']);

        $id = new TEntry('id');
        $placa = new TEntry('placa');
        $marca = new TEntry('marca');
        $modelo = new TEntry('modelo');
        $cor = new TEntry('cor');
        $ano_modelo = new TEntry('ano_modelo');
        $pessoa_id = new TDBUniqueSearch('pessoa_id', 'db_condominio', 'Pessoa', 'id', 'nome');

        $this->form->addFields([ new TLabel('Id')], [$id]);
        $this->form->addFields([ new TLabel('placa')], [$placa]);
        $this->form->addFields([ new TLabel('marca')], [$marca]);
        $this->form->addFields([ new TLabel('modelo')], [$modelo]);
        $this->form->addFields([ new TLabel('cor')], [$cor]);
        $this->form->addFields([ new TLabel('ano_modelo')], [$ano_modelo]);
        $this->form->addFields([ new TLabel('Pessoa_id')], [$pessoa_id]);

        $placa->addValidation('Placa', new TRequiredValidator);
        $marca->addValidation('Marca', new TRequiredValidator);
        $modelo->addValidation('Modelo', new TRequiredValidator);
        $cor->addValidation('Cor', new TRequiredValidator);
        //$ano_modelo->addValidation('Ano Modelo', new TRequiredValidator);
        $pessoa_id->addValidation('Pessoa', new TRequiredValidator);

        $id->setSize('100%');
        $placa->setSize('100%');
        $marca->setSize('100%');
        $cor->setSize('100%');
        $ano_modelo->setSize('100%');
        $pessoa_id->setSize('100%');

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