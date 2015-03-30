<?php
/**
 *
 * This file is part of the 2015_02_InShower
 *
 * Copyright (c) 2015
 *
 * @file AbstractForm.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace AppModule\Forms;


use Nette\Application\UI\Form;

class AbstractForm extends Form implements IAbstractForm {

    protected $redirect = 'Registration:';


    public function create() {
        $renderer = $this->getRenderer();
        $renderer->wrappers['controls']['container'] = 'dl';
        $renderer->wrappers['pair']['container'] = 'div class="wrapper"';
        $renderer->wrappers['label']['container'] = 'dt';
        $renderer->wrappers['control']['container'] = 'dd';
    }


} 