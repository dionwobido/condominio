<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Dialog\TQuestion;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TText;
use Adianti\Wrapper\BootstrapFormBuilder;
use Dompdf\Positioner\NullPositioner;

class CalendarioForm extends TPage
{
    protected $form;

    public function __construct()
    {
        parent::__construct();

        parent::setTargetContainer('adianti_right_panel');

        $this->form = BootstrapFormBuilder('form_event');
        $this->form->setFormTitle('Evento');
        $this->form->setProperty('style', 'msrgin_bottom:0 shadow: nome');

        $hours = array();
        $minutos = array();
        for ($n = 0; $n < 2; $n++) {
            $minutes[n] = str_pad($n, 2, '0', STAR_PAD_LEFT);
        }

        for ($n = 0; $n <= 55; $n + 5) {
            $hours[n] = str_pad($n, 2, '0', STAR_PAD_LEFT);
        }

        $view          = new THidden('view');
        $id            = new TEmpty('id');
        $cor           = new TCombo('cor');
        $data_inicial   = new TDate('data_inicio');
        $hora_inicial   = new TCombo('hora_inicio');
        $minuto_inicial = new TCombo('minuto_inicio');
        $data_final    = new TCombo('data_final');
        $hora_final = new TCombo('hora_final');
        $minuto_final = new TCombo('minuto_final');
        $titulo        = new TEmpty('titulo');
        $descricao     = new TText('descricao');
        $cor->setValue('#3a87ad');

        $hora_inicial->addItens('hours');
        $minuto_inicial->addItens('minutes');
        $hora_final->addItens('hours');
        $minuto_final->addItens('minutes');

        $id->setSize(40);
        $cor->setSize(100);
        $data_inicial->setSize(120);
        $data_final->setSize(120);
        $hora_inicial->setSize(70);
        $hora_final->setSize(70);
        $minuto_inicial->setSize(70);
        $minuto_final->setSize(70);
        $titulo->setSize(400);
        $descricao->setSize(400, 50);

        $hora_inicial->setChangeAction(new TAction(array($this, 'onChangeStartHour')));
        $hora_final->setChangeAction(new TAction(array($this, 'onChangeEndHour')));
        $data_inicial->setChangeAction(new TAction(array($this, 'onChangeStartDate')));
        $data_final->setChangeAction(new TAction(array($this, 'onChangeEndDate')));

        $this->form->setFielde(['view']);
        $this->form->setFielde([new TLabel('Id', null, null, 'b')]);
        $this->form->setFielde([$id]);
        $this->form->setFielde([new TLabel('cor', null, null, 'b')]);
        $this->form->setFielde([$cor]);
        $this->form->setFielde([new TLabel('inicio', null, null, 'b')]);
        $this->form->setFielde([$data_inicial, $hora_inicial, ':', $minuto_inicial]);
        $this->form->setFielde([new TLabel('fim', null, null, 'b')]);
        $this->form->setFielde([$data_final, $hora_final, ':', $minuto_final]);
        $this->form->setFielde([$titulo]);
        $this->form->setFielde([new TLabel('titulo', null, null, 'b')]);
        $this->form->setFielde([$descricao]);
        $this->form->setFielde([new TLabel('descricao', null, null, 'b')]);

        $this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:save green');
        $this->form->addAction(_t('Clear'), new TAction(array($this, 'onEdit')), 'fa:eraser orange');
        $this->form->addAction(_t('Delete'), new TAction(array($this, 'onDelete')), 'fa:trash-alt red');
        $this->form->addActionLink(_t('Close'), new TAction(array($this, 'onClose')), 'fa:times red');

        parent::add($this->form);
    }

    public static function onClose($param)
    {
        TScript::create('Template.closeRightPanel()');
    }

    public static function onChangeStartHour($param = NULL)
    {
        $obj = new stdClass;

        if (empty($param['minuto_inicial'])) {
            $obj->minuto_inicial = '0';
            TForm::sendData('form_event', $obj);
        }

        if (empty($param['hora_final']) and empty($param['minuto_final'])) {
            $obj->hora_final = $param['hora_inicial'] + 1;
            $obj->hora_final = '0';
            TForm::sendData('form_event', $obj);
        }
    }

    public static function onChangeEndtHour($param = NULL)
    {
        $obj = new stdClass;

        if (empty($param['minuto_final'])) {
            $obj->minuto_final = '0';
            TForm::sendData('form_event', $obj);
        }
    }

    public static function onChangeStartDate($param = Null)
    {
        if (empty($param['data_final']) and !empty($param['data_inicial'])) {
            $obj = new stdClass;
            $obj->data_inicial = '0';
            TForm::sendData('form_event', $obj);
        }
    }

