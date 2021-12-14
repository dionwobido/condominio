<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Wrapper\TDBUniqueSearch;
use Adianti\Wrapper\BootstrapFormBuilder;
use Adianti\Widget\Form\TDate;

class ContaReceberForm extends TPage
{
    protected $form;

    use \Adianti\Base\AdiantiStandardFormTrait;

    function __construct()
    {
        parent::__construct();

        parent::setTargetContainer('adianti_right_panel');
        $this->setAfterSaveAction(new TAction(['ContaReceberList', 'onReload'], ['register_state' => 'true']) );

        $this->setDatabase('db_condominio');
        $this->setActiveRecord('ContaReceber');

        $this->form = new BootstrapFormBuilder('form_ContaReceber');
        $this->form->setFormTitle('ContaReceber');
        $this->form->setClientValidation(true);
        $this->form->setColumnClasses(2, ['col-sm-5 col-lg-4', 'col-sm-7 col-lg-8']);

        $id = new TEntry('id');
        $documento = new TEntry('documento');
        $conta_id = new TDBUniqueSearch('conta_id', 'db_condominio', 'Conta','id', 'descricao');
        $conta_id->setMinLength(0);
        $conta_id->setMask('{descricao}');
        $data_vencimento = new TDate('data_vencimento');
        $pessoa_id = new TDBUniqueSearch('pessoa_id', 'db_condominio', 'Pessoa','id', 'nome');
        $valor = new TEntry('valor');
        $data_recebimento = new TDate('data_recebimento');
        $valor_recebido = new TEntry('valor_recebido');
        $juros_recebido = new TEntry('juros_recebido');
        $status = new TCombo('status');
        $status->addItems(['Liquidado' => 'Liquidado', 'Pendente' => 'Pendente', 'Parcelado' => 'Parcelado']);
        $observacao = new TEntry('observacao');

        $this->form->addFields([ new TLabel('Id')], [$id]);
        $this->form->addFields([ new TLabel('Documento')], [$documento]);
        $this->form->addFields([ new TLabel('Conta')], [$conta_id]);
        $this->form->addFields([ new TLabel('Data Vencimento')], [$data_vencimento]);
        $this->form->addFields([ new TLabel('Pessoa')], [$pessoa_id]);
        $this->form->addFields([ new TLabel('Valor')], [$valor]);
        $this->form->addFields([ new TLabel('Data Recebimento')], [$data_recebimento]);
        $this->form->addFields([ new TLabel('Valor Recebido')], [$valor_recebido]);
        $this->form->addFields([ new TLabel('Juro Recebido')], [$juros_recebido]);
        $this->form->addFields([ new TLabel('Status')], [$status]);
        $this->form->addFields([ new TLabel('Observação')], [$observacao]);
        
        
        $conta_id->addValidation('Conta', new TRequiredValidator);
        $valor->addValidation('Valor', new TRequiredValidator);
        $data_vencimento->addValidation('Data Vencimento', new TRequiredValidator);
        $pessoa_id->addValidation('Pessoa', new TRequiredValidator);

        $data_vencimento->setMask('dd/mm/yyyy');
        $data_vencimento->setDatabaseMask('yyyy/mm/dd');

        $data_recebimento->setMask('dd/mm/yyyy');
        $data_recebimento->setDatabaseMask('yyyy/mm/dd');

        $valor->setNumericMask(2, ',', '.', true);
        $valor_recebido->setNumericMask(2, ',', '.', true);
        $juros_recebido->setNumericMask(2, ',', '.', true);
        
        //$pessoa_id->forceUpperCase();

        $id->setSize('100%');
        $documento->setSize('100%');
        $conta_id->setSize('100%');
        $data_vencimento->setSize('100%');
        $pessoa_id->setSize('100%');
        $valor->setSize('100%');
        $data_recebimento->setSize('100%');
        $valor_recebido->setSize('100%');
        $juros_recebido->setSize('100%');
        $status->setSize('100%');
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