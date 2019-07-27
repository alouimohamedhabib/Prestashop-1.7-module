<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once('mymoduleClass.php');

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class Mymodule extends Module implements WidgetInterface
{
    public function __construct()
    {
        $this->name = 'mymodule';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Aloui Mohamed Habib';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Udemy module for prestashop 1.7');
        $this->description = $this->l('A module created for the purpose of Udemy course');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('MYMODULE_NAME')) {
            $this->warning = $this->l('No name provided');
        }
    }

    /**
     * @return bool
     * @throws PrestaShopException
     */

    public function install()
    {

        /**
         * create new Hook
         */

        /*
         * Check that the Multistore feature is enabled, and if so, set the current context
         * to all shops on this installation of PrestaShop.
         */

        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        /**
         * Check that the module parent class is installed.
         * Check that the module can be attached to the leftColumn hook.
         * Check that the module can be attached to the header hook.
         * Create the MYMODULE_NAME configuration setting, setting its value to “my friend”.
         */

        if (!parent::install() ||
            !$this->registerHook('displayAtSpecificPlace') ||
            !$this->registerHook('rightColumn') ||
            !$this->registerHook('header') ||
            !$this->createDatabaseTable() ||
            !$this->installTab('AdminCatalog', 'AdminMyModule', 'Udemy Admin controller') ||
            !Configuration::updateValue('MYMODULE_NAME', 'my friend')
        ) {
            return false;
        }
        return true;
    }

    /**
     * Create new tab for the AdminController
     * @param $parent
     * @param $class_name
     * @param $name
     * @return int
     */
    public function installTab($parent, $class_name, $name)
    {
        $tab = new Tab();
        $tab->id_parent = (int)Tab::getIdFromClassName($parent);
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang)
            $tab->name[$lang['id_lang']] = $name;
        $tab->class_name = $class_name;
        $tab->module = $this->name;
        $tab->active = 1;
        return $tab->add();
    }

    public function uninstallTab($class_name)
    {
        // Retrieve Tab ID
        $id_tab = (int)Tab::getIdFromClassName($class_name);
        // Load tab
        $tab = new Tab((int)$id_tab);
        // Delete it
        return $tab->delete();
    }

    /**
     * Create database
     */

    public function createDatabaseTable()
    {
        return Db::getInstance()->execute('
                CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'mymodule` (
                `id_mymodule` int(11) NOT NULL AUTO_INCREMENT,
                `parameter`varchar(255) NOT NULL,
                PRIMARY KEY (`id_mymodule`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 ;'
        );
    }

    /**
     * Get configuration page
     */

    public function getContent()
    {

        $message = "";
        $configuration = Configuration::get('MYMODULE_NAME');
        if (Tools::getValue('MYMODULE_NAME')) {
            if (
            Validate::isGenericName(Tools::getValue('MYMODULE_NAME'))
            ) {


                $param = new MymoduleClass();
                $param->parameter = Tools::getValue('MYMODULE_NAME');


                if ($param->save()) {
                    $configuration = Tools::getValue('MYMODULE_NAME');
                    $message = $this->displayConfirmation("All went well");
                } else {
                    $message = $this->displayError("Something went wrong");
                }

            } else {
                echo "Something went wrong with the input value";
            }
        }
        return $message . $this->displayForm();


    }

    /**
     * Display form using Helper
     * @return string
     */

    public function displayForm()
    {
        // Init Fields form array
        $fieldsForm[0]['form'] = [
            'legend' => [
                'title' => $this->l('Settings of my module'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Configuration value'),
                    'name' => 'MYMODULE_NAME',
                    'size' => 20,
                    'required' => true
                ]
            ],
            'submit' => [
                'title' => $this->l('Save the changes'),
                'class' => 'btn btn-default pull-right'
            ]
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        // Load current value
        $helper->fields_value['MYMODULE_NAME'] = Configuration::get('MYMODULE_NAME');
        return $helper->generateForm($fieldsForm);
    }


    /**
     * @return bool
     */
    public function uninstall()
    {

        // Storing a serialized array.
        Configuration::updateValue('MYMODULE_SETTINGS', serialize(array(true, true, false)));

        // Retrieving the array.
        $configuration_array = unserialize(Configuration::get('MYMODULE_SETTINGS'));


        if (!parent::uninstall() ||
            !Configuration::deleteByName('MYMODULE_NAME') ||
            !$this->uninstallTab('AdminMyModule')
        ) {
            return false;
        }

        return true;
    }

    public function hookHeader($params)
    {
        return "Hello from " . Configuration::get('MYMODULE_NAME');
    }

    public function renderWidget($hookName, array $configuration)
    {
        return "Hello from " . Configuration::get('MYMODULE_NAME');
    }

    public function getWidgetVariables($hookName, array $configuration)
    {
        // TODO: Implement getWidgetVariables() method.
    }
}