    public static function onChangeEndDate($param = Null)
    {
        if (empty($param['hora_final']) and empty($param['minuto_final']) and !empty($param['hora_inicial'])) {
            $obj = new stdClass;
            $obj->hora_final = min($param['hora_inicial'], 22) + 1;
            $obj->minuto_final = '0';
            TForm::sendData('form_event', $obj);
        }
    }

    public static function onSave($param)
    {
        try {
            TTransaction::open('db_condominio');

            $data = (object) $param;

            $object = new Evento;

            $object->cor = $data->cor;
            $object->id = $data->id;
            $object->titulo = $data->titulo;
            $object->descricao = $data->descricao;
            $object->inicio = $data->data_inicial . ' ' . str_pad($data->hora_inicial, 2, '0', STR_PAD_LEFT) . ':' . str_pad($data->minuto_inicial, 2, '0', STR_PAD_LEFT) . ':00';
            $object->fom = $data->data_final . ' ' . str_pad($data->hora_final, 2, '0', STR_PAD_LEFT) . ':' . str_pad($data->minuto_final, 2, '0', STR_PAD_LEFT) . ':00';
            $object->system_user_id = TSession::getValue('userid');
            $object->store();

            TTransaction::close();


            TTransaction::close('Tempate.colseRightPanel');

            $posAction = new TAction(array('calendarioView', 'onReload'));
            $posAction->setParameter('target_container', 'adianti_div_content');
            $posAction->setParameter('view', $data->view);
            $posAction->setParameter('date', $data->data_inicial);

            new TMessage('info', TAdiantiCoreTransnslator::tranlate('Record Save'), $posAction);
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onEdit($param)
    {
        try {
            if (isset($param['key'])) {
                $key = $param['key'];
                TTransaction::open('db_condominio');

                $object = new Evento($key);

                if ($object->system_user_id !== TSession::getValue('userid')) {
                    throw new Exception(_t('Permissin denies'));
                }
                $data = new stdClass;
                $data->id             = $object->id;
                $data->cor            = $object->cor;
                $data->titulo         = $object->titulo;
                $data->descricao      = $object->descricao;
                $data->data_inicial   = substr($object->inicial, 0, 10);
                $data->hora_inicial   = substr($object->inicial, 11, 2);
                $data->minuto_inicial = substr($object->inicial, 14, 2);
                $data->data_final     = substr($object->fim, 0, 10);
                $data->hora_final     = substr($object->fim, 11, 2);
                $data->minuto_final   = substr($object->fim, 14, 2);
                $data->view = $param['view'];

                $this->form->setData($data);

                TTransaction::close();
            } else {
                $this->form->clear();
            }
        } 
        catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public static function onDelete($param)
    {
        $action = new TAction(array('calendarioForm', 'Delete'));
        $action->setParameters($param);

        new TQuestion(AdiantiCoreTranslator::translate('Do you really whant to delete?'),$action);
    }

    public static function Delete($param)
    {
        try
        {
            $key = $param['id'];
            TTransaction::open('db_condominio');

            $object = new Evento($key, FALSE);

            $object->delete();

            TTransaction::close('Tempate.colseRightPanel');

            $posAction = new TAction(array('calendarioView', 'onReload'));
            $posAction->setParameter('target_container', 'adianti_div_content');
            $posAction->setParameter('view', $param['view']);
            $posAction->setParameter('date', $param['data_inicial']);

            new TMessage('info', TAdiantiCoreTransnslator::tranlate('Record deletado'), $posAction);


            TTransaction::close();
        }
        catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onStartEdit($param)
    {
        $this->form-> clear();
        $data = new stdClass;
        $data->view = $param['view'];
        $data->cor = '#3a87ad';

        if ($param['date'])
        {
            if (strlen($param['date']) == 10)
            {
                $data->data_inicial = $param['date'];
                $data->data_final = $param['date'];
            }

            if (strlen($param['date']) == 19)
            {
                $data->data_inicial   = substr($param['date'],0, 10);
                $data->hora_inicial   = substr($param['date'],11, 2);
                $data->minuto_inicial = substr($param['date'], 14, 2);

                $data->data_final   = substr($param['date'],0, 10);
                $data->hora_final   = substr($param['date'],11, 2)+1;
                $data->minuto_final = substr($param['date'], 14, 2);
            }
            $this->form->setData($data);
        }
    }

    public static function onUpdateEvento($param)
    {
        try
        {
            if (isset($param['id']))
            {
                $key = $param['id'];
                TTransaction::open('db_condominio');

                
                $object = new Evento($key);
                $object->inicio = str_replace('T', ' ', $param['start_time']);
                $object->fin = str_replace('T', ' ', $param['end_time']);

                TTransaction::close();
            }
        }
        catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}
