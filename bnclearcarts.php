<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class BNClearCarts extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'bnclearcarts';
        $this->tab = 'administration';
        $this->version = '0.1.0';
        $this->author = 'Brand New srl';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Clear Carts');
        $this->description = $this->l('Clear carts for a customer when an order is made by such customer.');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        return parent::install() &&
            $this->registerHook('actionObjectOrderAddAfter');
    }

    public function hookActionObjectOrderAddAfter($params)
    {
        $sql = 'UPDATE `'._DB_PREFIX_.'cart` as c
            LEFT JOIN `'._DB_PREFIX_.'orders` as o ON c.id_cart = o.`id_cart`
            SET c.`date_upd` = \''.date('Y-m-d H:i:s', 0).'\'
            WHERE c.`id_customer` = '.(int)$params['object']->id_customer .'
            AND c.`id_shop` = '.$this->context->shop->id.'
            AND o.id_order IS NULL';

        return Db::getInstance()->execute($sql);
    }
}
