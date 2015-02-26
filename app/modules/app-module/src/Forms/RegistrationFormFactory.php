<?php
/**
 *
 * This file is part of the 2015_02_Q10Plus
 *
 * Copyright (c) 2015
 *
 * @file RegistrationFormFactory.php
 * @author  Pavel Paulík <pavel.paulik1@gmail.com>
 */

namespace AppModule\Forms;


use AppModule\Entities\QuestionEntity;
use AppModule\Entities\UserEntity;
use AppModule\Managers\TransactionListener;
use AppModule\Managers\UserManager;
use Kdyby\Translation\ITranslator;
use Kdyby\Translation\PrefixedTranslator;
use Kdyby\Translation\Translator;
use Nette\Forms\Form;
use Nette;


interface IRegistrationFormFactory
{
    /** @return RegistrationFormFactory */
    function create();
}


class RegistrationFormFactory extends BasicForm implements IRegistrationFormFactory
{
    const MIN_YEARS = 16;

    protected $redirect = 'Registration:thanks';

    /** @var string */
    private $locale;

    /** @var \AppModule\Managers\TransactionListener */
    private $transitionListener;

    /** @var UserManager */
    private $userManager;


    /**
     * @param TransactionListener                       $transactionListener
     * @param UserManager                               $userManager
     * @param ITranslator|\Kdyby\Translation\Translator $translator
     */
    public function __construct(TransactionListener $transactionListener, UserManager $userManager, ITranslator $translator)
    {
        parent::__construct();
        $this->transitionListener = $transactionListener;
        $this->userManager = $userManager;
        $this->setTranslator($translator);
        $this->locale = $translator->getLocale();
    }


    /** @return RegistrationFormFactory */
    function create()
    {
        $this->addGroup();

        $this->addRadioList('gender', 'pohlaví', array(0 => 'žena', 1 => 'muž'))
            ->setValue(0)
            ->setDefaultValue(0)
            ->addRule(Form::FILLED, 'zvolte_pohlaví')
            ->setAttribute("tabindex",1)
            ->controlPrototype->class = 'inline-item';

        $this->addText('firstname', 'jméno')
            ->addRule(Form::FILLED, 'vyplňte_vaše_křestní_jméno')
            ->setAttribute('placeholder', 'jméno')
            ->setAttribute("tabindex",4)
            ->controlPrototype->class = 'id-firstname';

        $this->addText('lastname', 'příjmení')
            ->addRule(Form::FILLED, 'vyplňte_vaše_příjmení')
            ->setAttribute('placeholder', 'příjmení')
            ->setAttribute("tabindex",3)
            ->controlPrototype->class = 'id-lastname';

        $this->addText('email', 'e-mail')
            ->addRule(Form::EMAIL, 'vyplňte_platný_e-mail')
            ->addRule(Form::FILLED, 'vyplňte_e-mail')
            ->setAttribute('placeholder', 'e-mail')
            ->setAttribute("tabindex",5)
            ->controlPrototype->class = 'no-margin';

        $this->addText('street', 'ulice')
            ->addRule(Form::FILLED, 'vyplňte_vaši_ulici')
            ->setAttribute('placeholder', 'ulice')
            ->setAttribute("tabindex",12)
            ->controlPrototype->class = 'no-margin input-short';

        $this->addText('strno', 'č.p.')
            ->addRule(Form::FILLED, 'vyplňte_číslo_popisné')
            // ->addRule(Form::PATTERN, 'vyplňte_číslo_popisné_správně', '[\/0-9]+[aA-zZ]*')
            ->setAttribute('placeholder', 'č.p.')
            ->setAttribute("tabindex",13)
            ->controlPrototype->class = 'input-shorter';


        $this->addText('zip', 'PSČ')
            ->addRule(Form::FILLED, 'vyplňte_psč')
            ->setAttribute('placeholder', 'PSČ')
            ->setAttribute("tabindex",10)
            ->controlPrototype->class = 'no-margin input-shorter';

        if ($this->locale == 'hu') {
            $this['zip']
                ->addRule(Form::INTEGER, 'vyplňte_psč_správně')
                ->addRule(Form::LENGTH, 'vyplňte_psč_správně', 4);

        } elseif ($this->locale == 'sk') {
            $this['zip']
                ->addRule(Form::LENGTH, 'vyplňte_psč_správně', 5)
                ->addRule(Form::PATTERN, 'vyplňte_psč_správně', '([0-9]){5}');

        } else {
            $this['zip']
                ->addRule(Form::LENGTH, 'vyplňte_psč_správně', 5)
                ->addRule(Form::PATTERN, 'vyplňte_psč_správně', '([1-9]{1})([0-9]){4}');
        }

        $this->addText('city', 'město')
            ->addRule(Form::FILLED, 'vyplňte_město')
            ->setAttribute('placeholder', 'město')
            ->setAttribute("tabindex",11)
            ->controlPrototype->class = 'input-short';

        $days = array();
        foreach (range( 1, 31 ) as $index) {
            $days[$index] = $index;
        }

        $this->addSelect('day', 'den', $days)
            ->setPrompt($this->translator->translate('forms.registrationForm.den'))
            ->setTranslator(null)
            ->addRule(Form::FILLED, 'vyplňte_den_narození')
            ->setAttribute("tabindex",9)
            ->setAttribute('placeholder', 'den')
            ->addCondition(Form::FILLED)
            ->addRule(Form::RANGE, 'vyplňte_den_narození_správně', array(1,31));
        $this['day']->controlPrototype->class = 'select-day';

        $month = array();
        foreach (range( 1, 12 ) as $index) {
            $month[$index] = $index;
        }
        $this->addSelect('month', 'měsíc', $month)
            ->setPrompt($this->translator->translate('forms.registrationForm.měsíc'))
            ->setTranslator(null)
            ->setAttribute("tabindex",8)
            ->addRule(Form::FILLED, 'vyplňte_měsíc_narození')
            ->addCondition(Form::FILLED)
            ->addRule(Form::RANGE, 'vyplňte_měsíc_narození_správně', array(1,12));
        $this['month']->controlPrototype->class = 'select-month';

        $currentYear = intval(date('Y'));
        $years = array();
        for ($index = $currentYear; $index >= 1900; $index--) {
            $years[$index] = $index;
        }
        $this->addSelect('year', 'rok', $years)
            ->setPrompt($this->translator->translate('forms.registrationForm.rok'))
            ->setTranslator(null)
            ->addRule(Form::FILLED, 'vyplňte_rok_narození')
            ->setAttribute("tabindex",7)
            ->addCondition(Form::FILLED)
            ->addRule(Form::RANGE, 'musíte_být_starší_x_let', array(null, $currentYear - self::MIN_YEARS));
        $this['year']->controlPrototype->class = 'select-year';

        $this['day']->addConditionOn($this['month'], Form::FILLED)->addRule(Form::RANGE, 'vyplňte_den_narození_správně', array(1,31));
        $this['day']->addConditionOn($this['year'], Form::FILLED)->addRule(Form::RANGE, 'vyplňte_den_narození_správně', array(1,31));
        $this['month']->addConditionOn($this['day'], Form::FILLED)->addRule(Form::RANGE, 'vyplňte_měsíc_narození_správně', array(1,12));
        $this['month']->addConditionOn($this['year'], Form::FILLED)->addRule(Form::RANGE, 'vyplňte_měsíc_narození_správně', array(1,12));
        $this['year']->addConditionOn($this['day'], Form::FILLED)->addRule(Form::RANGE, 'musíte_být_starší_x_let', array(null, $currentYear - self::MIN_YEARS));
        $this['year']->addConditionOn($this['month'], Form::FILLED)->addRule(Form::RANGE, 'musíte_být_starší_x_let', array(null, $currentYear - self::MIN_YEARS));

        $this->addCheckbox('privacy', 'souhlas_se_zařazením_do_soutěže')
            ->addRule(Form::FILLED, 'potvrďte_souhlas_s_pravidly_soutěže')
            ->controlPrototype->class = 'id-privacy';

        $this->addCheckbox('newsletter')
            ->controlPrototype->class = 'id-newsletter';




        $btn = $this->addSubmit('send', 'odeslat')->setAttribute('class', 'send-button button next')->getControlPrototype()
            ->setName("button")
            ->create('strong', $this->translator->translate('forms.registrationForm.odeslat'));

        $this->onSuccess[] = array($this, 'processRegistrationForm');
        $this->onValidate[] = array($this, 'processValidationForm');
        $this->getElementPrototype()->class = 'registration-form';

    }


    public function processValidationForm(BasicForm $form)
    {
        $presenter = $this->getPresenter();
        $section = $presenter->getSession($this->section);

        if (!$section->quizOne)
            $presenter->redirect('Homepage:');
    }

    public function processRegistrationForm(BasicForm $form)
    {
        $presenter = $this->getPresenter();
        $section = $presenter->getSession($this->section);

        /** @var $entity UserEntity */
        $entity = $this->getEntity();

        if (($questions = $entity->getQuestions()) === NULL) {
            $questions = new QuestionEntity();
        }

        foreach ($section as $key => $val) {
            if (isset($questions->$key)) {
                $questions->$key = $val;
            }
        }

        $entity->setQuestions($questions->setLang($this->locale));

        try {
            $em = $this->getEntityMapper()->getEntityManager();
            $em->persist($entity);
            $em->flush();

        } catch (\Kdyby\Doctrine\DuplicateEntryException $exc) {
            if (Nette\Utils\Strings::contains($exc->getMessage(), "1062")) {
                $message = 'email_již_existuje';
                $form->getPresenter()->flashMessage($presenter->translator->translate($message));
                return;
            }

            throw new \Kdyby\Doctrine\DuplicateEntryException($exc);
        }

        $form->getPresenter()->redirect($form->getRedirect());
    }

}